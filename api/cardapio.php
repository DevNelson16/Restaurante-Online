<?php
header("Content-Type: application/json; charset=UTF-8");
require "../includes/db.php";

$action = $_POST['action'] ?? '';

if ($action == "list") {

    $sql = $pdo->query("
        SELECT p.id, p.nome, p.descricao, p.preco, p.foto, c.nome AS categoria
        FROM produtos p
        JOIN categorias c ON c.id = p.categoria_id
        ORDER BY c.nome, p.nome
    ");

    echo json_encode([
        "success" => true,
        "data" => $sql->fetchAll(PDO::FETCH_ASSOC)
    ]);
    exit;
}

echo json_encode(["success" => false, "message" => "Ação inválida"]);
