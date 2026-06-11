<?php
/**
 * app/views/orcamentos/form.php
 * Formulário de criação/edição de orçamento.
 * Variáveis: $orc (array|null), $produtos, $servicos
 */
$edicao    = isset($orc) && $orc;
$paginaAtiva = 'Orçamentos';
$pageTitle   = $edicao ? 'Editar Orçamento ' . h($orc['numero']) : 'Novo Orçamento';
require ROOT . '/app/views/partials/header.php';

$action = $edicao
    ? APP_URL . '/?c=orcamento&a=atualizar'
    : APP_URL . '/?c=orcamento&a=salvar';
?>

<form id="frmOrcamento" method="POST" action="<?= $action ?>">
<?php if ($edicao): ?>
    <input type="hidden" name="orcamento_id" value="<?= (int)$orc['id'] ?>">
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start">

    <!-- Coluna esquerda: cliente + itens -->
    <div style="display:flex;flex-direction:column;gap:20px">

        <!-- ── Cliente ────────────────────────────────────── -->
        <div class="card">
            <div class="card-title">
                <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                Cliente
            </div>

            <div class="form-group" style="position:relative">
                <label class="form-label">Buscar Cliente <span style="color:var(--error)">*</span></label>
                <input type="text" id="clienteBusca" class="form-control"
                       placeholder="Digite o nome do cliente..."
                       autocomplete="off"
                       value="<?= $edicao ? h($orc['cliente_nome']) : '' ?>">
                <input type="hidden" name="cliente_id" id="clienteId"
                       value="<?= $edicao ? (int)$orc['cliente_id'] : '' ?>" required>
                <div id="clienteSugestoes" style="display:none;position:absolute;top:100%;left:0;right:0;z-index:50;
                     background:var(--surface2);border:1px solid var(--border2);border-radius:var(--radius-sm);
                     box-shadow:var(--shadow);max-height:220px;overflow-y:auto"></div>
            </div>

            <div id="clienteInfo" style="<?= $edicao ? '' : 'display:none' ?>;margin-top:12px;
                 padding:12px;background:rgba(30,136,229,.08);border:1px solid rgba(30,136,229,.2);
                 border-radius:var(--radius-sm);font-size:.875rem;color:var(--muted)">
                <?php if ($edicao): ?>
                    📞 <?= h($orc['telefone']) ?> &nbsp;|&nbsp;
                    📧 <?= h($orc['cliente_email']) ?> &nbsp;|&nbsp;
                    📍 <?= h($orc['cidade']) ?>/<?= h($orc['estado']) ?>
                <?php endif; ?>
            </div>

            <div style="margin-top:12px;text-align:right">
                <a href="<?= APP_URL ?>/?c=cliente&a=novo" target="_blank" class="btn btn-ghost btn-sm">
                    + Cadastrar novo cliente
                </a>
            </div>
        </div>

        <!-- ── Itens ──────────────────────────────────────── -->
        <div class="card">
            <div class="card-title" style="justify-content:space-between">
                <span style="display:flex;align-items:center;gap:8px">
                    <svg viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>
                    Itens do Orçamento
                </span>
                <div style="display:flex;gap:8px">
                    <button type="button" class="btn btn-ghost btn-sm" onclick="adicionarItem('produto')">
                        + Produto
                    </button>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="adicionarItem('servico')">
                        + Serviço
                    </button>
                </div>
            </div>

            <div class="table-wrap">
                <table class="orc-items-table" id="tblItens">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th style="min-width:200px">Descrição</th>
                            <th style="width:80px">Qtd</th>
                            <th style="width:120px">Preço Unit.</th>
                            <th style="width:80px">Desc.%</th>
                            <th style="width:110px">Subtotal</th>
                            <th style="width:40px"></th>
                        </tr>
                    </thead>
                    <tbody id="itensBody">
                    <?php if ($edicao && !empty($orc['itens'])): ?>
                        <?php foreach ($orc['itens'] as $idx => $item): ?>
                        <tr id="row-<?= $idx ?>">
                            <td>
                                <input type="hidden" name="item_tipo[]" value="<?= h($item['tipo']) ?>">
                                <input type="hidden" name="item_ref_id[]" value="<?= (int)$item[$item['tipo'].'_id'] ?>">
                                <span class="badge-tipo-<?= h($item['tipo']) ?>"><?= $item['tipo'] === 'produto' ? '📦 Prod.' : '🔧 Serv.' ?></span>
                            </td>
                            <td><input type="text" name="item_descricao[]" class="form-control" style="min-width:180px" value="<?= h($item['descricao']) ?>"></td>
                            <td><input type="number" name="item_qtd[]" class="form-control item-qty" min="0.5" step="0.5" value="<?= h($item['quantidade']) ?>" oninput="recalcularLinha(this)"></td>
                            <td><input type="number" name="item_preco[]" class="form-control item-price" min="0" step="0.01" value="<?= h($item['preco_unitario']) ?>" oninput="recalcularLinha(this)"></td>
                            <td><input type="number" name="item_desconto[]" class="form-control item-desc" min="0" max="100" step="0.5" value="<?= h($item['desconto_item']) ?>" oninput="recalcularLinha(this)"></td>
                            <td class="item-sub" style="font-weight:600;color:var(--blue-lt)"><?= moeda($item['subtotal_item']) ?></td>
                            <td><button type="button" class="btn btn-danger btn-icon btn-sm" onclick="removerItem(this)">✕</button></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div id="semItens" style="<?= ($edicao && !empty($orc['itens'])) ? 'display:none' : '' ?>text-align:center;padding:32px;color:var(--muted)">
                Nenhum item adicionado. Use os botões acima para adicionar produtos ou serviços.
            </div>
        </div>

        <!-- ── Observações ────────────────────────────────── -->
        <div class="card">
            <div class="form-group">
                <label class="form-label">Observações / Condições de pagamento</label>
                <textarea name="observacoes" class="form-control" rows="3"
                          placeholder="Forma de pagamento, prazo de entrega, garantia..."><?= $edicao ? h($orc['observacoes']) : '' ?></textarea>
            </div>
        </div>
    </div>

    <!-- Coluna direita: totais + controles -->
    <div style="position:sticky;top:calc(var(--topbar-h) + 16px);display:flex;flex-direction:column;gap:16px">

        <!-- Totais -->
        <div class="totais-box">
            <div style="font-size:.85rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px">Resumo</div>

            <div class="totais-row">
                <span style="color:var(--muted)">Subtotal:</span>
                <strong id="dispSubtotal">R$ 0,00</strong>
            </div>

            <div class="totais-row" style="align-items:flex-end;gap:8px">
                <span style="color:var(--muted)">Desconto (%):</span>
                <input type="number" name="desconto_pct" id="descontoPct"
                       class="form-control" style="width:80px;text-align:right"
                       min="0" max="100" step="0.5"
                       value="<?= $edicao ? h($orc['desconto_pct']) : '0' ?>"
                       oninput="recalcularTotais()">
            </div>

            <div class="totais-row">
                <span style="color:var(--muted)">Desconto valor:</span>
                <span id="dispDesconto" style="color:var(--error)">- R$ 0,00</span>
            </div>

            <div class="totais-row" style="align-items:flex-end;gap:8px">
                <span style="color:var(--muted)">Imposto (%):</span>
                <input type="number" name="imposto_pct" id="impostoPct"
                       class="form-control" style="width:80px;text-align:right"
                       min="0" max="100" step="0.5"
                       value="<?= $edicao ? h($orc['imposto_pct']) : '0' ?>"
                       oninput="recalcularTotais()">
            </div>

            <div class="totais-row">
                <span style="color:var(--muted)">Imposto valor:</span>
                <span id="dispImposto" style="color:var(--warn)">+ R$ 0,00</span>
            </div>

            <div class="totais-row total-final">
                <strong style="font-size:1rem">TOTAL:</strong>
                <strong id="dispTotal" style="font-size:1.2rem">R$ 0,00</strong>
            </div>

            <!-- Hidden fields para POST -->
            <input type="hidden" name="subtotal" id="hdSubtotal" value="0">
            <input type="hidden" name="desconto_valor" id="hdDescontoVal" value="0">
            <input type="hidden" name="imposto_valor"  id="hdImpostoVal"  value="0">
            <input type="hidden" name="total"           id="hdTotal"       value="0">
        </div>

        <!-- Configurações -->
        <div class="card" style="padding:18px">
            <div class="form-group" style="margin-bottom:12px">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <?php foreach(['rascunho'=>'Rascunho','enviado'=>'Enviado','aprovado'=>'Aprovado','reprovado'=>'Reprovado','cancelado'=>'Cancelado'] as $v => $l): ?>
                        <option value="<?= $v ?>" <?= ($edicao && $orc['status'] === $v) ? 'selected' : ($v === 'rascunho' && !$edicao ? 'selected' : '') ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Validade (dias)</label>
                <input type="number" name="validade_dias" class="form-control"
                       min="1" max="365"
                       value="<?= $edicao ? (int)$orc['validade_dias'] : ORC_VALIDADE_DIAS ?>">
            </div>
        </div>

        <!-- Ações -->
        <div style="display:flex;flex-direction:column;gap:8px">
            <button type="submit" class="btn btn-primary" style="justify-content:center">
                <svg viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2zM17 21v-8H7v8M7 3v5h8"/></svg>
                <?= $edicao ? 'Salvar Alterações' : 'Salvar Orçamento' ?>
            </button>
            <a href="<?= APP_URL ?>/?c=orcamento" class="btn btn-ghost" style="justify-content:center">
                Cancelar
            </a>
        </div>
    </div>
