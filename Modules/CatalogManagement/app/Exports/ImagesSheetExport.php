<?php

namespace Modules\CatalogManagement\app\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\Models\Attachment;
use Illuminate\Support\Facades\Auth;

/**
 * Sheet: images
 * Exports product images from Attachment morph model
 */
class ImagesSheetExport implements FromQuery, WithHeadings, WithMapping, WithTitle
{
    protected bool $isAdmin;
    protected array $filters;

    public function __construct(bool $isAdmin = false, array $filters = [])
    {
        $this->isAdmin = $isAdmin;
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Attachment::where('attachable_type', 'Modules\CatalogManagement\app\Models\Product')
            ->whereIn('type', ['main_image', 'additional_image'])
            ->with(['attachmentable.vendorProducts']);

        // Filter by vendor if not admin
        if (!$this->isAdmin) {
            $vendorId = Auth::user()->vendor?->id;
            if ($vendorId) {
                $query->whereHas('attachmentable.vendorProducts', function($q) use ($vendorId) {
                    $q->where('vendor_id', $vendorId);
                });
            }
        }

        // Apply additional filters
        if (!empty($this->filters['vendor_id'])) {
            $query->whereHas('attachmentable.vendorProducts', function($q) {
                $q->where('vendor_id', $this->filters['vendor_id']);
            });
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->whereHas('attachmentable', function($q) use ($search) {
                $q->whereHas('vendorProducts', function($vq) use ($search) {
                    $vq->where('sku', 'like', "%{$search}%");
                })->orWhereHas('translations', function($tq) use ($search) {
                    $tq->where('lang_value', 'like', "%{$search}%");
                });
            });
        }

        return $query->orderBy('attachable_id')->orderBy('id');
    }

    public function headings(): array
    {
        return [
            'product_id',
            'image_url',
            'is_main',
        ];
    }

    public function map($attachment): array
    {
        // Get the vendor product ID for this product
        $product = $attachment->attachmentable;
        $vendorProduct = $product ? $product->vendorProducts->first() : null;
        
        return [
            $vendorProduct ? $vendorProduct->id : ($product ? $product->id : ''),
            $attachment->path ? asset('storage/' . $attachment->path) : '',
            $attachment->type === 'main_image' ? 'yes' : 'no',
        ];
    }

    public function title(): string
    {
        return 'images';
    }
}
