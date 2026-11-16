<?php
$flash = get_flash();
if ($flash):
    $alertType = $flash['type'] ?? 'info';
    $iconMap = [
        'success' => 'bi-check-circle-fill',
        'danger'  => 'bi-exclamation-triangle-fill',
        'warning' => 'bi-exclamation-circle-fill',
        'info'    => 'bi-info-circle-fill'
    ];
    $icon = $iconMap[$alertType] ?? 'bi-info-circle-fill';
?>
<div class="alert alert-<?= htmlspecialchars($alertType) ?> alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm" role="alert">
    <i class="bi <?= $icon ?> fs-5 me-2"></i>
    <div class="flex-grow-1">
        <?= $flash['text'] ?>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

