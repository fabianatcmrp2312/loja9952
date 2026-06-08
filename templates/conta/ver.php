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
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 0 auto; padding: 20px; color: #f5f5f5; background:#111; }
        a { color: #ffd21f; }
        h1, h2 { color:#ffd21f; }
        .topo { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 24px; }
        .boas-vindas { padding: 18px; border: 1px solid #333; border-radius: 12px; background: #1a1a1a; margin-bottom: 18px; }
        .boas-vindas h1 { margin: 0 0 8px; color: #ffd21f; }
        .dados { display: grid; gap: 8px; margin: 0; }
        .dados dt { font-weight: bold; }
        .dados dd { margin: 0 0 8px; }
        .reservas { padding: 24px; border: 1px dashed #555; border-radius: 12px; background: #1a1a1a; color: #ddd; }
        .mensagem { padding: 12px 14px; border-radius: 8px; margin-bottom: 16px; background: #243b20; color: #b7f397; }
        .mensagem-info { background: #3b300f; color: #ffd21f; }
        .lista-reservas { display: grid; gap: 12px; margin-top: 14px; }
        .reserva { display: flex; justify-content: space-between; gap: 16px; border-bottom: 1px solid #333; padding: 12px 0; }
        .reserva h3 { margin: 0 0 6px; color: #ffd21f; font-size: 1rem; }
        .reserva p { margin: 0; }
        .estado { color: #ffd21f; font-weight: bold; }

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

    <?php if (!empty($_SESSION['msg_ok'])): ?>
        <p class="mensagem"><?= htmlspecialchars($_SESSION['msg_ok']) ?></p>
        <?php unset($_SESSION['msg_ok']); ?>
    <?php endif ?>

    <?php if (!empty($_SESSION['msg_info'])): ?>
        <p class="mensagem mensagem-info"><?= htmlspecialchars($_SESSION['msg_info']) ?></p>
        <?php unset($_SESSION['msg_info']); ?>
    <?php endif ?>

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
        <h2>As minhas reservas</h2>

        <?php if (empty($reservas)): ?>
            <p>As tuas reservas aparecerao aqui.</p>
        <?php else: ?>
            <div class="lista-reservas">
                <?php foreach ($reservas as $reserva): ?>
                    <article class="reserva">
                        <div>
                            <h3><?= htmlspecialchars($reserva['marca'] . ' ' . $reserva['modelo']) ?></h3>
                            <p><?= number_format((float) $reserva['preco'], 2, ',', '.') ?> EUR</p>
                        </div>
                        <p class="estado">Vendido</p>
                    </article>
                <?php endforeach ?>
            </div>
        <?php endif ?>
    </section>
</body>
</html>
