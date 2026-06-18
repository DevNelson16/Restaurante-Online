<?php
header("Content-Type: application/json; charset=UTF-8");
require "../includes/functions.php";
require "../includes/db.php";

$action = $_POST['action'] ?? '';

/* ==========================================
   LISTAR ESTOQUE
========================================== */
if ($action == "list") {

    $sql = $pdo->query("SELECT id, nome, quantidade FROM produtos ORDER BY nome");

    echo json_encode([
        "success" => true,
        "data" => $sql->fetchAll(PDO::FETCH_ASSOC)
    ]);
    exit;
}

/* ==========================================
   ATUALIZAR ESTOQUE
========================================== */
if ($action == "update") {

    $id = $_POST['id'] ?? null;
    $qtd = $_POST['quantidade'] ?? null;

    if (!$id || $qtd === null) {
        echo json_encode(["success" => false, "message" => "Dados incompletos"]);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE produtos SET quantidade = ? WHERE id = ?");
    $stmt->execute([$qtd, $id]);

    echo json_encode(["success" => true, "message" => "Estoque atualizado"]);
    exit;
}

echo json_encode(["success" => false, "message" => "Ação inválida"]);
