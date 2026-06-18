<?php
require '../includes/db.php';
require 'includes/admin_auth.php';

$id = (int)($_GET['id'] ?? 0);

if ($id) {
    $pdo->prepare("DELETE FROM reservas WHERE id=?")->execute([$id]);
}

header("Location: reservas.php");
exit;
