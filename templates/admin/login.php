<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$baseUrl = $baseUrl ?? rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'Administração') ?></title>
    <style>
        body { margin:0; font-family:Arial, Helvetica, sans-serif; background:linear-gradient(135deg, #0f172a, #111827); color:#e5e7eb; min-height:100vh; display:grid; place-items:center; }
        .card { width:min(420px, calc(100vw - 32px)); background:rgba(17,24,39,.92); border:1px solid #334155; border-radius:20px; padding:28px; box-shadow:0 24px 60px rgba(0,0,0,.35); }
        h1 { margin-top:0; }
        .muted { color:#94a3b8; }
        .actions { display:flex; gap:12px; flex-wrap:wrap; margin-top:18px; }
        .btn-link { display:inline-flex; justify-content:center; align-items:center; padding:12px 14px; border-radius:10px; border:1px solid #475569; color:#e5e7eb; text-decoration:none; font-weight:700; flex:1 1 0; min-width:140px; }
        .btn-link.primary { background:#f6c445; color:#111827; border-color:#f6c445; }
        .erro { background:#7f1d1d; color:#fecaca; padding:12px 14px; border-radius:12px; margin:14px 0; }
        label { display:block; font-weight:700; margin:14px 0 8px; }
        input { width:100%; padding:12px 14px; border-radius:10px; border:1px solid #475569; background:#0f172a; color:#fff; font:inherit; }
        button { width:100%; margin-top:18px; padding:12px 14px; border:none; border-radius:10px; background:#f6c445; color:#111827; font-weight:800; cursor:pointer; }
        a { color:#f6c445; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Login Admin</h1>
        <p class="muted">Acede ao backoffice com credenciais de administrador.</p>

        <?php if (!empty($erro)): ?>
            <div class="erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" required>

            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>

            <button type="submit">Entrar</button>
        </form>

        <div class="actions">
            <a class="btn-link primary" href="<?= htmlspecialchars($baseUrl . '/') ?>">Voltar ao site</a>
            <a class="btn-link" href="<?= htmlspecialchars($baseUrl . '/login') ?>">Login de cliente</a>
        </div>

        <p class="muted" style="margin-top:16px;">Voltar ao <a href="<?= htmlspecialchars($baseUrl . '/') ?>">site</a>.</p>
    </div>
</body>
</html>
