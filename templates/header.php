<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$baseUrl = $baseUrl ?? rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
$total_carrinho = count($_SESSION['carrinho'] ?? []);
?>
<header style="background:#1A237E;color:#fff;padding:14px 24px;display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
    <a href="<?= htmlspecialchars($baseUrl . '/') ?>" style="color:#fff;font-size:1.3rem;font-weight:bold;text-decoration:none;">AutoShop</a>
    <nav style="display:flex;gap:20px;align-items:center;">
        <a href="<?= htmlspecialchars($baseUrl . '/') ?>" style="color:#fff;text-decoration:none;">Catalogo</a>
        <a href="<?= htmlspecialchars($baseUrl . '/carrinho') ?>" style="color:#fff;text-decoration:none;">Lista (<?= $total_carrinho ?>)</a>
        <a href="<?= htmlspecialchars($baseUrl . '/login') ?>" style="color:#fff;text-decoration:none;">Entrar</a>
    </nav>
</header>
