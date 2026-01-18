<?php

namespace Modules\SystemSetting\app\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\SystemSetting\app\Models\UserPointsTransaction;
use App\Models\User;

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
        ?\DateTime $expiresAt = null
    ): UserPointsTransaction {
        return DB::transaction(function () use ($userId, $points, $transactionableType, $transactionableId, $description, $expiresAt) {
            // Update user points
            $user = User::findOrFail($userId);
            $user->increment('points', $points);

            // Create transaction record
            $transaction = UserPointsTransaction::create([
                'user_id' => $userId,
                'points' => $points,
                'type' => 'earned',
                'transactionable_type' => $transactionableType,
                'transactionable_id' => $transactionableId,
                'description' => $description,
                'expires_at' => $expiresAt,
            ]);

            Log::info('Points added to user', [
                'user_id' => $userId,
                'points' => $points,
                'transaction_id' => $transaction->id,
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
        string $description
    ): UserPointsTransaction {
        return DB::transaction(function () use ($userId, $points, $transactionableType, $transactionableId, $description) {
            // Update user points
            $user = User::findOrFail($userId);
            $user->decrement('points', $points);

            // Create transaction record (negative points)
            $transaction = UserPointsTransaction::create([
                'user_id' => $userId,
                'points' => -$points,
                'type' => 'adjusted',
                'transactionable_type' => $transactionableType,
                'transactionable_id' => $transactionableId,
                'description' => $description,
            ]);

            Log::info('Points deducted from user', [
                'user_id' => $userId,
                'points' => $points,
                'transaction_id' => $transaction->id,
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
        string $description
    ): UserPointsTransaction {
        return DB::transaction(function () use ($userId, $points, $transactionableType, $transactionableId, $description) {
            // Update user points
            $user = User::findOrFail($userId);
            $user->decrement('points', $points);

            // Create transaction record (negative points)
            $transaction = UserPointsTransaction::create([
                'user_id' => $userId,
                'points' => -$points,
                'type' => 'redeemed',
                'transactionable_type' => $transactionableType,
                'transactionable_id' => $transactionableId,
                'description' => $description,
            ]);

            Log::info('Points redeemed by user', [
                'user_id' => $userId,
                'points' => $points,
                'transaction_id' => $transaction->id,
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
            // Update user points
            $user = User::findOrFail($userId);
            $user->decrement('points', $points);

            // Create transaction record (negative points)
            $transaction = UserPointsTransaction::create([
                'user_id' => $userId,
                'points' => -$points,
                'type' => 'expired',
                'transactionable_type' => null,
                'transactionable_id' => null,
                'description' => $description,
            ]);

            Log::info('Points expired for user', [
                'user_id' => $userId,
                'points' => $points,
                'transaction_id' => $transaction->id,
            ]);

            return $transaction;
        });
    }

    /**
     * Get user's total points
     */
    public function getUserPoints(int $userId): float
    {
        $user = User::find($userId);
        return $user ? $user->points : 0;
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
