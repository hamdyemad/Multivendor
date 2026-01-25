<?php

namespace Modules\CatalogManagement\app\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;

/**
 * Sheet: images
 * Exports product images from Attachment morph model
 */
class ImagesSheetExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected bool $isAdmin;
    protected $vendorProducts;
    protected array $productIdMapping;

    public function __construct(bool $isAdmin = false, $vendorProducts = null, array $productIdMapping = [])
    {
        $this->isAdmin = $isAdmin;
        $this->vendorProducts = $vendorProducts;
        $this->productIdMapping = $productIdMapping;
    }

    public function collection()
    {
        // Extract all images from vendor products
        $images = new Collection();
        
        foreach ($this->vendorProducts as $vendorProduct) {
            if ($vendorProduct->product && $vendorProduct->product->attachments) {
                foreach ($vendorProduct->product->attachments as $image) {
                    // Store vendor product ID with the image for mapping
                    $image->vendor_product_id = $vendorProduct->id;
                    $images->push($image);
                }
            }
        }
        
        return $images;
    }

    public function headings(): array
    {
        return [
            'product_id',
            'image',
            'is_main',
        ];
    }

    public function map($image): array
    {
        // Use the vendor_product_id we stored in collection()
        $productId = $this->productIdMapping[$image->vendor_product_id] ?? '';
        
        return [
            $productId,
            $image->path ? asset('storage/' . $image->path) : '',
            $image->type === 'main_image' ? 'yes' : 'no',
        ];
    }

    public function title(): string
    {
        return 'images';
    }
}
