<?php

namespace Modules\Order\app\Pipelines;

use Closure;

class AddShipping
{
    /**
     * Add shipping cost to context (default 0 for now)
     */
    public function handle($payload, Closure $next)
    {
        $data = $payload['data'];
        $context = $payload['context'];

        // Set shipping cost (default 0 for now)
        $context['shipping'] = 0;

        return $next([
            'data' => $data,
            'context' => $context,
        ]);
    }
}
