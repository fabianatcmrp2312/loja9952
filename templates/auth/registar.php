<?php
$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titulo ?? 'Criar conta') ?> - AutoShop</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 0 auto; padding: 20px; color: #222; }
        .auth { max-width: 420px; }
        .campo { margin-bottom: 14px; }
        label { display: block; margin-bottom: 6px; font-weight: bold; }
        input { width: 100%; box-sizing: border-box; padding: 9px; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #1565C0; color: #fff; border: 0; border-radius: 4px; padding: 10px 16px; cursor: pointer; }
        .erros { color: #b71c1c; margin-bottom: 16px; }
        .erros ul { margin: 6px 0 0; padding-left: 20px; }
        a { color: #1565C0; }
    </style>
</head>
<body>
    <?php require __DIR__ . '/../header.php'; ?>

    <main class="auth">
        <h1><?= htmlspecialchars($titulo ?? 'Criar conta') ?></h1>

        <?php if (!empty($erros)): ?>
            <div class="erros">
                <strong>Corrige os seguintes erros:</strong>
                <ul>
                    <?php foreach ($erros as $erro): ?>
                        <li><?= htmlspecialchars($erro) ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif ?>

        <form method="POST" action="<?= htmlspecialchars($baseUrl . '/registar') ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <div class="campo">
                <label for="nome">Nome</label>
                <input id="nome" type="text" name="nome" value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" required>
            </div>

            <div class="campo">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>

            <div class="campo">
                <label for="telefone">Telefone</label>
                <input id="telefone" type="text" name="telefone" value="<?= htmlspecialchars($_POST['telefone'] ?? '') ?>">
            </div>

            <div class="campo">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required>
            </div>

            <div class="campo">
                <label for="password2">Confirmar password</label>
                <input id="password2" type="password" name="password2" required>
            </div>

            <button type="submit">Registar</button>
        </form>

        <p>Ja tens conta? <a href="<?= htmlspecialchars($baseUrl . '/login') ?>">Entrar</a></p>
    </main>
</body>
</html>
