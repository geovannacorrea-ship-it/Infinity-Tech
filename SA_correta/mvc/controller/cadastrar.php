<?php
// cadastrar.php - Cadastro de usuário na tabela 'usuarios'
require('conexao.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cadastro.php');
    exit();
}

$nome    = trim($_POST['nome']    ?? '');
$usuario = trim($_POST['usuario'] ?? '');
$senha   = $_POST['senha'] ?? '';
$tipo    = 'cliente';

if (empty($nome) || empty($usuario) || empty($senha)) {
    header('Location: cadastro.php?erro=campos_obrigatorios');
    exit();
}

try {
    // Verifica se o usuário já existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE usuario = :usuario");
    $stmt->execute([':usuario' => $usuario]);

    if ($stmt->fetch()) {
        header('Location: cadastro.php?erro=usuario_existente');
        exit();
    }

    $senha_hash = password_hash($senha, PASSWORD_BCRYPT);

    $sql = "INSERT INTO usuarios (nome, usuario, senha, tipo) VALUES (:nome, :usuario, :senha, :tipo)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nome'    => $nome,
        ':usuario' => $usuario,
        ':senha'   => $senha_hash,
        ':tipo'    => $tipo,
    ]);

    header('Location: index.php?cadastro=sucesso');
    exit();

} catch (PDOException $e) {
    // Exibe erro detalhado para diagnóstico
    $codigo   = $e->getCode();
    $mensagem = $e->getMessage();
    error_log("Erro ao cadastrar: " . $mensagem);
    ?>
    <!DOCTYPE html>
    <html lang="pt-br">
    <head><meta charset="UTF-8"><title>Erro de Diagnóstico</title>
    <style>
        body { font-family: monospace; background: #0d1117; color: #e6edf3; display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; }
        .box { background:#161b22; border:1px solid #30363d; border-radius:12px; padding:32px; max-width:680px; width:100%; }
        h2 { color:#f85149; margin-bottom:16px; }
        .campo { margin:8px 0; padding:10px 14px; background:#0d1117; border-radius:8px; border-left:3px solid #388bfd; }
        .label { color:#8b949e; font-size:.8rem; text-transform:uppercase; margin-bottom:4px; }
        .valor { color:#e6edf3; word-break:break-all; }
        .btn { display:inline-block; margin-top:20px; padding:10px 20px; background:#1f6feb; color:#fff; border-radius:8px; text-decoration:none; font-size:.9rem; }
    </style>
    </head>
    <body>
    <div class="box">
        <h2>⚠ Erro ao cadastrar — Diagnóstico</h2>
        <div class="campo"><div class="label">SQLSTATE / Código</div><div class="valor"><?= htmlspecialchars($codigo) ?></div></div>
        <div class="campo"><div class="label">Mensagem do MySQL</div><div class="valor"><?= htmlspecialchars($mensagem) ?></div></div>
        <div class="campo"><div class="label">Dados enviados</div>
            <div class="valor">nome = <?= htmlspecialchars($nome) ?><br>
            usuario = <?= htmlspecialchars($usuario) ?><br>
            tipo = cliente<br>
            senha = [preenchida]</div>
        </div>
        <a class="btn" href="cadastro.php">← Voltar</a>
    </div>
    </body>
    </html>
    <?php
    exit();
}
