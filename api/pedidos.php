<?php
header("Content-Type: application/json; charset=UTF-8");
require "../includes/db.php";

$action = $_POST['action'] ?? '';

/* ==========================================
   1. CRIAR PEDIDO (ÁREA PÚBLICA)
========================================== */
if ($action == "new") {

    $produto_id = $_POST['produto_id'] ?? null;
    $quantidade = $_POST['quantidade'] ?? null;

    if (!$produto_id || !$quantidade) {
        echo json_encode(["success" => false, "message" => "Dados incompletos"]);
        exit;
    }

    // Criar cliente rápido (para pedidos simples)
    $pdo->query("INSERT INTO clientes (nome) VALUES ('Cliente Online')");
    $cliente_id = $pdo->lastInsertId();

    // Buscar preço real do produto
    $p = $pdo->prepare("SELECT preco FROM produtos WHERE id=?");
    $p->execute([$produto_id]);
    $produto = $p->fetch();

    if (!$produto) {
        echo json_encode(["success" => false, "message" => "Produto não encontrado"]);
        exit;
    }

    $preco_total = $produto['preco'] * $quantidade;

    // Criar pedido
    $stmt = $pdo->prepare("INSERT INTO pedidos (cliente_id, total) VALUES (?, ?)");
    $stmt->execute([$cliente_id, $preco_total]);

    $pedido_id = $pdo->lastInsertId();

    // Adicionar itens
    $ins = $pdo->prepare("
        INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco)
        VALUES (?, ?, ?, ?)
    ");
    $ins->execute([$pedido_id, $produto_id, $quantidade, $produto['preco']]);

    echo json_encode([
        "success" => true,
        "message" => "Pedido criado com sucesso"
    ]);
    exit;
}

/* ==========================================
   2. LISTAR PEDIDOS (ADMIN)
========================================== */
if ($action == "list") {

    $sql = $pdo->query("
        SELECT 
            p.id, 
            p.status,
            p.total,
            p.data,
            cli.nome AS cliente,
            (
                SELECT GROUP_CONCAT(CONCAT(pr.nome, ' x', it.quantidade) SEPARATOR '<br>')
                FROM pedido_itens it
                JOIN produtos pr ON pr.id = it.produto_id
                WHERE it.pedido_id = p.id
            ) AS itens
        FROM pedidos p
        LEFT JOIN clientes cli ON cli.id = p.cliente_id
        ORDER BY p.id DESC
    ");

    echo json_encode([
        "success" => true,
        "data" => $sql->fetchAll(PDO::FETCH_ASSOC)
    ]);
    exit;
}

/* ==========================================
   3. MUDAR STATUS DO PEDIDO
========================================== */
if ($action == "update_status") {

    $id = $_POST['id'] ?? null;
    $status = $_POST['status'] ?? null;

    if (!$id || !$status) {
        echo json_encode(["success" => false, "message" => "Dados incompletos"]);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE pedidos SET status=? WHERE id=?");
    $stmt->execute([$status, $id]);

    echo json_encode([
        "success" => true,
        "message" => "Status atualizado"
    ]);
    exit;
}

/* ==========================================
   4. CANCELAR PEDIDO
========================================== */
if ($action == "cancel") {

    $id = $_POST['id'] ?? null;

    if (!$id) {
        echo json_encode(["success" => false, "message" => "ID inválido"]);
        exit;
    }

    $pdo->prepare("UPDATE pedidos SET status='cancelado' WHERE id=?")->execute([$id]);

    echo json_encode([
        "success" => true,
        "message" => "Pedido cancelado"
    ]);
    exit;
}

echo json_encode(["success" => false, "message" => "Ação inválida"]);
