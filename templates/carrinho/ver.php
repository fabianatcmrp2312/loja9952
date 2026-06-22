<?php
$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
$baseUrl = preg_replace('#/public$#', '', $baseUrl) ?: '';
$projectUrl = preg_replace('#/public$#', '', $baseUrl);
$totalVeiculos = count($veiculos);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titulo ?? 'Carrinho') ?></title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px; color: #f5f5f5; background:#111; }
        a { color: #ffd21f; }
        h1 { color:#ffd21f; }
        .topo { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 24px; }
        .mensagem { padding: 12px 14px; border-radius: 8px; margin-bottom: 16px; background: #243b20; color: #b7f397; }
        .mensagem-info { background: #3b300f; color: #ffd21f; }
        .vazio { padding: 24px; border: 1px solid #333; border-radius: 12px; background: #1a1a1a; }
        .lista { display: grid; gap: 12px; }
        .item { display: grid; grid-template-columns: 96px 1fr auto; gap: 16px; align-items: center; border: 1px solid #333; border-radius: 12px; padding: 12px; background:#1a1a1a; }
        .item img { width: 96px; height: 64px; object-fit: cover; border-radius: 14px; background: #222; }
        .item h2 { margin: 0 0 6px; font-size: 1rem; color: #ffd21f; }
        .preco { margin: 0; font-weight: bold; color: #ffd21f; }
        .remover { background: #2a2a2a; color: #ffd21f; border: 1px solid #ffd21f; border-radius: 8px; padding: 8px 14px; cursor: pointer; }
        .resumo { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-top: 24px; padding-top: 16px; border-top: 1px solid #333; }
        .checkout { display: inline-block; background: #ffd21f; color: #111; text-decoration: none; border-radius: 8px; padding: 10px 16px; font-weight:bold; }

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
                <?php
                $imagem = $v['imagem'] ?? '';
                $imagemSrc = $imagem !== ''
                    ? (filter_var($imagem, FILTER_VALIDATE_URL) ? $imagem : $projectUrl . '/public/uploads/' . $imagem)
                    : $baseUrl . '/img/placeholder.png';
                ?>
                <article class="item">
                    <img src="<?= htmlspecialchars($imagemSrc) ?>"
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
