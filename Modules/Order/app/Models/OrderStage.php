<?php

namespace Modules\Order\app\Models;

use App\Models\BaseModel;
use App\Models\Traits\HumanDates;
use App\Traits\HasSlug;
use App\Traits\Translation;
use App\Models\Traits\CountryCheckIdTrait;
use App\Models\Traits\AutoStoreCountryId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Modules\AreaSettings\app\Models\Country;

class OrderStage extends BaseModel
{
    use HasFactory, SoftDeletes, Translation, HumanDates, HasSlug, AutoStoreCountryId, CountryCheckIdTrait;

    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean',
        'is_system' => 'boolean',
    ];

    /**
     * Stage type constants for step validation
     * Order matters: lower step = earlier in workflow
     */
    const STAGE_STEPS = [
        'new' => 1,
        'in_progress' => 2,
        'deliver' => 3,
        'cancel' => 3,  // Same level as deliver (final stages)
        'refund' => 4,  // Can only happen after deliver
    ];

    /**
     * Final stages that cannot transition to other stages
     */
    const FINAL_STAGES = ['deliver', 'cancel'];

    /**
     * Get the step number for this stage based on its type
     */
    public function getStepAttribute(): int
    {
        return self::STAGE_STEPS[$this->type] ?? 0;
    }

    /**
     * Check if this stage is a final stage
     */
    public function isFinalStage(): bool
    {
        if (!$this->type) {
            return false;
        }
        return in_array($this->type, self::FINAL_STAGES);
    }

    /**
     * Check if this stage can transition to another stage
     * 
     * Rules:
     * - Cannot change from final stages (deliver, cancel)
     * - Cannot transition to the same stage
     * - Must follow sequential order: new -> in_progress -> deliver/cancel
     * - Cannot skip steps (e.g., new cannot go directly to deliver)
     * - Refund can only happen after deliver
     * 
     * @param OrderStage $newStage The target stage to transition to
     * @return bool
     */
    public function canTransitionTo(OrderStage $newStage): bool
    {
        // Cannot change from final stages (deliver, cancel)
        if ($this->isFinalStage()) {
            return false;
        }

        // Cannot transition to the same stage
        if ($this->id === $newStage->id) {
            return false;
        }

        // If current type is null, allow only to step 1 (new) or step 2 (in_progress)
        if (!$this->type) {
            $newStep = $newStage->step;
            return $newStep <= 2; // Can go to new or in_progress
        }

        // If new type is null, allow transition
        if (!$newStage->type) {
            return true;
        }

        // Get step values
        $currentStep = $this->step;
        $newStep = $newStage->step;

        // Cannot go backwards (e.g., from in_progress to new)
        if ($newStep < $currentStep) {
            return false;
        }

        // Cannot skip steps - must go to next step only
        // Exception: can go to cancel from any non-final stage
        if ($newStage->type === 'cancel') {
            return true; // Can cancel from any stage
        }

        // For other transitions, can only go to the next step
        if ($newStep > $currentStep + 1) {
            return false; // Cannot skip steps
        }

        // Refund can only happen after deliver
        if ($newStage->type === 'refund' && $this->type !== 'deliver') {
            return false;
        }

        return true;
    }

    /**
     * Get the reason why transition is not allowed
     * 
     * @param OrderStage $newStage The target stage
     * @return string|null Reason message or null if transition is allowed
     */
    public function getTransitionBlockReason(OrderStage $newStage): ?string
    {
        if ($this->isFinalStage()) {
            return trans('order::order.cannot_change_final_stage');
        }

        if ($this->id === $newStage->id) {
            return trans('order::order.same_stage_selected');
        }

        // If current type is null, check step limit
        if (!$this->type) {
            $newStep = $newStage->step;
            if ($newStep > 2) {
                return trans('order::order.cannot_skip_steps');
            }
            return null;
        }

        // If new type is null, allow transition
        if (!$newStage->type) {
            return null;
        }

        $currentStep = $this->step;
        $newStep = $newStage->step;

        if ($newStep < $currentStep) {
            return trans('order::order.cannot_go_back_stage');
        }

        // Can always cancel
        if ($newStage->type === 'cancel') {
            return null;
        }

        // Cannot skip steps
        if ($newStep > $currentStep + 1) {
            return trans('order::order.cannot_skip_steps');
        }

        if ($newStage->type === 'refund' && $this->type !== 'deliver') {
            return trans('order::order.refund_only_after_deliver');
        }

        return null;
    }

    /**
     * Get all stages that this stage can transition to
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllowedNextStages()
    {
        // If final stage, no transitions allowed
        if ($this->isFinalStage()) {
            return collect([]);
        }

        $currentStep = $this->step;

        return static::withoutGlobalScopes()
            ->active()
            ->where('id', '!=', $this->id)
            ->get()
            ->filter(function ($stage) use ($currentStep) {
                // Only allow forward transitions
                $stageStep = $stage->step;
                
                // Cannot go backwards
                if ($stageStep < $currentStep) {
                    return false;
                }

                // Refund only after deliver
                if ($stage->type === 'refund' && $this->type !== 'deliver') {
                    return false;
                }

                return true;
            });
    }

    public function country() {
        return $this->belongsTo(Country::class);
    }

    /**
     * Scope for active order stages
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('active', 1);
    }

    /**
     * Scope for system stages
     */
    public function scopeSystem(Builder $query)
    {
        return $query->where('is_system', 1);
    }

    /**
     * Scope for custom stages (non-system)
     */
    public function scopeCustom(Builder $query)
    {
        return $query->where('is_system', 0);
    }

    /**
     * Scope to order by step sequence
     */
    public function scopeOrderByStep(Builder $query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    /**
     * Filter scope
     */
    public function scopeFilter(Builder $query, array $filters)
    {
        // Apply filters
        if (!empty($filters['search'])) {
            $query->whereHas('translations', function ($q) use ($filters) {
                $q->where('lang_value', 'like', '%' . $filters['search'] . '%')
                  ->where('lang_key', 'name');
            });
        }

        if (isset($filters['active']) && $filters['active'] !== '') {
            $query->where('active', $filters['active']);
        }

        if (!empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        if (!empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        return $query;
    }

    /**
     * Check if stage can be deleted
     */
    public function canBeDeleted(): bool
    {
        return !$this->is_system;
    }
}
