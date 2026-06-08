<?php
$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
$projectUrl = preg_replace('#/public$#', '', $baseUrl);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titulo) ?></title>
    <style>
        body { font-family: Arial, sans-serif; max-width:1100px; margin:0 auto; padding:20px; background:#111; color:#f5f5f5; }
        a { color:#ffd21f; }
        h1 { color:#ffd21f; }
        .filtros { background:#1b1b1b; padding:16px; border:1px solid #2d2d2d; border-radius:12px; margin-bottom:24px; display:flex; gap:12px; flex-wrap:wrap; box-shadow:0 8px 22px rgba(0,0,0,.28); }
        .filtros input, .filtros select { padding:9px; border:1px solid #3a3a3a; border-radius:8px; background:#0f0f0f; color:#f5f5f5; }
        .filtros button { background:#ffd21f; color:#111; padding:9px 18px; border:none; border-radius:8px; cursor:pointer; font-weight:bold; }
        .grelha { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:20px; }
        .card { border:1px solid #2f2f2f; border-radius:14px; overflow:hidden; background:#1a1a1a; transition:transform .2s, box-shadow .2s, border-color .2s; }
        .card:hover { transform:translateY(-3px); border-color:#ffd21f; box-shadow:0 12px 30px rgba(0,0,0,.38); }
        .card img { width:calc(100% - 24px); height:180px; object-fit:cover; background:#222; border-radius:14px; margin:12px 12px 0; display:block; }
        .card-body { padding:14px; }
        .card-body h3 { margin:0 0 6px; font-size:1rem; color:#ffd21f; }
        .preco { font-size:1.3rem; font-weight:bold; color:#ffd21f; }
        .estado { display:inline-block; margin-top:8px; padding:5px 9px; border-radius:999px; font-size:.85rem; font-weight:bold; }
        .disponivel { background:#243b20; color:#b7f397; }
        .reservado { background:#3b300f; color:#ffd21f; }
        .acoes { display:flex; gap:8px; align-items:center; margin-top:10px; }
        .acoes form { margin:0; }
        .detalhe, .adicionar { display:inline-block; background:#ffd21f; color:#111;
                   padding:8px 14px; border:0; border-radius:8px; text-decoration:none; font-size:.9rem; cursor:pointer; font-weight:bold; }
        .detalhe { background:#2a2a2a; color:#ffd21f; border:1px solid #ffd21f; }
        .adicionar[disabled] { background:#333; color:#aaa; cursor:not-allowed; border:1px solid #444; }
    </style>
</head>
<body>
    <?php require __DIR__ . '/../header.php'; ?>

    <h1>AutoShop - Catalogo de Veiculos</h1>

    <form class="filtros" method="GET" action="">
        <select name="marca_id">
            <option value="">Todas as marcas</option>
            <?php foreach ($marcas as $m): ?>
            <option value="<?= htmlspecialchars($m['id']) ?>"
                <?= (($_GET['marca_id'] ?? '') == $m['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($m['nome']) ?>
            </option>
            <?php endforeach ?>
        </select>
        <select name="combustivel">
            <option value="">Combustivel</option>
            <?php foreach (['Gasolina', 'Diesel', 'Eletrico', 'Hibrido'] as $c): ?>
            <option <?= (($_GET['combustivel'] ?? '') === $c) ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
            <?php endforeach ?>
        </select>
        <input type="number" name="preco_max" placeholder="Preco max. (EUR)"
               value="<?= htmlspecialchars($_GET['preco_max'] ?? '') ?>">
        <input type="number" name="ano_min" placeholder="Ano minimo"
               value="<?= htmlspecialchars($_GET['ano_min'] ?? '') ?>">
        <input type="text" name="pesquisa" placeholder="Pesquisar modelo..."
               value="<?= htmlspecialchars($_GET['pesquisa'] ?? '') ?>">
        <button type="submit">Filtrar</button>
        <a href="<?= htmlspecialchars($baseUrl . '/') ?>" style="padding:8px 14px;color:#ffd21f;text-decoration:none;">Limpar</a>
    </form>

    <p><?= count($veiculos) ?> veiculo(s) encontrado(s)</p>

    <?php if (empty($veiculos)): ?>
        <p style="color:#bbb;">Nenhum veiculo corresponde aos filtros selecionados.</p>
    <?php else: ?>
    <div class="grelha">
    <?php foreach ($veiculos as $v): ?>
        <?php
        $imagem = $v['imagem'] ?? '';
        $imagemSrc = $imagem !== ''
            ? (filter_var($imagem, FILTER_VALIDATE_URL) ? $imagem : $projectUrl . '/uploads/' . $imagem)
            : $baseUrl . '/img/placeholder.png';
        $disponivel = (int) ($v['disponivel'] ?? 0) === 1;
        ?>
        <div class="card">
            <img src="<?= htmlspecialchars($imagemSrc) ?>"
                 alt="<?= htmlspecialchars($v['marca'] . ' ' . $v['modelo']) ?>">
            <div class="card-body">
                <h3><?= htmlspecialchars($v['marca'] . ' ' . $v['modelo']) ?></h3>
                <p><?= htmlspecialchars($v['ano']) ?> - <?= number_format((float) $v['quilometros'], 0, '.', '.') ?> km - <?= htmlspecialchars($v['combustivel']) ?></p>
                <div class="preco"><?= number_format((float) $v['preco'], 2, ',', '.') ?> EUR</div>
                <span class="estado <?= $disponivel ? 'disponivel' : 'reservado' ?>">
                    <?= $disponivel ? 'Disponivel' : 'Reservado' ?>
                </span>
                <div class="acoes">
                    <a class="detalhe" href="<?= htmlspecialchars($baseUrl . '/veiculo/detalhe/' . $v['id']) ?>">Ver detalhe</a>
                    <?php if ($disponivel): ?>
                        <form method="POST" action="<?= htmlspecialchars($baseUrl . '/carrinho/adicionar') ?>">
                            <input type="hidden" name="veiculo_id" value="<?= htmlspecialchars($v['id']) ?>">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <button class="adicionar" type="submit">Adicionar</button>
                        </form>
                    <?php else: ?>
                        <button class="adicionar" type="button" disabled>Reservado</button>
                    <?php endif ?>
                </div>
            </div>
        </div>
    <?php endforeach ?>
    </div>
    <?php endif ?>
</body>
</html>
