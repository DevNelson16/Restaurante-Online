<?php
require '../includes/db.php';
require '../includes/functions.php';
check_login();

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if (!$action) {
    echo json_encode(['success' => false, 'message' => 'Ação inválida']);
    exit;
}

switch ($action) {

    // --------------------------------------------------------------------
    // ATUALIZAR SOMENTE A QUANTIDADE
    // --------------------------------------------------------------------
    case 'update':
        $id = (int)($_POST['id'] ?? 0);
        $quantidade = (int)($_POST['quantidade'] ?? 0);

        if ($id <= 0 || $quantidade < 0) {
            echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE produtos SET quantidade=? WHERE id=?");
        if ($stmt->execute([$quantidade, $id])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar estoque']);
        }
        break;

    // --------------------------------------------------------------------
    // APAGAR PRODUTO
    // --------------------------------------------------------------------
    case 'delete':
        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID inválido']);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM produtos WHERE id=?");
        if ($stmt->execute([$id])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao remover produto']);
        }
        break;

    // --------------------------------------------------------------------
    // ADICIONAR PRODUTO
    // --------------------------------------------------------------------
    case 'add':
        $nome = trim($_POST['nome'] ?? '');
        $preco = (float)($_POST['preco'] ?? 0);
        $quantidade = (int)($_POST['quantidade'] ?? 0);

        if (!$nome || $preco <= 0 || $quantidade < 0) {
            echo json_encode(['success' => false, 'message' => 'Preencha todos os campos corretamente']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO produtos (nome, preco, quantidade) VALUES (?, ?, ?)");
        if ($stmt->execute([$nome, $preco, $quantidade])) {
            $novoId = $pdo->lastInsertId();
            echo json_encode([
                'success' => true,
                'produto' => [
                    'id' => $novoId,
                    'nome' => $nome,
                    'preco' => $preco,
                    'quantidade' => $quantidade
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao adicionar produto']);
        }
        break;

    // --------------------------------------------------------------------
    // EDITAR TUDO (nome, preço e quantidade)
    // --------------------------------------------------------------------
    case 'updateFull':
        $id = (int)$_POST['id'];
        $nome = trim($_POST['nome']);
        $preco = (float)$_POST['preco'];
        $quantidade = (int)$_POST['quantidade'];

        if ($id <= 0 || !$nome || $preco <= 0 || $quantidade < 0) {
            echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE produtos SET nome=?, preco=?, quantidade=? WHERE id=?");
        if ($stmt->execute([$nome, $preco, $quantidade, $id])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar produto']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Ação inválida']);
}
