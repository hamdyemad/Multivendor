<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ListModels extends Command
{
    protected $signature = 'models:list {--exclude-system : Exclude system models}';
    protected $description = 'List all discovered models in the application';

    public function handle()
    {
        $models = $this->getAllModels();
        
        if ($this->option('exclude-system')) {
            $models = array_filter($models, function ($model) {
                return !str_starts_with($model, 'Illuminate\\');
            });
        }
        
        $this->info("Found " . count($models) . " models:");
        $this->newLine();
        
        // Group by namespace
        $grouped = [];
        foreach ($models as $model) {
            $namespace = substr($model, 0, strrpos($model, '\\'));
            $grouped[$namespace][] = $model;
        }
        
        foreach ($grouped as $namespace => $namespaceModels) {
            $this->line("<fg=yellow>{$namespace}</>");
            foreach ($namespaceModels as $model) {
                $modelName = class_basename($model);
                $this->line("  - {$modelName}");
            }
            $this->newLine();
        }
        
        return 0;
    }

    private function getAllModels(): array
    {
        $models = [];
        
        // Get models from app/Models
        $models = array_merge($models, $this->getModelsFromDirectory(app_path('Models'), 'App\\Models'));
        
        // Get models from Modules
        if (File::exists(base_path('Modules'))) {
            $modules = File::directories(base_path('Modules'));
            
            foreach ($modules as $module) {
                $moduleName = basename($module);
                
                $possiblePaths = [
                    $module . '/app/Models',
                    $module . '/Models',
                    $module . '/Entities',
                ];
                
                foreach ($possiblePaths as $modelsPath) {
                    if (File::exists($modelsPath)) {
                        $namespace = $this->getNamespaceForPath($modelsPath, $moduleName);
                        $models = array_merge(
                            $models,
                            $this->getModelsFromDirectory($modelsPath, $namespace)
                        );
                    }
                }
            }
        }
        
        return array_unique($models);
    }

    private function getNamespaceForPath(string $path, string $moduleName): string
    {
        $baseNamespace = "Modules\\{$moduleName}\\";
        
        if (str_contains($path, '/app/Models')) {
            return $baseNamespace . 'app\\Models';
        } elseif (str_contains($path, '/Models')) {
            return $baseNamespace . 'Models';
        } else {
            return $baseNamespace . 'Entities';
        }
    }

    private function getModelsFromDirectory(string $path, string $namespace): array
    {
        $models = [];
        
        if (!File::exists($path)) {
            return $models;
        }
        
        $files = File::allFiles($path);
        
        foreach ($files as $file) {
            $relativePath = str_replace($path, '', $file->getRealPath());
            $relativePath = str_replace('.php', '', $relativePath);
            $relativePath = str_replace('/', '\\', $relativePath);
            $relativePath = ltrim($relativePath, '\\');
            
            $class = $namespace . '\\' . $relativePath;
            
            try {
                if (class_exists($class)) {
                    $reflection = new \ReflectionClass($class);
                    
                    if (
                        $reflection->isSubclassOf(\Illuminate\Database\Eloquent\Model::class) &&
                        !$reflection->isAbstract()
                    ) {
                        $models[] = $class;
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        
        return $models;
    }
}