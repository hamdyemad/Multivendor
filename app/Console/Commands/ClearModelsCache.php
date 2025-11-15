<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearModelsCache extends Command
{
    protected $signature = 'models:clear-cache';
    protected $description = 'Clear the cached models list';

    public function handle()
    {
        Cache::forget('app.discovered_models');
        $this->info('Models cache cleared successfully!');
        return 0;
    }
}