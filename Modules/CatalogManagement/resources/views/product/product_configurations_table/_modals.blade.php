{{-- Product Modals --}}

{{-- Delete Modal --}}
<x-delete-with-loading 
    modalId="modal-delete-product" 
    tableId="productsDataTable" 
    deleteButtonClass="delete-product"
    :title="trans('main.confirm delete')" 
    :message="trans('main.are you sure you want to delete this')" 
    itemNameId="delete-product-name" 
    confirmBtnId="confirmDeleteProductBtn"
    :cancelText="trans('main.cancel')" 
    :deleteText="trans('main.delete')" 
    :loadingDeleting="trans('main.deleting')" 
    :loadingPleaseWait="trans('main.please wait')" 
    :loadingDeletedSuccessfully="trans('main.deleted success')" 
    :loadingRefreshing="trans('main.refreshing')"
    :errorDeleting="trans('main.error on delete')" 
/>

{{-- Change Status Modal --}}
<div class="modal fade" id="modal-change-status" tabindex="-1" role="dialog" aria-labelledby="changeStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-info" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeStatusModalLabel">{{ __('catalogmanagement::product.change_product_status') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('common.close') }}"></button>
            </div>
            <div class="modal-body">
                <div class="modal-info-body d-flex mb-3">
                    <div class="modal-info-icon primary">
                        <img src="{{ asset('assets/img/svg/info.svg') }}" alt="info" class="svg">
                    </div>
                    <div class="modal-info-text">
                        <p class="fw-500" id="status-product-name"></p>
                        <p class="text-muted fs-13">{{ __('catalogmanagement::product.select_new_status_for_product') }}</p>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="product-status" class="form-label il-gray fs-14 fw-500 align-center">
                        {{ __('catalogmanagement::product.approval_status') }} <span class="text-danger">*</span>
                    </label>
                    <select class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select" id="product-status" required>
                        <option value="">{{ __('common.select_option') }}</option>
                        @foreach(\Modules\CatalogManagement\app\Models\VendorProduct::getStatuses() as $statusValue => $statusLabel)
                            <option value="{{ $statusValue }}">{{ $statusLabel }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" id="rejection-reason-group" style="display: none;">
                    <label for="rejection-reason" class="form-label il-gray fs-14 fw-500 align-center">
                        {{ __('catalogmanagement::product.rejection_reason') }}
                    </label>
                    <textarea class="form-control ih-medium ip-gray radius-xs b-light px-15" id="rejection-reason" rows="3" placeholder="{{ __('catalogmanagement::product.enter_rejection_reason') }}"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-outlined btn-sm" data-bs-dismiss="modal">
                    <i class="uil uil-times"></i> {{ __('common.cancel') }}
                </button>
                <button type="button" class="btn btn-primary btn-sm" id="confirmChangeStatusBtn">
                    <i class="uil uil-check"></i> {{ __('common.confirm') }}
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Move to Bank Modal --}}
<div class="modal fade" id="modal-move-to-bank" tabindex="-1" role="dialog" aria-labelledby="moveToBankModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-info" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="moveToBankModalLabel">{{ __('catalogmanagement::product.move_to_bank') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('common.close') }}"></button>
            </div>
            <div class="modal-body">
                <div class="modal-info-body d-flex mb-3">
                    <div class="modal-info-icon warning">
                        <img src="{{ asset('assets/img/svg/alert-circle.svg') }}" alt="alert" class="svg">
                    </div>
                    <div class="modal-info-text">
                        <p class="fw-500" id="bank-product-name"></p>
                        <p class="text-muted fs-13">{{ __('catalogmanagement::product.move_to_bank_confirmation') ?? 'Are you sure you want to move this product to the bank? This will make it available for all vendors.' }}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-outlined btn-sm" data-bs-dismiss="modal">
                    <i class="uil uil-times"></i> {{ __('common.cancel') }}
                </button>
                <button type="button" class="btn btn-secondary btn-sm" id="confirmMoveToBankBtn">
                    <i class="uil uil-database"></i> {{ __('catalogmanagement::product.move_to_bank') }}
                </button>
            </div>
        </div>
    </div>
</div>
