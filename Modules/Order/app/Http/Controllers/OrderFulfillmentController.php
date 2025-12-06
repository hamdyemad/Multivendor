<?php

namespace Modules\Order\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Order\app\Services\OrderFulfillmentService;
use Modules\Order\app\Services\OrderStageTransitionService;
use Modules\Order\app\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderFulfillmentController extends Controller
{
    protected $fulfillmentService;
    protected $stageTransitionService;

    public function __construct(
        OrderFulfillmentService $fulfillmentService,
        OrderStageTransitionService $stageTransitionService
    ) {
        $this->fulfillmentService = $fulfillmentService;
        $this->stageTransitionService = $stageTransitionService;
    }

    /**
     * Show the stock allocation page for an order
     */
    public function show($orderId)
    {
        $data = $this->fulfillmentService->getStockDataForOrder($orderId);

        return view('order::fulfillments.allocate', $data);
    }

    /**
     * Save stock allocations and update order stage
     */
    public function allocate(Request $request, $orderId)
    {
        $request->validate([
            'allocations' => 'required|array',
            'allocations.*.order_product_id' => 'required|exists:order_products,id',
            'allocations.*.region_id' => 'required|exists:regions,id',
            'allocations.*.quantity' => 'required|integer|min:0',
        ]);

        try {
            $order = Order::findOrFail($orderId);

            // Save allocations
            $this->fulfillmentService->saveAllocations($orderId, $request->allocations);

            // Update stock regions
            $this->fulfillmentService->updateStockRegions($orderId);

            // Update order stage to "in-progress" (slug: in-progress)
            $inProgressStage = \Modules\Order\app\Models\OrderStage::where('slug', 'in-progress')->firstOrFail();
            $order->update(['stage_id' => $inProgressStage->id]);

            return redirect()->route('admin.orders.show', $orderId)
                ->with('success', trans('order::order.fulfillment_completed_successfully'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return back()->withInput()->with('error', trans('order::order.fulfillment_error') . ': ' . $e->getMessage());
        }
    }
}
