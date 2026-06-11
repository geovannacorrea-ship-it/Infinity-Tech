<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login · <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/app.css">
</head>
<body class="auth-page">

<div class="auth-card">

    <div style="display:flex;align-items:center;justify-content:center;
                width:64px;height:64px;border-radius:16px;
                background:linear-gradient(135deg,#1e88e5,#42a5f5);
                margin:0 auto 24px;box-shadow:0 8px 24px rgba(30,136,229,.4)">
        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" style="width:30px;height:30px">
            <path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
        </svg>
    </div>

    <h1 style="text-align:center;font-size:1.6rem;font-weight:700;color:#fff;letter-spacing:-.3px">Bem-vindo</h1>
    <p style="text-align:center;font-size:.82rem;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:.5px;margin:4px 0 28px"><?= APP_NAME ?></p>

    <?php if (!empty($erro)): ?>
        <div class="alert alert-erro" style="margin-bottom:20px">
            <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
            <?= h($erro) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($sucesso)): ?>
        <div class="alert alert-sucesso" style="margin-bottom:20px">
            <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14.59L6.41 12 7.83 10.58 11 13.75l5.17-5.17 1.42 1.42L11 16.59z"/></svg>
            <?= h($sucesso) ?>
        </div>
    <?php endif; ?>

    <form action="<?= APP_URL ?>/?c=auth" method="POST" autocomplete="on">

        <div class="form-group" style="margin-bottom:18px">
            <label class="form-label">Usuário</label>
            <div style="position:relative">
                <svg viewBox="0 0 24 24" style="position:absolute;left:13px;top:50%;transform:translateY(-50%);width:17px;height:17px;fill:rgba(255,255,255,.35);pointer-events:none">
                    <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                </svg>
                <input type="text" name="usuario" class="form-control" style="padding-left:38px"
                       placeholder="seu usuário" required
                       value="<?= h($_POST['usuario'] ?? '') ?>">
            </div>
        </div>

        <div class="form-group" style="margin-bottom:24px">
            <label class="form-label">Senha</label>
            <div style="position:relative">
                <svg viewBox="0 0 24 24" style="position:absolute;left:13px;top:50%;transform:translateY(-50%);width:17px;height:17px;fill:rgba(255,255,255,.35);pointer-events:none">
                    <path d="M18 8h-1V6A5 5 0 007 6v2H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V10a2 2 0 00-2-2zm-6 9a2 2 0 110-4 2 2 0 010 4zm3.1-9H8.9V6a3.1 3.1 0 116.2 0v2z"/>
                </svg>
                <input type="password" name="senha" id="senha" class="form-control" style="padding-left:38px;padding-right:40px"
                       placeholder="••••••••" required>
                <button type="button" onclick="toggleSenha()" title="Mostrar/ocultar"
                        style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer">
                    <svg viewBox="0 0 24 24" style="width:17px;height:17px;fill:rgba(255,255,255,.35)">
                        <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zm0 12.5a5 5 0 110-10 5 5 0 010 10zm0-8a3 3 0 100 6 3 3 0 000-6z"/>
                    </svg>
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:14px;font-size:1rem">
            Entrar
        </button>
    </form>

    <hr style="border:none;border-top:1px solid rgba(255,255,255,.1);margin:28px 0">
    <p style="text-align:center;font-size:.875rem;color:rgba(255,255,255,.5)">
        Não tem conta?
        <a href="<?= APP_URL ?>/?c=auth&a=cadastro" style="color:#64b5f6;text-decoration:none;font-weight:600">Criar conta</a>
    </p>
</div>

<script>
function toggleSenha() {
    const i = document.getElementById('senha');
    i.type = i.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>
