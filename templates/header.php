<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$baseUrl = $baseUrl ?? rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
$total_carrinho = count($_SESSION['carrinho'] ?? []);
$adminLogado = (bool) ($_SESSION['admin_logado'] ?? false);
?>
<header style="background:#0b0b0b;color:#ffd21f;padding:16px 24px;display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;border:1px solid #2a2a2a;border-radius:12px;box-shadow:0 10px 28px rgba(0,0,0,.35);">
    <a href="<?= htmlspecialchars($baseUrl . '/') ?>" style="color:#ffd21f;font-size:1.4rem;font-weight:bold;text-decoration:none;letter-spacing:.5px;">AutoShop</a>
    <nav style="display:flex;gap:18px;align-items:center;flex-wrap:wrap;">
        <a href="<?= htmlspecialchars($baseUrl . '/') ?>" style="color:#f5f5f5;text-decoration:none;">Catalogo</a>
        <a href="<?= htmlspecialchars($baseUrl . '/carrinho') ?>" style="color:#f5f5f5;text-decoration:none;">Lista (<?= $total_carrinho ?>)</a>
        <?php if ($adminLogado): ?>
            <a href="<?= htmlspecialchars($baseUrl . '/admin') ?>" style="color:#f5f5f5;text-decoration:none;">Dashboard</a>
            <a href="<?= htmlspecialchars($baseUrl . '/admin/veiculos') ?>" style="color:#f5f5f5;text-decoration:none;">Veículos</a>
            <a href="<?= htmlspecialchars($baseUrl . '/admin/reservas') ?>" style="color:#f5f5f5;text-decoration:none;">Reservas</a>
            <a href="<?= htmlspecialchars($baseUrl . '/logout') ?>" style="color:#f5f5f5;text-decoration:none;">Sair</a>
        <?php else: ?>
            <a href="<?= htmlspecialchars($baseUrl . '/login') ?>" style="color:#f5f5f5;text-decoration:none;">Entrar</a>
            <a href="<?= htmlspecialchars($baseUrl . '/admin/login') ?>" style="color:#f5f5f5;text-decoration:none;">Admin</a>
        <?php endif; ?>
    </nav>
</header>
