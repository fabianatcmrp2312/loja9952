<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$baseUrl = $baseUrl ?? rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
$veiculos = $veiculos ?? [];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'Veículos') ?></title>
    <style>
        body { margin:0; font-family:Arial, Helvetica, sans-serif; background:#f4f6fb; color:#182033; }
        main { max-width:1200px; margin:0 auto; padding:32px 20px 48px; }
        .topbar { display:flex; justify-content:space-between; gap:16px; align-items:center; flex-wrap:wrap; margin-bottom:18px; }
        .topbar h1 { margin:0; }
        .btn, .btn-ghost, .btn-danger { display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:10px 14px; border-radius:10px; text-decoration:none; border:1px solid transparent; font-weight:700; }
        .btn { background:#182033; color:#fff; }
        .btn-ghost { background:#fff; color:#182033; border-color:#cbd5e1; }
        .btn-danger { background:#c0392b; color:#fff; }
        .panel { background:#fff; border:1px solid #dbe3f0; border-radius:18px; overflow:hidden; box-shadow:0 10px 28px rgba(24,32,51,.08); }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:14px 12px; text-align:left; vertical-align:top; border-bottom:1px solid #eef2f7; }
        th { background:#f7f9fc; font-size:.92rem; color:#52607a; }
        tr:hover td { background:#fcfdff; }
        .actions { display:flex; gap:8px; flex-wrap:wrap; }
        .thumb { width:72px; height:48px; object-fit:cover; border-radius:8px; background:#edf2f7; border:1px solid #dbe3f0; }
        .empty { padding:28px; color:#52607a; }
        .muted { color:#52607a; font-size:.95rem; }
        @media (max-width: 860px) {
            table, thead, tbody, th, td, tr { display:block; }
            thead { display:none; }
            tr { border-bottom:1px solid #eef2f7; }
            td { border:none; padding:10px 12px; }
            td::before { content:attr(data-label); display:block; font-size:.78rem; font-weight:700; color:#52607a; margin-bottom:4px; text-transform:uppercase; letter-spacing:.04em; }
        }
    </style>
</head>
<body>
<main>
    <div class="topbar">
        <div>
            <h1>Veículos</h1>
            <p class="muted">Lista completa do inventário com ações rápidas de gestão.</p>
        </div>
        <a class="btn" href="<?= htmlspecialchars($baseUrl . '/admin/veiculos/novo') ?>">+ Adicionar novo</a>
    </div>

    <section class="panel">
        <?php if (empty($veiculos)): ?>
            <div class="empty">Ainda não existem veículos registados.</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Imagem</th>
                        <th>Veículo</th>
                        <th>Ano</th>
                        <th>Preço</th>
                        <th>Estado</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($veiculos as $veiculo): ?>
                        <?php
                        $imagem = !empty($veiculo['imagem']) ? $baseUrl . '/uploads/' . $veiculo['imagem'] : '';
                        $tituloVeiculo = trim(($veiculo['marca'] ?? '') . ' ' . ($veiculo['modelo'] ?? ''));
                        $disponivel = (int) ($veiculo['disponivel'] ?? 0) === 1;
                        ?>
                        <tr>
                            <td data-label="Imagem">
                                <?php if ($imagem): ?>
                                    <img class="thumb" src="<?= htmlspecialchars($imagem) ?>" alt="<?= htmlspecialchars($tituloVeiculo) ?>">
                                <?php else: ?>
                                    <div class="thumb" aria-hidden="true"></div>
                                <?php endif; ?>
                            </td>
                            <td data-label="Veículo">
                                <strong><?= htmlspecialchars($tituloVeiculo) ?></strong><br>
                                <span class="muted">ID <?= htmlspecialchars((string) ($veiculo['id'] ?? '')) ?></span>
                            </td>
                            <td data-label="Ano"><?= htmlspecialchars((string) ($veiculo['ano'] ?? '')) ?></td>
                            <td data-label="Preço"><?= number_format((float) ($veiculo['preco'] ?? 0), 2, ',', '.') ?> EUR</td>
                            <td data-label="Estado"><?= $disponivel ? 'Disponível' : 'Reservado' ?></td>
                            <td data-label="Ações">
                                <div class="actions">
                                    <a class="btn-ghost" href="<?= htmlspecialchars($baseUrl . '/admin/veiculos/editar/' . (int) ($veiculo['id'] ?? 0)) ?>">Editar</a>
                                    <a class="btn-danger" href="<?= htmlspecialchars($baseUrl . '/admin/veiculos/apagar/' . (int) ($veiculo['id'] ?? 0)) ?>">Apagar</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