</div>

</form>

<!-- Modal: seleção de produto/serviço -->
<div id="modalItem" style="display:none;position:fixed;inset:0;z-index:300;
     background:rgba(0,0,0,.7);display:flex;align-items:center;justify-content:center;
     flex-direction:column" hidden>
    <div style="background:var(--surface2);border:1px solid var(--border2);border-radius:var(--radius);
                width:100%;max-width:560px;padding:24px;max-height:80vh;overflow-y:auto;position:relative">
        <button onclick="fecharModal()" style="position:absolute;top:12px;right:12px;background:none;border:none;color:var(--muted);font-size:1.5rem;cursor:pointer;line-height:1">✕</button>
        <h3 id="modalTitulo" style="margin-bottom:16px;color:var(--blue-lt)">Selecionar</h3>
        <input type="text" id="modalBusca" class="form-control" placeholder="Filtrar..." style="margin-bottom:12px" oninput="filtrarModal(this.value)">
        <div id="modalLista"></div>
    </div>
</div>

<script>
// ─────────────────────────────────────────────────────────
//  DADOS dos produtos e serviços (injetados pelo PHP)
// ─────────────────────────────────────────────────────────
const PRODUTOS = <?= json_encode($produtos, JSON_UNESCAPED_UNICODE) ?>;
const SERVICOS = <?= json_encode($servicos, JSON_UNESCAPED_UNICODE) ?>;
let tipoModalAtual = 'produto';
let rowCounter = <?= ($edicao && !empty($orc['itens'])) ? count($orc['itens']) : 0 ?>;

