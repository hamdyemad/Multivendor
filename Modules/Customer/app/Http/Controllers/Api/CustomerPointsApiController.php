<?php

namespace Modules\Customer\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\SystemSetting\app\Models\PointsSetting;
use Modules\SystemSetting\app\Models\PointsSystem;
use Modules\SystemSetting\app\Models\UserPoints;
use Modules\SystemSetting\app\Models\UserPointsTransaction;

class CustomerPointsApiController extends Controller
{
    use Res;

    /**
     * Get customer's points summary
     */
    public function myPoints(Request $request)
    {
        try {
            $customer = $request->user();

            // Use dynamic calculations from Customer model
            $currencyId = $customer->country?->currency?->id;
            $settings = $currencyId ? PointsSetting::where('currency_id', $currencyId)->first() : null;
            
            // Calculate points value in currency
            $pointsValue = 0;
            if ($customer->total_points > 0 && $settings && $settings->points_per_currency > 0) {
                $pointsValue = ($customer->total_points / $settings->points_per_currency) * $settings->currency_per_point;
            }

            // Get expiring soon transactions
            $expiringSoon = UserPointsTransaction::where('user_id', $customer->id)
                ->where('expires_at', '>', now())
                ->where('expires_at', '<=', now()->addDays(30))
                ->where('points', '>', 0)
                ->orderBy('expires_at')
                ->get();

            $data = [
                'total_points' => round($customer->total_points, 2),
                'points_value' => $pointsValue ? round($pointsValue, 2) : 0,
                'earned_points' => round($customer->earned_points, 2),
                'redeemed_points' => round($customer->redeemed_points, 2),
                'expired_points' => round($customer->expired_points, 2),
                'adjusted_points' => round($customer->adjusted_points, 2),
                'available_points' => round($customer->available_points, 2),
                'expiring_soon' => $expiringSoon
            ];

            return $this->sendRes(
                trans('systemsetting::points.points_retrieved_successfully'),
                true,
                $data
            );
        } catch (\Exception $e) {
            return $this->sendRes(
                trans('common.error_occurred') . ': ' . $e->getMessage(),
                false,
                [],
                [],
                500
            );
        }
    }

    /**
     * Get customer's points transactions
     */
    public function transactions(Request $request)
    {
        try {
            $user = $request->user();

            // Get filter parameters
            $type = $request->input('type');
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);

            // Build query
            $query = UserPointsTransaction::where('user_id', $user->id);
            // Apply type filter
            if ($type) {
                $query->where('type', $type);
            }

            // Get total count before pagination
            $total = $query->count();
            // Get paginated transactions
            $transactions = $query->latest()
                ->paginate($perPage);

            // Format transactions
            $formattedTransactions = $transactions->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'points' => number_format($transaction->points, 2),
                    'type' => $transaction->type,
                    'type_label' => trans('systemsetting::points.type_' . $transaction->type),
                    'description' => $transaction->description,
                    'expires_at' => $transaction->expires_at,
                    'is_expired' => $transaction->is_expired,
                    'created_at' => $transaction->created_at,
                ];
            });

            $data = [
                'items' => $formattedTransactions,
                'pagination' => [
                    'total' => $total,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'last_page' => $transactions->lastPage(),
                    'from' => $transactions->firstItem(),
                    'to' => $transactions->lastItem(),
                ],
            ];

            return $this->sendRes(
                trans('systemsetting::points.transactions_retrieved_successfully'),
                true,
                $data
            );
        } catch (\Exception $e) {
            return $this->sendRes(
                trans('common.error_occurred'),
                false,
                [],
                [],
                500
            );
        }
    }


    public function settings(Request $request)
    {
        try {
            $user = auth()->user();
            // Get points settings
            $settings = [
                'system_enabled' => PointsSystem::isEnabled(),
                'points_per_currency' => floatval(number_format(PointsSetting::where('currency_id', $user->country->currency->id)->first()?->points_value ?? 1, 2)), // Points per currency unit
                'welcome_points' => floatval(number_format(PointsSetting::where('currency_id', $user->country->currency->id)->first()?->welcome_points ?? 1, 2)), // Welcome points
            ];
            
            return $this->sendRes(
                trans('systemsetting::points.settings_retrieved_successfully'),
                true,
                $settings
            );
        } catch (\Exception $e) {
            return $this->sendRes(
                trans('common.error_occurred'),
                false,
                [],
                [],
                500
            );
        }
    }
}
