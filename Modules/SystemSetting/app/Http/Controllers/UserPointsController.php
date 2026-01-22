<?php

namespace Modules\SystemSetting\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\LanguageService;
use Illuminate\Http\Request;
use Modules\Customer\app\Services\CustomerService;

class UserPointsController extends Controller
{

    public function __construct(
        protected CustomerService $customerService,
        protected LanguageService $languageService
    ) {
        $this->middleware('can:points-settings.user-points.index')->only(['index', 'datatable', 'transactionsView', 'transactions']);
        $this->middleware('can:points-settings.user-points.adjust')->only(['adjustPoints']);
    }
    /**
     * Display user points list
     */
    public function index()
    {
        $data = [
            'title' => trans('systemsetting::points.user_points_management'),
        ];

        return view('systemsetting::user_points.index', $data);
    }

    /**
     * Get user points datatable
     */
    public function datatable(Request $request)
    {
        try {
            // Get all customers with their points
            $query = \Modules\Customer\app\Models\Customer::with(['country'])->latest();

            // Search filter
            $searchValue = $request->input('search');
            if ($searchValue) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('first_name', 'like', "%$searchValue%")
                      ->orWhere('last_name', 'like', "%$searchValue%")
                      ->orWhere('email', 'like', "%$searchValue%")
                      ->orWhere('phone', 'like', "%$searchValue%");
                });
            }

            // Total records
            $totalRecords = $query->count();

            // Apply pagination
            $perPage = $request->input('length', 10);
            $skip = $request->input('start', 0);
            $customers = $query->skip($skip)->take($perPage)->get();

            // Format data for datatable
            $data = [];
            foreach ($customers as $index => $customer) {
                // Use dynamic calculations from Customer model
                $data[] = [
                    'id' => $customer->id,
                    'index' => $skip + $index + 1,
                    'customer_information' => [
                        'full_name' => $customer->full_name ?? '-',
                        'email' => strtolower($customer->email ?? '-'),
                        'phone' => $customer->phone ?? '-',
                    ],
                    'total_points' => number_format($customer->total_points, 2),
                    'earned_points' => number_format($customer->earned_points, 2),
                    'redeemed_points' => number_format($customer->redeemed_points, 2),
                    'adjusted_points' => number_format($customer->adjusted_points, 2),
                    'available_points' => number_format($customer->available_points, 2),
                ];
            }

            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show transactions view
     */
    public function transactionsView($lang, $countryCode, $userId)
    {
        try {
            $customer = \Modules\Customer\app\Models\Customer::findOrFail($userId);
            $languages = $this->languageService->getAll();

            // Use dynamic calculations from Customer model
            $data = [
                'title' => trans('systemsetting::points.transaction_history'),
                'customer' => $customer,
                'languages' => $languages,
                'total_points' => $customer->total_points,
                'earned_points' => $customer->earned_points,
                'redeemed_points' => $customer->redeemed_points,
                'expired_points' => $customer->expired_points,
                'available_points' => $customer->available_points,
                'adjusted_points' => $customer->adjusted_points,
            ];

            return view('systemsetting::user_points.transactions', $data);
        } catch (\Exception $e) {
            return redirect()->route('admin.user-points.index')->with('error', trans('common.error_occurred'));
        }
    }

    /**
     * Get user points transactions
     */
    public function transactions(Request $request, $lang, $countryCode, $userId)
    {
        try {
            $query = \Modules\SystemSetting\app\Models\UserPointsTransaction::where('user_id', $userId)
                ->latest();

            // Filter by type
            $type = $request->input('type');
            if ($type) {
                $query->where('type', $type);
            }

            // Filter by created_from date
            $createdFrom = $request->input('created_from');
            if ($createdFrom) {
                $query->whereDate('created_at', '>=', $createdFrom);
            }

            // Filter by created_to date
            $createdTo = $request->input('created_to');
            if ($createdTo) {
                $query->whereDate('created_at', '<=', $createdTo);
            }

            // Total records
            $totalRecords = $query->count();

            // Apply pagination
            $perPage = $request->input('length', 10);
            $skip = $request->input('start', 0);
            $transactions = $query->skip($skip)->take($perPage)->get();

            // Format data
            $data = [];
            foreach ($transactions as $index => $transaction) {
                // Get order number if related to an order
                $orderNumber = null;
                if ($transaction->transactionable_type && 
                    (str_contains($transaction->transactionable_type, 'Order') || $transaction->transactionable_type === 'order_checkout') &&
                    $transaction->transactionable_id) {
                    $order = \Modules\Order\app\Models\Order::withoutGlobalScopes()->find($transaction->transactionable_id);
                    $orderNumber = $order?->order_number;
                }

                $data[] = [
                    'id' => $transaction->id,
                    'index' => $skip + $index + 1,
                    'points' => number_format($transaction->points, 2),
                    'type' => $transaction->type,
                    'type_label' => trans('systemsetting::points.type_' . $transaction->type),
                    'description' => truncateString($transaction->description),
                    'full_description' => $transaction->description,
                    'expires_at' => $transaction->expires_at ? $transaction->expires_at : '-',
                    'is_expired' => $transaction->is_expired,
                    'created_at' => $transaction->created_at,
                    'transactionable_type' => $transaction->transactionable_type,
                    'transactionable_id' => $transaction->transactionable_id,
                    'order_number' => $orderNumber,
                ];
            }

            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Adjust customer points
     */
    public function adjustPoints(Request $request, $lang, $countryCode, $userId)
    {

        try {
            $validated = $request->validate([
                'points' => 'required|numeric',
                'description_en' => 'required|string|max:500',
                'description_ar' => 'required|string|max:500',
            ]);

            // Get customer
            $customer = \Modules\Customer\app\Models\Customer::findOrFail($userId);

            // Create transaction record with correct type
            $transaction = \Modules\SystemSetting\app\Models\UserPointsTransaction::create([
                'user_id' => $userId,
                'points' => $validated['points'],
                'type' => 'adjusted',
                'transactionable_id' => null,
                'transactionable_type' => null,
            ]);

            // Store descriptions in both languages
            $transaction->setTranslation('description', 'en', $validated['description_en']);
            $transaction->setTranslation('description', 'ar', $validated['description_ar']);
            $transaction->save();

            // Refresh customer to get updated calculations
            $customer->refresh();

            return response()->json([
                'success' => true,
                'message' => trans('systemsetting::points.points_adjusted_successfully'),
                'data' => [
                    'total_points' => $customer->total_points,
                    'earned_points' => $customer->earned_points,
                    'adjusted_points' => $customer->adjusted_points,
                    'redeemed_points' => $customer->redeemed_points,
                    'available_points' => $customer->available_points,
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => trans('common.validation_error'),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('common.error_occurred'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
