<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e(__('order.stage_updated_title')); ?></title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 30px; border-radius: 10px;">
        <h1 style="color: #007bff; margin-bottom: 20px;"><?php echo e(__('order.stage_updated_title')); ?></h1>

        <p><?php echo e(__('order.dear_customer', ['name' => $order->customer_name])); ?></p>

        <p><?php echo e(__('order.stage_updated_message', ['order_number' => $order->order_number, 'stage' => $newStage->name])); ?></p>

        <div style="background-color: #fff; padding: 20px; border-radius: 5px; margin: 20px 0;">
            <h3 style="color: #007bff; margin-bottom: 15px;"><?php echo e(__('order.order_details')); ?>:</h3>
            <ul style="list-style: none; padding: 0;">
                <li style="margin-bottom: 10px;"><strong><?php echo e(__('order.order_number')); ?>:</strong> <?php echo e($order->order_number); ?></li>
                <li style="margin-bottom: 10px;"><strong><?php echo e(__('order.current_stage')); ?>:</strong> <?php echo e($newStage->name); ?></li>
                <li style="margin-bottom: 10px;"><strong><?php echo e(__('order.total_amount')); ?>:</strong> <?php echo e(number_format($order->total_price, 2)); ?> <?php echo e(currency()); ?></li>
            </ul>
        </div>

        <p style="margin-top: 30px;"><?php echo e(__('order.thank_you')); ?></p>

        <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">

        <p style="color: #666; font-size: 14px; text-align: center;"><?php echo e(config('app.name')); ?></p>
    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/Order\resources/views/emails/stage-updated.blade.php ENDPATH**/ ?>