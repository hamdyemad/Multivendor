@extends('layout.app')
@section('title', trans('order.edit_order'))
@section('content')
    <div style="padding: 20px;">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    ['title' => trans('order.order_management'), 'url' => route('admin.orders.index')],
                    ['title' => trans('order.edit_order') . ' #' . $order->order_number],
                ]" />
            </div>
        </div>

        <div class="responsive-grid">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-20">
                    <h5 class="mb-0 fw-500">
                        {{ trans('order.edit_order') }} #{{ $order->order_number }}
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Alert Container -->
                    <div id="alertContainer" class="mb-2"></div>

                    <form id="editOrderForm" action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Customer Selection Section --}}
                        <div class="mb-30">
                            <h6 class="fw-500 mb-20">
                                <i class="uil uil-user me-2"></i>{{ trans('order.customer_information') }}
                            </h6>

                            {{-- Customer Type Selection --}}
                            <div class="row mb-20">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('order.customer_type') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="btn-group w-100" role="group">
                                            <input type="radio" class="btn-check" name="customer_type"
                                                id="existing_customer" value="existing" {{ $order->customer_id ? 'checked' : '' }}>
                                            <label class="btn btn-outline-primary" for="existing_customer">
                                                <i class="uil uil-database me-1"></i>{{ trans('order.existing_customer') }}
                                            </label>

                                            <input type="radio" class="btn-check" name="customer_type"
                                                id="external_customer" value="external" {{ !$order->customer_id ? 'checked' : '' }}>
                                            <label class="btn btn-outline-primary" for="external_customer">
                                                <i class="uil uil-user-plus me-1"></i>{{ trans('order.external_customer') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Existing Customer Section --}}
                            <div id="existing_customer_section" style="{{ !$order->customer_id ? 'display: none;' : '' }}">
                                <div class="row">
                                    <div class="col-md-12 mb-25">
                                        <div class="form-group">
                                            <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                {{ trans('order.select_customer') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="position-relative">
                                                <input type="text"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    id="customer_search"
                                                    placeholder="{{ __('common.search') }} {{ trans('order.customer_name') }}..."
                                                    autocomplete="off"
                                                    value="{{ $order->customer?->full_name ?? '' }}">
                                                <div class="position-absolute w-100 bg-white border rounded-bottom shadow-sm"
                                                    id="customer_suggestions"
                                                    style="display: none; top: 100%; left: 0; z-index: 1000; max-height: 300px; overflow-y: auto;">
                                                </div>
                                            </div>
                                            <input type="hidden" id="selected_customer_id" name="selected_customer_id"
                                                value="{{ $order->customer_id ?? '' }}">
                                        </div>
                                    </div>
                                </div>

                                {{-- Customer Address Selection --}}
                                <div class="row" id="customer_address_section" style="{{ $order->customer_id ? '' : 'display: none;' }}">
                                    <div class="col-md-12 mb-25">
                                        <div class="form-group">
                                            <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                {{ trans('order.customer_address') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="d-flex gap-2">
                                                <select
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                    id="customer_address_select" name="customer_address_id">
                                                    <option value="">{{ trans('order.select_address') }}
                                                    </option>
                                                </select>
                                                <button type="button" class="btn btn-primary" id="addNewAddressBtn"
                                                    title="Add new address">
                                                    <i class="uil uil-plus m-0"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- No Address Message --}}
                                <div class="row" id="no_address_section" style="display: none;">
                                    <div class="col-md-12 mb-25">
                                        <div class="alert alert-info" role="alert">
                                            <i class="uil uil-info-circle me-2"></i>
                                            {{ trans('order.customer_has_no_address') }}
                                            <button type="button" class="btn btn-sm btn-primary ms-2"
                                                id="createAddressBtn">
                                                <i class="uil uil-plus me-1"></i>{{ trans('order.create_address') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Auto-filled Customer Details --}}
                                <div class="row">
                                    <div class="col-md-6 mb-25">
                                        <div class="form-group">
                                            <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                {{ trans('order.customer_email') }}
                                            </label>
                                            <input type="email"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="customer_email" name="customer_email"
                                                placeholder="{{ trans('order.customer_email') }}" readonly
                                                value="{{ $order->customer?->email ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-25">
                                        <div class="form-group">
                                            <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                {{ trans('order.customer_phone') }}
                                            </label>
                                            <input type="tel"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="customer_phone" name="customer_phone"
                                                placeholder="{{ trans('order.customer_phone') }}" readonly
                                                value="{{ $order->customer?->phone ?? $order->customer_phone ?? '' }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-25">
                                        <div class="form-group">
                                            <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                {{ trans('order.customer_address') }}
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="customer_address" name="customer_address"
                                                placeholder="{{ trans('order.customer_address') }}" readonly
                                                value="{{ $order->customer_address ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- External Customer Section --}}
                            <div id="external_customer_section" style="{{ $order->customer_id ? 'display: none;' : '' }}">
                                <div class="row">
                                    <div class="col-md-6 mb-25">
                                        <div class="form-group">
                                            <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                {{ trans('order.customer_name') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="external_customer_name" name="external_customer_name"
                                                placeholder="{{ trans('order.customer_name') }}"
                                                value="{{ !$order->customer_id ? $order->customer_name : '' }}">
                                            @error('external_customer_name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-25">
                                        <div class="form-group">
                                            <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                {{ trans('order.customer_email') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="email"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="external_customer_email" name="external_customer_email"
                                                placeholder="{{ trans('order.customer_email') }}"
                                                value="{{ !$order->customer_id ? $order->customer_email : '' }}">
                                            @error('external_customer_email')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-25">
                                        <div class="form-group">
                                            <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                {{ trans('order.customer_phone') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="tel"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="external_customer_phone" name="external_customer_phone"
                                                placeholder="{{ trans('order.customer_phone') }}"
                                                value="{{ !$order->customer_id ? $order->customer_phone : '' }}">
                                            @error('external_customer_phone')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-25">
                                        <div class="form-group">
                                            <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                {{ trans('order.city') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="external_city_id" name="external_city_id">
                                                <option value="">{{ __('common.select') }}</option>
                                            </select>
                                            @error('external_city_id')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-25">
                                        <div class="form-group">
                                            <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                {{ trans('order.region') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="external_region_id" name="external_region_id" disabled>
                                                <option value="">{{ __('common.select') }}</option>
                                            </select>
                                            @error('external_region_id')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-25">
                                        <div class="form-group">
                                            <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                {{ trans('order.customer_address') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="external_customer_address" name="external_customer_address"
                                                placeholder="{{ trans('order.customer_address') }}"
                                                value="{{ !$order->customer_id ? $order->customer_address : '' }}">
                                            @error('external_customer_address')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        {{-- Products Section --}}
                        <div class="mb-30">
                            <h6 class="fw-500 mb-20">
                                <i class="uil uil-shopping-bag me-2"></i>{{ trans('order.add_product') }}
                            </h6>

                            <div class="row mb-20">
                                <div class="col-md-8 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('order.add_product') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="position-relative">
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="product_search"
                                                placeholder="{{ __('common.search') }}"
                                                autocomplete="off">
                                            <div class="position-absolute w-100 bg-white border rounded-bottom shadow-sm"
                                                id="product_suggestions"
                                                style="display: none; top: 100%; left: 0; z-index: 1000; max-height: 300px; overflow-y: auto;">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('order.items_count') }}
                                            <small class="text-muted" id="limitationText"></small>
                                        </label>
                                        <div class="input-group">
                                            <input type="number"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="product_quantity" name="product_quantity" placeholder="1"
                                                min="1" value="1">
                                            <button type="button" class="btn btn-primary" id="addProductBtn" disabled>
                                                <i class="uil uil-plus me-1"></i>{{ trans('order.add_product') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Hidden input for selected product --}}
                            <input type="hidden" id="selected_product_id" value="">
                            <input type="hidden" id="selected_product_variant_id" value="">
                            <input type="hidden" id="selected_product_name" value="">
                            <input type="hidden" id="selected_product_price" value="">
                            <input type="hidden" id="selected_product_limitation" value="">
                            <input type="hidden" id="selected_product_tax_rate" value="">
                            <input type="hidden" id="selected_product_taxes_info" value="">
                            <input type="hidden" id="selected_product_unit_price_before_tax" value="">
                            <input type="hidden" id="selected_product_category_id" value="">
                            <input type="hidden" id="selected_product_category_name" value="">
                            <input type="hidden" id="selected_product_sku" value="">
                            <input type="hidden" id="selected_product_variant_name" value="">
                            <input type="hidden" id="selected_product_vendor_name" value="">
                            <input type="hidden" id="selected_product_image" value="">
                            <input type="hidden" id="selected_product_stock" value="">
                            {{-- Products List --}}
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered" id="productsTable">
                                    <thead>
                                        <tr class="userDatatable-header">
                                            <th>{{ trans('order.product_name') }}</th>
                                            <th class="text-center">{{ trans('order.price') }}</th>
                                            <th class="text-center">{{ trans('order.items_count') }}</th>
                                            <th class="text-center">{{ trans('order.tax') }}</th>
                                            <th class="text-center">{{ trans('order.total') }}</th>
                                            <th class="text-center">{{ __('common.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="productsTableBody">
                                    </tbody>
                                </table>
                            </div>

                            {{-- Hidden input for products --}}
                            <input type="hidden" id="productsData" name="products" value="[]">
                        </div>


                        {{-- Hidden inputs for fees, discounts, and shipping --}}
                        <input type="hidden" id="feesData" name="feesData" value="[]">
                        <input type="hidden" id="discountsData" name="discountsData" value="[]">
                        <input type="hidden" id="shipping" name="shipping" value="0">
                    </form>
                </div>
            </div>

            {{-- Order Summary Sidebar --}}
            <div class="card border-0 shadow-sm" style="position: sticky; top: 20px; height: fit-content;">
                <div class="card-header bg-white border-bottom py-20">
                    <h5 class="mb-0 fw-500">
                        {{ trans('order.order_summary') }}
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Subtotal --}}
                    <div class="d-flex justify-content-between align-items-center mb-15 pb-15 border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="uil uil-receipt text-warning me-2" style="font-size: 18px;"></i>
                            <span class="fw-500">{{ trans('order.subtotal') }}</span>
                        </div>
                        <span class="fw-500" id="subtotal">0.00 {{ currency() }}</span>
                    </div>

                    {{-- Shipping Cost --}}
                    <div class="d-flex justify-content-between align-items-center mb-15 pb-15 border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="uil uil-truck text-info me-2" style="font-size: 18px;"></i>
                            <span class="fw-500">{{ trans('order.shipping') }}</span>
                        </div>
                        <span class="fw-500" id="shippingDisplay">0.00 {{ currency() }}</span>
                    </div>

                    {{-- Total Tax --}}
                    <div class="d-flex justify-content-between align-items-center mb-15 pb-15 border-bottom d-none"
                        id="taxSection">
                        <div class="d-flex align-items-center">
                            <i class="uil uil-chart-pie text-info me-2" style="font-size: 18px;"></i>
                            <span class="fw-500">{{ trans('order.tax') }}</span>
                        </div>
                        <span class="fw-500" id="totalTax">0.00 {{ __('common.currency') }}</span>
                    </div>

                    {{-- Additional Fees Section --}}
                    <div class="mb-15 pb-15 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-10">
                            <div class="d-flex align-items-center">
                                <i class="uil uil-plus-circle text-success me-2" style="font-size: 18px;"></i>
                                <span class="fw-500">{{ trans('order.add_fee') }}</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-success" id="addFeeBtn">
                                <i class="uil uil-plus me-1"></i>{{ trans('order.add_fee') }}
                            </button>
                        </div>
                        <div id="feesContainer"></div>
                        <span class="fw-500" id="totalFeesDisplay">0.00 {{ currency() }}</span>
                    </div>

                    {{-- Additional Discounts Section --}}
                    <div class="mb-15 pb-15 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-10">
                            <div class="d-flex align-items-center">
                                <i class="uil uil-gift text-danger me-2" style="font-size: 18px;"></i>
                                <span class="fw-500">{{ trans('order.add_discount') }}</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-warning" id="addDiscountBtn">
                                <i class="uil uil-plus me-1"></i>{{ trans('order.add_discount') }}
                            </button>
                        </div>
                        <div id="discountsContainer"></div>
                        <span class="fw-500" id="totalDiscountsDisplay">0.00 {{ currency() }}</span>
                    </div>

                    {{-- Grand Total --}}
                    <div class="d-flex justify-content-between align-items-center pt-15 mb-20 pb-15 border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="uil uil-receipt text-primary me-2" style="font-size: 18px;"></i>
                            <span class="fw-500 fs-16">{{ trans('order.total') }}</span>
                        </div>
                        <span class="fw-bold fs-16 text-primary" id="grandTotal">0.00
                            {{ __('common.currency') }}</span>
                    </div>

                    {{-- Form Actions --}}
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-light btn-default btn-squared">
                            <i class="uil uil-arrow-left me-1"></i>
                            {{ trans('main.cancel') }}
                        </a>
                        <button type="submit" form="editOrderForm" class="btn btn-primary btn-squared"
                            id="submitBtn">
                            <i class="uil uil-check me-1"></i>
                            {{ trans('order.edit_order') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>


        {{-- Add Address Modal --}}
        <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAddressModalLabel">
                            <i class="uil uil-map-pin me-2"></i>{{ trans('order.add_new_address') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addAddressForm" novalidate>
                            <div id="addressFormErrors" class="alert alert-danger" style="display: none;"></div>

                            <div class="row">
                                <div class="col-md-12 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('order.address_title') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                            class="form-control ih-medium ip-gray radius-xs b-light px-15 address-required"
                                            id="address_title" name="address_title" placeholder="{{ __('order.address_title') }}"
                                            data-field="title">
                                        <small class="text-danger d-none error-message"></small>
                                    </div>
                                </div>
                            </div>

                            {{-- Hidden country field - automatically set from session --}}
                            <input type="hidden" id="address_country_id" name="address_country_id" data-field="country_id">

                            <div class="row">
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('order.city') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select
                                            class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select address-required"
                                            id="address_city_id" name="address_city_id" data-field="city_id">
                                            <option value="">{{ __('common.select') }}</option>
                                        </select>
                                        <small class="text-danger d-none error-message"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('order.region') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select
                                            class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select address-required"
                                            id="address_region_id" name="address_region_id" disabled
                                            data-field="region_id">
                                            <option value="">{{ __('common.select') }}</option>
                                        </select>
                                        <small class="text-danger d-none error-message"></small>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('order.sub_region') }}
                                        </label>
                                        <select class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                            id="address_subregion_id" name="address_subregion_id" disabled
                                            data-field="subregion_id">
                                            <option value="">{{ __('common.select') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('order.customer_address') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                            class="form-control ih-medium ip-gray radius-xs b-light px-15 address-required"
                                            id="address_address" name="address_address" placeholder="{{ __('order.add_new_address') }}"
                                            data-field="address">
                                        <small class="text-danger d-none error-message"></small>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-25">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="address_is_primary"
                                            name="address_is_primary">
                                        <label class="form-check-label" for="address_is_primary">
                                            {{ trans('order.set_as_primary') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light btn-default" data-bs-dismiss="modal">
                            <i class="uil uil-times me-1"></i>{{ trans('main.cancel') }}
                        </button>
                        <button type="button" class="btn btn-primary" id="saveAddressBtn">
                            <i class="uil uil-check me-1"></i>{{ trans('main.create') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                $(document).ready(function() {
                    // Get country code from session
                    const countryCode = '{{ strtoupper(session('country_code', 'EG')) }}';

                    let feeCounter = 0;
                    let discountCounter = 0;
                    let fees = [];
                    let discounts = [];
                    let products = [];
                    let productCounter = 0;
                    let allCustomers = [];

                    // Debug: Check order data
                    console.log('Order products count from PHP:', {{ $order->products ? $order->products->count() : 0 }});
                    console.log('Order extraFeesDiscounts count from PHP:', {{ $order->extraFeesDiscounts ? $order->extraFeesDiscounts->count() : 0 }});
                    console.log('Order fees count from PHP:', {{ $order->extraFeesDiscounts ? $order->extraFeesDiscounts->where('type', 'fee')->count() : 0 }});
                    console.log('Order discounts count from PHP:', {{ $order->extraFeesDiscounts ? $order->extraFeesDiscounts->where('type', 'discount')->count() : 0 }});

                    // Pre-populate existing order products
                    @if($order->products && $order->products->count() > 0)
                        @foreach($order->products as $orderProduct)
                            @php
                                $productName = $orderProduct->vendorProduct?->product?->getTranslation('title', app()->getLocale()) ?? 'N/A';
                                $variantName = $orderProduct->vendorProductVariant?->variantConfiguration?->getTranslation('name', app()->getLocale()) ?? '';
                                $fullName = $variantName ? $productName . ' - ' . $variantName : $productName;
                                // Get tax rate from all taxes
                                $taxRate = $orderProduct->vendorProduct?->taxes?->sum('percentage') ?? 0;
                                $categoryId = $orderProduct->vendorProduct?->product?->category_id ?? null;
                                $categoryName = $orderProduct->vendorProduct?->product?->category?->getTranslation('name', app()->getLocale()) ?? '';
                                
                                // Price stored is total price WITH tax for all quantities
                                $totalPriceWithTax = $orderProduct->price ?? 0;
                                $quantity = $orderProduct->quantity ?? 1;
                                
                                // Calculate unit price with tax
                                $unitPriceWithTax = $quantity > 0 ? $totalPriceWithTax / $quantity : 0;
                                
                                // Calculate unit price before tax
                                $unitPriceBeforeTax = $taxRate > 0 ? $unitPriceWithTax / (1 + $taxRate / 100) : $unitPriceWithTax;
                                
                                $sku = $orderProduct->vendorProductVariant?->sku ?? $orderProduct->vendorProduct?->sku ?? 'N/A';
                                $vendorName = $orderProduct->vendorProduct?->vendor?->getTranslation('name', app()->getLocale()) ?? 'N/A';
                                $productImage = $orderProduct->vendorProduct?->product?->image ?? '';
                            @endphp
                            products.push({
                                id: productCounter++,
                                vendor_product_id: {{ $orderProduct->vendor_product_id ?? 'null' }},
                                vendor_product_variant_id: {{ $orderProduct->vendor_product_variant_id ?? 'null' }},
                                name: "{{ addslashes($fullName) }}",
                                price: {{ $unitPriceWithTax }},
                                unitPriceBeforeTax: {{ $unitPriceBeforeTax }},
                                quantity: {{ $quantity }},
                                taxRate: {{ $taxRate }},
                                total: {{ $totalPriceWithTax }},
                                category_id: {{ $categoryId ?? 'null' }},
                                category_name: "{{ addslashes($categoryName) }}",
                                sku: "{{ addslashes($sku) }}",
                                variantName: "{{ addslashes($variantName) }}",
                                vendorName: "{{ addslashes($vendorName) }}",
                                image: "{{ $productImage }}"
                            });
                        @endforeach
                    @endif

                    // Pre-populate existing fees
                    console.log('Checking for fees...');
                    console.log('extraFeesDiscounts exists:', {{ $order->extraFeesDiscounts ? 'true' : 'false' }});
                    console.log('extraFeesDiscounts count:', {{ $order->extraFeesDiscounts ? $order->extraFeesDiscounts->count() : 0 }});
                    @if($order->extraFeesDiscounts)
                        @php
                            $feesCollection = $order->extraFeesDiscounts->where('type', 'fee');
                        @endphp
                        console.log('Fees collection count:', {{ $feesCollection->count() }});
                        @if($feesCollection->count() > 0)
                            console.log('Loading fees from order...');
                            @foreach($feesCollection as $fee)
                                console.log('Fee found:', { reason: "{{ addslashes($fee->reason ?? '') }}", amount: {{ $fee->cost ?? 0 }} });
                                fees.push({
                                    id: feeCounter++,
                                    reason: "{{ addslashes($fee->reason ?? '') }}",
                                    amount: {{ $fee->cost ?? 0 }}
                                });
                            @endforeach
                        @else
                            console.log('No fees found (count is 0)');
                        @endif
                    @else
                        console.log('extraFeesDiscounts is null/empty');
                    @endif

                    // Pre-populate existing discounts
                    console.log('Checking for discounts...');
                    @if($order->extraFeesDiscounts)
                        @php
                            $discountsCollection = $order->extraFeesDiscounts->where('type', 'discount');
                        @endphp
                        console.log('Discounts collection count:', {{ $discountsCollection->count() }});
                        @if($discountsCollection->count() > 0)
                            console.log('Loading discounts from order...');
                            @foreach($discountsCollection as $discount)
                                console.log('Discount found:', { reason: "{{ addslashes($discount->reason ?? '') }}", amount: {{ $discount->cost ?? 0 }} });
                                discounts.push({
                                    id: discountCounter++,
                                    reason: "{{ addslashes($discount->reason ?? '') }}",
                                    amount: {{ $discount->cost ?? 0 }}
                                });
                            @endforeach
                        @else
                            console.log('No discounts found (count is 0)');
                        @endif
                    @else
                        console.log('extraFeesDiscounts is null/empty');
                    @endif

                    console.log('Fees array after loading:', fees);
                    console.log('Discounts array after loading:', discounts);

                    // Note: Products are now loaded via direct AJAX search, not pre-loaded

                    // Load all products
                    function loadAllProducts() {
                        $.ajax({
                            url: '/api/products/variants-all', // Products API endpoint
                            type: 'GET',
                            dataType: 'json',
                            data: {
                                vendor_id: "{{ !isAdmin() && auth()->user()->vendor ? auth()->user()->vendor->id : '' }}"
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Accept': 'application/json',
                                'X-Country-Code': countryCode,
                                'lang': "{{ app()->getLocale() }}",
                            },
                            success: function(response) {
                                console.log('Products loaded:', response);

                                if (response.data) {
                                    allProducts = response.data;
                                    console.log('Products loaded:', allProducts.length);
                                    console.log('Sample product:', allProducts[0]);
                                    console.log('Sample product full structure:', JSON.stringify(allProducts[0],
                                        null, 2));
                                } else if (response && Array.isArray(response)) {
                                    allProducts = response;
                                    console.log('Products loaded:', allProducts.length);
                                    console.log('Sample product:', allProducts[0]);
                                }
                            },
                            error: function(xhr) {
                                console.log('Failed to load products', xhr);
                                showAlert('danger', 'Failed to load products');
                            }
                        });
                    }

                    // Load all customers
                    function loadAllCustomers() {
                        let vendor_id = "{{ auth()->user()->vendor?->id }}";
                        $.ajax({
                            url: '/api/customers', // Customers API endpoint
                            type: 'GET',
                            data: {
                                vendor_id
                            },
                            dataType: 'json',
                            xhrFields: {
                                withCredentials: true
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Accept': 'application/json',
                                'X-Country-Code': countryCode,
                                'lang': "{{ app()->getLocale() }}",
                            },
                            success: function(response) {
                                console.log(response)
                                if (response.data) {
                                    allCustomers = response.data;
                                    console.log('Customers loaded:', allCustomers.length);
                                } else if (response && Array.isArray(response)) {
                                    allCustomers = response;
                                    console.log('Customers loaded:', allCustomers.length);
                                }
                            },
                            error: function(xhr) {
                                console.log('Failed to load customers', xhr);
                                showAlert('danger', 'Failed to load customers');
                            }
                        });
                    }

                    // Live product search with direct AJAX
                    let searchTimeout;
                    $('#product_search').on('keyup', function() {
                        clearTimeout(searchTimeout);
                        const searchTerm = $(this).val().trim();
                        const suggestions = $('#product_suggestions');

                        if (searchTerm.length < 2) {
                            suggestions.hide();
                            return;
                        }

                        searchTimeout = setTimeout(function() {
                            // Show loading state
                            suggestions.html('<div class="p-2 text-muted"><i class="uil uil-spinner-alt spin"></i> {{ trans('order::order.searching') }}...</div>').show();

                            // Make AJAX request to search products
                            $.ajax({
                                url: '/api/products/variants-all',
                                type: 'GET',
                                dataType: 'json',
                                xhrFields: {
                                    withCredentials: true
                                },
                                data: {
                                    search: searchTerm,
                                    per_page: 10,
                                    paginated: false,
                                    vendor_id: "{{ !isAdmin() && auth()->user()->vendor ? auth()->user()->vendor->id : '' }}"
                                },
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                    'Accept': 'application/json',
                                    'X-Country-Code': countryCode,
                                    'lang': "{{ app()->getLocale() }}"
                                },
                                success: function(response) {
                                    const products = response.data || [];

                                    if (products.length > 0) {
                                        let html = '';

                                        // For each product, show all its variants
                                        products.forEach(product => {
                                            const productName = product.name || product.title || 'N/A';
                                            const limitation = product.limitation || 0;
                                            // Calculate total tax rate from all taxes and build taxes info
                                            let taxRate = 0;
                                            let taxesInfo = [];
                                            if (product.taxes && Array.isArray(product.taxes)) {
                                                product.taxes.forEach(tax => {
                                                    const percentage = parseFloat(tax.percentage || tax.tax_rate || 0);
                                                    taxRate += percentage;
                                                    taxesInfo.push({
                                                        name: tax.name || 'Tax',
                                                        percentage: percentage
                                                    });
                                                });
                                            } else if (product.tax && product.tax.tax_rate) {
                                                taxRate = product.tax.tax_rate;
                                                taxesInfo.push({
                                                    name: product.tax.name || 'Tax',
                                                    percentage: taxRate
                                                });
                                            }
                                            const productStock = product.remaining_stock || 0;
                                            const productImage = product.image || '';
                                            const vendorId = product.vendor_id || null;

                                            // Extract category from product
                                            let categoryId = null;
                                            let categoryName = null;

                                            if (product.category) {
                                                categoryId = product.category.id;
                                                categoryName = product.category.name;
                                            }

                                            if (product.variants && product.variants.length > 0) {
                                                product.variants.forEach(variant => {
                                                    const price = parseFloat(variant.real_price) || 0;
                                                    // Calculate unit price before tax for display
                                                    const unitPriceBeforeTax = taxRate > 0 ? price / (1 + taxRate / 100) : price;
                                                    const variantKey = variant.variant_key;
                                                    const variantValue = variant.variant_value;
                                                    const variantName = variantKey ?
                                                        (variantValue ?
                                                            `${variantKey}: ${variantValue}` :
                                                            variantKey) :
                                                        (variant.variant_name || 'Default');
                                                    const variantSku = variant.sku || product.sku || 'N/A';
                                                    const variantStock = variant.remaining_stock ?? 0;
                                                    const vendorName = variant.vendor_name || 'N/A';

                                                    html += `
                                                <div class="p-2 border-bottom cursor-pointer product-suggestion ${variantStock <= 0 ? 'text-muted' : ''}"
                                                     data-id="${variant.id}"
                                                     data-product-id="${product.id}"
                                                     data-name="${productName} - ${variantName}"
                                                     data-price="${price}"
                                                     data-unit-price-before-tax="${unitPriceBeforeTax.toFixed(2)}"
                                                     data-limitation="${limitation}"
                                                     data-tax-rate="${taxRate}"
                                                     data-taxes-info='${JSON.stringify(taxesInfo)}'
                                                     data-category-id="${categoryId}"
                                                     data-category-name="${categoryName}"
                                                     data-sku="${variantSku}"
                                                     data-variant-name="${variantName}"
                                                     data-vendor-name="${vendorName}"
                                                     data-image="${productImage || ''}"
                                                     data-stock="${variantStock}"
                                                     style="cursor: pointer;">
                                                    <div class="d-flex align-items-center gap-2">
                                                        ${productImage ? 
                                                            `<img src="${productImage}" alt="${productName}" style="width: 50px; height: 50px;  border-radius: 4px; border: 1px solid #dee2e6;">` : 
                                                            `<div class="rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border: 1px solid #dee2e6;"><i class="uil uil-image text-muted"></i></div>`
                                                        }
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex justify-content-between">
                                                                <span class="fw-500">${productName}</span>
                                                                <span class="text-muted">${price.toFixed(2)} {{ currency() }}</span>
                                                            </div>
                                                            <small class="text-muted d-block">${variantName} (SKU: ${variantSku} | Stock: ${variantStock <= 0 ? '<span class="text-danger">Out of Stock</span>' : variantStock}) ${limitation > 0 ? `- Max: ${limitation}` : ''}</small>
                                                            <small class="text-primary d-block"><i class="uil uil-store me-1"></i>${vendorName}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            `;
                                                });
                                            } else {
                                                // Fallback if no variants
                                                html += `
                                            <div class="p-2 border-bottom cursor-pointer product-suggestion"
                                                 data-id="${product.id}"
                                                 data-product-id="${product.id}"
                                                 data-name="${productName}"
                                                 data-price="0"
                                                 data-limitation="${limitation}"
                                                 data-tax-rate="${taxRate}"
                                                 data-category-id="${categoryId}"
                                                 data-category-name="${categoryName}"
                                                 style="cursor: pointer;">
                                                <div class="d-flex align-items-center gap-2">
                                                    ${productImage ? 
                                                        `<img src="${productImage}" alt="${productName}" style="width: 50px; height: 50px;  border-radius: 4px; border: 1px solid #dee2e6;">` : 
                                                        `<div class="rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border: 1px solid #dee2e6;"><i class="uil uil-image text-muted"></i></div>`
                                                    }
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex justify-content-between">
                                                            <span class="fw-500">${productName}</span>
                                                            <span class="text-muted">No variants</span>
                                                        </div>
                                                        <small class="text-muted d-block">SKU: ${product.sku || 'N/A'} | Stock: ${productStock} ${limitation > 0 ? `- Max: ${limitation}` : ''}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                            }
                                        });
                                        suggestions.html(html).show();
                                    } else {
                                        suggestions.html(
                                            '<div class="p-2 text-muted">{{ trans('order::order.no_products_found') }}</div>'
                                        ).show();
                                    }
                                },
                                error: function(xhr) {
                                    console.error('Product search error:', xhr);
                                    suggestions.html(
                                        '<div class="p-2 text-danger">{{ trans('order::order.error_searching_products') }}</div>'
                                    ).show();
                                }
                            });
                        }, 300);
                    });

                    // Product suggestion click
                    $(document).on('click', '.product-suggestion', function() {
                        const productId = $(this).data('product-id');
                        const variantId = $(this).data('id');
                        const name = $(this).data('name');
                        const price = $(this).data('price');
                        const unitPriceBeforeTax = $(this).data('unit-price-before-tax') || price;
                        const limitation = $(this).data('limitation') || 0;
                        const taxRate = $(this).data('tax-rate') || 0;
                        const taxesInfo = $(this).data('taxes-info') || [];
                        const categoryId = $(this).data('category-id');
                        const categoryName = $(this).data('category-name');
                        const sku = $(this).data('sku') || 'N/A';
                        const variantName = $(this).data('variant-name') || '';
                        const vendorName = $(this).data('vendor-name') || 'N/A';
                        const image = $(this).data('image') || '';
                        const stock = parseInt($(this).data('stock')) || 0;

                        // Check stock availability
                        if (stock <= 0) {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ trans("order::order.out_of_stock") }}',
                                text: '{{ trans("order::order.product_out_of_stock_message") }}',
                                confirmButtonText: '{{ trans("common.ok") }}'
                            });
                            return;
                        }

                        console.log('Product selected:', {
                            productId,
                            variantId,
                            name,
                            price,
                            unitPriceBeforeTax,
                            limitation,
                            taxRate,
                            taxesInfo,
                            categoryId,
                            categoryName,
                            sku,
                            variantName,
                            vendorName,
                            image,
                            stock
                        });

                        $('#product_search').val(name);
                        $('#selected_product_id').val(productId);
                        $('#selected_product_variant_id').val(variantId);
                        $('#selected_product_name').val(name);
                        $('#selected_product_price').val(price);
                        $('#selected_product_unit_price_before_tax').val(unitPriceBeforeTax);
                        $('#selected_product_limitation').val(limitation);
                        $('#selected_product_tax_rate').val(taxRate);
                        $('#selected_product_taxes_info').val(JSON.stringify(taxesInfo));
                        $('#selected_product_category_id').val(categoryId);
                        $('#selected_product_category_name').val(categoryName);
                        $('#selected_product_sku').val(sku);
                        $('#selected_product_variant_name').val(variantName);
                        $('#selected_product_vendor_name').val(vendorName);
                        $('#selected_product_image').val(image);
                        $('#selected_product_stock').val(stock);
                        $('#product_suggestions').hide();
                        $('#addProductBtn').prop('disabled', false);

                        // Update quantity input max and show limitation text
                        if (limitation > 0) {
                            $('#product_quantity').attr('max', limitation);
                            $('#limitationText').text(`(Max: ${limitation})`);
                        } else {
                            $('#product_quantity').removeAttr('max');
                            $('#limitationText').text('');
                        }

                        console.log('Hidden fields set:', {
                            id: $('#selected_product_id').val(),
                            name: $('#selected_product_name').val(),
                            price: $('#selected_product_price').val(),
                            limitation: $('#selected_product_limitation').val(),
                            taxRate: $('#selected_product_tax_rate').val()
                        });
                    });

                    // Hide suggestions on blur
                    $(document).on('click', function(e) {
                        if (!$(e.target).closest('#product_search, #product_suggestions').length) {
                            $('#product_suggestions').hide();
                        }
                    });

                    // Customer type toggle
                    $('input[name="customer_type"]').on('change', function() {
                        if ($(this).val() === 'existing') {
                            $('#existing_customer_section').show();
                            $('#external_customer_section').hide();
                        } else {
                            $('#existing_customer_section').hide();
                            $('#external_customer_section').show();
                        }
                    });

                    // Live customer search
                    let customerSearchTimeout;
                    $('#customer_search').on('keyup', function() {
                        clearTimeout(customerSearchTimeout);
                        const searchTerm = $(this).val().toLowerCase();
                        const suggestions = $('#customer_suggestions');

                        if (searchTerm.length < 1) {
                            suggestions.hide();
                            return;
                        }

                        customerSearchTimeout = setTimeout(function() {
                            // Check if customers are loaded
                            if (!allCustomers || allCustomers.length === 0) {
                                console.log('Customers not loaded yet, loading...');
                                suggestions.html(
                                    '<div class="p-2 text-muted">{{ trans('order.loading_customers') }}</div>'
                                ).show();
                                return;
                            }

                            const filtered = allCustomers.filter(customer => {
                                const name = (customer.full_name || '').toLowerCase();
                                const email = (customer.email || '').toLowerCase();
                                const phone = (customer.phone || '').toString();

                                return name.includes(searchTerm) ||
                                    email.includes(searchTerm) ||
                                    phone.includes(searchTerm);
                            }).slice(0, 10);

                            if (filtered.length > 0) {
                                let html = '';
                                filtered.forEach(customer => {
                                    html += `
                                <div class="p-2 border-bottom cursor-pointer customer-suggestion"
                                     data-id="${customer.id}"
                                     data-name="${customer.full_name || ''}"
                                     data-email="${customer.email || ''}"
                                     data-phone="${customer.phone || ''}"
                                     style="cursor: pointer;">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-500">${customer.full_name || 'N/A'}</span>
                                        <small class="text-muted">${customer.email || 'N/A'}</small>
                                    </div>
                                    <small class="text-muted">${customer.phone || 'N/A'}</small>
                                </div>
                            `;
                                });
                                suggestions.html(html).show();
                            } else {
                                suggestions.html(
                                    '<div class="p-2 text-muted">{{ trans('order.no_customers_found') }}</div>'
                                ).show();
                            }
                        }, 300);
                    });

                    // Customer suggestion click
                    $(document).on('click', '.customer-suggestion', function() {
                        const id = $(this).data('id');
                        const name = $(this).data('name');
                        const email = $(this).data('email');
                        const phone = $(this).data('phone');

                        console.log('Customer selected:', {
                            id,
                            name,
                            email,
                            phone
                        });

                        $('#customer_search').val(name);
                        $('#selected_customer_id').val(id);
                        $('#customer_suggestions').hide();

                        // Immediately populate email and phone fields
                        $('#customer_email').val(email || '');
                        $('#customer_phone').val(phone || '');

                        // Load customer addresses
                        loadCustomerAddresses(id);
                    });

                    // Load customer addresses
                    function loadCustomerAddresses(customerId, preSelectAddress = null) {
                        $.ajax({
                            url: `/api/customers/${customerId}/addresses`,
                            type: 'GET',
                            dataType: 'json',
                            xhrFields: {
                                withCredentials: true
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Accept': 'application/json',
                                'X-Country-Code': countryCode,
                                'lang': "{{ app()->getLocale() }}",
                            },
                            success: function(response) {
                                const addressSelect = $('#customer_address_select');
                                addressSelect.empty();
                                addressSelect.append(
                                    '<option value="">{{ trans('order.select_address') }}</option>');

                                if (response.data && response.data.length > 0) {
                                    let matchedAddressId = null;
                                    
                                    response.data.forEach(address => {
                                        addressSelect.append(
                                            `<option value="${address.id}" data-address="${address.address}">${address.title} - ${address.address}</option>`
                                        );
                                        
                                        // Check if this address matches the pre-select address
                                        if (preSelectAddress && address.address === preSelectAddress) {
                                            matchedAddressId = address.id;
                                        }
                                    });
                                    
                                    // Pre-select the matched address
                                    if (matchedAddressId) {
                                        addressSelect.val(matchedAddressId);
                                        $('#customer_address').val(preSelectAddress);
                                    }
                                    
                                    $('#customer_address_section').show();
                                    $('#no_address_section').hide();
                                } else {
                                    $('#customer_address_section').hide();
                                    $('#no_address_section').show();
                                }

                                // Store current customer ID for address creation
                                $('#addAddressForm').data('customer-id', customerId);
                            },
                            error: function(xhr) {
                                console.error('Failed to load addresses:', xhr);
                                showAlert('danger', 'Failed to load customer addresses');
                            }
                        });
                    }

                    // Customer address select change
                    $('#customer_address_select').on('change', function() {
                        const address = $(this).find('option:selected').data('address');
                        $('#customer_address').val(address);
                    });

                    // Load cities for the current country (from session)
                    function loadCitiesForCurrentCountry() {
                        const countryId = $("meta[name='current_country_id']").attr('content');
                        
                        if (!countryId) {
                            console.error('No country ID found in meta tag');
                            return;
                        }

                        // Set the hidden country field
                        $('#address_country_id').val(countryId);

                        const citySelect = $('#address_city_id');
                        citySelect.empty().append('<option value="">{{ __('common.select') }}</option>');

                        $.ajax({
                            url: `/api/area/countries/${countryId}/cities`,
                            type: 'GET',
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Accept': 'application/json',
                                'X-Country-Code': countryCode,
                                'lang': "{{ app()->getLocale() }}",
                            },
                            success: function(response) {
                                if (response.data && response.data.length > 0) {
                                    response.data.forEach(city => {
                                        citySelect.append(
                                            `<option value="${city.id}">${city.name || city.title}</option>`
                                        );
                                    });
                                }
                            }
                        });
                    }

                    // Load regions based on city
                    $('#address_city_id').on('change', function() {
                        const cityId = $(this).val();
                        const regionSelect = $('#address_region_id');

                        regionSelect.empty().append('<option value="">{{ __('common.select') }}</option>').prop(
                            'disabled', true);
                        $('#address_subregion_id').empty().append(
                            '<option value="">{{ __('common.select') }}</option>').prop('disabled', true);

                        if (!cityId) return;

                        $.ajax({
                            url: `/api/area/cities/${cityId}/regions`,
                            type: 'GET',
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Accept': 'application/json',
                                'X-Country-Code': countryCode,
                                'lang': "{{ app()->getLocale() }}",
                            },
                            success: function(response) {
                                if (response.data && response.data.length > 0) {
                                    response.data.forEach(region => {
                                        regionSelect.append(
                                            `<option value="${region.id}">${region.name || region.title}</option>`
                                        );
                                    });
                                    regionSelect.prop('disabled', false);
                                }
                            }
                        });
                    });

                    // Load sub-regions based on region
                    $('#address_region_id').on('change', function() {
                        const regionId = $(this).val();
                        const subregionSelect = $('#address_subregion_id');

                        subregionSelect.empty().append('<option value="">{{ __('common.select') }}</option>').prop(
                            'disabled', true);

                        if (!regionId) return;

                        $.ajax({
                            url: `/api/area/regions/${regionId}/subregions`,
                            type: 'GET',
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Accept': 'application/json',
                                'X-Country-Code': countryCode,
                                'lang': "{{ app()->getLocale() }}",
                            },
                            success: function(response) {
                                if (response.data && response.data.length > 0) {
                                    response.data.forEach(subregion => {
                                        subregionSelect.append(
                                            `<option value="${subregion.id}">${subregion.name || subregion.title}</option>`
                                        );
                                    });
                                    subregionSelect.prop('disabled', false);
                                }
                            }
                        });
                    });

                    // ========== External Customer Location Handlers ==========
                    // Load cities for external customer
                    function loadExternalCities(selectedCityId = null, selectedRegionId = null) {
                        const countryId = $("meta[name='current_country_id']").attr('content');
                        
                        if (!countryId) return;

                        const citySelect = $('#external_city_id');
                        citySelect.empty().append('<option value="">{{ __('common.select') }}</option>');

                        $.ajax({
                            url: `/api/area/countries/${countryId}/cities`,
                            type: 'GET',
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Accept': 'application/json',
                                'X-Country-Code': countryCode,
                                'lang': "{{ app()->getLocale() }}",
                            },
                            success: function(response) {
                                if (response.data && response.data.length > 0) {
                                    response.data.forEach(city => {
                                        citySelect.append(
                                            `<option value="${city.id}" ${selectedCityId == city.id ? 'selected' : ''}>${city.name || city.title}</option>`
                                        );
                                    });
                                    // If city was pre-selected, load regions
                                    if (selectedCityId) {
                                        loadExternalRegions(selectedCityId, selectedRegionId);
                                    }
                                }
                            }
                        });
                    }

                    // Load regions for external customer based on city
                    function loadExternalRegions(cityId, selectedRegionId = null) {
                        const regionSelect = $('#external_region_id');
                        regionSelect.empty().append('<option value="">{{ __('common.select') }}</option>').prop('disabled', true);

                        if (!cityId) return;

                        $.ajax({
                            url: `/api/area/cities/${cityId}/regions`,
                            type: 'GET',
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Accept': 'application/json',
                                'X-Country-Code': countryCode,
                                'lang': "{{ app()->getLocale() }}",
                            },
                            success: function(response) {
                                if (response.data && response.data.length > 0) {
                                    response.data.forEach(region => {
                                        regionSelect.append(
                                            `<option value="${region.id}" ${selectedRegionId == region.id ? 'selected' : ''}>${region.name || region.title}</option>`
                                        );
                                    });
                                    regionSelect.prop('disabled', false);
                                }
                            }
                        });
                    }

                    $('#external_city_id').on('change', function() {
                        loadExternalRegions($(this).val());
                    });

                    // Load external cities when switching to external customer or on page load for external orders
                    $('input[name="customer_type"]').on('change', function() {
                        if ($(this).val() === 'external') {
                            loadExternalCities();
                        }
                    });

                    // On page load, if order is external customer, load cities with pre-selected values
                    @if(!$order->customer_id)
                        loadExternalCities({{ $order->city_id ?? 'null' }}, {{ $order->region_id ?? 'null' }});
                    @endif

                    // Open add address modal
                    $('#addNewAddressBtn, #createAddressBtn').on('click', function() {
                        const customerId = $('#selected_customer_id').val();
                        if (!customerId) {
                            showAlert('warning', '{{ trans('order.please_select_customer') }}');
                            return;
                        }

                        // Set country from session
                        const countryId = $("meta[name='current_country_id']").attr('content');
                        $('#address_country_id').val(countryId);

                        // Pre-fill email and phone if available
                        const email = $('#customer_email').val();
                        const phone = $('#customer_phone').val();

                        $('#address_email').val(email);
                        $('#address_phone').val(phone);

                        const modal = new bootstrap.Modal(document.getElementById('addAddressModal'));
                        modal.show();
                    });

                    // Validate address form
                    function validateAddressForm() {
                        let isValid = true;
                        const requiredFields = $('#addAddressForm').find('.address-required');

                        // Clear previous errors
                        $('#addAddressForm').find('.error-message').addClass('d-none').text('');
                        $('#addAddressForm').find('.address-required').removeClass('is-invalid');
                        $('#addressFormErrors').hide().html('');

                        requiredFields.each(function() {
                            const value = $(this).val();
                            if (!value || value === '') {
                                isValid = false;
                                $(this).addClass('is-invalid');
                                $(this).closest('.form-group').find('.error-message').removeClass('d-none').text(
                                    translations.fieldRequired);
                            }
                        });

                        return isValid;
                    }

                    // Real-time validation on input change
                    $('#addAddressForm').find('.address-required').on('change keyup', function() {
                        const value = $(this).val();
                        if (value && value !== '') {
                            $(this).removeClass('is-invalid');
                            $(this).closest('.form-group').find('.error-message').addClass('d-none').text('');
                        }
                    });

                    // Save new address - Remove previous handlers to prevent multiple submissions
                    $('#saveAddressBtn').off('click').on('click', function() {
                        // Validate form first
                        if (!validateAddressForm()) {
                            showAlert('warning', translations.fillRequiredFields);
                            return;
                        }

                        const customerId = $('#selected_customer_id').val();
                        const formData = {
                            title: $('#address_title').val(),
                            country_id: $('#address_country_id').val(),
                            city_id: $('#address_city_id').val(),
                            region_id: $('#address_region_id').val(),
                            subregion_id: $('#address_subregion_id').val() || null,
                            address: $('#address_address').val(),
                            phone: $('#address_phone').val(),
                            email: $('#address_email').val() || null,
                            is_primary: $('#address_is_primary').is(':checked') ? 1 : 0
                        };

                        $.ajax({
                            url: `/api/customers/${customerId}/addresses`,
                            type: 'POST',
                            dataType: 'json',
                            contentType: 'application/json',
                            data: JSON.stringify(formData),
                            xhrFields: {
                                withCredentials: true
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Accept': 'application/json',
                                'X-Country-Code': countryCode,
                                'lang': "{{ app()->getLocale() }}",
                            },
                            success: function(response) {
                                if (response.status && response.data) {
                                    // Get the new address from response
                                    const newAddress = response.data;

                                    // Close modal - use multiple methods to ensure it closes
                                    const modalElement = document.getElementById('addAddressModal');
                                    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
                                    modal.hide();
                                    console.log('Modal should close');

                                    // Remove modal backdrop if it exists
                                    setTimeout(function() {
                                        $('.modal-backdrop').remove();
                                        $('body').removeClass('modal-open');
                                    }, 300);

                                    // Fill the address field with the created address
                                    $('#customer_address').val(newAddress.address);

                                    // Add new address to dropdown
                                    const addressSelect = $('#customer_address_select');
                                    const addressOption =
                                        `<option value="${newAddress.id}" data-address="${newAddress.address}">${newAddress.title} - ${newAddress.address}</option>`;
                                    addressSelect.append(addressOption);

                                    // Select the new address in dropdown
                                    addressSelect.val(newAddress.id);

                                    // Show address select section and hide no address message
                                    $('#customer_address_section').show();
                                    $('#no_address_section').hide();

                                    // Show success message
                                    showAlert('success',
                                        '{{ trans('order.address_created_successfully') }}');

                                    // Reset form
                                    $('#addAddressForm')[0].reset();
                                    $('#address_country_id').val('');
                                    $('#address_city_id').empty().append(
                                            '<option value="">{{ __('common.select') }}</option>')
                                        .prop('disabled', true);
                                    $('#address_region_id').empty().append(
                                            '<option value="">{{ __('common.select') }}</option>')
                                        .prop('disabled', true);
                                    $('#address_subregion_id').empty().append(
                                            '<option value="">{{ __('common.select') }}</option>')
                                        .prop('disabled', true);
                                    $('#addressFormErrors').hide().html('');
                                }
                            },
                            error: function(xhr) {
                                let errorMessage =
                                    '{{ trans('order.error_creating_address') }}';

                                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                    const errors = xhr.responseJSON.errors;
                                    let errorHtml = '<ul class="mb-0">';
                                    $.each(errors, function(key, value) {
                                        errorHtml += '<li>' + value[0] + '</li>';
                                    });
                                    errorHtml += '</ul>';
                                    $('#addressFormErrors').html(errorHtml).show();
                                } else {
                                    errorMessage = xhr.responseJSON?.message || errorMessage;
                                    showAlert('danger', errorMessage);
                                }
                            }
                        });
                    });

                    // Hide customer suggestions on blur
                    $(document).on('click', function(e) {
                        if (!$(e.target).closest('#customer_search, #customer_suggestions').length) {
                            $('#customer_suggestions').hide();
                        }
                    });

                    // Alert function
                    function showAlert(type, message) {
                        const alertHtml = `
                            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                                <i class="uil uil-info-circle me-2"></i>${message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                        $('#alertContainer').html(alertHtml);
                        $('html, body').animate({
                            scrollTop: 0
                        }, 'slow');
                    }

                    // No longer need to pre-load all products - using direct AJAX search
                    loadAllCustomers();
                    loadCitiesForCurrentCountry();

                    // Load customer addresses on page load if there's an existing customer
                    @if($order->customer_id)
                        loadCustomerAddresses({{ $order->customer_id }}, "{{ addslashes($order->customer_address ?? '') }}");
                    @endif

                    // Pre-populate shipping value
                    @if($order->shipping)
                        $('#shipping').val({{ $order->shipping }});
                    @endif

                    // Debug: Log products array
                    console.log('Pre-populated products:', products);
                    console.log('Products count:', products.length);

                    // Render pre-populated fees on page load FIRST
                    console.log('Rendering fees, count:', fees.length);
                    fees.forEach(function(fee) {
                        const feeHtml = `
                        <div class="fee-item mb-10 p-10 rounded" data-fee-id="fee_${fee.id}">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <input type="text" class="form-control fee-reason" placeholder="{{ trans('order.reason') }}" value="${fee.reason}" required style="background-color: transparent; border: 1px solid #ddd;">
                                </div>
                                <div class="col-md-4">
                                    <input type="number" class="form-control fee-amount" placeholder="0.00" step="0.01" min="0" value="${fee.amount}" required style="background-color: transparent; border: 1px solid #ddd;">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-sm btn-danger remove-fee w-100">
                                        <i class="uil uil-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>`;
                        $('#feesContainer').append(feeHtml);
                    });

                    // Render pre-populated discounts on page load FIRST
                    console.log('Rendering discounts, count:', discounts.length);
                    discounts.forEach(function(discount) {
                        const discountHtml = `
                        <div class="discount-item mb-10 p-10 rounded" data-discount-id="discount_${discount.id}">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <input type="text" class="form-control discount-reason" placeholder="{{ trans('order.reason') }}" value="${discount.reason}" required style="background-color: transparent; border: 1px solid #ddd;">
                                </div>
                                <div class="col-md-4">
                                    <input type="number" class="form-control discount-amount" placeholder="0.00" step="0.01" min="0" value="${discount.amount}" required style="background-color: transparent; border: 1px solid #ddd;">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-sm btn-danger remove-discount w-100">
                                        <i class="uil uil-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>`;
                        $('#discountsContainer').append(discountHtml);
                    });

                    // Render pre-populated products on page load AFTER fees/discounts
                    if (products.length > 0) {
                        console.log('Rendering products table...');
                        renderProductsTable();
                    }

                    // Update totals after rendering ALL pre-populated data
                    updateSummary();

                    // Add Fee
                    $('#addFeeBtn').on('click', function() {
                        const feeId = `fee_${feeCounter++}`;
                        const feeHtml = `
                    <div class="fee-item mb-10 p-10 rounded" data-fee-id="${feeId}">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="text" class="form-control fee-reason" placeholder="{{ trans('order.reason') }}" required style="background-color: transparent; border: 1px solid #ddd;">
                            </div>
                            <div class="col-md-4">
                                <input type="number" class="form-control fee-amount" placeholder="0.00" step="0.01" min="0" required style="background-color: transparent; border: 1px solid #ddd;">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-sm btn-danger remove-fee w-100">
                                    <i class="uil uil-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                        $('#feesContainer').append(feeHtml);
                        updateSummary();
                    });

                    // Add Discount
                    $('#addDiscountBtn').on('click', function() {
                        const discountId = `discount_${discountCounter++}`;
                        const discountHtml = `
                    <div class="discount-item mb-10 p-10 rounded" data-discount-id="${discountId}">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="text" class="form-control discount-reason" placeholder="{{ trans('order.reason') }}" required style="background-color: transparent; border: 1px solid #ddd;">
                            </div>
                            <div class="col-md-4">
                                <input type="number" class="form-control discount-amount" placeholder="0.00" step="0.01" min="0" required style="background-color: transparent; border: 1px solid #ddd;">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-sm btn-danger remove-discount w-100">
                                    <i class="uil uil-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                        $('#discountsContainer').append(discountHtml);
                        updateSummary();
                    });

                    // Remove Fee
                    $(document).on('click', '.remove-fee', function() {
                        $(this).closest('.fee-item').remove();
                        updateSummary();
                    });

                    // Remove Discount
                    $(document).on('click', '.remove-discount', function() {
                        $(this).closest('.discount-item').remove();
                        updateSummary();
                    });

                    // Add Product to Order
                    $('#addProductBtn').click(function() {
                        const productId = $('#selected_product_id').val();
                        const variantId = $('#selected_product_variant_id').val() || null;
                        const productName = $('#selected_product_name').val();
                        const productPrice = parseFloat($('#selected_product_price').val()) || 0;
                        const unitPriceBeforeTax = parseFloat($('#selected_product_unit_price_before_tax').val()) || productPrice;
                        const quantity = parseInt($('#product_quantity').val()) || 1;
                        const limitation = parseInt($('#selected_product_limitation').val()) || 0;
                        const taxRate = parseFloat($('#selected_product_tax_rate').val()) || 0;
                        let taxesInfo = [];
                        try {
                            taxesInfo = JSON.parse($('#selected_product_taxes_info').val() || '[]');
                        } catch(e) {
                            taxesInfo = [];
                        }
                        const categoryId = parseInt($('#selected_product_category_id').val()) || null;
                        const categoryName = $('#selected_product_category_name').val();
                        const sku = $('#selected_product_sku').val() || 'N/A';
                        const variantName = $('#selected_product_variant_name').val() || '';
                        const vendorName = $('#selected_product_vendor_name').val() || 'N/A';
                        const image = $('#selected_product_image').val() || '';

                        console.log('Adding product:', {
                            productId,
                            variantId,
                            productName,
                            productPrice,
                            unitPriceBeforeTax,
                            quantity,
                            limitation,
                            taxRate,
                            taxesInfo,
                            categoryId,
                            categoryName,
                            sku,
                            variantName,
                            vendorName,
                            image
                        });

                        if (!productId) {
                            showAlert('warning', '{{ trans('order.please_select_product') }}');
                            return;
                        }

                        // Validate quantity against limitation
                        if (limitation > 0 && quantity > limitation) {
                            showAlert('warning', `Maximum quantity for this product is ${limitation}`);
                            return;
                        }

                        const productTotal = productPrice * quantity;

                        // Create unique ID for this product (same logic as when adding)
                        const uniqueProductId = productId + (variantId ? '_' + variantId : '');

                        // Check if product already exists
                        const existingProduct = products.find(p => p.id == uniqueProductId);
                        if (existingProduct) {
                            const newQuantity = existingProduct.quantity + quantity;

                            // Validate total quantity against limitation
                            if (limitation > 0 && newQuantity > limitation) {
                                showAlert('warning',
                                    `Total quantity for this product cannot exceed ${limitation}. Current: ${existingProduct.quantity}, Adding: ${quantity}`
                                );
                                return;
                            }

                            existingProduct.quantity = newQuantity;
                            existingProduct.total = existingProduct.price * existingProduct.quantity;
                        } else {
                            products.push({
                                // Minimal data for server (form submission)
                                vendor_product_id: productId,
                                vendor_product_variant_id: variantId,
                                quantity: quantity,
                                // Category data for shipping calculation
                                category_id: categoryId,
                                category_name: categoryName,
                                // Display data for UI (not sent to server)
                                id: productId + (variantId ? '_' + variantId : ''), // Unique ID for UI
                                name: productName,
                                price: productPrice,
                                unitPriceBeforeTax: unitPriceBeforeTax,
                                total: productTotal,
                                taxRate: taxRate,
                                taxesInfo: taxesInfo,
                                sku: sku,
                                variantName: variantName,
                                vendorName: vendorName,
                                image: image
                            });
                        }

                        renderProductsTable();
                        // Reset form fields
                        $('#product_search').val('');
                        $('#selected_product_id').val('');
                        $('#selected_product_variant_id').val('');
                        $('#selected_product_name').val('');
                        $('#selected_product_price').val('');
                        $('#selected_product_unit_price_before_tax').val('');
                        $('#selected_product_limitation').val('');
                        $('#selected_product_tax_rate').val('');
                        $('#selected_product_taxes_info').val('');
                        $('#selected_product_sku').val('');
                        $('#selected_product_variant_name').val('');
                        $('#selected_product_vendor_name').val('');
                        $('#selected_product_image').val('');
                        $('#selected_product_stock').val('');
                        $('#product_quantity').val(1);
                        $('#limitationText').text('');
                        $('#product_quantity').removeAttr('max');
                        $('#addProductBtn').prop('disabled', true);
                        updateSummary();
                        // Trigger shipping calculation when product is added
                        $(document).trigger('productAdded');
                    });

                    // Remove Product
                    $(document).on('click', '.remove-product', function() {
                        const productId = $(this).data('product-id');
                        console.log('Removing product with ID:', productId);
                        console.log('Products before removal:', JSON.stringify(products));
                        
                        // Use == for loose comparison to handle both string and number IDs
                        products = products.filter(p => p.id != productId);
                        
                        console.log('Products after removal:', JSON.stringify(products));
                        
                        renderProductsTable();
                        updateSummary();
                        // Recalculate shipping when product is removed
                        calculateShipping();
                    });

                    // Render Products Table
                    function renderProductsTable() {
                        const tbody = $('#productsTableBody');
                        tbody.empty();

                        products.forEach(product => {
                            const taxRate = product.taxRate || 0;
                            const taxesInfo = product.taxesInfo || [];
                            // Unit price before tax for display in Price column
                            const unitPriceBeforeTax = product.unitPriceBeforeTax || product.price;
                            // Total is price (with tax) × quantity
                            const lineTotal = product.price * product.quantity;
                            
                            // Build tax badges HTML
                            let taxBadgesHtml = '';
                            if (taxesInfo && taxesInfo.length > 0) {
                                taxesInfo.forEach(tax => {
                                    taxBadgesHtml += `<span class="badge badge-lg badge-round bg-info text-white me-1 mb-1">${tax.name} (${tax.percentage}%)</span>`;
                                });
                                // Add total tax rate badge
                                if (taxesInfo.length > 1) {
                                    taxBadgesHtml += `<br><span class="badge badge-lg badge-round bg-primary text-white mt-1">{{ __('common.total') }}: ${taxRate.toFixed(2)}%</span>`;
                                }
                            } else if (taxRate > 0) {
                                taxBadgesHtml = `<span class="badge badge-lg badge-round bg-info text-white">${taxRate.toFixed(2)}%</span>`;
                            } else {
                                taxBadgesHtml = '<span class="text-muted">-</span>';
                            }
                            
                            const row = `
                        <tr>
                            <td>
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    ${product.image ? 
                                        `<img src="${product.image}" alt="${product.name}" style="width: 45px; height: 45px;  border-radius: 4px; border: 1px solid #dee2e6;">` : 
                                        `<div class="rounded d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; border: 1px solid #dee2e6;"><i class="uil uil-image text-muted"></i></div>`
                                    }
                                    <div>
                                        <div class="fw-500">${product.name}</div>
                                        <small class="text-muted d-block">SKU: ${product.sku || 'N/A'} ${product.variantName ? '| ' + product.variantName : ''}</small>
                                        <small class="text-primary d-block"><i class="uil uil-store me-1"></i>${product.vendorName || 'N/A'}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center align-middle">${parseFloat(unitPriceBeforeTax).toFixed(2)} {{ currency() }}</td>
                            <td class="text-center align-middle">${product.quantity}</td>
                            <td class="text-center align-middle">${taxBadgesHtml}</td>
                            <td class="text-center align-middle">${lineTotal.toFixed(2)} {{ currency() }}</td>
                            <td class="text-center align-middle">
                                <button type="button" class="btn btn-sm btn-danger remove-product" data-product-id="${product.id}">
                                    <i class="uil uil-trash m-0"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                            tbody.append(row);
                        });

                        // Update hidden input
                        $('#productsData').val(JSON.stringify(products));
                    }

                    // Update Summary
                    function updateSummary() {
                        let totalFees = 0;
                        let totalDiscounts = 0;
                        let subtotal = 0;
                        let totalTax = 0;

                        // Calculate subtotal (before tax) and tax from products
                        products.forEach(product => {
                            const taxRate = product.taxRate || 0;
                            // Use unit price before tax for subtotal
                            const unitPriceBeforeTax = parseFloat(product.unitPriceBeforeTax) || product.price;
                            const lineSubtotal = unitPriceBeforeTax * product.quantity;
                            const lineTax = lineSubtotal * (taxRate / 100);

                            console.log('Product tax calculation:', {
                                name: product.name,
                                taxRate: taxRate,
                                unitPriceBeforeTax: unitPriceBeforeTax,
                                lineSubtotal: lineSubtotal,
                                lineTax: lineTax
                            });

                            subtotal += lineSubtotal;
                            totalTax += lineTax;
                        });

                        console.log('Summary totals:', {
                            subtotal: subtotal,
                            totalTax: totalTax,
                            productsCount: products.length
                        });

                        // Sync fees array from DOM inputs
                        let tempFees = [];
                        $('.fee-item').each(function() {
                            const reason = $(this).find('.fee-reason').val();
                            const amount = parseFloat($(this).find('.fee-amount').val()) || 0;
                            tempFees.push({ reason: reason, amount: amount });
                            totalFees += amount;
                        });
                        fees = tempFees;

                        // Sync discounts array from DOM inputs
                        let tempDiscounts = [];
                        $('.discount-item').each(function() {
                            const reason = $(this).find('.discount-reason').val();
                            const amount = parseFloat($(this).find('.discount-amount').val()) || 0;
                            tempDiscounts.push({ reason: reason, amount: amount });
                            totalDiscounts += amount;
                        });
                        discounts = tempDiscounts;

                        const shipping = parseFloat($('#shipping').val()) || 0;
                        const grandTotal = subtotal + shipping + totalFees + totalTax - totalDiscounts;

                        console.log('Updating UI with:', {
                            subtotal: subtotal,
                            shipping: shipping,
                            totalTax: totalTax,
                            totalFees: totalFees,
                            totalDiscounts: totalDiscounts,
                            grandTotal: grandTotal
                        });

                        $('#subtotal').text(subtotal.toFixed(2) + ' {{ __('common.currency') }}');
                        $('#shippingDisplay').text(shipping.toFixed(2) + ' {{ __('common.currency') }}');

                        // Show tax only if > 0
                        if (totalTax > 0) {
                            $('#totalTax').text(totalTax.toFixed(2) + ' {{ __('common.currency') }}');
                            $('#taxSection').removeClass('d-none').addClass('d-flex');
                            console.log('Showing tax section with value:', totalTax.toFixed(2));
                        } else {
                            $('#totalTax').text('0.00 {{ __('common.currency') }}');
                            $('#taxSection').removeClass('d-flex').addClass('d-none');
                            console.log('Hiding tax section, totalTax is:', totalTax);
                        }

                        $('#totalFeesDisplay').text(totalFees.toFixed(2) + ' {{ __('common.currency') }}');
                        $('#totalDiscountsDisplay').text(totalDiscounts.toFixed(2) + ' {{ __('common.currency') }}');
                        $('#grandTotal').text(grandTotal.toFixed(2) + ' {{ __('common.currency') }}');

                        // Update hidden inputs
                        $('#feesData').val(JSON.stringify(fees));
                        $('#discountsData').val(JSON.stringify(discounts));
                    }

                    // Update summary on input change
                    $(document).on('change keyup',
                        '.fee-reason, .fee-amount, .discount-reason, .discount-amount',
                        function() {
                            updateSummary();
                        });

                    // Calculate shipping when product is added
                    $(document).on('productAdded', function() {
                        calculateShipping();
                    });

                    // Calculate shipping when address is changed
                    $('#customer_address_select').on('change', function() {
                        calculateShipping();
                    });

                    // Calculate shipping when external city is changed
                    $('#external_city_id').on('change', function() {
                        calculateShipping();
                    });

                    // Calculate shipping cost via API
                    function calculateShipping() {
                        const customerType = $('input[name="customer_type"]:checked').val();
                        
                        // Check if we have products
                        if (products.length === 0) {
                            $('#shipping').val(0);
                            updateSummary();
                            return;
                        }

                        // Format cart items for shipping calculation
                        const cartItems = products.map(p => ({
                            category_id: p.category_id,
                            category_name: p.category_name,
                            product_id: p.vendor_product_id,
                            quantity: p.quantity
                        }));

                        let requestData = {
                            cart_items: cartItems
                        };

                        if (customerType === 'existing') {
                            const customerId = $('#selected_customer_id').val();
                            const addressId = $('#customer_address_select').val();

                            if (!customerId || !addressId) {
                                $('#shipping').val(0);
                                updateSummary();
                                return;
                            }

                            requestData.customer_id = customerId;
                            requestData.customer_address_id = addressId;
                        } else {
                            // External customer - use city_id directly
                            const cityId = $('#external_city_id').val();

                            if (!cityId) {
                                $('#shipping').val(0);
                                updateSummary();
                                return;
                            }

                            requestData.city_id = cityId;
                        }

                        // Call shipping calculation endpoint
                        $.ajax({
                            url: '{{ route('admin.shipping.calculate') }}',
                            type: 'POST',
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Accept': 'application/json',
                                'X-Country-Code': countryCode,
                                'lang': "{{ app()->getLocale() }}",
                            },
                            data: JSON.stringify(requestData),
                            success: function(response) {
                                console.log('Shipping response:', response);
                                if (response.success && response.data) {
                                    const shippingCost = parseFloat(response.data.shipping_cost) || 0;
                                    $('#shipping').val(shippingCost);
                                    updateSummary();
                                } else {
                                    $('#shipping').val(0);
                                    updateSummary();
                                }
                            },
                            error: function(xhr) {
                                console.error('Shipping calculation error:', xhr);
                                console.log('Response:', xhr.responseJSON);
                                $('#shipping').val(0);
                                updateSummary();
                            }
                        });
                    }

                    // Form validation function
                    function validateForm() {
                        let errors = [];

                        // Check customer selection
                        const customerType = $('input[name="customer_type"]:checked').val();

                        if (customerType === 'existing') {
                            const customerId = $('#selected_customer_id').val();
                            const addressId = $('#customer_address_select').val();

                            if (!customerId || customerId.trim() === '') {
                                errors.push('{{ trans('order.select_customer') }} is required');
                            }
                            if (!addressId || addressId.trim() === '') {
                                errors.push('{{ trans('order.customer_address') }} is required');
                            }
                        } else if (customerType === 'external') {
                            const name = $('#external_customer_name').val();
                            const email = $('#external_customer_email').val();
                            const phone = $('#external_customer_phone').val();
                            const address = $('#external_customer_address').val();

                            if (!name || name.trim() === '') {
                                errors.push('{{ trans('order.customer_name') }} is required');
                            }
                            if (!email || email.trim() === '') {
                                errors.push('{{ trans('order.customer_email') }} is required');
                            } else if (!isValidEmail(email)) {
                                errors.push('{{ trans('order.customer_email') }} must be valid');
                            }
                            if (!phone || phone.trim() === '') {
                                errors.push('{{ trans('order.customer_phone') }} is required');
                            }
                            if (!address || address.trim() === '') {
                                errors.push('{{ trans('order.customer_address') }} is required');
                            }
                        }

                        // Check products
                        if (!products || products.length === 0) {
                            errors.push('{{ trans('order.add_product') }} - At least one product is required');
                        }

                        return errors;
                    }

                    // Email validation helper
                    function isValidEmail(email) {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        return emailRegex.test(email);
                    }

                    // Form submission
                    $('#editOrderForm').on('submit', function(e) {
                        e.preventDefault();

                        // Validate form first
                        const validationErrors = validateForm();
                        if (validationErrors.length > 0) {
                            let errorHtml =
                                '<strong>{{ trans('order.validation_error') }}:</strong><ul class="mb-0 mt-2">';
                            validationErrors.forEach(function(error) {
                                errorHtml += '<li>' + error + '</li>';
                            });
                            errorHtml += '</ul>';
                            showAlert('danger', errorHtml);
                            return;
                        }

                        // Collect fees and discounts BEFORE creating FormData
                        let fees = [];
                        let discounts = [];

                        $('.fee-item').each(function() {
                            fees.push({
                                reason: $(this).find('.fee-reason').val(),
                                amount: parseFloat($(this).find('.fee-amount').val()) || 0
                            });
                        });

                        $('.discount-item').each(function() {
                            discounts.push({
                                reason: $(this).find('.discount-reason').val(),
                                amount: parseFloat($(this).find('.discount-amount').val()) || 0
                            });
                        });

                        // Prepare products with required fields including category data
                        const productsForServer = products.map(p => ({
                            vendor_product_id: p.vendor_product_id,
                            vendor_product_variant_id: p.vendor_product_variant_id,
                            quantity: p.quantity,
                            category_id: p.category_id,
                            category_name: p.category_name
                        }));

                        // Update hidden inputs with collected data
                        $('#feesData').val(JSON.stringify(fees));
                        $('#discountsData').val(JSON.stringify(discounts));
                        $('#productsData').val(JSON.stringify(productsForServer));

                        // Debug logging
                        console.log('Products for server:', productsForServer);
                        console.log('Fees collected:', fees);
                        console.log('Discounts collected:', discounts);
                        console.log('Shipping value:', $('#shipping').val());

                        if (typeof LoadingOverlay !== 'undefined') {
                            LoadingOverlay.show({
                                text: '{{ trans('order.edit_order') }}',
                                subtext: '{{ trans('main.please wait') }}'
                            });
                        }

                        const formData = new FormData(this);
                        const url = $(this).attr('action');

                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Accept': 'application/json',
                                'X-Country-Code': countryCode,
                                'lang': "{{ app()->getLocale() }}",
                            },
                            success: function(response) {
                                if (response.status) {
                                    if (typeof LoadingOverlay !== 'undefined') {
                                        LoadingOverlay.showSuccess(
                                            response.message,
                                            '{{ trans('main.redirecting') }}'
                                        );
                                    }

                                    setTimeout(function() {
                                        window.location.href =
                                            '{{ route('admin.orders.index') }}';
                                    }, 1500);
                                } else {
                                    if (typeof LoadingOverlay !== 'undefined') {
                                        LoadingOverlay.hide();
                                    }
                                    showAlert('danger', response.message);
                                }
                            },
                            error: function(xhr) {
                                if (typeof LoadingOverlay !== 'undefined') {
                                    LoadingOverlay.hide();
                                }

                                let errorMessage = '{{ trans('order.error_creating_order') }}';
                                let errorDetails = [];

                                // Check if we have a response
                                if (!xhr.responseJSON) {
                                    // Network error or server didn't respond
                                    if (xhr.status === 0) {
                                        errorMessage =
                                            'Network error. Please check your connection and try again.';
                                    } else if (xhr.status === 500) {
                                        errorMessage = 'Server error. Please try again later.';
                                    } else if (xhr.status === 403) {
                                        errorMessage = 'You do not have permission to create orders.';
                                    } else if (xhr.status === 404) {
                                        errorMessage = 'The requested resource was not found.';
                                    }
                                    showAlert('danger', errorMessage);
                                    return;
                                }

                                // Handle validation errors (422)
                                if (xhr.status === 422) {
                                    if (xhr.responseJSON?.errors) {
                                        const errors = xhr.responseJSON.errors;
                                        $.each(errors, function(key, value) {
                                            if (Array.isArray(value)) {
                                                errorDetails.push(value[0]);
                                            } else {
                                                errorDetails.push(value);
                                            }
                                        });
                                    }
                                }
                                // Handle other errors
                                else if (xhr.responseJSON?.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                // Handle errors array (from backend exceptions)
                                else if (xhr.responseJSON?.errors && Array.isArray(xhr.responseJSON
                                        .errors)) {
                                    errorDetails = xhr.responseJSON.errors;
                                }

                                // Build error HTML
                                let errorHtml = errorMessage;
                                if (errorDetails.length > 0) {
                                    errorHtml += '<ul class="mb-0 mt-2">';
                                    errorDetails.forEach(function(error) {
                                        errorHtml += '<li>' + error + '</li>';
                                    });
                                    errorHtml += '</ul>';
                                }

                                console.error('Order creation error:', {
                                    status: xhr.status,
                                    message: errorMessage,
                                    details: errorDetails,
                                    response: xhr.responseJSON
                                });

                                showAlert('danger', errorHtml);
                            }
                        });
                    });

                    // Alert function
                    function showAlert(type, message) {
                        let icon = 'uil-info-circle';
                        if (type === 'danger') {
                            icon = 'uil-exclamation-circle';
                        } else if (type === 'success') {
                            icon = 'uil-check-circle';
                        } else if (type === 'warning') {
                            icon = 'uil-alert-triangle';
                        }

                        const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        <i class="uil ${icon} me-2"></i>
                        <span>${message}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                        $('#alertContainer').html(alertHtml);
                        $('html, body').animate({
                            scrollTop: 0
                        }, 'slow');
                    }
                });
            </script>
        @endpush
    @endsection

    {{-- Responsive Grid CSS --}}
    @push('styles')
        <style>
            .responsive-grid {
                display: grid !important;
                grid-template-columns: 1fr !important;
                gap: 1.5rem !important;
                margin-top: 20px !important;
            }

            /* Tablet screens (768px and below) */
            @media (max-width: 991px) {
                .responsive-grid {
                    grid-template-columns: 1fr !important;
                }
            }

            /* Mobile screens (576px and below) */
            @media (max-width: 575px) {
                .responsive-grid {
                    grid-template-columns: 1fr !important;
                    gap: 1rem !important;
                }
            }
        </style>
    @endpush


    {{-- Include Loading Overlay Component --}}
    @push('after-body')
        <x-loading-overlay :loadingText="trans('order.edit_order')" :loadingSubtext="trans('main.please wait')" />
    @endpush
