<?php
$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
$projectUrl = preg_replace('#/public$#', '', $baseUrl);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titulo) ?> - AutoShop</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 0 auto; padding: 20px; color: #222; }
        img { max-width: 100%; border-radius: 8px; background: #eee; }
        table { border-collapse: collapse; margin: 20px 0; width: 100%; }
        th, td { border-bottom: 1px solid #ddd; padding: 10px; text-align: left; }
        button { background: #1565C0; color: #fff; border: 0; border-radius: 4px; padding: 10px 16px; cursor: pointer; }
    </style>
</head>
<body>
    <?php require __DIR__ . '/../header.php'; ?>

    <a href="<?= htmlspecialchars($baseUrl . '/') ?>">Voltar ao catalogo</a>
    <h1><?= htmlspecialchars($veiculo['marca'] . ' ' . $veiculo['modelo']) ?></h1>

    <img src="<?= !empty($veiculo['imagem']) ? htmlspecialchars($projectUrl . '/uploads/' . $veiculo['imagem']) : htmlspecialchars($baseUrl . '/img/placeholder.png') ?>"
         alt="<?= htmlspecialchars($veiculo['marca'] . ' ' . $veiculo['modelo']) ?>">

    <table>
        <tr><th>Marca</th><td><?= htmlspecialchars($veiculo['marca']) ?></td></tr>
        <tr><th>Modelo</th><td><?= htmlspecialchars($veiculo['modelo']) ?></td></tr>
        <tr><th>Ano</th><td><?= htmlspecialchars($veiculo['ano']) ?></td></tr>
        <tr><th>Quilometros</th><td><?= number_format((float) $veiculo['quilometros'], 0, '.', '.') ?> km</td></tr>
        <tr><th>Combustivel</th><td><?= htmlspecialchars($veiculo['combustivel']) ?></td></tr>
        <?php if (!empty($veiculo['cilindrada'])): ?>
        <tr><th>Cilindrada</th><td><?= htmlspecialchars($veiculo['cilindrada']) ?></td></tr>
        <?php endif ?>
        <tr><th>Preco</th><td><strong><?= number_format((float) $veiculo['preco'], 2, ',', '.') ?> EUR</strong></td></tr>
    </table>

    <?php if (!empty($veiculo['descricao'])): ?>
        <h2>Descricao</h2>
        <p><?= nl2br(htmlspecialchars($veiculo['descricao'])) ?></p>
    <?php endif ?>

    <form method="POST" action="<?= htmlspecialchars($baseUrl . '/carrinho/adicionar') ?>">
        <input type="hidden" name="veiculo_id" value="<?= htmlspecialchars($veiculo['id']) ?>">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <button type="submit">Adicionar a lista de reservas</button>
    </form>
</body>
</html>
