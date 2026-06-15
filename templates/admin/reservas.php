<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$baseUrl = $baseUrl ?? rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
$reservas = $reservas ?? [];
$estados = [
    'pendente' => 'Pendente',
    'confirmada' => 'Confirmada',
    'cancelada' => 'Cancelada',
];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'Reservas') ?></title>
    <style>
        body { margin:0; font-family:Arial, Helvetica, sans-serif; background:#f4f6fb; color:#182033; }
        main { max-width:1280px; margin:0 auto; padding:32px 20px 48px; }
        .topbar { display:flex; justify-content:space-between; gap:16px; align-items:center; flex-wrap:wrap; margin-bottom:18px; }
        h1 { margin:0; }
        .muted { color:#52607a; }
        .panel { background:#fff; border:1px solid #dbe3f0; border-radius:18px; overflow:hidden; box-shadow:0 10px 28px rgba(24,32,51,.08); }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:14px 12px; text-align:left; vertical-align:top; border-bottom:1px solid #eef2f7; }
        th { background:#f7f9fc; font-size:.92rem; color:#52607a; }
        .estado { font-weight:700; }
        form { margin:0; }
        select, button { font:inherit; }
        select { padding:10px 12px; border-radius:10px; border:1px solid #cbd5e1; background:#fff; }
        button { margin-top:8px; padding:9px 12px; border:none; border-radius:10px; background:#182033; color:#fff; font-weight:700; cursor:pointer; }
        .empty { padding:28px; color:#52607a; }
        .cliente { font-weight:700; }
        .sub { display:block; color:#52607a; font-size:.92rem; margin-top:4px; }
        @media (max-width: 900px) {
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
            <h1>Reservas</h1>
            <p class="muted">Acompanha cada pedido e altera o estado diretamente nesta tabela.</p>
        </div>
        <a href="<?= htmlspecialchars($baseUrl . '/admin') ?>">Voltar ao dashboard</a>
    </div>

    <section class="panel">
        <?php if (empty($reservas)): ?>
            <div class="empty">Ainda não existem reservas para gerir.</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Veículo</th>
                        <th>Estado</th>
                        <th>Data</th>
                        <th>Mudar estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservas as $reserva): ?>
                        <?php $estadoAtual = (string) ($reserva['estado'] ?? 'pendente'); ?>
                        <tr>
                            <td data-label="Cliente">
                                <span class="cliente"><?= htmlspecialchars($reserva['cliente'] ?? '') ?></span>
                                <span class="sub"><?= htmlspecialchars($reserva['email'] ?? '') ?></span>
                                <?php if (!empty($reserva['telefone'])): ?>
                                    <span class="sub"><?= htmlspecialchars($reserva['telefone']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Veículo">
                                <?= htmlspecialchars(trim(($reserva['marca'] ?? '') . ' ' . ($reserva['modelo'] ?? ''))) ?><br>
                                <span class="sub"><?= htmlspecialchars((string) ($reserva['ano'] ?? '')) ?> | <?= number_format((float) ($reserva['preco'] ?? 0), 2, ',', '.') ?> EUR</span>
                            </td>
                            <td data-label="Estado" class="estado"><?= htmlspecialchars($estados[$estadoAtual] ?? ucfirst($estadoAtual)) ?></td>
                            <td data-label="Data"><?= htmlspecialchars((string) ($reserva['criado_em'] ?? '')) ?></td>
                            <td data-label="Mudar estado">
                                <form method="post" action="<?= htmlspecialchars($baseUrl . '/admin/reserva/estado') ?>">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                                    <input type="hidden" name="reserva_id" value="<?= htmlspecialchars((string) ($reserva['id'] ?? 0)) ?>">
                                    <select name="estado">
                                        <?php foreach ($estados as $valor => $label): ?>
                                            <option value="<?= htmlspecialchars($valor) ?>" <?= $estadoAtual === $valor ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit">Guardar</button>
                                </form>
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
