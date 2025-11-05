<?php
/** @var CodeIgniter\Pager\PagerRenderer $pager */
// Request a large surround so we can slice down to exactly 4 numbered buttons
$pager->setSurroundCount(50);

$allLinks = $pager->links(); // array of ['uri','title','active'] for numeric pages
$window = 4;
if (count($allLinks) > $window) {
    // Find the current page index
    $currentIndex = 0;
    foreach ($allLinks as $idx => $lnk) {
        if (!empty($lnk['active'])) { $currentIndex = $idx; break; }
    }
    // Bias a little to the left so you usually see the next pages ahead
    $start = max(0, $currentIndex - 1);
    // Ensure we always have exactly 4 items when possible
    if ($start + $window > count($allLinks)) {
        $start = max(0, count($allLinks) - $window);
    }
    $displayLinks = array_slice($allLinks, $start, $window);
} else {
    $displayLinks = $allLinks;
}
?>
<nav class="pagination-nav" aria-label="Pagination">
    <ul class="pagination modern">
        <?php if ($pager->hasPrevious()) : ?>
            <li class="page-item">
                <a class="page-link prev" href="<?= $pager->getFirst() ?>" aria-label="First">
                    <span>&laquo;</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link prev" href="<?= $pager->getPrevious() ?>" aria-label="Previous">
                    <span>&lsaquo;</span>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled"><span class="page-link prev"><span>&laquo;</span></span></li>
            <li class="page-item disabled"><span class="page-link prev"><span>&lsaquo;</span></span></li>
        <?php endif ?>

        <?php foreach ($displayLinks as $link) : ?>
            <li class="page-item <?= !empty($link['active']) ? 'active' : '' ?>">
                <a class="page-link" href="<?= $link['uri'] ?>">
                    <?= $link['title'] ?>
                </a>
            </li>
        <?php endforeach ?>

        <?php if ($pager->hasNext()) : ?>
            <li class="page-item">
                <a class="page-link next" href="<?= $pager->getNext() ?>" aria-label="Next">
                    <span>&rsaquo;</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link next" href="<?= $pager->getLast() ?>" aria-label="Last">
                    <span>&raquo;</span>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled"><span class="page-link next"><span>&rsaquo;</span></span></li>
            <li class="page-item disabled"><span class="page-link next"><span>&raquo;</span></span></li>
        <?php endif ?>
    </ul>
    
</nav>
