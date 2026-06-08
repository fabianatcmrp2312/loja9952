<?php
$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titulo ?? 'Iniciar sessao') ?> - AutoShop</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 0 auto; padding: 20px; color: #f5f5f5; background:#111; }
        .auth { max-width: 420px; background:#1a1a1a; border:1px solid #333; border-radius:12px; padding:22px; }
        h1 { color:#ffd21f; }
        .campo { margin-bottom: 14px; }
        label { display: block; margin-bottom: 6px; font-weight: bold; }
        input { width: 100%; box-sizing: border-box; padding: 10px; border: 1px solid #3a3a3a; border-radius: 8px; background:#0f0f0f; color:#f5f5f5; }
        button { background: #ffd21f; color: #111; border: 0; border-radius: 8px; padding: 10px 16px; cursor: pointer; font-weight:bold; }
        .erro { color: #ff8a80; margin-bottom: 16px; }
        .mensagem { color: #b7f397; margin-bottom: 16px; }
        a { color: #ffd21f; }
    </style>
</head>
<body>
    <?php require __DIR__ . '/../header.php'; ?>

    <main class="auth">
        <h1><?= htmlspecialchars($titulo ?? 'Iniciar sessao') ?></h1>

        <?php if (!empty($_SESSION['msg_ok'])): ?>
            <p class="mensagem"><?= htmlspecialchars($_SESSION['msg_ok']) ?></p>
            <?php unset($_SESSION['msg_ok']); ?>
        <?php endif ?>

        <?php if (!empty($erro)): ?>
            <p class="erro"><?= htmlspecialchars($erro) ?></p>
        <?php endif ?>

        <form method="POST" action="<?= htmlspecialchars($baseUrl . '/login') ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <div class="campo">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>

            <div class="campo">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required>
            </div>

            <button type="submit">Entrar</button>
        </form>

        <p>Ainda nao tens conta? <a href="<?= htmlspecialchars($baseUrl . '/registar') ?>">Criar conta</a></p>
    </main>
</body>
</html>
