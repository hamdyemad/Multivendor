<?php

namespace Modules\Withdraw\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\LanguageService;
use Illuminate\Http\Request;
use Modules\Order\app\Models\OrderProduct;
use Modules\SystemSetting\app\Resources\VendorResource;
use Modules\Vendor\app\Models\Vendor;
use Modules\Withdraw\app\Models\Withdraw;
use Modules\Withdraw\app\Services\WithdrawService;

class WithdrawController extends Controller
{
    public function __construct(
        protected WithdrawService $withdrawService,
        protected LanguageService $languageService,
    ) {}

    public function sendMoney()
    {
        if (auth()->user()->user_type->name == "vendor") {
            abort(404);
        }

        $vendors = $this->withdrawService->getVendor();
        $languages = $this->languageService->getAll();
        return view('withdraw::send_money', compact('languages', 'vendors'));
    }

    public function allTransactionsDatabase(Request $request)
    {
        if (auth()->user()->user_type->name == "vendor") {
            abort(404);
        }

        $perPage = $request->input('length', 10);
        $page = ($request->input('start', 0) / $perPage) + 1;
        $searchValue = $request->input('search', '');

        try {
            $query = Withdraw::with([
                'vendor.translations' => function ($q) {
                    $q->where('lang_key', 'name');
                },
                'admin'
            ]);

            // Filter by search
            if ($searchValue) {
                $query->where(function ($q) use ($searchValue) {
                    $q->whereHas('vendor.translations', function ($q2) use ($searchValue) {
                        $q2->where('lang_value', 'like', "%$searchValue%");
                    });
                });
            }

            // Filter by vendor
            if ($request->filled('vendor_filter')) {
                $query->where('reciever_id', $request->vendor_filter);
            }

            // Filter by status
            if ($request->filled('status_filter')) {
                $query->where('status', $request->status_filter);
            }

            // Filter by date range
            if ($request->filled('created_date_from')) {
                $query->whereDate('created_at', '>=', $request->created_date_from);
            }

            if ($request->filled('created_date_to')) {
                $query->whereDate('created_at', '<=', $request->created_date_to);
            }

            $totalRecords = $query->count();
            $dataPaginated = $query->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);

            // Prepare data for DataTable
            $data = $query->get()->map(function ($item) {
                // Get logo safely
                $logoPath = '';
                try {
                    if ($item->vendor && $item->vendor->logo && isset($item->vendor->logo->path)) {
                        $logoPath = asset('storage/' . $item->vendor->logo->path);
                    }
                } catch (\Exception $e) {
                    $logoPath = '';
                }

                // Get vendor name safely
                $vendorName = '-';
                try {
                    if ($item->vendor) {
                        // Try to get name from translations first, then fallback to direct name property
                        if ($item->vendor->translations && $item->vendor->translations->count() > 0) {
                            $vendorName = $item->vendor->translations->first()->lang_value ?? '-';
                        } else {
                            $vendorName = $item->vendor->name ?? '-';
                        }
                    }
                } catch (\Exception $e) {
                    $vendorName = '-';
                }

                return [
                    'id' => $item->id,
                    'vendor_logo' => $logoPath,
                    'vendor' => $vendorName,
                    'status' => $item->status,
                    'invoice' => $item->invoice_url,
                    'before_sending_money' => number_format($item->before_sending_money, 2) . ' ' . __('withdraw::withdraw.currency'),
                    'sent_amount' => number_format($item->sent_amount, 2) . ' ' . __('withdraw::withdraw.currency'),
                    'after_sending_amount' => number_format($item->after_sending_amount, 2) . ' ' . __('withdraw::withdraw.currency'),
                    'created_at' => $item->created_at,
                ];
            });

            return response()->json([
                'draw' => $request->input('draw', 1),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data,
                'current_page' => $dataPaginated->currentPage(),
                'last_page' => $dataPaginated->lastPage(),
                'per_page' => $dataPaginated->perPage(),
                'total' => $dataPaginated->total(),
                'from' => $dataPaginated->firstItem(),
                'to' => $dataPaginated->lastItem()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'draw' => $request->input('draw', 1),
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function allTransactions()
    {
        if (auth()->user()->user_type->name == "vendor") {
            abort(404);
        }

        $languages = $this->languageService->getAll();

        // Get vendors for filter dropdown
        $vendors = Vendor::with(['translations' => function ($q) {
            $q->where('lang_key', 'name');
        }])->get()->map(function ($vendor) {
            $vendorName = 'Unknown Vendor';
            try {
                if ($vendor->translations && $vendor->translations->count() > 0) {
                    $vendorName = $vendor->translations->first()->lang_value ?? 'Unknown Vendor';
                }
            } catch (\Exception $e) {
                $vendorName = 'Unknown Vendor';
            }

            return [
                'id' => $vendor->id,
                'name' => $vendorName
            ];
        });

        return view('withdraw::all_transactions', compact('languages', 'vendors'));
    }










    public function allVendorsTransactionsDatatable(Request $request)
    {
        if (auth()->user()->user_type->name == "vendor") {
            abort(404);
        }

        $perPage = $request->input('length', 10);
        $page = ($request->input('start', 0) / $perPage) + 1;
        $searchValue = $request->input('search', '');

        // Use simple query without problematic relationships
        $query = Vendor::query();

        // Search filter
        if ($searchValue) {
            $query->where(function($q) use ($searchValue) {
                $q->whereHas('translations', function($query) use ($searchValue) {
                    $query->where('lang_value', 'like', "%{$searchValue}%");
                });
            });
        }

        // Filter by vendor
        if ($request->filled('vendor_filter')) {
            $query->where('id', $request->vendor_filter);
        }

        // Filter by date range
        if ($request->filled('created_date_from')) {
            $query->whereDate('created_at', '>=', $request->created_date_from);
        }

        if ($request->filled('created_date_to')) {
            $query->whereDate('created_at', '<=', $request->created_date_to);
        }

        $totalRecords = $query->count();

        // Get vendors for this page
        $vendors = $query->orderBy('created_at', 'desc')
                       ->skip($request->input('start', 0))
                       ->take($perPage)
                       ->get();

        // Prepare data for DataTable with safe calculations
        $data = [];
        foreach ($vendors as $item) {
            $totalOrders = OrderProduct::where('vendor_id', $item->id)
                         ->sum('price') ?? 0;
            $totalSentMoney = Withdraw::where('reciever_id', $item->id)
                                    ->where('status', 'accepted')
                                    ->sum('sent_amount') ?? 0;

            $remaining = $totalOrders - $totalSentMoney;

            // Get logo safely
            $logoPath = asset('storage/' . $item->logo->path);

            // Get vendor name safely
            $vendorName = $item->name;
            $data[] = [
                'id' => $item->id,
                'vendor' => [
                    'logo' => $logoPath,
                    'name' => $vendorName
                ],
                'before_sending_money' => number_format($totalOrders, 2) . ' ' . __('withdraw::withdraw.currency'),
                'sent_amount' => number_format($totalSentMoney, 2) . ' ' . __('withdraw::withdraw.currency'),
                'after_sending_amount' => number_format($remaining, 2) . ' ' . __('withdraw::withdraw.currency'),
                'created_at' => $item->created_at,
            ];
        }

        return response()->json([
            'draw' => $request->input('draw', 1),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data
        ]);
    }

    public function allVendorsTransactions()
    {
        if (auth()->user()->user_type->name == "vendor") {
            abort(404);
        }

        $languages = $this->languageService->getAll();

        // Get vendors for filter dropdown
        $vendors = Vendor::with(['translations' => function ($q) {
            $q->where('lang_key', 'name');
        }])->get()->map(function ($vendor) {
            return [
                'id' => $vendor->id,
                'name' => optional($vendor->translations->first())->lang_value ?? $vendor->name ?? 'Unknown Vendor'
            ];
        });

        return view('withdraw::all_vendors_transactions', compact('languages', 'vendors'));
    }

    public function getVendorBalance($vendor_id)
    {
        return $vendors = $this->withdrawService->getVendorBalance($vendor_id);
    }

    public function sendMoneyToVendorAction(Request $request)
    {
        if (auth()->user()->user_type->name == "vendor") {
            abort(404);
        }

        $data = $request->all();

        // return $data ;

        // Get the vendor to access the user_id
        $vendor = Vendor::find($data["vendor_id"]);

        if (!$vendor) {
            return redirect()->back()
                ->with('error', "Vendor not found!");
        }

        if (!$vendor) {
            return redirect()->back()
                ->with('error', "Vendor is not associated with a user!");
        }

        $last_withdraw = Withdraw::where(function ($q) use ($vendor) {
            $q->where('reciever_id', $vendor->id);
        })
            ->where('status', 'accepted')
            ->latest()
            ->first();

        $final_last_before_sending_money = $last_withdraw ? $last_withdraw->after_sending_amount : $vendor->total_balance;
        if ($data["sent_amount"] > $final_last_before_sending_money) {
            return redirect()->back()
                ->with('error', "Invalid amount !");
        }

        Withdraw::create([
            "request_from" => "admin",
            "sender_id" => auth()->user()->id,
            "reciever_id" => $vendor->id,
            "before_sending_money" => $final_last_before_sending_money,
            "sent_amount" => $data["sent_amount"],
            "after_sending_amount" => $final_last_before_sending_money - $data["sent_amount"],
            "invoice" => $data["invoice"],
            "status" => "accepted"
        ]);

        return redirect()->route('admin.allTransactions')
            ->with('success', "Money sent successfully !");
    }

    public function sendMoneyRequest()
    {
        if (auth()->user()->user_type->name == "super_admin") {
            abort(404);
        }

        $languages = $this->languageService->getAll();
        $general_info = $this->getVendorBalance(auth()->user()->vendor->id);

        $vendor_name = auth()->user()->vendor->translations[0]->lang_value;

        $final_remaining = floatval(str_replace(',', '', $general_info['remaining'])) - floatval(str_replace(',', '', $general_info['waiting_approve_requests']));

        return view('withdraw::send_money_request', compact('languages', 'general_info', 'vendor_name', 'final_remaining'));
    }

    public function sendMoneyRequestAction(Request $request)
    {
        if (auth()->user()->user_type->name == "super_admin") {
            abort(404);
        }

        $data = $request->all();

        $vendor = auth()->user()->vendor;
        $vendor_id = $vendor->id;
        $user_id = auth()->user()->id;

        // get latest withdraw transaction for this vendor
        $orders = OrderProduct::where("vendor_id", $vendor_id);
        $total_vendor_balance = $orders->sum("price") - ($orders->sum("price") * ($orders->first()->commission / 100));

        $last_withdraw = Withdraw::where(function ($q) use ($user_id) {
            $q->where('sender_id', $user_id)
                ->orWhere('reciever_id', $user_id);
        })
            ->where('status', 'accepted')
            ->latest()
            ->first();

        $final_last_before_sending_money = $last_withdraw ? $last_withdraw->after_sending_amount : $total_vendor_balance;

        if ($data["sent_amount"] > $final_last_before_sending_money) {
            return redirect()->back()
                ->with('error', "Invalid amount !");
        }
        // Find an admin user to receive the request (get first super admin or admin)
        $adminUser = \App\Models\User::whereIn('user_type_id', [1, 2])->first();

        if (!$adminUser) {
            return redirect()->back()
                ->with('error', "No admin user found to receive the request!");
        }

        Withdraw::create([
            "request_from" => "vendor",
            // "sender_id" => $user_id,
            "reciever_id" => $vendor_id,
            "before_sending_money" => $final_last_before_sending_money,
            "sent_amount" => $data["sent_amount"],
            "after_sending_amount" => $final_last_before_sending_money - $data["sent_amount"],
            "status" => "new"
        ]);

        return redirect()->route('admin.sendMoneyRequest')
            ->with('success', "Money request sent successfully !");
    }

    public function transactionsRequestsDatatable(Request $request, $status)
    {
        $perPage = $request->input('length', 10);
        $page = ($request->input('start', 0) / $perPage) + 1;
        $searchValue = $request->input('search.value', '');

        // try {

        $query = Withdraw::where("status", $status);

        $vendor = auth()->user()->vendor;

        if ($vendor && $vendor->id) {
            $query->where("reciever_id", $vendor->id);
        }
        $query->with([
            'vendor.translations' => function ($q) {
                $q->where('lang_key', 'name');
            },
            'admin'
        ]);

        // Filter by search
        if ($searchValue) {
            $query->where(function ($q) use ($searchValue) {
                $q->whereHas('vendor', function ($q2) use ($searchValue) {
                    $q2->where('name', 'like', "%$searchValue%");
                })->orWhereHas('admin', function ($q2) use ($searchValue) {
                    $q2->where('name', 'like', "%$searchValue%");
                });
            });
        }

        // Filter by vendor
        if ($request->filled('vendor_filter')) {
            $query->where('reciever_id', $request->vendor_filter);
        }

        // Filter by date range
        if ($request->filled('created_date_from')) {
            $query->whereDate('created_at', '>=', $request->created_date_from);
        }

        if ($request->filled('created_date_to')) {
            $query->whereDate('created_at', '<=', $request->created_date_to);
        }

        $totalRecords = $query->count();
        $dataPaginated = $query->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);

        // Prepare data for DataTable
        $data = $query->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'vendor_logo' => asset('storage/' . $item->vendor->logo->path),
                'vendor' => $item->vendor
                    ? optional($item->vendor->translations->first())->lang_value ?? $item->vendor->name
                    : '-',
                'status' => $item->status,
                'invoice' => $item->invoice_url,
                'before_sending_money' => number_format($item->before_sending_money, 2) . ' ' . __("common.currency"),
                'sent_amount' => number_format($item->sent_amount, 2) . ' ' . __("common.currency"),
                'after_sending_amount' => number_format($item->after_sending_amount, 2) . ' ' . __("common.currency"),
                'created_at' => $item->created_at,
            ];
        });