// ─────────────────────────────────────────────────────────
//  AUTOCOMPLETE de Cliente via AJAX
// ─────────────────────────────────────────────────────────
let ajaxTimer;
document.getElementById('clienteBusca').addEventListener('input', function () {
    clearTimeout(ajaxTimer);
    const q = this.value.trim();
    if (q.length < 2) { fecharSugestoes(); return; }
    ajaxTimer = setTimeout(() => buscarClientes(q), 300);
});

async function buscarClientes(q) {
    const res  = await fetch(`<?= APP_URL ?>/?c=orcamento&a=buscarClientes&q=${encodeURIComponent(q)}`);
    const data = await res.json();
    const box  = document.getElementById('clienteSugestoes');
    box.innerHTML = '';
    if (!data.length) { box.style.display = 'none'; return; }
    data.forEach(c => {
        const el = document.createElement('div');
        el.style.cssText = 'padding:10px 14px;cursor:pointer;border-bottom:1px solid var(--border);font-size:.9rem';
        el.innerHTML = `<strong>${escHtml(c.nome)}</strong><br><small style="color:var(--muted)">${c.telefone||''} ${c.email||''}</small>`;
        el.onmousedown = () => selecionarCliente(c);
        box.appendChild(el);
    });
    box.style.display = 'block';
}

