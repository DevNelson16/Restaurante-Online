<?php
session_start();
require 'includes/db.php';

// Verifica se o utilizador está logado
if (!isset($_SESSION['user_id'])) {
    die("Erro: Usuário não autenticado.");
}

$funcionario_id = $_SESSION['user_id'];

// Verifica se existe um carrinho
if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
    die("Erro: O carrinho está vazio.");
}

// Calcula o total
$total = 0;
foreach ($_SESSION['carrinho'] as $item) {
    $total += $item['preco'] * $item['quantidade'];
}

// Grava o pedido
$stmt = $pdo->prepare("
    INSERT INTO pedidos (funcionario_id, total, created_at)
    VALUES (:funcionario_id, :total, NOW())
");

$stmt->execute([
    'funcionario_id' => $funcionario_id,
    'total' => $total
]);

$pedido_id = $pdo->lastInsertId();

// Grava os itens do pedido
$stmt_item = $pdo->prepare("
    INSERT INTO pedidos_items (pedido_id, produto_id, quantidade, preco)
    VALUES (:pedido_id, :produto_id, :quantidade, :preco)
");

foreach ($_SESSION['carrinho'] as $item) {
    $stmt_item->execute([
        'pedido_id' => $pedido_id,
        'produto_id' => $item['id'],
        'quantidade' => $item['quantidade'],
        'preco' => $item['preco']
    ]);
}

// Limpa o carrinho
unset($_SESSION['carrinho']);

echo "Pedido finalizado com sucesso! Pedido Nº: " . $pedido_id;
?>
