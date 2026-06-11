<?php

$paginaAtiva = 'Dashboard';
$pageTitle = 'Dashboard';

define('ROOT', dirname(__DIR__)); 

require_once ROOT . '/app/helpers.php';
require_once ROOT . '/app/config/conexao.php';

// Inicializando a variável $stats para a Dashboard não quebrar
$stats = [
    'totalClientes' => 15, 
    'totalProdutos' => 42,
    'totalServicos' => 8,
    'orcMes'        => 5,
    'recMes'        => 3500.50,
    'ultOrc'        => [] // Array vazio, fará aparecer a mensagem "Nenhum orçamento"
];
 require_once ROOT . '/app/views/layouts/header.php';
?>
<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Infinity Tech' ?></title>
    
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/app.css">
</head>
<body>

<div class="layout">
    <div class="main-wrapper">
        <main class="content">

            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-label">Clientes Ativos</div>
                    <div class="kpi-valor"><?= $stats['totalClientes'] ?></div>
                </div>
                <div class="kpi-card" style="border-left-color:#43a047">
                    <div class="kpi-label">Produtos Cadastrados</div>
                    <div class="kpi-valor"><?= $stats['totalProdutos'] ?></div>
                </div>
                <div class="kpi-card" style="border-left-color:#fb8c00">
                    <div class="kpi-label">Serviços Disponíveis</div>
                    <div class="kpi-valor"><?= $stats['totalServicos'] ?></div>
                </div>
                <div class="kpi-card" style="border-left-color:#8e24aa">
                    <div class="kpi-label">Orçamentos no Mês</div>
                    <div class="kpi-valor"><?= $stats['orcMes'] ?></div>
                </div>
                <div class="kpi-card" style="border-left-color:#e53935">
                    <div class="kpi-label">Receita no Mês</div>
                    <div class="kpi-valor" style="font-size:1.3rem"><?= moeda($stats['recMes']) ?></div>
                </div>
            </div>

            <div class="card">
                <div class="card-title">
                    <svg viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Últimos Orçamentos
                </div>
                <?php if (empty($stats['ultOrc'])): ?>
                    <p style="color:var(--muted);text-align:center;padding:24px 0">Nenhum orçamento registado ainda.</p>
                <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Número</th>
                                <th>Cliente</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Data</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($stats['ultOrc'] as $o): ?>
                            <tr>
                                <td><strong style="color:var(--blue-lt)"><?= h($o['numero']) ?></strong></td>
                                <td><?= h($o['cliente']) ?></td>
                                <td><?= badgeStatus($o['status']) ?></td>
                                <td><?= moeda($o['total']) ?></td>
                                <td style="color:var(--muted)"><?= dataFormatada($o['criado_em']) ?></td>
                                <td>
                                    <a href="<?= APP_URL ?>/?c=orcamento&a=ver&id=<?= (int)$o['id'] ?? '' ?>"
                                       class="btn btn-ghost btn-sm">Ver</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>

            <div style="display:flex;gap:12px;margin-top:20px;flex-wrap:wrap">
                <a href="<?= APP_URL ?>/?c=orcamento&a=novo" class="btn btn-primary">
                    <svg viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
                    Novo Orçamento
                </a>
                <a href="<?= APP_URL ?>/?c=dimensionamento" class="btn btn-ghost">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/></svg>
                    Calcular BTUs
                </a>
                <a href="<?= APP_URL ?>/?c=cliente&a=novo" class="btn btn-ghost">
                    <svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                    Novo Cliente
                </a>
            </div>
            </main>
    </div>
</div>

</body>
</html>