function selecionarCliente(c) {
    document.getElementById('clienteBusca').value = c.nome;
    document.getElementById('clienteId').value    = c.id;
    document.getElementById('clienteInfo').style.display = 'block';
    document.getElementById('clienteInfo').innerHTML =
        `📞 ${c.telefone||'—'} &nbsp;|&nbsp; 📧 ${c.email||'—'}`;
    fecharSugestoes();
}

function fecharSugestoes() {
    document.getElementById('clienteSugestoes').style.display = 'none';
}
document.addEventListener('click', e => {
    if (!e.target.closest('#clienteBusca')) fecharSugestoes();
});

// ─────────────────────────────────────────────────────────
//  MODAL de seleção de item
// ─────────────────────────────────────────────────────────
function adicionarItem(tipo) {
    tipoModalAtual = tipo;
    document.getElementById('modalTitulo').textContent =
        tipo === 'produto' ? '📦 Selecionar Produto' : '🔧 Selecionar Serviço';
    document.getElementById('modalBusca').value = '';
    renderizarLista(tipo === 'produto' ? PRODUTOS : SERVICOS);
    const modal = document.getElementById('modalItem');
    modal.removeAttribute('hidden');
    modal.style.display = 'flex';
    document.getElementById('modalBusca').focus();
}

function filtrarModal(q) {
    const lista = tipoModalAtual === 'produto' ? PRODUTOS : SERVICOS;
    const filtrada = lista.filter(i =>
        i.nome.toLowerCase().includes(q.toLowerCase()) ||
        (i.modelo||'').toLowerCase().includes(q.toLowerCase())
    );
    renderizarLista(filtrada);
}

function renderizarLista(itens) {
    const el = document.getElementById('modalLista');
    el.innerHTML = '';
    if (!itens.length) { el.innerHTML = '<p style="color:var(--muted);text-align:center;padding:20px">Nenhum resultado</p>'; return; }
    itens.forEach(item => {
        const div = document.createElement('div');
        div.style.cssText = 'padding:12px 14px;cursor:pointer;border-radius:8px;margin-bottom:4px;display:flex;justify-content:space-between;align-items:center;transition:background .15s';
        div.onmouseenter = () => div.style.background = 'rgba(255,255,255,.06)';
        div.onmouseleave = () => div.style.background = '';

        const preco = tipoModalAtual === 'produto' ? item.preco_venda : item.preco;
        const btus  = item.btus ? ` · ${item.btus.toLocaleString('pt-BR')} BTU` : '';
        const marca = item.marca_nome ? ` · ${item.marca_nome}` : '';

        div.innerHTML = `
            <div>
                <strong style="color:var(--text)">${escHtml(item.nome)}</strong>
                <div style="font-size:.78rem;color:var(--muted)">${escHtml(item.modelo||'')}${marca}${btus}</div>
            </div>
            <div style="font-weight:700;color:var(--blue-lt);white-space:nowrap">${formatMoeda(preco)}</div>
        `;
        div.onclick = () => inserirItemNaTabela(item, tipoModalAtual, preco);
        el.appendChild(div);
    });
}

function fecharModal() {
    const modal = document.getElementById('modalItem');
    modal.style.display = 'none';
    modal.setAttribute('hidden','');
}

function inserirItemNaTabela(item, tipo, preco) {
    fecharModal();
    const idx   = rowCounter++;
    const tbody = document.getElementById('itensBody');
    const row   = document.createElement('tr');
    row.id      = `row-${idx}`;

    const desc = item.nome + (item.modelo ? ` - ${item.modelo}` : '');
    row.innerHTML = `
        <td>
            <input type="hidden" name="item_tipo[]"   value="${tipo}">
            <input type="hidden" name="item_ref_id[]" value="${item.id}">
            <span>${tipo === 'produto' ? '📦 Prod.' : '🔧 Serv.'}</span>
        </td>
        <td><input type="text"   name="item_descricao[]" class="form-control" style="min-width:180px" value="${escHtml(desc)}"></td>
        <td><input type="number" name="item_qtd[]"       class="form-control item-qty"   min="0.5" step="0.5"  value="1"     oninput="recalcularLinha(this)"></td>
        <td><input type="number" name="item_preco[]"     class="form-control item-price" min="0"   step="0.01" value="${preco}" oninput="recalcularLinha(this)"></td>
        <td><input type="number" name="item_desconto[]"  class="form-control item-desc"  min="0"   max="100" step="0.5" value="0" oninput="recalcularLinha(this)"></td>
        <td class="item-sub" style="font-weight:600;color:var(--blue-lt)">${formatMoeda(preco)}</td>
        <td><button type="button" class="btn btn-danger btn-icon btn-sm" onclick="removerItem(this)">✕</button></td>
    `;
    tbody.appendChild(row);
    document.getElementById('semItens').style.display = 'none';
    recalcularTotais();
}

