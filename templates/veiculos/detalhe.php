<?php
$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
$projectUrl = preg_replace('#/public$#', '', $baseUrl);
$imagem = $veiculo['imagem'] ?? '';
$imagemSrc = $imagem !== ''
    ? (filter_var($imagem, FILTER_VALIDATE_URL) ? $imagem : $projectUrl . '/uploads/' . $imagem)
    : $baseUrl . '/img/placeholder.png';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titulo) ?> - AutoShop</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 0 auto; padding: 20px; color: #f5f5f5; background:#111; }
        a { color:#ffd21f; }
        h1, h2 { color:#ffd21f; }
        img { max-width: 100%; border-radius: 18px; background: #222; box-shadow:0 12px 30px rgba(0,0,0,.4); }
        table { border-collapse: collapse; margin: 20px 0; width: 100%; }
        th, td { border-bottom: 1px solid #333; padding: 10px; text-align: left; }
        th { color:#ffd21f; }
        button { background: #ffd21f; color: #111; border: 0; border-radius: 8px; padding: 10px 16px; cursor: pointer; font-weight:bold; }
    </style>
</head>
<body>
    <?php require __DIR__ . '/../header.php'; ?>

    <a href="<?= htmlspecialchars($baseUrl . '/') ?>">Voltar ao catalogo</a>
    <h1><?= htmlspecialchars($veiculo['marca'] . ' ' . $veiculo['modelo']) ?></h1>

    <img src="<?= htmlspecialchars($imagemSrc) ?>"
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
        <tr><th>Estado</th><td><strong><?= ((int) ($veiculo['disponivel'] ?? 0) === 1) ? 'Disponivel' : 'Reservado' ?></strong></td></tr>
    </table>

    <?php if (!empty($veiculo['descricao'])): ?>
        <h2>Descricao</h2>
        <p><?= nl2br(htmlspecialchars($veiculo['descricao'])) ?></p>
    <?php endif ?>

    <?php if ((int) ($veiculo['disponivel'] ?? 0) === 1): ?>
        <form method="POST" action="<?= htmlspecialchars($baseUrl . '/carrinho/adicionar') ?>">
            <input type="hidden" name="veiculo_id" value="<?= htmlspecialchars($veiculo['id']) ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <button type="submit">Adicionar a lista de reservas</button>
        </form>
    <?php else: ?>
        <p><strong>Reservado</strong></p>
    <?php endif ?>
</body>
</html>
