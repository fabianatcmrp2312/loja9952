<?php
$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
$baseUrl = preg_replace('#/public$#', '', $baseUrl) ?: '';
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
        .filters-toggle { position:absolute; opacity:0; pointer-events:none; }
        .filters-toggle-label { display:none; align-items:center; justify-content:space-between; gap:12px; margin:0 0 12px; padding:12px 14px; background:#1b1b1b; border:1px solid #2d2d2d; border-radius:12px; color:#ffd21f; font-weight:bold; cursor:pointer; box-shadow:0 8px 22px rgba(0,0,0,.28); }
        .filters-toggle-label::after { content:"+"; width:26px; height:26px; display:inline-grid; place-items:center; border-radius:999px; background:rgba(255,210,31,.12); color:#ffd21f; }
        .filters-toggle:checked + .filters-toggle-label::after { content:"–"; }
        .filters-panel { background:#1b1b1b; padding:16px; border:1px solid #2d2d2d; border-radius:12px; margin-bottom:24px; display:flex; gap:12px; flex-wrap:wrap; box-shadow:0 8px 22px rgba(0,0,0,.28); }
        .filtros input, .filtros select { padding:9px; border:1px solid #3a3a3a; border-radius:8px; background:#0f0f0f; color:#f5f5f5; }
        .filtros button { background:#ffd21f; color:#111; padding:9px 18px; border:none; border-radius:8px; cursor:pointer; font-weight:bold; }
        .grelha { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:20px; }
        .card { border:1px solid #2f2f2f; border-radius:14px; overflow:hidden; background:#1a1a1a; transition:transform .2s, box-shadow .2s, border-color .2s; }
        .card:hover { transform:translateY(-3px); border-color:#ffd21f; box-shadow:0 12px 30px rgba(0,0,0,.38); }
        .card img { width:calc(100% - 24px); height:180px; object-fit:cover; background:#222; border-radius:14px; margin:12px 12px 0; display:block; }
        .card-body { padding:14px; }
        .card-body h3 { margin:0 0 6px; font-size:1rem; color:#ffd21f; }
        .preco { font-size:1.3rem; font-weight:bold; color:#ffd21f; }
        .estado { display:inline-flex; margin-top:8px; padding:5px 9px; border-radius:999px; font-size:.85rem; font-weight:bold; }
        .disponivel { background:rgba(46,125,50,.12); color:#2e7d32; }
        .reservado { background:rgba(198,40,40,.12); color:#c62828; }
        .acoes { display:flex; gap:8px; align-items:center; margin-top:10px; }
        .acoes form { margin:0; }
        .detalhe, .adicionar { display:inline-block; background:#ffd21f; color:#111;
                   padding:8px 14px; border:0; border-radius:8px; text-decoration:none; font-size:.9rem; cursor:pointer; font-weight:bold; }
        .detalhe { background:#2a2a2a; color:#ffd21f; border:1px solid #ffd21f; }
        .adicionar[disabled] { background:#333; color:#aaa; cursor:not-allowed; border:1px solid #444; }
        .notice { background:#0f1d14; color:#b7f397; border:1px solid #29442f; padding:12px 14px; border-radius:12px; margin:0 0 16px; }
        .count { color:#bbb; margin:0 0 18px; }
        @media (max-width: 640px) {
            .filters-toggle-label { display:flex; }
            .filters-panel { display:grid; grid-template-columns:1fr; gap:10px; overflow:hidden; max-height:0; padding-top:0; padding-bottom:0; border-width:0; margin-bottom:0; opacity:0; transform:translateY(-6px); transition:max-height .28s ease, padding .28s ease, opacity .22s ease, transform .22s ease, border-width .28s ease, margin-bottom .28s ease; }
            .filters-toggle:checked ~ .filters-panel { max-height:560px; padding:16px; border-width:1px; margin-bottom:24px; opacity:1; transform:translateY(0); }
            .filtros input, .filtros select, .filtros button, .filtros a { width:100%; }
        }
    </style>
</head>
<body>
    <?php require __DIR__ . '/../header.php'; ?>

    <h1>AutoShop - Catalogo de Veiculos</h1>

    <?php if (!empty($_SESSION['msg_ok'])): ?>
        <p class="notice">
            <?= htmlspecialchars($_SESSION['msg_ok']) ?>
        </p>
        <?php unset($_SESSION['msg_ok']); ?>
    <?php endif; ?>

    <input class="filters-toggle" type="checkbox" id="filters-toggle">
    <label class="filters-toggle-label" for="filters-toggle">Filtros de pesquisa</label>

    <form class="filtros filters-panel" method="GET" action="">
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

    <p class="count"><?= count($veiculos) ?> veiculo(s) encontrado(s)</p>

    <?php if (empty($veiculos)): ?>
        <p style="color:#bbb;">Nenhum veiculo corresponde aos filtros selecionados.</p>
    <?php else: ?>
    <div class="grelha">
    <?php foreach ($veiculos as $v): ?>
        <?php
        $imagem = $v['imagem'] ?? '';
        $imagemSrc = $imagem !== ''
            ? (filter_var($imagem, FILTER_VALIDATE_URL) ? $imagem : $projectUrl . '/public/uploads/' . $imagem)
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
                <span class="badge <?= $disponivel ? 'success' : 'danger' ?>">
                    <?= $disponivel ? 'Disponivel' : 'Vendido' ?>
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
                        <button class="adicionar" type="button" disabled>Vendido</button>
                    <?php endif ?>
                </div>
            </div>
        </div>
    <?php endforeach ?>
    </div>
    <?php endif ?>
</body>
</html>
