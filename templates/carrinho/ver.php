<?php
$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
$projectUrl = preg_replace('#/public$#', '', $baseUrl);
$totalVeiculos = count($veiculos);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titulo ?? 'Carrinho') ?></title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px; color: #222; }
        a { color: #1565C0; }
        .topo { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 24px; }
        .mensagem { padding: 12px 14px; border-radius: 6px; margin-bottom: 16px; background: #e8f5e9; color: #1b5e20; }
        .mensagem-info { background: #e3f2fd; color: #0d47a1; }
        .vazio { padding: 24px; border: 1px solid #ddd; border-radius: 8px; background: #fafafa; }
        .lista { display: grid; gap: 12px; }
        .item { display: grid; grid-template-columns: 96px 1fr auto; gap: 16px; align-items: center; border: 1px solid #ddd; border-radius: 8px; padding: 12px; }
        .item img { width: 96px; height: 64px; object-fit: cover; border-radius: 6px; background: #eee; }
        .item h2 { margin: 0 0 6px; font-size: 1rem; color: #1A237E; }
        .preco { margin: 0; font-weight: bold; color: #1565C0; }
        .remover { background: #b71c1c; color: #fff; border: 0; border-radius: 4px; padding: 8px 14px; cursor: pointer; }
        .resumo { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-top: 24px; padding-top: 16px; border-top: 1px solid #ddd; }
        .checkout { display: inline-block; background: #1565C0; color: #fff; text-decoration: none; border-radius: 4px; padding: 10px 16px; }

        @media (max-width: 640px) {
            .topo, .resumo { align-items: flex-start; flex-direction: column; }
            .item { grid-template-columns: 80px 1fr; }
            .item img { width: 80px; height: 56px; }
            .item form { grid-column: 1 / -1; }
        }
    </style>
</head>
<body>
    <?php require __DIR__ . '/../header.php'; ?>

    <div class="topo">
        <h1><?= htmlspecialchars($titulo ?? 'A minha lista de reservas') ?></h1>
        <a href="<?= htmlspecialchars($baseUrl . '/') ?>">Voltar ao catalogo</a>
    </div>

    <?php if (!empty($_SESSION['msg_ok'])): ?>
        <p class="mensagem"><?= htmlspecialchars($_SESSION['msg_ok']) ?></p>
        <?php unset($_SESSION['msg_ok']); ?>
    <?php endif ?>

    <?php if (!empty($_SESSION['msg_info'])): ?>
        <p class="mensagem mensagem-info"><?= htmlspecialchars($_SESSION['msg_info']) ?></p>
        <?php unset($_SESSION['msg_info']); ?>
    <?php endif ?>

    <?php if (empty($veiculos)): ?>
        <div class="vazio">
            <p>A sua lista de reservas esta vazia.</p>
            <a href="<?= htmlspecialchars($baseUrl . '/') ?>">Ver veiculos disponiveis</a>
        </div>
    <?php else: ?>
        <div class="lista">
            <?php foreach ($veiculos as $v): ?>
                <article class="item">
                    <img src="<?= !empty($v['imagem']) ? htmlspecialchars($projectUrl . '/uploads/' . $v['imagem']) : htmlspecialchars($baseUrl . '/img/placeholder.png') ?>"
                         alt="<?= htmlspecialchars($v['marca'] . ' ' . $v['modelo']) ?>">

                    <div>
                        <h2><?= htmlspecialchars($v['marca'] . ' ' . $v['modelo']) ?></h2>
                        <p class="preco"><?= number_format((float) $v['preco'], 2, ',', '.') ?> EUR</p>
                    </div>

                    <form method="POST" action="<?= htmlspecialchars($baseUrl . '/carrinho/remover') ?>">
                        <input type="hidden" name="veiculo_id" value="<?= htmlspecialchars($v['id']) ?>">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <button class="remover" type="submit">Remover</button>
                    </form>
                </article>
            <?php endforeach ?>
        </div>

        <div class="resumo">
            <strong>Total de veiculos na lista: <?= $totalVeiculos ?></strong>
            <a class="checkout" href="<?= htmlspecialchars($baseUrl . '/checkout') ?>">Prosseguir para reserva</a>
        </div>
    <?php endif ?>
</body>
</html>
