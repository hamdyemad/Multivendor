<?php

namespace Modules\Order\app\Models;

use App\Traits\Translation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\CatalogManagement\app\Models\Tax;

class OrderProductTax extends Model
{
    use HasFactory, Translation;

    protected $fillable = [
        'order_product_id',
        'tax_id',
        'percentage',
        'amount',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
    ];

    /**
     * Get the order product.
     */
    public function orderProduct(): BelongsTo
    {
        return $this->belongsTo(OrderProduct::class);
    }

    /**
     * Get the tax.
     */
    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }

    /**
     * Get tax title in current locale.
     */
    public function getTaxTitleAttribute()
    {
        $translation = $this->getTranslation('tax_title', app()->getLocale());
        return $translation?->tax_title ?? $this->tax->name ?? '';
    }
}
