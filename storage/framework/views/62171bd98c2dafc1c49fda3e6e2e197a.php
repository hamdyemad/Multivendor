
<?php $__env->startSection('title'); ?>
    <?php echo e(trans('order::order.order_details')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    ['title' => trans('order::order.order_management'), 'url' => route('admin.orders.index')],
                    ['title' => trans('order::order.order_details')],
                ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    ['title' => trans('order::order.order_management'), 'url' => route('admin.orders.index')],
                    ['title' => trans('order::order.order_details')],
                ])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2)): ?>
<?php $attributes = $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2; ?>
<?php unset($__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale19f62b34dfe0bfdf95075badcb45bc2)): ?>
<?php $component = $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2; ?>
<?php unset($__componentOriginale19f62b34dfe0bfdf95075badcb45bc2); ?>
<?php endif; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div id="printableArea" class="bg-white p-40 radius-xl">
                    <!-- Order Info & Customer Details with QR Code -->
                    <div class="row mb-40">
                        <!-- Order Details Card -->
                        <div class="col-lg-6 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold mb-20 d-flex align-items-center">
                                        <i class="uil uil-receipt me-2" style="color: #5f63f2; font-size: 20px;"></i>
                                        <?php echo e(trans('order::order.order_information')); ?>

                                    </h6>
                                    <div class="order-details">
                                        <div class="detail-row mb-15">
                                            <span class="detail-label"><?php echo e(trans('order::order.order_id')); ?>:</span>
                                            <span
                                                class="detail-value fw-bold text-primary"><?php echo e($order->order_number); ?></span>
                                        </div>
                                        <div class="detail-row mb-15">
                                            <span class="detail-label"><?php echo e(trans('order::order.created_at')); ?>:</span>
                                            <span class="detail-value"><?php echo e($order->created_at); ?></span>
                                        </div>
                                        <div class="detail-row mb-15 justify-content-between">
                                            <span class="detail-label"><?php echo e(trans('order::order.stage')); ?>:</span>
                                            <span class="badge badge-lg badge-round"
                                                style="background: <?php echo e($order->stage?->color ?? '#6c757d'); ?>; color: white"><?php echo e($order->stage?->name ?? 'N/A'); ?></span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label"><?php echo e(trans('order::order.order_from')); ?>:</span>
                                            <span class="detail-value">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->order_from === 'web'): ?>
                                                    <span class="badge badge-lg badge-round bg-info"><i
                                                            class="uil uil-globe me-1"></i><?php echo e(trans('order::order.web')); ?></span>
                                                <?php elseif($order->order_from === 'ios'): ?>
                                                    <span class="badge badge-lg badge-round bg-dark"><i
                                                            class="uil uil-apple me-1"></i><?php echo e(trans('order::order.ios')); ?></span>
                                                <?php elseif($order->order_from === 'android'): ?>
                                                    <span class="badge badge-lg badge-round bg-success"><i
                                                            class="uil uil-android me-1"></i><?php echo e(trans('order::order.android')); ?></span>
                                                <?php else: ?>
                                                    <span
                                                        class="badge badge-lg badge-round bg-secondary"><?php echo e($order->order_from); ?></span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Details Card -->
                        <div class="col-lg-6 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold mb-20 d-flex align-items-center">
                                        <i class="uil uil-user me-2" style="color: #5f63f2; font-size: 20px;"></i>
                                        <?php echo e(trans('order::order.customer_information')); ?>

                                    </h6>
                                    <div class="customer-details">
                                        <div class="detail-row mb-15">
                                            <span class="detail-label"><i
                                                    class="uil uil-user-circle me-1"></i><?php echo e(trans('order::order.customer_name')); ?>:</span>
                                            <span class="detail-value fw-bold"><?php echo e($order->customer_name); ?></span>
                                        </div>
                                        <div class="detail-row mb-15">
                                            <span class="detail-label"><i
                                                    class="uil uil-envelope me-1"></i><?php echo e(trans('order::order.customer_email')); ?>:</span>
                                            <span class="detail-value"><a
                                                    href="mailto:<?php echo e($order->customer_email); ?>"><?php echo e($order->customer_email); ?></a></span>
                                        </div>
                                        <div class="detail-row mb-15">
                                            <span class="detail-label"><i
                                                    class="uil uil-phone me-1"></i><?php echo e(trans('order::order.customer_phone')); ?>:</span>
                                            <span class="detail-value"><a
                                                    href="tel:<?php echo e($order->customer_phone); ?>"><?php echo e($order->customer_phone); ?></a></span>
                                        </div>
                                        <div class="detail-row mb-15">
                                            <span class="detail-label"><i
                                                    class="uil uil-map-pin me-1"></i><?php echo e(trans('order::order.customer_address')); ?>:</span>
                                            <span class="detail-value"><?php echo e($order->customer_address); ?></span>
                                        </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->country || $order->city || $order->region): ?>
                                            <hr class="my-15">
                                            <h6 class="fw-bold mb-15 d-flex align-items-center">
                                                <i class="uil uil-location-point me-2" style="color: #5f63f2; font-size: 18px;"></i>
                                                <?php echo e(trans('order::order.location')); ?>

                                            </h6>
                                            <div class="detail-row mb-15">
                                                <span class="detail-label"><?php echo e(trans('order::order.country')); ?>:</span>
                                                <span class="detail-value"><?php echo e($order->country?->name ?? 'N/A'); ?></span>
                                            </div>
                                            <div class="detail-row mb-15">
                                                <span class="detail-label"><?php echo e(trans('order::order.city')); ?>:</span>
                                                <span class="detail-value"><?php echo e($order->city?->name ?? 'N/A'); ?></span>
                                            </div>
                                            <div class="detail-row">
                                                <span class="detail-label"><?php echo e(trans('order::order.region')); ?>:</span>
                                                <span class="detail-value"><?php echo e($order->region?->name ?? 'N/A'); ?></span>
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <style>
                        .detail-row {
                            display: flex;
                            justify-content: space-between;
                            align-items: flex-start;
                            padding: 10px 0;
                            border-bottom: 1px solid #f0f0f0;
                        }

                        .detail-row:last-child {
                            border-bottom: none;
                        }

                        .detail-label {
                            font-weight: 600;
                            color: #666;
                            min-width: 150px;
                        }

                        .detail-value {
                            color: #333;
                            text-align: right;
                            flex: 1;
                        }

                        .detail-value a {
                            color: #5f63f2;
                            text-decoration: none;
                        }

                        .detail-value a:hover {
                            text-decoration: underline;
                        }
                    </style>

                    <!-- Products Table -->
                    <div class="mb-40">
                        <div class="table-responsive">
                            <table class="table mb-0 table-hover" style="border-color: #dee2e6;">
                                <thead class="userDatatable-header" style="background-color: #003d82; color: white;">
                                    <tr>
                                        <th class="text-white fw-bold text-center">#</th>
                                        <th class="text-white fw-bold text-center"><?php echo e(trans('order::order.product')); ?></th>
                                        <th class="text-white fw-bold text-center"><?php echo e(trans('order::order.price_before_tax')); ?>

                                        </th>
                                        <th class="text-white fw-bold text-center"><?php echo e(trans('order::order.price_per_unit')); ?>

                                        </th>
                                        <th class="text-white fw-bold text-center"><?php echo e(trans('order::order.quantity')); ?>

                                        </th>
                                        <th class="text-white fw-bold text-center"><?php echo e(trans('order::order.total_price')); ?>

                                        </th>
                                        <th class="text-white fw-bold text-center"><?php echo e(trans('order::order.bnaia_commission')); ?>

                                        </th>
                                        <th class="text-white fw-bold text-center"><?php echo e(trans('order::order.remaining')); ?>

                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        // Use vendor-filtered products if available, otherwise use all products
                                        $displayProducts = isset($vendorProducts) && $vendorProducts !== null ? $vendorProducts : $order->products;
                                    ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $displayProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <?php
                                            $productImage = $product->vendorProduct?->product?->mainImage?->path;
                                            $vendorName = $product->vendorProduct?->vendor?->getTranslation('name', app()->getLocale()) ?? 'N/A';
                                            
                                            // Build variant path: Key → Value
                                            $variantConfig = $product->vendorProductVariant?->variantConfiguration;
                                            $variantKey = $variantConfig?->key?->getTranslation('name', app()->getLocale()) ?? null;
                                            $variantValue = $variantConfig?->getTranslation('name', app()->getLocale()) ?? null;
                                            $variantPath = null;
                                            if ($variantKey && $variantValue) {
                                                $variantPath = $variantKey . ' → ' . $variantValue;
                                            } elseif ($variantValue) {
                                                $variantPath = $variantValue;
                                            }
                                            
                                            // Price stored is total price INCLUDING tax
                                            $productTotalWithTax = $product->price;
                                            
                                            // Get tax amount (sum of all taxes)
                                            $taxAmount = $product->taxes->sum('amount') ?? 0;
                                            
                                            // Calculate price before tax
                                            $productTotalBeforeTax = $productTotalWithTax - $taxAmount;
                                            
                                            // Unit prices
                                            $unitPriceWithTax = $product->quantity > 0 ? $productTotalWithTax / $product->quantity : 0;
                                            $unitPriceBeforeTax = $product->quantity > 0 ? $productTotalBeforeTax / $product->quantity : 0;
                                            $unitTaxAmount = $product->quantity > 0 ? $taxAmount / $product->quantity : 0;
                                            
                                            // Commission is stored directly (calculated from price with tax)
                                            $bnaiaCommission = $product->commission;
                                            $departmentCommission = $product->vendorProduct?->product?->department?->commission ?? 0;
                                            $commissionPercent = $departmentCommission;
                                            
                                            // Remaining = Total with tax - Commission
                                            $remaining = $productTotalWithTax - $bnaiaCommission;
                                        ?>
                                        <tr>
                                            <td class="fw-bold text-center"><?php echo e($key + 1); ?></td>
                                            <td class="text-center">
                                                <div class="d-flex align-items-center justify-content-center gap-3">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($productImage): ?>
                                                        <img src="<?php echo e(asset('storage/' . $productImage)); ?>" 
                                                             alt="<?php echo e($product->vendorProduct->product->name ?? 'Product'); ?>"
                                                             class="rounded"
                                                             style="width: 60px; height: 60px;  border: 1px solid #dee2e6;">
                                                    <?php else: ?>
                                                        <img src="<?php echo e(asset('assets/img/default.png')); ?>" 
                                                             alt="<?php echo e($product->vendorProduct->product->name ?? 'Product'); ?>"
                                                             class="rounded"
                                                             style="width: 60px; height: 60px;  border: 1px solid #dee2e6;">
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    <div class="text-start">
                                                        <p class="fw-bold mb-2">
                                                            <?php echo e($product->vendorProduct->product->name ?? 'N/A'); ?></p>
                                                        <small class="text-muted d-block mb-1">
                                                            <strong><?php echo e(trans('order::order.sku')); ?>:</strong>
                                                            <?php echo e($product->vendorProductVariant?->sku ?? $product->vendorProduct?->sku ?? 'N/A'); ?>

                                                        </small>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variantPath): ?>
                                                            <small class="text-muted d-block mb-1">
                                                                <i class="uil uil-tag me-1"></i>
                                                                <strong><?php echo e(trans('order::order.variant')); ?>:</strong>
                                                                <?php echo e($variantPath); ?>

                                                            </small>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        <small class="text-muted d-block">
                                                            <i class="uil uil-store me-1"></i>
                                                            <strong><?php echo e(trans('order::order.vendor')); ?>:</strong>
                                                            <?php echo e($vendorName); ?>

                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <?php echo e(number_format($unitPriceBeforeTax, 2)); ?>

                                                <?php echo e(currency()); ?>

                                            </td>
                                            <td class="text-center">
                                                <?php echo e(number_format($unitPriceWithTax, 2)); ?>

                                                <?php echo e(currency()); ?>

                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unitTaxAmount > 0): ?>
                                                    <small class="d-block text-muted">(<?php echo e(trans('order::order.tax')); ?>: <?php echo e(number_format($unitTaxAmount, 2)); ?> <?php echo e(currency()); ?>)</small>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                            <td class="text-center"><?php echo e($product->quantity); ?></td>
                                            <td class="text-center fw-bold">
                                                <?php echo e(number_format($productTotalWithTax, 2)); ?>

                                                <?php echo e(currency()); ?></td>
                                            <td class="text-center text-danger">
                                                <?php echo e(number_format($bnaiaCommission, 2)); ?>

                                                <?php echo e(currency()); ?>

                                                <small class="d-block text-muted">(<?php echo e($commissionPercent); ?>%)</small>
                                            </td>
                                            <td class="text-center fw-bold text-success">
                                                <?php echo e(number_format($remaining, 2)); ?>

                                                <?php echo e(currency()); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-20">
                                                <?php echo e(trans('common.no_data')); ?>

                                            </td>
                                        </tr>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Fees & Discounts Details -->
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->extraFeesDiscounts->count() > 0): ?>
                        <div class="mb-40">
                            <div class="table-responsive">
                                <table class="table mb-0 table-hover" style="border-color: #dee2e6;">
                                    <thead class="userDatatable-header" style="background-color: #003d82; color: white;">
                                        <tr>
                                            <th class="text-white fw-bold"><?php echo e(trans('order::order.type')); ?></th>
                                            <th class="text-white fw-bold"><?php echo e(trans('order::order.reason')); ?></th>
                                            <th class="text-white fw-bold text-end"><?php echo e(trans('order::order.amount')); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $order->extraFeesDiscounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $extra): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($extra->type === 'fee'): ?>
                                                        <span
                                                            class="badge badge-lg badge-round bg-danger"><?php echo e(trans('order::order.fee')); ?></span>
                                                    <?php else: ?>
                                                        <span
                                                            class="badge badge-lg badge-round bg-success"><?php echo e(trans('order::order.discount')); ?></span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </td>
                                                <td><?php echo e($extra->reason); ?></td>
                                                <td class="text-end fw-bold">
                                                    <?php echo e($extra->type === 'fee' ? '+' : '-'); ?><?php echo e(number_format($extra->cost, 2)); ?>

                                                    <?php echo e(currency()); ?>

                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <!-- Summary Section -->
                    <div class="row mb-40">
                        <?php
                            // Calculate total commission and remaining for all displayed products
                            $totalProductsPriceBeforeTax = 0;
                            $totalCommission = 0;
                            $totalRemaining = 0;
                            $totalProductsTax = 0;
                            
                            $productsToCalculate = isset($vendorProducts) && $vendorProducts !== null ? $vendorProducts : $order->products;
                            
                            foreach ($productsToCalculate as $prod) {
                                // Price stored is total price INCLUDING tax
                                $prodTotalWithTax = $prod->price;
                                
                                // Get tax amount (sum of all taxes)
                                $prodTax = $prod->taxes->sum('amount') ?? 0;
                                
                                // Calculate price before tax
                                $prodTotalBeforeTax = $prodTotalWithTax - $prodTax;
                                
                                // Commission is stored directly (calculated from price with tax)
                                $commAmount = $prod->commission;
                                
                                $totalProductsPriceBeforeTax += $prodTotalBeforeTax;
                                $totalProductsTax += $prodTax;
                                $totalCommission += $commAmount;
                                $totalRemaining += ($prodTotalWithTax - $commAmount);
                            }
                            
                            // Total with tax for vendor remaining calculation
                            $totalProductsPriceWithTax = $totalProductsPriceBeforeTax + $totalProductsTax;
                        ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($isVendorUser) && $isVendorUser && isset($vendorProductTotal)): ?>
                            
                            <div class="col-md-6 mb-3">
                                <div class="card border-0 shadow-sm h-100"
                                    style="background: linear-gradient(135deg, #28a745 0%, #5dd879 100%); color: white;">
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold mb-20 d-flex align-items-center text-white">
                                            <i class="uil uil-money-bill me-2" style="font-size: 20px;"></i>
                                            <?php echo e(trans('order::order.vendor_remaining_summary')); ?>

                                        </h6>
                                        <div class="summary-details">
                                            <div class="summary-row mb-12">
                                                <span class="fw-bold"><?php echo e(trans('order::order.your_products_total_including_tax')); ?></span>
                                                <span class="fw-bold"><?php echo e(number_format($totalProductsPriceWithTax, 2)); ?>

                                                    <?php echo e(currency()); ?></span>
                                            </div>
                                            <div class="summary-row mb-12">
                                                <span class="fw-bold"><?php echo e(trans('order::order.total_commission_including_tax')); ?></span>
                                                <span class="fw-bold" style="color: #ffcccc;">-<?php echo e(number_format($totalCommission, 2)); ?>

                                                    <?php echo e(currency()); ?></span>
                                            </div>
                                            <hr style="border-color: rgba(255,255,255,0.3); margin: 15px 0;">
                                            <div class="summary-row" style="font-size: 18px;">
                                                <span class="fw-bold"><?php echo e(trans('order::order.final_remaining')); ?></span>
                                                <span class="fw-bold" style="color: #ffffcc;"><?php echo e(number_format($totalRemaining, 2)); ?>

                                                    <?php echo e(currency()); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            
                            
                            
                            <div class="col-md-6 mb-3">
                                <div class="card border-0 shadow-sm h-100"
                                    style="background: linear-gradient(135deg, #28a745 0%, #5dd879 100%); color: white;">
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold mb-20 d-flex align-items-center text-white">
                                            <i class="uil uil-money-bill me-2" style="font-size: 20px;"></i>
                                            <?php echo e(trans('order::order.vendor_remaining_summary')); ?>

                                        </h6>
                                        <div class="summary-details">
                                            <div class="summary-row mb-12">
                                                <span class="fw-bold"><?php echo e(trans('order::order.subtotal_including_tax')); ?></span>
                                                <span class="fw-bold"><?php echo e(number_format($totalProductsPriceWithTax, 2)); ?>

                                                    <?php echo e(currency()); ?></span>
                                            </div>
                                            <div class="summary-row mb-12">
                                                <span class="fw-bold"><?php echo e(trans('order::order.total_commission_including_tax')); ?></span>
                                                <span class="fw-bold" style="color: #ffcccc;">-<?php echo e(number_format($totalCommission, 2)); ?>

                                                    <?php echo e(currency()); ?></span>
                                            </div>
                                            <hr style="border-color: rgba(255,255,255,0.3); margin: 15px 0;">
                                            <div class="summary-row" style="font-size: 18px;">
                                                <span class="fw-bold"><?php echo e(trans('order::order.vendors_remaining')); ?></span>
                                                <span class="fw-bold" style="color: #ffffcc;"><?php echo e(number_format($totalRemaining, 2)); ?>

                                                    <?php echo e(currency()); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            
                            <div class="col-md-6 mb-3">
                                <div class="card border-0 shadow-sm h-100"
                                    style="background: linear-gradient(135deg, #5f63f2 0%, #8e92f7 100%); color: white;">
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold mb-20 d-flex align-items-center text-white">
                                            <i class="uil uil-receipt me-2" style="font-size: 20px;"></i>
                                            <?php echo e(trans('order::order.order_summary')); ?>

                                        </h6>
                                        <div class="summary-details">
                                            <div class="summary-row mb-12">
                                                <span class="fw-bold"><?php echo e(trans('order::order.subtotal')); ?></span>
                                                <span class="fw-bold"><?php echo e(number_format($totalProductsPriceBeforeTax, 2)); ?>

                                                    <?php echo e(currency()); ?></span>
                                            </div>
                                            <div class="summary-row mb-12">
                                                <span class="fw-bold"><?php echo e(trans('order::order.tax')); ?></span>
                                                <span class="fw-bold">+<?php echo e(number_format($order->total_tax, 2)); ?>

                                                    <?php echo e(currency()); ?></span>
                                            </div>
                                            <div class="summary-row mb-12">
                                                <span class="fw-bold"><?php echo e(trans('order::order.subtotal_including_tax')); ?></span>
                                                <span class="fw-bold"><?php echo e(number_format($totalProductsPriceWithTax, 2)); ?>

                                                    <?php echo e(currency()); ?></span>
                                            </div>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->total_discounts > 0): ?>
                                                <div class="summary-row mb-12">
                                                    <span class="fw-bold"><?php echo e(trans('order::order.discounts')); ?></span>
                                                    <span class="fw-bold" style="color: #ffcccc;">-<?php echo e(number_format($order->total_discounts, 2)); ?>

                                                        <?php echo e(currency()); ?></span>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->total_fees > 0): ?>
                                                <div class="summary-row mb-12">
                                                    <span class="fw-bold"><?php echo e(trans('order::order.fees')); ?></span>
                                                    <span class="fw-bold">+<?php echo e(number_format($order->total_fees, 2)); ?>

                                                        <?php echo e(currency()); ?></span>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <div class="summary-row mb-12">
                                                <span class="fw-bold"><?php echo e(trans('order::order.shipping')); ?></span>
                                                <span class="fw-bold">+<?php echo e(number_format($order->shipping, 2)); ?>

                                                    <?php echo e(currency()); ?></span>
                                            </div>
                                            <hr style="border-color: rgba(255,255,255,0.3); margin: 15px 0;">
                                            <div class="summary-row" style="font-size: 18px;">
                                                <span class="fw-bold"><?php echo e(trans('order::order.total')); ?></span>
                                                <span class="fw-bold"><?php echo e(number_format($order->total_price, 2)); ?>

                                                    <?php echo e(currency()); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <style>
                        .summary-row {
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            padding: 8px 0;
                            font-size: 14px;
                        }
                    </style>
                </div>

                <!-- Action Buttons Section (Not Printable) -->
                <div class="mt-30 mb-40 no-print">
                    <div class="bg-white p-20 radius-xl global-shadow border-light-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-500"><?php echo e(trans('order::order.actions')); ?></h6>
                            <div class="d-flex gap-2">
                                <a href="<?php echo e(route('admin.orders.index')); ?>" class="btn btn-outline-secondary btn-sm">
                                    <i class="uil uil-arrow-left me-2"></i><?php echo e(trans('common.back')); ?>

                                </a>
                                <?php
                                    $finalStages = ['deliver', 'cancel', 'refund'];
                                    $isFinalStage = in_array($order->stage?->slug, $finalStages);
                                ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isFinalStage && isAdmin()): ?>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#changeStageModal"
                                        data-id="<?php echo e($order->id); ?>"
                                        data-stage-id="<?php echo e($order->stage_id); ?>"
                                        data-stage-type="<?php echo e($order->stage?->type); ?>">
                                        <i
                                            class="uil uil-check-circle me-2"></i><?php echo e(trans('order::order.change_order_stage')); ?>

                                    </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <button class="btn btn-info btn-sm" onclick="printInvoice()">
                                    <i class="uil uil-print me-2"></i><?php echo e(trans('order::order.print_invoice')); ?>

                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Change Stage Modal Component (Admin Only) -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isAdmin()): ?>
        <?php if (isset($component)) { $__componentOriginale890c050104d0aaf208369cc0a43e7e6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale890c050104d0aaf208369cc0a43e7e6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'order::components.change-stage-modal','data' => ['orderId' => $order->id,'currentStageId' => $order->stage_id,'orderStages' => $orderStages ?? []]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('order::change-stage-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['order-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($order->id),'current-stage-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($order->stage_id),'order-stages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orderStages ?? [])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale890c050104d0aaf208369cc0a43e7e6)): ?>
