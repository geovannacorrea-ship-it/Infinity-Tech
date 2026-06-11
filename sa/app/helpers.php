<?php
// --- CONFIGURAÇÕES GERAIS ---
// Defina o URL base do seu sistema (ajuste se a pasta for diferente)
// Verifique se já não está definida antes de definir
// No seu ficheiro app/helpers.php
if (!defined('APP_URL')) {
    define('APP_URL', '/sa/public'); 
}
// Nome do seu sistema
define('APP_NAME', 'Infinity Tech'); 


// --- FUNÇÕES AUXILIARES (HELPERS) ---

// Função h(): Protege o sistema contra XSS escapando o HTML
function h($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// Função moeda(): Formata valores numéricos para Reais (R$)
function moeda($valor) {
    return 'R$ ' . number_format((float)$valor, 2, ',', '.');
}

// Função dataFormatada(): Converte o formato do banco (Y-m-d) para o formato brasileiro
function dataFormatada($data) {
    if (!$data) return '-';
    return date('d/m/Y', strtotime($data));
}

// Função badgeStatus(): Cria as etiquetas coloridas de status do orçamento
function badgeStatus($status) {
    $status = strtolower($status);
    $cores = [
        'aprovado' => 'background:#43a047;color:#fff;padding:2px 8px;border-radius:12px;font-size:0.8rem;',
        'pendente' => 'background:#fb8c00;color:#fff;padding:2px 8px;border-radius:12px;font-size:0.8rem;',
        'cancelado' => 'background:#e53935;color:#fff;padding:2px 8px;border-radius:12px;font-size:0.8rem;'
    ];
    
    $estilo = $cores[$status] ?? 'background:#757575;color:#fff;padding:2px 8px;border-radius:12px;font-size:0.8rem;';
    
    return "<span style=\"{$estilo}\">" . ucfirst($status) . "</span>";
}