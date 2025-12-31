<?php

namespace Modules\Accounting\app\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class VendorScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (isVendor()) {
            $vendorId = auth()->user()->vendor->id ?? null;
            $builder->whereNotNull('vendor_id')->where('vendor_id', $vendorId);
        } else {
            if ($model instanceof \Modules\Accounting\app\Models\ExpenseItem ||
                $model instanceof \Modules\Accounting\app\Models\Expense) {
                $builder->whereNull('vendor_id');
            }
        }
    }
}