<?php $attributes = $__attributesOriginale890c050104d0aaf208369cc0a43e7e6; ?>
<?php unset($__attributesOriginale890c050104d0aaf208369cc0a43e7e6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale890c050104d0aaf208369cc0a43e7e6)): ?>
<?php $component = $__componentOriginale890c050104d0aaf208369cc0a43e7e6; ?>
<?php unset($__componentOriginale890c050104d0aaf208369cc0a43e7e6); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <style>
        .no-print {
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }

        @media print {

            /* Hide non-printable elements */
            .sidebar,
            .header,
            .navbar,
            .no-print,
            .breadcrumb,
            nav,
            .btn,
            button,
            footer {
                display: none !important;
            }

            .badge {
                background-color: white !important;
                color: black !important;
            }

            /* Reset body and container */
            body * {
                visibility: hidden;
            }

            #printableArea,
            #printableArea * {
                visibility: visible;
            }

            body {
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                font-size: 11pt;
            }

            .container-fluid {
                margin: 0 !important;
                padding: 10mm !important;
                max-width: 100% !important;
                width: 100% !important;
            }

            #printableArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 15px !important;
                background: white !important;
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
            }

            /* Card styling for print */
            .card {
                border: 1pt solid #ddd !important;
                box-shadow: none !important;
                page-break-inside: avoid;
                margin-bottom: 8pt !important;
            }

            .card-body {
                padding: 8pt !important;
            }

            .card-title {
                font-size: 10pt !important;
                margin-bottom: 8pt !important;
                border-bottom: 1pt solid #ddd !important;
                padding-bottom: 6pt !important;
            }

            /* Hide icons in print */
            .uil {
                display: none !important;
            }

            /* Header section - Order Info & Customer Details */
            .row.mb-40 {
                page-break-inside: avoid;
                margin-bottom: 10pt !important;
                padding-bottom: 0 !important;
                display: block !important;
            }

            /* Fix column layout for print */
            .row {
                display: block !important;
                page-break-inside: avoid;
            }

            .col-lg-6,
            .col-md-6,
            .col-md-3 {
                width: 100% !important;
                float: none !important;
                display: block !important;
                margin-bottom: 10pt !important;
            }

            /* Detail rows styling */
            .detail-row {
                display: flex !important;
                justify-content: space-between !important;
                padding: 4pt 0 !important;
                border-bottom: none !important;
                font-size: 9pt !important;
                margin-bottom: 3pt !important;
            }

            .detail-label {
                font-weight: 600 !important;
                color: #333 !important;
                min-width: 100pt !important;
            }

            .detail-value {
                color: #333 !important;
                text-align: right !important;
                flex: 1;
            }

            /* Text styling */
            p {
                margin-bottom: 4pt !important;
                line-height: 1.3 !important;
            }

            .text-primary {
                color: #003d82 !important;
            }

            .fw-bold {
                font-weight: bold !important;
            }

            /* Text alignment */
            .text-end {
                text-align: right !important;
            }

            .text-center {
                text-align: center !important;
            }

            /* Table styling */
            .table-responsive {
                width: 100% !important;
                overflow: visible !important;
                page-break-inside: avoid;
            }

            .table {
                width: 100% !important;
                border-collapse: collapse !important;
                margin-bottom: 10pt !important;
                page-break-inside: auto;
                font-size: 10pt !important;
            }

            .table thead {
                background-color: #003d82 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                color: white !important;
                page-break-inside: avoid;
                page-break-after: avoid;
            }

            .table th {
                padding: 6pt 4pt !important;
                border: 0.5pt solid #003d82 !important;
                font-weight: bold !important;
                color: white !important;
                background-color: #003d82 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .table td {
                padding: 5pt !important;
                border: 0.5pt solid #ddd !important;
                line-height: 1.2 !important;
            }

            .table tbody tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            .table tbody tr:nth-child(even) {
                background-color: #f9f9f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            /* Badge styling */
            .badge {
                padding: 2pt 4pt !important;
                font-size: 8pt !important;
                border-radius: 2pt !important;
                display: inline-block !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .badge-lg {
                padding: 3pt 6pt !important;
                font-size: 9pt !important;
            }

            .bg-info {
                background-color: #17a2b8 !important;
                color: white !important;
            }

            .bg-dark {
                background-color: #343a40 !important;
                color: white !important;
            }

            .bg-success {
                background-color: #28a745 !important;
                color: white !important;
            }

            .bg-secondary {
                background-color: #6c757d !important;
                color: white !important;
            }

            .bg-danger {
                background-color: #dc3545 !important;
                color: white !important;
            }

            /* Summary card with gradient */
            .card[style*="gradient"] {
                background: #003d82 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                border: 1pt solid #003d82 !important;
                box-shadow: none !important;
                page-break-inside: avoid;
                margin-bottom: 8pt !important;
            }

            .card[style*="gradient"] .card-body {
                padding: 8pt !important;
            }

            .card[style*="gradient"] .card-title {
                color: white !important;
                border-bottom: 1pt solid rgba(255, 255, 255, 0.3) !important;
                font-size: 10pt !important;
                margin-bottom: 8pt !important;
                padding-bottom: 6pt !important;
            }

            .card[style*="gradient"] * {
                color: white !important;
            }

            .card[style*="gradient"] .summary-row {
                color: white !important;
            }

            .card[style*="gradient"] .summary-row span {
                color: white !important;
            }

            .summary-row {
                display: flex !important;
                justify-content: space-between !important;
                padding: 4pt 0 !important;
                font-size: 9pt !important;
                margin-bottom: 2pt !important;
            }

            .summary-row span {
                color: white !important;
            }

            /* Summary details section */
            .summary-details {
                width: 100% !important;
            }

            /* Horizontal rule */
            hr {
                border: 0 !important;
                border-top: 0.5pt solid #ddd !important;
                margin: 4pt 0 !important;
                page-break-inside: avoid;
            }

            hr.bg-white {
                border-top: 0.5pt solid rgba(255, 255, 255, 0.3) !important;
                margin: 4pt 0 !important;
            }

            .card[style*="gradient"] hr {
                border-top: 0.5pt solid rgba(255, 255, 255, 0.3) !important;
            }

            /* Flex utilities for print */
            .d-flex {
                display: flex !important;
            }

            .justify-content-between {
                justify-content: space-between !important;
            }

            .justify-content-start {
                justify-content: flex-start !important;
            }

            .justify-content-end {
                justify-content: flex-end !important;
            }

            .align-items-center {
                align-items: center !important;
            }

            .mb-10 {
                margin-bottom: 8pt !important;
            }

            .mb-15 {
                margin-bottom: 10pt !important;
            }

            .mb-5 {
                margin-bottom: 4pt !important;
            }

            .mb-0 {
                margin-bottom: 0 !important;
            }

            /* Font sizes */
            .fs-16 {
                font-size: 14pt !important;
            }

            h6 {
                font-size: 12pt !important;
                margin-bottom: 10pt !important;
            }

            small {
                font-size: 9pt !important;
            }

            /* Text utilities */
            .text-muted {
                color: #6c757d !important;
            }

            /* Page settings */
            @page {
                size: A4;
                margin: 15mm 10mm;
            }

            /* Prevent orphans and widows */
            p,
            h1,
            h2,
            h3,
            h4,
            h5,
            h6 {
                orphans: 3;
                widows: 3;
            }

            /* Color adjustment for all colored elements */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>

    <script>
        function printInvoice() {
            // Get order number for filename
            const orderNumber = '<?php echo e($order->order_number); ?>';

            // Set document title for print dialog
            const originalTitle = document.title;
            document.title = `Invoice_${orderNumber}`;

            // Trigger print dialog
            window.print();

            // Restore original title after print dialog closes
            setTimeout(() => {
                document.title = originalTitle;
            }, 100);
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/Order\resources/views/orders/show.blade.php ENDPATH**/ ?>