function removerItem(btn) {
    btn.closest('tr').remove();
    if (!document.querySelectorAll('#itensBody tr').length)
        document.getElementById('semItens').style.display = 'block';
    recalcularTotais();
}

// ─────────────────────────────────────────────────────────
//  CÁLCULO EM TEMPO REAL (JavaScript puro — sem AJAX)
// ─────────────────────────────────────────────────────────
function recalcularLinha(input) {
    const row   = input.closest('tr');
    const qty   = parseFloat(row.querySelector('.item-qty').value)   || 0;
    const pu    = parseFloat(row.querySelector('.item-price').value)  || 0;
    const dsc   = parseFloat(row.querySelector('.item-desc').value)   || 0;
    const sub   = qty * pu * (1 - dsc / 100);
    row.querySelector('.item-sub').textContent = formatMoeda(sub);
    recalcularTotais();
}

function recalcularTotais() {
    // 1. Soma todos os subtotais das linhas
    let subtotal = 0;
    document.querySelectorAll('#itensBody tr').forEach(row => {
        const qty = parseFloat(row.querySelector('.item-qty')?.value)   || 0;
        const pu  = parseFloat(row.querySelector('.item-price')?.value)  || 0;
        const dsc = parseFloat(row.querySelector('.item-desc')?.value)   || 0;
        subtotal += qty * pu * (1 - dsc / 100);
    });

    const descPct  = Math.min(100, Math.max(0, parseFloat(document.getElementById('descontoPct').value) || 0));
    const impPct   = Math.min(100, Math.max(0, parseFloat(document.getElementById('impostoPct').value)  || 0));
    const descVal  = subtotal * descPct / 100;
    const impBase  = subtotal - descVal;
    const impVal   = impBase  * impPct  / 100;
    const total    = impBase + impVal;

    // Atualiza exibição
    document.getElementById('dispSubtotal').textContent = formatMoeda(subtotal);
    document.getElementById('dispDesconto').textContent = '- ' + formatMoeda(descVal);
    document.getElementById('dispImposto').textContent  = '+ ' + formatMoeda(impVal);
    document.getElementById('dispTotal').textContent    = formatMoeda(total);

    // Atualiza hidden fields para POST
    document.getElementById('hdSubtotal').value    = subtotal.toFixed(2);
    document.getElementById('hdDescontoVal').value = descVal.toFixed(2);
    document.getElementById('hdImpostoVal').value  = impVal.toFixed(2);
    document.getElementById('hdTotal').value       = total.toFixed(2);
}

// ─────────────────────────────────────────────────────────
//  UTILITÁRIOS
// ─────────────────────────────────────────────────────────
function formatMoeda(v) {
    return 'R$ ' + parseFloat(v || 0).toLocaleString('pt-BR', {minimumFractionDigits:2, maximumFractionDigits:2});
}
function escHtml(s) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(s || ''));
    return d.innerHTML;
}

// Recalcula ao carregar (modo edição)
document.addEventListener('DOMContentLoaded', () => {
    // Inicializa subtotais das linhas existentes (edição)
    document.querySelectorAll('#itensBody tr').forEach(row => {
        const sub = row.querySelector('.item-sub');
        if (!sub) return;
        const qty = parseFloat(row.querySelector('.item-qty')?.value)  || 0;
        const pu  = parseFloat(row.querySelector('.item-price')?.value) || 0;
        const dsc = parseFloat(row.querySelector('.item-desc')?.value)  || 0;
        sub.textContent = formatMoeda(qty * pu * (1 - dsc/100));
    });
    recalcularTotais();
});

// Fecha modal com ESC
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') fecharModal();
});
</script>

<?php require ROOT . '/app/views/partials/footer.php'; ?>
