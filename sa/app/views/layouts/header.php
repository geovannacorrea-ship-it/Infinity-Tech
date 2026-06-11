<?php
/**
 * app/views/partials/header.php
 * Topbar reutilizável. Variável esperada: $pageTitle
 */
$pageTitle = $pageTitle ?? APP_NAME;

// Flash message (uma vez por request)
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle) ?> · <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/app.css">
</head>
<body>
<div class="layout">

<?php require __DIR__ . '/sidebar.php'; ?>

<div class="main-wrapper">
    <header class="topbar">
        <div class="topbar-left">
            <button class="btn-menu" id="menuBtn" aria-label="Abrir menu">
                <svg viewBox="0 0 24 24"><path d="M3 12h18M3 6h18M3 18h18" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <span class="topbar-title"><?= h($pageTitle) ?></span>
        </div>
        <div class="topbar-right">
            <span style="font-size:.82rem;color:var(--muted)">
                <?= date('d/m/Y') ?>
            </span>
        </div>
    </header>

    <main class="content">

    <?php if ($flash): ?>
        <div class="alert alert-<?= h($flash['tipo']) ?>">
            <?php if ($flash['tipo'] === 'sucesso'): ?>
                <svg viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <?php else: ?>
                <svg viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <?php endif; ?>
            <?= h($flash['msg']) ?>
        </div>
    <?php endif; ?>
