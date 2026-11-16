<?php
$progress_status = $order['order_status'] ?? '';
$progress_steps = ['paid' => 'Paid', 'processing' => 'Processing', 'shipped' => 'Shipped', 'delivered' => 'Delivered'];
?>
<?php if ($progress_status === 'cancelled'): ?>
    <p class="order-cancelled" role="status">This order was cancelled.</p>
<?php elseif (!isset($progress_steps[$progress_status])): ?>
    <p class="meta-line">Current order status: <?php echo htmlspecialchars($progress_status); ?></p>
<?php else: ?>
    <?php
    $step_keys = array_keys($progress_steps);
    $current_step = array_search($progress_status, $step_keys, true);
    $current_step = $current_step === false ? 0 : $current_step;
    ?>
    <ol class="order-progress" aria-label="Order progress">
        <?php foreach ($progress_steps as $key => $label): ?>
            <?php
            $step_index = array_search($key, $step_keys, true);
            $step_class = $step_index < $current_step ? 'is-complete' : ($step_index === $current_step ? 'is-current' : 'is-upcoming');
            ?>
            <li class="<?php echo $step_class; ?>" <?php echo $step_index === $current_step ? 'aria-current="step"' : ''; ?>>
                <span class="order-progress-marker" aria-hidden="true"><?php echo $step_index < $current_step ? '&#10003;' : ($step_index + 1); ?></span>
                <span><?php echo $label; ?></span>
            </li>
        <?php endforeach; ?>
    </ol>
<?php endif; ?>
