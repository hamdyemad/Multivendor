<?php

namespace Modules\CatalogManagement\app\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Modules\CatalogManagement\app\Models\Product;
use App\Models\Attachment;

class ImagesSheetImport implements ToCollection, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    protected array $processedImagesByProduct = []; // Track which images were processed for each product

    public function __construct(
        protected array &$productMap,
        protected array &$importErrors = [],
        protected bool $isAdmin = false
    ) {}

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $excelProductId = (int)($row['product_id'] ?? 0);
            $imageUrl = trim((string)($row['image'] ?? ''));

            $validator = Validator::make($row->toArray(), [
                'product_id' => 'required|integer|min:1',
                'image' => 'required|string',
                'is_main' => 'nullable|in:0,1,true,false,yes,no',
            ], [
                'product_id.required' => __('validation.required', ['attribute' => 'product_id']),
                'image.required' => __('validation.required', ['attribute' => 'image']),
            ]);

            if ($validator->fails()) {
                $this->importErrors[] = [
                    'sheet' => 'images',
                    'row' => $index + 2,
                    'product_id' => $excelProductId,
                    'errors' => $validator->errors()->all()
                ];
                continue;
            }

            if ($excelProductId <= 0 || $imageUrl === '') {
                $this->importErrors[] = [
                    'sheet' => 'images',
                    'row' => $index + 2,
                    'product_id' => $excelProductId,
                    'errors' => [__('catalogmanagement::product.invalid_product_id_or_image_url')]
                ];
                continue;
            }

            if (!isset($this->productMap[$excelProductId])) {
                continue;
            }

            $dbProductId = $this->productMap[$excelProductId];
            $product = Product::whereNull('deleted_at')->find($dbProductId);
            if (!$product) {
                continue;
            }

            // Track this product as having images processed
            if (!isset($this->processedImagesByProduct[$dbProductId])) {
                $this->processedImagesByProduct[$dbProductId] = [];
            }

            // Download and store the image
            $storedPath = $this->downloadAndStoreImage($imageUrl, $dbProductId);
            
            if (!$storedPath) {
                $this->importErrors[] = [
                    'sheet' => 'images',
                    'row' => $index + 2,
                    'product_id' => $excelProductId,
                    'errors' => [__('catalogmanagement::product.failed_to_download_image')]
                ];
                continue;
            }

            $isMain = in_array(
                strtolower(trim((string)($row['is_main'] ?? '0'))),
                ['1', 'true', 'yes'],
                true
            );

            if ($isMain) {
                $attachment = $product->mainImage()->updateOrCreate(
                    [
                        'attachable_id' => $dbProductId,
                        'attachable_type' => Product::class,
                        'type' => 'main_image'
                    ],
                    [
                        'path' => $storedPath
                    ]
                );
                $this->processedImagesByProduct[$dbProductId][] = $attachment->id;
            } else {
                $attachment = Attachment::updateOrCreate(
                    [
                        'attachable_id' => $dbProductId,
                        'attachable_type' => Product::class,
                        'type' => 'additional_image',
                        'path' => $storedPath
                    ],
                    [
                        'path' => $storedPath
                    ]
                );
                $this->processedImagesByProduct[$dbProductId][] = $attachment->id;
            }
        }
        
        // After processing all rows, delete images that weren't in the Excel file
        $this->deleteUnprocessedImages();
    }

    /**
     * Download image from URL and store it locally
     */
    private function downloadAndStoreImage(string $imageUrl, int $productId): ?string
    {
        try {
            // Check if it's a valid URL
            if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                // If not a URL, assume it's already a local path
                return $imageUrl;
            }

            // Download the image
            $response = Http::timeout(30)->get($imageUrl);
            
            if (!$response->successful()) {
                return null;
            }

            $imageContent = $response->body();
            
            // Get the file extension from URL or content type
            $extension = $this->getImageExtension($imageUrl, $response->header('Content-Type'));
            
            if (!$extension) {
                $extension = 'jpg'; // Default to jpg
            }

            // Generate unique filename
            $filename = 'products/' . $productId . '/' . Str::uuid() . '.' . $extension;
            
            // Store the image
            Storage::disk('public')->put($filename, $imageContent);
            
            return $filename;
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to download image: ' . $e->getMessage(), [
                'url' => $imageUrl,
                'product_id' => $productId
            ]);
            return null;
        }
    }

    /**
     * Get image extension from URL or content type
     */
    private function getImageExtension(string $url, ?string $contentType): ?string
    {
        // Try to get from URL
        $pathInfo = pathinfo(parse_url($url, PHP_URL_PATH));
        if (!empty($pathInfo['extension'])) {
            $ext = strtolower($pathInfo['extension']);
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                return $ext;
            }
        }

        // Try to get from content type
        if ($contentType) {
            $mimeToExt = [
                'image/jpeg' => 'jpg',
                'image/jpg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
                'image/svg+xml' => 'svg',
            ];
            
            foreach ($mimeToExt as $mime => $ext) {
                if (str_contains($contentType, $mime)) {
                    return $ext;
                }
            }
        }

        return null;
    }
    
    /**
     * Delete images that exist in the database but weren't in the Excel file
     * This ensures the Excel file is the source of truth for images
     */
    private function deleteUnprocessedImages(): void
    {
        foreach ($this->processedImagesByProduct as $productId => $processedImageIds) {
            // Get all existing images for this product (main and additional)
            $existingImages = Attachment::where('attachable_id', $productId)
                ->where('attachable_type', Product::class)
                ->whereIn('type', ['main_image', 'additional_image'])
                ->get();
            
            foreach ($existingImages as $image) {
                // If this image wasn't processed (not in Excel), delete it
                if (!in_array($image->id, $processedImageIds)) {
                    // Delete the physical file if it exists
                    if ($image->path && Storage::disk('public')->exists($image->path)) {
                        Storage::disk('public')->delete($image->path);
                    }
                    
                    // Delete the database record
                    $image->delete();
                }
            }
        }
    }
}
