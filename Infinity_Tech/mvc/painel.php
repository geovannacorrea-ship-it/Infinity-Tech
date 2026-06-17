<?php
// painel.php - Gerenciador de Estoque (CRUD de Produtos)

if (!isset($_SESSION)) session_start();

if (empty($_SESSION['id'])) {
    header("Location: index.php");
    exit;
}

include 'conexao.php';

$nomeUsuario    = $_SESSION['nome'] ?? 'Usuário';
$mensagem       = "";
$tipo_msg       = "";
$produto_editar = null;

// ── Ação: EXCLUIR ────────────────────────────────────────
if (isset($_GET['acao']) && $_GET['acao'] === 'excluir' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM produtos WHERE idProdutos = :id");
        $stmt->execute([':id' => $id]);
        $mensagem = "Produto excluído com sucesso.";
        $tipo_msg = "sucesso";
    } catch (PDOException $e) {
        $mensagem = "Erro ao excluir: " . $e->getMessage();
        $tipo_msg = "erro";
    }
}

// ── Ação: CARREGAR DADOS PARA EDITAR ────────────────────
if (isset($_GET['acao']) && $_GET['acao'] === 'editar' && isset($_GET['id'])) {
    $id   = (int) $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE idProdutos = :id");
    $stmt->execute([':id' => $id]);
    $produto_editar = $stmt->fetch() ?: null;
}

// ── Ação: SALVAR (novo ou atualização) ──────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome   = trim($_POST['Nome_produto'] ?? '');
    $modelo = trim($_POST['Modelo']       ?? '');
    $marca  = trim($_POST['Marca']        ?? '');
    $preco  = number_format((float) str_replace(',', '.', $_POST['Preco'] ?? '0'), 2, '.', '');
    $id_post = (int) ($_POST['id'] ?? 0);

    if (empty($nome)) {
        $mensagem = "O nome do produto é obrigatório.";
        $tipo_msg = "erro";
    } elseif ((float)$preco < 0) {
        $mensagem = "O preço não pode ser negativo.";
        $tipo_msg = "erro";
    } else {
        try {
            if ($id_post > 0) {
                $stmt = $pdo->prepare("UPDATE produtos SET Nome_produto=:nome, Modelo=:modelo, Marca=:marca, Preco=:preco WHERE idProdutos=:id");
                $stmt->execute([':nome'=>$nome,':modelo'=>$modelo,':marca'=>$marca,':preco'=>$preco,':id'=>$id_post]);
                $mensagem = "Produto atualizado com sucesso.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO produtos (Nome_produto, Modelo, Marca, Preco) VALUES (:nome, :modelo, :marca, :preco)");
                $stmt->execute([':nome'=>$nome,':modelo'=>$modelo,':marca'=>$marca,':preco'=>$preco]);
                $mensagem = "Produto cadastrado com sucesso.";
            }
            $tipo_msg = "sucesso";
            $produto_editar = null;
        } catch (PDOException $e) {
            $mensagem = "Erro ao salvar: " . $e->getMessage();
            $tipo_msg = "erro";
        }
    }
}

