<?php if ($total_pages > 1): ?>
    <?php
    $pagination_query = $_GET;
    unset($pagination_query['page']);
    $previous_query = $pagination_query;
    $previous_query['page'] = $page - 1;
    $next_query = $pagination_query;
    $next_query['page'] = $page + 1;
    $previous_url = $pagination_base_url . '?' . http_build_query($previous_query);
    $next_url = $pagination_base_url . '?' . http_build_query($next_query);
    ?>
    <nav class="pagination" aria-label="Product pages">
        <?php if ($page > 1): ?>
            <a href="<?php echo htmlspecialchars($previous_url, ENT_QUOTES, 'UTF-8'); ?>">Previous</a>
        <?php else: ?>
            <span class="pagination-disabled">Previous</span>
        <?php endif; ?>
        <span aria-current="page">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
        <?php if ($page < $total_pages): ?>
            <a href="<?php echo htmlspecialchars($next_url, ENT_QUOTES, 'UTF-8'); ?>">Next</a>
        <?php else: ?>
            <span class="pagination-disabled">Next</span>
        <?php endif; ?>
    </nav>
<?php endif; ?>
