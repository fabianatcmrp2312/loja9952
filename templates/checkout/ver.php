<?php
$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
$baseUrl = preg_replace('#/public$#', '', $baseUrl) ?: '';
$totalVeiculos = count($veiculos ?? []);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titulo ?? 'Confirmar reserva') ?></title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px; color: #f5f5f5; background:#111; }
        a { color: #ffd21f; }
        h1 { color:#ffd21f; }
        .topo { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 24px; }
        .lista { display: grid; gap: 12px; margin-bottom: 20px; }
        .item { display: grid; grid-template-columns: 1fr auto; gap: 16px; align-items: center; border: 1px solid #333; border-radius: 12px; padding: 14px; background:#1a1a1a; }
        .item h2 { margin: 0 0 6px; font-size: 1rem; color: #ffd21f; }
        .item p { margin: 0; }
        .preco { font-weight: bold; color: #ffd21f; white-space: nowrap; }
        .resumo { margin: 18px 0; padding: 14px; border: 1px solid #333; border-radius: 12px; background: #1a1a1a; }
        .aviso { margin: 0 0 18px; padding: 12px 14px; border-radius: 8px; background: #3b300f; color: #ffd21f; }
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        textarea { width: 100%; min-height: 110px; box-sizing: border-box; padding: 10px; border: 1px solid #3a3a3a; border-radius: 8px; font: inherit; resize: vertical; background:#0f0f0f; color:#f5f5f5; }
        .acoes { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-top: 20px; }
        .confirmar { background: #ffd21f; color: #111; border: 0; border-radius: 8px; padding: 10px 16px; font: inherit; cursor: pointer; font-weight:bold; }

        @media (max-width: 640px) {
            .topo, .acoes { align-items: flex-start; flex-direction: column; }
            .item { grid-template-columns: 1fr; }
            .preco { white-space: normal; }
        }
    </style>
</head>
<body>
    <?php require __DIR__ . '/../header.php'; ?>

    <div class="topo">
        <h1><?= htmlspecialchars($titulo ?? 'Confirmar reserva') ?></h1>
        <a href="<?= htmlspecialchars($baseUrl . '/carrinho') ?>">Voltar ao carrinho</a>
    </div>

    <form method="POST" action="<?= htmlspecialchars($baseUrl . '/checkout/confirmar') ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">

        <div class="lista">
            <?php foreach (($veiculos ?? []) as $v): ?>
                <article class="item">
                    <div>
                        <h2><?= htmlspecialchars(($v['marca'] ?? '') . ' ' . ($v['modelo'] ?? '')) ?></h2>
                        <p>Marca: <?= htmlspecialchars($v['marca'] ?? '') ?></p>
                        <p>Modelo: <?= htmlspecialchars($v['modelo'] ?? '') ?></p>
                    </div>
                    <p class="preco"><?= number_format((float) ($v['preco'] ?? 0), 2, ',', '.') ?> EUR</p>
                </article>
            <?php endforeach ?>
        </div>

        <div class="resumo">
            <strong>Total de ve&iacute;culos a reservar: <?= $totalVeiculos ?></strong>
        </div>

        <p class="aviso">Esta &eacute; uma reserva simulada &mdash; sem pagamento online.</p>

        <label for="mensagem">Informa&ccedil;&otilde;es adicionais para o vendedor</label>
        <textarea id="mensagem" name="mensagem" placeholder="Opcional"></textarea>

        <div class="acoes">
            <a href="<?= htmlspecialchars($baseUrl . '/carrinho') ?>">Voltar ao carrinho</a>
            <button class="confirmar" type="submit">Confirmar reserva</button>
        </div>
    </form>
</body>
</html>
