<?php

namespace Modules\SystemSetting\app\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\SystemSetting\app\Models\UserPointsTransaction;
use Modules\Customer\app\Models\Customer;

class UserPointsService
{
    /**
     * Add points to user (earned)
     */
    public function addPoints(
        int $userId,
        float $points,
        string $transactionableType,
        int $transactionableId,
        string $description,
        ?\DateTime $expiresAt = null,
        ?float $pointsPerCurrency = null
    ): UserPointsTransaction {
        return DB::transaction(function () use ($userId, $points, $transactionableType, $transactionableId, $description, $expiresAt, $pointsPerCurrency) {
            // Verify customer exists
            $customer = Customer::findOrFail($userId);

            // Create transaction record (points are calculated from transactions, not stored in customer table)
            $transaction = UserPointsTransaction::create([
                'user_id' => $userId,
                'points' => $points,
                'type' => 'earned',
                'transactionable_type' => $transactionableType,
                'transactionable_id' => $transactionableId,
                'expires_at' => $expiresAt,
                'points_per_currency' => $pointsPerCurrency,
            ]);

            // Set description using Translation trait
            $transaction->setTranslation('description', 'en', $description);
            $transaction->setTranslation('description', 'ar', $description);
            $transaction->save();

            Log::info('Points added to customer', [
                'customer_id' => $userId,
                'points' => $points,
                'points_per_currency' => $pointsPerCurrency,
                'transaction_id' => $transaction->id,
                'total_points' => $this->getUserPoints($userId),
            ]);

            return $transaction;
        });
    }

    /**
     * Deduct points from user (adjusted)
     */
    public function deductPoints(
        int $userId,
        float $points,
        string $transactionableType,
        int $transactionableId,
        string $description,
        ?float $pointsPerCurrency = null
    ): UserPointsTransaction {
        return DB::transaction(function () use ($userId, $points, $transactionableType, $transactionableId, $description, $pointsPerCurrency) {
            // Verify customer exists
            $customer = Customer::findOrFail($userId);

            // Create transaction record with negative points (points are calculated from transactions, not stored in customer table)
            $transaction = UserPointsTransaction::create([
                'user_id' => $userId,
                'points' => -$points,
                'type' => 'adjusted',
                'transactionable_type' => $transactionableType,
                'transactionable_id' => $transactionableId,
                'points_per_currency' => $pointsPerCurrency,
            ]);

            // Set description using Translation trait
            $transaction->setTranslation('description', 'en', $description);
            $transaction->setTranslation('description', 'ar', $description);
            $transaction->save();

            Log::info('Points deducted from customer', [
                'customer_id' => $userId,
                'points' => $points,
                'points_per_currency' => $pointsPerCurrency,
                'transaction_id' => $transaction->id,
                'total_points' => $this->getUserPoints($userId),
            ]);

            return $transaction;
        });
    }

    /**
     * Redeem points (when user uses points for purchase)
     */
    public function redeemPoints(
        int $userId,
        float $points,
        string $transactionableType,
        int $transactionableId,
        string $description,
        ?float $pointsPerCurrency = null
    ): UserPointsTransaction {
        return DB::transaction(function () use ($userId, $points, $transactionableType, $transactionableId, $description, $pointsPerCurrency) {
            // Verify customer exists
            $customer = Customer::findOrFail($userId);

            // Create transaction record with negative points (points are calculated from transactions, not stored in customer table)
            $transaction = UserPointsTransaction::create([
                'user_id' => $userId,
                'points' => -$points,
                'type' => 'redeemed',
                'transactionable_type' => $transactionableType,
                'transactionable_id' => $transactionableId,
                'points_per_currency' => $pointsPerCurrency,
            ]);

            // Set description using Translation trait
            $transaction->setTranslation('description', 'en', $description);
            $transaction->setTranslation('description', 'ar', $description);
            $transaction->save();

            Log::info('Points redeemed by customer', [
                'customer_id' => $userId,
                'points' => $points,
                'points_per_currency' => $pointsPerCurrency,
                'transaction_id' => $transaction->id,
                'total_points' => $this->getUserPoints($userId),
            ]);

            return $transaction;
        });
    }

    /**
     * Expire points
     */
    public function expirePoints(
        int $userId,
        float $points,
        string $description
    ): UserPointsTransaction {
        return DB::transaction(function () use ($userId, $points, $description) {
            // Verify customer exists
            $customer = Customer::findOrFail($userId);

            // Create transaction record with negative points (points are calculated from transactions, not stored in customer table)
            $transaction = UserPointsTransaction::create([
                'user_id' => $userId,
                'points' => -$points,
                'type' => 'expired',
                'transactionable_type' => null,
                'transactionable_id' => null,
            ]);

            // Set description using Translation trait
            $transaction->setTranslation('description', 'en', $description);
            $transaction->setTranslation('description', 'ar', $description);
            $transaction->save();

            Log::info('Points expired for customer', [
                'customer_id' => $userId,
                'points' => $points,
                'transaction_id' => $transaction->id,
                'total_points' => $this->getUserPoints($userId),
            ]);

            return $transaction;
        });
    }

    /**
     * Get user's total points (calculated from transactions)
     */
    public function getUserPoints(int $userId): float
    {
        // Calculate total points from all transactions
        return UserPointsTransaction::where('user_id', $userId)->sum('points');
    }

    /**
     * Get user's points history
     */
    public function getUserPointsHistory(int $userId, int $limit = 50)
    {
        return UserPointsTransaction::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get user's points balance by type
     */
    public function getUserPointsBalance(int $userId): array
    {
        $transactions = UserPointsTransaction::where('user_id', $userId)->get();

        return [
            'total' => $transactions->sum('points'),
            'earned' => $transactions->where('type', 'earned')->sum('points'),
            'redeemed' => abs($transactions->where('type', 'redeemed')->sum('points')),
            'adjusted' => $transactions->where('type', 'adjusted')->sum('points'),
            'expired' => abs($transactions->where('type', 'expired')->sum('points')),
        ];
    }
}
