<?php
$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titulo ?? 'Iniciar sessao') ?> - AutoShop</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 0 auto; padding: 20px; color: #222; }
        .auth { max-width: 420px; }
        .campo { margin-bottom: 14px; }
        label { display: block; margin-bottom: 6px; font-weight: bold; }
        input { width: 100%; box-sizing: border-box; padding: 9px; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #1565C0; color: #fff; border: 0; border-radius: 4px; padding: 10px 16px; cursor: pointer; }
        .erro { color: #b71c1c; margin-bottom: 16px; }
        .mensagem { color: #1b5e20; margin-bottom: 16px; }
        a { color: #1565C0; }
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
