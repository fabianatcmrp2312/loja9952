<?php
$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
$nomeCliente = $_SESSION['cliente_nome'] ?? ($cliente['nome'] ?? 'Cliente');
$emailCliente = $_SESSION['cliente_email'] ?? ($cliente['email'] ?? '');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titulo ?? 'A minha conta') ?> - AutoShop</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 0 auto; padding: 20px; color: #222; }
        a { color: #1565C0; }
        .topo { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 24px; }
        .boas-vindas { padding: 18px; border: 1px solid #ddd; border-radius: 8px; background: #f7fbff; margin-bottom: 18px; }
        .boas-vindas h1 { margin: 0 0 8px; color: #1A237E; }
        .dados { display: grid; gap: 8px; margin: 0; }
        .dados dt { font-weight: bold; }
        .dados dd { margin: 0 0 8px; }
        .reservas { padding: 24px; border: 1px dashed #bbb; border-radius: 8px; background: #fafafa; color: #555; }

        @media (max-width: 640px) {
            .topo { align-items: flex-start; flex-direction: column; }
        }
    </style>
</head>
<body>
    <?php require __DIR__ . '/../header.php'; ?>

    <div class="topo">
        <h1><?= htmlspecialchars($titulo ?? 'A minha conta') ?></h1>
        <a href="<?= htmlspecialchars($baseUrl . '/') ?>">Voltar ao catalogo</a>
    </div>

    <section class="boas-vindas">
        <h1>Bem-vindo, <?= htmlspecialchars($nomeCliente) ?>!</h1>
        <p>Esta e a tua area pessoal na AutoShop.</p>

        <dl class="dados">
            <dt>Nome</dt>
            <dd><?= htmlspecialchars($nomeCliente) ?></dd>

            <dt>Email</dt>
            <dd><?= htmlspecialchars($emailCliente) ?></dd>
        </dl>
    </section>

    <section class="reservas">
        <p>As tuas reservas aparecerao aqui.</p>
    </section>
</body>
</html>
