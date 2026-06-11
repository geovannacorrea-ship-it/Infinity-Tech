<?php
/**
 * app/views/partials/sidebar.php
 * Sidebar de navegação principal do sistema.
 * Uso: require ROOT.'/app/views/partials/sidebar.php';
 * Variáveis esperadas: $paginaAtiva (string)
 */
$paginaAtiva = $paginaAtiva ?? '';
$isAdmin     = Auth::isAdmin();
$nomeUsuario = Auth::nome();
$tipoUsuario = Auth::tipo();

function sidebarItem(string $href, string $icon, string $label, string $ativo): string {
    $cls = ($ativo === $label) ? ' active' : '';
    return "<a href=\"{$href}\" class=\"nav-item{$cls}\">
        <span class=\"nav-icon\">{$icon}</span>
        <span class=\"nav-label\">{$label}</span>
    </a>";
}
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
            </svg>
        </div>
        <div class="sidebar-brand">
            <span class="brand-name">AirControl</span>
            <span class="brand-sub">Pro</span>
        </div>
        <button class="sidebar-close" id="sidebarClose" title="Fechar menu">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M18 6L6 18M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <div class="sidebar-user">
        <div class="user-avatar"><?= strtoupper(substr($nomeUsuario, 0, 1)) ?></div>
        <div class="user-info">
            <strong><?= h($nomeUsuario) ?></strong>
            <span><?= $isAdmin ? 'Administrador' : 'Vendedor' ?></span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Principal</div>
        <?= sidebarItem(APP_URL.'/?c=painel','<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>','Dashboard',$paginaAtiva) ?>
        <?= sidebarItem(APP_URL.'/?c=orcamento','<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>','Orçamentos',$paginaAtiva) ?>
        <?= sidebarItem(APP_URL.'/?c=dimensionamento','<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/></svg>','Calcular BTUs',$paginaAtiva) ?>

        <div class="nav-section-label">Cadastros</div>
        <?= sidebarItem(APP_URL.'/?c=cliente','<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>','Clientes',$paginaAtiva) ?>
        <?= sidebarItem(APP_URL.'/?c=produto','<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>','Produtos',$paginaAtiva) ?>
        <?= sidebarItem(APP_URL.'/?c=servico','<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>','Serviços',$paginaAtiva) ?>

        <?php if ($isAdmin): ?>
        <div class="nav-section-label">Administração</div>
        <?= sidebarItem(APP_URL.'/?c=usuario','<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>','Usuários',$paginaAtiva) ?>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <a href="<?= APP_URL ?>/?c=auth&a=logout" class="btn-logout">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>
            </svg>
            Sair
        </a>
    </div>
</aside>

<div class="sidebar-overlay" id="sidebarOverlay"></div>
