<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\InjectDataController;

class InjectDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inject:data 
                            {type : Type of data to inject (departments, main_categories, sub_categories, categories, brands, taxes, products, etc.)}
                            {--truncate : Truncate existing data before injection}
                            {--limit-pages= : Limit number of pages to process}
                            {--start-page=1 : Starting page number}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inject data from external API in background';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');
        $truncate = $this->option('truncate');
        $limitPages = $this->option('limit-pages');
        $startPage = $this->option('start-page');

        $this->info("Starting data injection for: {$type}");
        $this->info("Truncate: " . ($truncate ? 'Yes' : 'No'));
        if ($limitPages) {
            $this->info("Limit pages: {$limitPages}");
        }
        $this->info("Start page: {$startPage}");
        $this->newLine();

        // Create a fake request object
        $request = new \Illuminate\Http\Request([
            'include' => $type,
            'truncate' => $truncate ? '1' : '0',
            'limit_pages' => $limitPages,
            'page' => $startPage,
        ]);

        // Instantiate the controller
        $controller = new InjectDataController();

        // Call the inject method
        $this->info("Processing...");
        $progressBar = $this->output->createProgressBar();
        $progressBar->start();

        try {
            $response = $controller->inject($request);
            $progressBar->finish();
            $this->newLine(2);

            $data = $response->getData(true);

            if ($data['status']) {
                $this->info("✓ Success!");
                $this->newLine();

                // Display results
                $this->table(
                    ['Metric', 'Value'],
                    [
                        ['Total Fetched', $data['total_fetched'] ?? 0],
                        ['Pages Processed', $data['pages_processed'] ?? 0],
                        ['Last Page', $data['last_page'] ?? 0],
                    ]
                );

                $result = $data['result'] ?? [];
                
                $this->newLine();
                $this->info("Results:");
                $this->table(
                    ['Type', 'Injected', 'Updated', 'Skipped', 'Errors'],
                    [
                        [
                            $result['type'] ?? $type,
                            $result['injected'] ?? 0,
                            $result['updated'] ?? 0,
                            $result['skipped'] ?? 0,
                            count($result['errors'] ?? []),
                        ]
                    ]
                );

                // Show additional metrics if available
                $additionalMetrics = [];
                foreach ($result as $key => $value) {
                    if (!in_array($key, ['type', 'injected', 'updated', 'skipped', 'errors']) && is_numeric($value)) {
                        $additionalMetrics[] = [ucwords(str_replace('_', ' ', $key)), $value];
                    }
                }

                if (!empty($additionalMetrics)) {
                    $this->newLine();
                    $this->info("Additional Metrics:");
                    $this->table(['Metric', 'Count'], $additionalMetrics);
                }

                // Show errors if any
                if (!empty($result['errors'])) {
                    $this->newLine();
                    $this->warn("Errors encountered:");
                    foreach (array_slice($result['errors'], 0, 10) as $error) {
                        $this->line("  • {$error}");
                    }
                    if (count($result['errors']) > 10) {
                        $this->line("  ... and " . (count($result['errors']) - 10) . " more errors");
                    }
                }

                // Show truncate results if available
                if (!empty($data['truncated'])) {
                    $this->newLine();
                    $this->info("Truncated:");
                    $this->table(
                        ['Metric', 'Count'],
                        [
                            ['Records Deleted', $data['truncated']['records_deleted'] ?? 0],
                            ['Files Deleted', $data['truncated']['files_deleted'] ?? 0],
                            ['Attachments Deleted', $data['truncated']['attachments_deleted'] ?? 0],
                            ['Users Deleted', $data['truncated']['users_deleted'] ?? 0],
                        ]
                    );
                }

                return Command::SUCCESS;
            } else {
                $this->error("✗ Failed!");
                $this->error($data['message'] ?? 'Unknown error');
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $progressBar->finish();
            $this->newLine(2);
            $this->error("✗ Error: " . $e->getMessage());
            Log::error("InjectDataCommand error: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
