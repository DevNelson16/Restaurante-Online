<?php
require '../includes/db.php';
require 'includes/admin_auth.php';

$id = (int)($_GET['id'] ?? 0);
$status = $_GET['s'] ?? '';

$permitidos = ['pendente', 'confirmada', 'cancelada', 'concluida'];

if ($id && in_array($status, $permitidos)) {
    $stmt = $pdo->prepare("UPDATE reservas SET status=? WHERE id=?");
    $stmt->execute([$status, $id]);
}

header("Location: reservas.php");
exit;
