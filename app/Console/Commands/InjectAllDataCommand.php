<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InjectAllDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inject:all 
                            {--truncate : Truncate existing data before injection}
                            {--skip= : Skip specific types (comma-separated)}
                            {--only= : Only inject specific types (comma-separated)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inject all data types in the correct order';

    /**
     * Data types in dependency order
     */
    protected array $dataTypes = [
        'departments',
        'main_categories',
        'sub_categories',
        'variant_keys',
        'variants',
        'brands',
        'taxes',
        'cities',
        'blog_categories',
        'blogs',
        'ads_positions',
        'ads',
        'bundle_categories',
        'bundles',
        'occasions',
        'products',
        'users',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $truncate = $this->option('truncate');
        $skip = $this->option('skip') ? explode(',', $this->option('skip')) : [];
        $only = $this->option('only') ? explode(',', $this->option('only')) : [];

        // Filter data types
        $typesToProcess = $this->dataTypes;
        
        if (!empty($only)) {
            $typesToProcess = array_intersect($typesToProcess, $only);
        }
        
        if (!empty($skip)) {
            $typesToProcess = array_diff($typesToProcess, $skip);
        }

        $this->info("╔════════════════════════════════════════════════════════════╗");
        $this->info("║          BNAIA DATA INJECTION - FULL IMPORT                ║");
        $this->info("╚════════════════════════════════════════════════════════════╝");
        $this->newLine();

        $this->info("Data types to process: " . implode(', ', $typesToProcess));
        $this->info("Truncate: " . ($truncate ? 'Yes' : 'No'));
        $this->newLine();

        if (!$this->confirm('Do you want to continue?', true)) {
            $this->warn('Operation cancelled.');
            return Command::SUCCESS;
        }

        $this->newLine();
        $startTime = microtime(true);
        $results = [];

        foreach ($typesToProcess as $index => $type) {
            $typeNumber = $index + 1;
            $totalTypes = count($typesToProcess);

            $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->info("Processing [{$typeNumber}/{$totalTypes}]: {$type}");
            $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->newLine();

            $typeStartTime = microtime(true);

            // Call the inject:data command
            $exitCode = $this->call('inject:data', [
                'type' => $type,
                '--truncate' => $truncate && $typeNumber === 1, // Only truncate on first type
            ]);

            $typeEndTime = microtime(true);
            $typeDuration = round($typeEndTime - $typeStartTime, 2);

            $results[$type] = [
                'success' => $exitCode === Command::SUCCESS,
                'duration' => $typeDuration,
            ];

            if ($exitCode === Command::SUCCESS) {
                $this->info("✓ {$type} completed in {$typeDuration}s");
            } else {
                $this->error("✗ {$type} failed after {$typeDuration}s");
                
                if (!$this->confirm("Continue with remaining types?", true)) {
                    break;
                }
            }

            $this->newLine();
        }

        $endTime = microtime(true);
        $totalDuration = round($endTime - $startTime, 2);

        // Summary
        $this->newLine();
        $this->info("╔════════════════════════════════════════════════════════════╗");
        $this->info("║                    IMPORT SUMMARY                          ║");
        $this->info("╚════════════════════════════════════════════════════════════╝");
        $this->newLine();

        $successCount = count(array_filter($results, fn($r) => $r['success']));
        $failCount = count($results) - $successCount;

        $summaryData = [];
        foreach ($results as $type => $result) {
            $summaryData[] = [
                $type,
                $result['success'] ? '✓ Success' : '✗ Failed',
                $result['duration'] . 's',
            ];
        }

        $this->table(['Type', 'Status', 'Duration'], $summaryData);

        $this->newLine();
        $this->info("Total types processed: " . count($results));
        $this->info("Successful: {$successCount}");
        if ($failCount > 0) {
            $this->warn("Failed: {$failCount}");
        }
        $this->info("Total duration: {$totalDuration}s (" . gmdate('H:i:s', $totalDuration) . ")");

        return $failCount === 0 ? Command::SUCCESS : Command::FAILURE;
    }
}
