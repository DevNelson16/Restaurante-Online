<?php
require '../includes/db.php';
require '../includes/functions.php';
check_login();

if (!isset($_GET['id'])) {
    die("Pedido não especificado.");
}

$pedido_id = (int)$_GET['id'];

// Buscar dados do pedido
$stmtPed = $pdo->prepare("SELECT p.id, p.total, p.status, p.data, c.nome AS cliente_nome, c.email, c.telefone, c.endereco
                          FROM pedidos p
                          JOIN clientes c ON c.id = p.cliente_id
                          WHERE p.id = ?");
$stmtPed->execute([$pedido_id]);
$pedido = $stmtPed->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    die("Pedido não encontrado.");
}

// Buscar itens do pedido
$stmtItens = $pdo->prepare("SELECT pi.quantidade, pi.preco, pr.nome
                            FROM pedido_itens pi
                            JOIN produtos pr ON pr.id = pi.produto_id
                            WHERE pi.pedido_id = ?");
$stmtItens->execute([$pedido_id]);
$itens = $stmtItens->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Fatura #<?= $pedido['id'] ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background: #f5f7fa;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #0a7cff;
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            border-bottom: 2px solid #0a7cff;
            padding-bottom: 5px;
        }

        .cliente-info,
        .pedido-info {
            margin-bottom: 25px;
        }

        .cliente-info p,
        .pedido-info p {
            margin: 4px 0;
            font-size: 15px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        th,
        td {
            padding: 12px 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 15px;
        }

        th {
            background: #0a7cff;
            color: #fff;
            font-weight: 600;
        }

        tr:hover {
            background: #f1f5fa;
        }

        .total {
            text-align: right;
            font-size: 22px;
            font-weight: 700;
            color: #0a7cff;
            margin-bottom: 15px;
        }

        .btn-print {
            background: #0a7cff;
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
        }

        .btn-print:hover {
            background: #005fcc;
        }

        @media print {
            .btn-print {
                display: none;
            }
            body {
                background: #fff;
            }
            .container {
                box-shadow: none;
                margin: 0;
                border-radius: 0;
                padding: 0;
            }
        }

        /* Responsividade */
        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }

            th, td {
                font-size: 14px;
                padding: 8px;
            }

            h1 {
                font-size: 24px;
            }

            .section-title {
                font-size: 18px;
            }

            .total {
                font-size: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Fatura #<?= $pedido['id'] ?></h1>

        <div class="cliente-info">
            <div class="section-title">Dados do Cliente</div>
            <p><strong>Nome:</strong> <?= htmlspecialchars($pedido['cliente_nome']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($pedido['email']) ?></p>
            <p><strong>Telefone:</strong> <?= htmlspecialchars($pedido['telefone']) ?></p>
            <p><strong>Endereço:</strong> <?= htmlspecialchars($pedido['endereco']) ?></p>
        </div>

        <div class="pedido-info">
            <div class="section-title">Detalhes do Pedido</div>
            <p><strong>Data:</strong> <?= date("d/m/Y H:i", strtotime($pedido['data'])) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($pedido['status']) ?></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Qtd</th>
                    <th>Preço Unitário (€)</th>
                    <th>Subtotal (€)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($itens as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nome']) ?></td>
                        <td><?= $item['quantidade'] ?></td>
                        <td><?= number_format($item['preco'], 2, ',', '.') ?></td>
                        <td><?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total">Total: € <?= number_format($pedido['total'], 2, ',', '.') ?></div>

        <button class="btn-print" onclick="window.print()">🖨️ Imprimir Fatura</button>
    </div>
</body>

</html>
