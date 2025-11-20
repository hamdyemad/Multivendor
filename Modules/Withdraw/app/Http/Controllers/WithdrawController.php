<?php

namespace Modules\Withdraw\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\LanguageService;
use Illuminate\Http\Request;
use Modules\Order\app\Models\OrderProduct;
use Modules\SystemSetting\app\Resources\VendorResource;
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
        $searchValue = $request->input('search.value', '');

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
                    $q->whereHas('vendor', function ($q2) use ($searchValue) {
                        $q2->where('name', 'like', "%$searchValue%");
                    })->orWhereHas('admin', function ($q2) use ($searchValue) {
                        $q2->where('name', 'like', "%$searchValue%");
                    });
                });
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
                    'before_sending_money' => number_format($item->before_sending_money, 2) . " EGP",
                    'sent_amount' => number_format($item->sent_amount, 2) . " EGP",
                    'after_sending_amount' => number_format($item->after_sending_amount, 2) . " EGP",
                    'created_at' => $item->created_at->format('Y-m-d H:i:s'),
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
        return view('withdraw::all_transactions', compact('languages'));
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

        // get latest withdraw transaction for this vendor
        $orders = OrderProduct::where("vendor_id", $data["vendor_id"]);
        $total_vendor_balance = $orders->sum("price") - ($orders->sum("price") * ($orders->first()->commission / 100));

        $last_withdraw = Withdraw::where(function ($q) use ($data) {
            $q->where('sender_id', $data['vendor_id'])
                ->orWhere('reciever_id', $data['vendor_id']);
        })
            ->where('status', 'accepted')
            ->latest()
            ->first();

        $final_last_before_sending_money = $last_withdraw ? $last_withdraw->after_sending_amount : $total_vendor_balance;

        if ($data["sent_amount"] > $final_last_before_sending_money) {
            return redirect()->back()
                ->with('error', "Invalid amount !");
        }
        Withdraw::create([
            "request_from" => "admin",
            "sender_id" => auth()->user()->id,
            "reciever_id" => $data["vendor_id"],
            "before_sending_money" => $final_last_before_sending_money,
            "sent_amount" => $data["sent_amount"],
            "after_sending_amount" => $final_last_before_sending_money - $data["sent_amount"],
            "invoice" => $data["invoice"],
            "status" => "accepted"
        ]);

        return redirect()->route('admin.sendMoney')
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

        $final_remaining = floatval(str_replace(',', '', $general_info['remaining'])) - floatval(str_replace(',', '', $general_info['waiting_approve_requests'])) ;

        return view('withdraw::send_money_request', compact('languages', 'general_info', 'vendor_name', 'final_remaining'));
    }

    public function sendMoneyRequestAction(Request $request)
    {
        if (auth()->user()->user_type->name == "super_admin") {
            abort(404);
        }

        $data = $request->all();

        $vendor_id = auth()->user()->vendor->id;

        // get latest withdraw transaction for this vendor
        $orders = OrderProduct::where("vendor_id", $vendor_id);
        $total_vendor_balance = $orders->sum("price") - ($orders->sum("price") * ($orders->first()->commission / 100));

        $last_withdraw = Withdraw::where(function ($q) use ($vendor_id) {
            $q->where('sender_id', $vendor_id)
                ->orWhere('reciever_id', $vendor_id);
        })
            ->where('status', 'accepted')
            ->latest()
            ->first();

        $final_last_before_sending_money = $last_withdraw ? $last_withdraw->after_sending_amount : $total_vendor_balance;

        if ($data["sent_amount"] > $final_last_before_sending_money) {
            return redirect()->back()
                ->with('error', "Invalid amount !");
        }
        Withdraw::create([
            "request_from" => "admin",
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
                'before_sending_money' => number_format($item->before_sending_money, 2) . " EGP",
                'sent_amount' => number_format($item->sent_amount, 2) . " EGP",
                'after_sending_amount' => number_format($item->after_sending_amount, 2) . " EGP",
                'created_at' => $item->created_at->format('Y-m-d H:i:s'),
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
        return view('withdraw::all_transactions_requests', compact('languages', 'status', 'vendor'));
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

        return redirect()->route('admin.sendMoney')
            ->with('success', "Request is updated successfully !");
    }
}