        return response()->json([
            'draw' => $request->input('draw', 1),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data,
            'current_page' => $dataPaginated->currentPage(),
            'last_page' => $dataPaginated->lastPage(),
            'per_page' => $dataPaginated->perPage(),
            'total' => $dataPaginated->total(),
            'from' => $dataPaginated->firstItem(),
            'to' => $dataPaginated->lastItem()
        ]);
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'draw' => $request->input('draw', 1),
        //         'data' => [],
        //         'recordsTotal' => 0,
        //         'recordsFiltered' => 0,
        //         'error' => $e->getMessage()
        //     ], 500);
        // }
    }

    public function transactionsRequests($status)
    {
        $languages = $this->languageService->getAll();
        $vendor = auth()->user()->vendor;

        // Get vendors for filter dropdown
        $vendors = Vendor::with(['translations' => function ($q) {
            $q->where('lang_key', 'name');
        }])->get()->map(function ($vendor) {
            return [
                'id' => $vendor->id,
                'name' => optional($vendor->translations->first())->lang_value ?? $vendor->name ?? 'Unknown Vendor'
            ];
        });

        return view('withdraw::all_transactions_requests', compact('languages', 'status', 'vendor', 'vendors'));
    }

    public function changeTransactionRequestsStatus(Request $request)
    {
        // get request
        $data = $request->all();
        $withdraw = Withdraw::find($request["request_id"]);

        if ($data["status"] == "accepted") {
            $data["sender_id"] = auth()->user()->id;

            $withdraw->update($data);
        } elseif ($data["status"] == "rejected") {
            $withdraw->update($data);
        }

        return redirect()->route('admin.allTransactions')
            ->with('success', "Request is updated successfully !");
    }
}
