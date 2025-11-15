<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

/**
 * Trait HandlesExceptions
 * 
 * Automatically wraps service/repository methods with exception handling.
 * Usage in services/repositories:
 *   - Use this trait in your class
 *   - Call methods with: $this->executeWithExceptionHandling(callback, 'Operation name')
 * 
 * Example:
 *   return $this->executeWithExceptionHandling(
 *       fn() => $this->repository->getAllItems($filters),
 *       'Fetching items'
 *   );
 */
trait HandlesExceptions
{
    /**
     * Execute a callback with automatic exception handling and logging
     * 
     * @param callable $callback The function to execute
     * @param string $operationName Name of the operation (for logging)
     * @param bool $rethrow Whether to rethrow the exception (default: true)
     * 
     * @return mixed The callback result or null if exception caught
     * @throws \Exception If rethrow is true
     */
    protected function executeWithExceptionHandling(callable $callback, string $operationName, bool $rethrow = true)
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            $context = [
                'operation' => $operationName,
                'class' => get_class($this),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ];
            
            Log::error("Exception in {$operationName}: " . $e->getMessage(), $context);
            
            if ($rethrow) {
                throw $e;
            }
            
            return null;
        }
    }
}
