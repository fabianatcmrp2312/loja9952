<?php
require 'vendor/autoload.php';
if (is_file('.env')) {
    foreach (parse_ini_file('.env') ?: [] as $k => $v) {
        $_ENV[$k] = $v;
    }
}
$pdo = App\Database::getConnection();
try {
    $rows = $pdo->query("SELECT id,nome,email,password FROM clientes WHERE email='cliente@autoshop.pt' LIMIT 1")->fetchAll();
    var_export($rows);
} catch (Throwable $e) {
    echo 'ERR: ' . $e->getMessage();
}