// ── LISTAR todos os produtos ─────────────────────────────
try {
    $stmt  = $pdo->query("SELECT * FROM produtos ORDER BY idProdutos ASC");
    $lista = $stmt->fetchAll();
} catch (PDOException $e) {
    $lista    = [];
    $mensagem = "Erro ao listar produtos: " . $e->getMessage();
    $tipo_msg = "erro";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel · Gerenciador de Estoque</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg:      #0d1b2e;
            --surface: #112240;
            --border:  rgba(255,255,255,.08);
            --blue:    #1e88e5;
            --blue-lt: #42a5f5;
            --text:    #e2e8f0;
            --muted:   rgba(226,232,240,.5);
            --success: #81c784;
            --error:   #ef9a9a;
        }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; }

        /* Topbar */
        .topbar {
            position: sticky; top: 0; z-index: 100;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 32px; height: 60px;
            background: rgba(17,34,64,.9);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            box-shadow: 0 2px 16px rgba(0,0,0,.3);
        }
        .topbar-titulo { font-size: 1.05rem; font-weight: 700; color: var(--blue-lt); display: flex; align-items: center; gap: 10px; }
        .topbar-titulo svg { width: 22px; height: 22px; fill: var(--blue-lt); }
        .topbar-usuario { font-size: .875rem; color: var(--muted); display: flex; align-items: center; gap: 10px; }
        .topbar-usuario strong { color: var(--text); }
        .topbar-usuario a {
            padding: 6px 14px; border-radius: 8px;
            background: rgba(239,83,80,.15); border: 1px solid rgba(239,83,80,.3);
            color: var(--error); text-decoration: none; font-size: .8rem; font-weight: 600;
            transition: background .2s;
        }
        .topbar-usuario a:hover { background: rgba(239,83,80,.28); }

        /* Layout */
        .main { max-width: 1200px; margin: 0 auto; padding: 32px 24px; }

        /* Alerta */
        .alerta {
            display: flex; align-items: center; gap: 10px;
            padding: 14px 18px; border-radius: 12px;
            font-size: .9rem; margin-bottom: 24px;
            animation: fadeIn .3s ease;
        }
        .alerta.sucesso { background: rgba(129,199,132,.12); border: 1px solid rgba(129,199,132,.3); color: var(--success); }
        .alerta.erro    { background: rgba(239,154,154,.12); border: 1px solid rgba(239,154,154,.3); color: var(--error); }
        .alerta svg { width: 18px; height: 18px; fill: currentColor; flex-shrink: 0; }
        @keyframes fadeIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:none} }

        /* Resumo */
        .resumo { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); gap: 16px; margin-bottom: 28px; }
        .resumo-card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 14px; padding: 20px 24px;
        }
        .resumo-label { font-size: .72rem; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .5px; margin-bottom: 6px; }
        .resumo-valor { font-size: 1.5rem; font-weight: 800; color: var(--text); }
        .resumo-valor small { font-size: .85rem; font-weight: 400; color: var(--muted); }

        /* Card */
        .card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 28px 32px; margin-bottom: 28px; box-shadow: 0 4px 24px rgba(0,0,0,.25); }
        .card h2 { font-size: .9rem; font-weight: 700; color: var(--blue-lt); text-transform: uppercase; letter-spacing: .6px; margin-bottom: 22px; display: flex; align-items: center; gap: 8px; }
        .card h2 svg { width: 17px; height: 17px; fill: var(--blue-lt); }

        /* Form */
        .form-linha { display: flex; gap: 16px; flex-wrap: wrap; }
        .campo { display: flex; flex-direction: column; gap: 7px; flex: 1; min-width: 160px; }
        .campo-sm { max-width: 180px; }
        label { font-size: .75rem; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .4px; }
        input[type="text"], input[type="number"] {
            padding: 11px 14px;
            background: rgba(255,255,255,.06);
            border: 1px solid var(--border);
            border-radius: 9px;
            color: var(--text); font-size: .95rem; outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        input:focus { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(30,136,229,.18); }
        input::placeholder { color: rgba(226,232,240,.2); }
        .form-acoes { margin-top: 20px; display: flex; gap: 12px; align-items: center; }
        .btn-principal {
            padding: 11px 26px;
            background: linear-gradient(135deg, #1565c0, var(--blue));
            border: none; border-radius: 9px;
            color: #fff; font-size: .9rem; font-weight: 700; cursor: pointer;
            box-shadow: 0 4px 14px rgba(21,101,192,.4);
            transition: filter .15s, transform .15s;
        }
        .btn-principal:hover { filter: brightness(1.1); transform: translateY(-1px); }
        .btn-cancelar {
            padding: 11px 20px; background: rgba(255,255,255,.07);
            border: 1px solid var(--border); border-radius: 9px;
            color: var(--muted); font-size: .9rem; font-weight: 600;
            text-decoration: none; transition: background .2s;
        }
        .btn-cancelar:hover { background: rgba(255,255,255,.13); }

        /* Tabela */
        .tabela-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: .875rem; }
        thead tr { border-bottom: 1px solid var(--border); }
        th { padding: 10px 14px; text-align: left; font-size: .72rem; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .5px; white-space: nowrap; }
        tbody tr { border-bottom: 1px solid rgba(255,255,255,.04); transition: background .15s; }
        tbody tr:hover { background: rgba(255,255,255,.04); }
        td { padding: 13px 14px; }
        td.id-col { color: var(--muted); font-size: .8rem; }
        .acoes { display: flex; gap: 8px; }
        .btn-acao { padding: 6px 14px; border-radius: 7px; font-size: .78rem; font-weight: 600; text-decoration: none; transition: filter .15s; white-space: nowrap; }
        .btn-editar  { background: rgba(66,165,245,.15); border: 1px solid rgba(66,165,245,.3); color: var(--blue-lt); }
        .btn-excluir { background: rgba(239,154,154,.12); border: 1px solid rgba(239,154,154,.3); color: var(--error); }
        .btn-editar:hover, .btn-excluir:hover { filter: brightness(1.3); }
        .sem-registros { color: var(--muted); font-size: .9rem; padding: 24px 0; text-align: center; }
    </style>
</head>
<body>

<header class="topbar">
    <div class="topbar-titulo">
        <svg viewBox="0 0 24 24"><path d="M21 8.5V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2.5M21 8.5H3M21 8.5V20a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8.5m6 4h6"/></svg>
        Gerenciador de Estoque
    </div>
    <div class="topbar-usuario">
        Olá, <strong><?= htmlspecialchars($nomeUsuario) ?></strong>
        <a href="logout.php">Sair</a>
    </div>
</header>

<main class="main">

    <?php if (!empty($mensagem)): ?>
        <div class="alerta <?= $tipo_msg ?>">
            <?php if ($tipo_msg === 'sucesso'): ?>
                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14.59L6.41 12 7.83 10.58 11 13.75l5.17-5.17 1.42 1.42L11 16.59z"/></svg>
            <?php else: ?>
                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
            <?php endif; ?>
            <?= htmlspecialchars($mensagem) ?>
        </div>
    <?php endif; ?>

    <!-- Resumo -->
    <?php
        $total_produtos = count($lista);
        $valor_total    = array_sum(array_column($lista, 'Preco'));
        $marcas         = count(array_unique(array_filter(array_column($lista, 'Marca'))));
    ?>
    <div class="resumo">
        <div class="resumo-card">
            <div class="resumo-label">Total de produtos</div>
            <div class="resumo-valor"><?= $total_produtos ?> <small>itens</small></div>
        </div>
        <div class="resumo-card">
            <div class="resumo-label">Marcas cadastradas</div>
            <div class="resumo-valor"><?= $marcas ?> <small>marcas</small></div>
        </div>
        <div class="resumo-card">
            <div class="resumo-label">Valor total (catálogo)</div>
            <div class="resumo-valor"><small>R$</small> <?= number_format($valor_total, 2, ',', '.') ?></div>
        </div>
    </div>

    <!-- Formulário -->
    <div class="card">
        <h2>
            <svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
            <?= $produto_editar ? 'Editar Produto' : 'Cadastrar Produto' ?>
        </h2>
        <form action="painel.php" method="POST">
            <?php if ($produto_editar): ?>
                <input type="hidden" name="id" value="<?= (int) $produto_editar['idProdutos'] ?>">
            <?php endif; ?>
            <div class="form-linha">
                <div class="campo">
                    <label for="Nome_produto">Nome do produto</label>
                    <input type="text" id="Nome_produto" name="Nome_produto" placeholder="Ex: Notebook Inspiron" required
                           value="<?= htmlspecialchars($produto_editar['Nome_produto'] ?? '') ?>">
                </div>
                <div class="campo">
                    <label for="Modelo">Modelo</label>
                    <input type="text" id="Modelo" name="Modelo" placeholder="Ex: 15 3000"
                           value="<?= htmlspecialchars($produto_editar['Modelo'] ?? '') ?>">
                </div>
                <div class="campo">
                    <label for="Marca">Marca</label>
                    <input type="text" id="Marca" name="Marca" placeholder="Ex: Dell"
                           value="<?= htmlspecialchars($produto_editar['Marca'] ?? '') ?>">
                </div>
                <div class="campo campo-sm">
                    <label for="Preco">Preço (R$)</label>
                    <input type="number" id="Preco" name="Preco" min="0" step="0.01" required
                           value="<?= htmlspecialchars($produto_editar['Preco'] ?? '0.00') ?>">
                </div>
            </div>
            <div class="form-acoes">
                <button type="submit" class="btn-principal">
                    <?= $produto_editar ? '💾 Salvar alterações' : '+ Cadastrar produto' ?>
                </button>
                <?php if ($produto_editar): ?>
                    <a href="painel.php" class="btn-cancelar">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Tabela -->
    <div class="card">
        <h2>
            <svg viewBox="0 0 24 24"><path d="M3 3h18v2H3V3zm0 4h18v2H3V7zm0 4h18v2H3v-2zm0 4h18v2H3v-2zm0 4h18v2H3v-2z"/></svg>
            Produtos cadastrados
        </h2>
        <?php if (!empty($lista)): ?>
        <div class="tabela-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Modelo</th>
                        <th>Marca</th>
                        <th>Preço</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista as $p): ?>
                    <tr>
                        <td class="id-col"><?= (int) $p['idProdutos'] ?></td>
                        <td><?= htmlspecialchars($p['Nome_produto'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($p['Modelo'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($p['Marca']  ?? '—') ?></td>
                        <td>R$ <?= number_format((float)$p['Preco'], 2, ',', '.') ?></td>
                        <td class="acoes">
                            <a href="painel.php?acao=editar&id=<?= (int)$p['idProdutos'] ?>" class="btn-acao btn-editar">Editar</a>
                            <a href="painel.php?acao=excluir&id=<?= (int)$p['idProdutos'] ?>"
                               class="btn-acao btn-excluir"
                               onclick="return confirm('Excluir \'<?= htmlspecialchars(addslashes($p['Nome_produto'] ?? '')) ?>\'?')">Excluir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p class="sem-registros">Nenhum produto cadastrado ainda.</p>
        <?php endif; ?>
    </div>

</main>
</body>
</html>
