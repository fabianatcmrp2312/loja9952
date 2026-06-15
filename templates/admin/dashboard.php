<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$baseUrl = $baseUrl ?? rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
$totalVeic = (int) ($totalVeic ?? 0);
$totalRes = (int) ($totalRes ?? 0);
$pendentes = (int) ($pendentes ?? 0);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'Dashboard Admin') ?></title>
    <style>
        :root { color-scheme: dark; --bg:#0b1020; --card:#121a31; --card2:#18213b; --text:#eef2ff; --muted:#aab4d6; --accent:#f6c445; --line:#25304f; }
        * { box-sizing:border-box; }
        body { margin:0; font-family:Arial, Helvetica, sans-serif; background:radial-gradient(circle at top, #172044 0, var(--bg) 45%, #060913 100%); color:var(--text); }
        main { max-width:1120px; margin:0 auto; padding:32px 20px 48px; }
        .hero, .card { background:rgba(18,26,49,.92); border:1px solid var(--line); border-radius:18px; box-shadow:0 18px 40px rgba(0,0,0,.28); }
        .hero { padding:28px; margin-bottom:22px; display:flex; justify-content:space-between; gap:16px; flex-wrap:wrap; align-items:end; }
        .hero h1, .section h2 { margin:0; }
        .hero p { margin:10px 0 0; color:var(--muted); max-width:60ch; }
        .grid { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:16px; margin:18px 0 24px; }
        .card { padding:22px; background:linear-gradient(180deg, rgba(24,33,59,.96), rgba(18,26,49,.96)); }
        .card span { display:block; color:var(--muted); font-size:.92rem; margin-bottom:8px; }
        .card strong { font-size:2.2rem; line-height:1; color:var(--accent); }
        .links { display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:14px; }
        .link { display:block; padding:18px 20px; text-decoration:none; color:var(--text); background:var(--card2); border:1px solid var(--line); border-radius:14px; transition:transform .15s ease, border-color .15s ease; }
        .link:hover { transform:translateY(-2px); border-color:var(--accent); }
        .link small { display:block; color:var(--muted); margin-top:6px; }
        .section { margin-top:26px; }
        @media (max-width: 760px) { .grid { grid-template-columns:1fr; } .hero { padding:22px; } }
    </style>
</head>
<body>
<main>
    <section class="hero">
        <div>
            <h1>Dashboard do Backoffice</h1>
            <p>Visão geral rápida do inventário, das reservas e das pendências operacionais.</p>
        </div>
        <a class="link" href="<?= htmlspecialchars($baseUrl . '/admin/veiculos') ?>">Ir para veículos</a>
    </section>

    <section class="grid" aria-label="Resumo">
        <article class="card">
            <span>Total de veículos</span>
            <strong><?= number_format($totalVeic, 0, ',', '.') ?></strong>
        </article>
        <article class="card">
            <span>Total de reservas</span>
            <strong><?= number_format($totalRes, 0, ',', '.') ?></strong>
        </article>
        <article class="card">
            <span>Reservas pendentes</span>
            <strong><?= number_format($pendentes, 0, ',', '.') ?></strong>
        </article>
    </section>

    <section class="section">
        <h2>Atalhos</h2>
        <div class="links">
            <a class="link" href="<?= htmlspecialchars($baseUrl . '/admin/veiculos') ?>">
                Gerir veículos
                <small>Ver, editar e apagar anúncios.</small>
            </a>
            <a class="link" href="<?= htmlspecialchars($baseUrl . '/admin/veiculos/novo') ?>">
                Adicionar veículo
                <small>Criar um novo registo com imagem.</small>
            </a>
            <a class="link" href="<?= htmlspecialchars($baseUrl . '/admin/reservas') ?>">
                Gerir reservas
                <small>Alterar estados e acompanhar pedidos.</small>
            </a>
        </div>
    </section>
</main>
</body>
</html>
