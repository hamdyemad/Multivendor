<?php

namespace Modules\CatalogManagement\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\CatalogManagement\app\Models\Review;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\Customer\app\Models\Customer;
use Modules\Vendor\app\Models\Vendor;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    /**
     * Arabic review templates for realistic data
     */
    protected $arabicReviews = [
        'منتج ممتاز جداً، أنصح به بشدة',
        'جودة عالية وسعر مناسب',
        'التوصيل كان سريع والمنتج كما هو موصوف',
        'راضي جداً عن الشراء',
        'منتج جيد لكن التغليف يحتاج تحسين',
        'خدمة العملاء ممتازة',
        'سأشتري مرة أخرى بالتأكيد',
        'المنتج يستحق السعر',
        'تجربة شراء رائعة',
        'منتج عادي، ليس سيء ولا ممتاز',
    ];

    /**
     * English review templates for realistic data
     */
    protected $englishReviews = [
        'Excellent product, highly recommended!',
        'Great quality for the price',
        'Fast delivery and product as described',
        'Very satisfied with my purchase',
        'Good product but packaging needs improvement',
        'Customer service was excellent',
        'Will definitely buy again',
        'Product is worth the price',
        'Amazing shopping experience',
        'Average product, not bad not great',
    ];

    public function definition(): array
    {
        $customer = Customer::inRandomOrder()->first();
        $vendorProduct = VendorProduct::inRandomOrder()->first();
        $vendor = Vendor::inRandomOrder()->first();

        // Randomly choose between product review and vendor review
        $isProductReview = $this->faker->boolean(70); // 70% product reviews

        if ($isProductReview && $vendorProduct) {
            $reviewableType = VendorProduct::class;
            $reviewableId = $vendorProduct->id;
        } elseif ($vendor) {
            $reviewableType = Vendor::class;
            $reviewableId = $vendor->id;
        } else {
            $reviewableType = VendorProduct::class;
            $reviewableId = $vendorProduct?->id ?? 1;
        }

        // Generate realistic star rating (weighted towards positive)
        $star = $this->faker->randomElement([3, 4, 4, 4, 5, 5, 5, 5, 5, 5]);

        // Choose review text based on star rating
        $isArabic = $this->faker->boolean(50);
        $reviews = $isArabic ? $this->arabicReviews : $this->englishReviews;

        // Higher ratings get more positive reviews (first items in array)
        $reviewIndex = $star >= 4 ? $this->faker->numberBetween(0, 4) : $this->faker->numberBetween(5, 9);
        $reviewText = $reviews[$reviewIndex] ?? $this->faker->sentence(10);

        return [
            'reviewable_type' => $reviewableType,
            'reviewable_id' => $reviewableId,
            'customer_id' => $customer?->id ?? 1,
            'review' => $reviewText,
            'star' => $star,
            'status' => $this->faker->randomElement([
                Review::STATUS_APPROVED,
                Review::STATUS_APPROVED,
                Review::STATUS_APPROVED,
                Review::STATUS_PENDING,
                Review::STATUS_REJECTED,
            ]), // 60% approved, 20% pending, 20% rejected
            'rejection_reason' => null,
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => now(),
        ];
    }

    /**
     * State for approved reviews
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Review::STATUS_APPROVED,
        ]);
    }

    /**
     * State for pending reviews
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Review::STATUS_PENDING,
        ]);
    }

    /**
     * State for rejected reviews
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Review::STATUS_REJECTED,
            'rejection_reason' => $this->faker->sentence(),
        ]);
    }
}
