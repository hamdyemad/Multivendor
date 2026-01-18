<?php

namespace Modules\Refund\app\Interfaces;

interface RefundRequestRepositoryInterface
{
    public function getAllPaginated(array $filters = [], int $perPage = 15);
    
    public function findById(int $id);
    
    public function create(array $data);
    
    public function update(int $id, array $data);
    
    public function delete(int $id);
    
    public function getStatistics(array $filters = []);
    
    public function canUserAccessRefund(int $refundId, $user): bool;
    
    public function canUserCancelRefund(int $refundId, $user): bool;
    
    // Main refund operations
    public function createRefundWithVendorSplit(array $data, $user);
    
    public function updateRefundStatus(int $id, array $data, $user);
    
    public function cancelRefund(int $id, $user);
    
    // Helper methods
    public function getRefundWithRelations(int $refundId, array $relations = []);
}
