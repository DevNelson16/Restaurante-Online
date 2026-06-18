<?php
require '../includes/db.php';
require '../includes/functions.php';


if (isset($_POST['status'])) {
    $stmt = $pdo->prepare("UPDATE pedidos SET status=? WHERE id=?");
    $stmt->execute([$_POST['status'], $_POST['pedido_id']]);
}

// Query atualizada com JOIN para pegar nome e email do cliente
$pedidos = $pdo->query("
    SELECT p.*, c.nome AS cliente_nome, c.email AS cliente_email
    FROM pedidos p
    JOIN clientes c ON p.cliente_id = c.id
    ORDER BY p.criado_em DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Pedidos</title>
    <link rel="stylesheet" href="../assets/css/estilo.css">
        <style>
    /* Fonte moderna */
    body {
        font-family: 'Inter', sans-serif;
        background: #f4f6f9;
        margin: 0;
        padding: 0;
    }

    .container {
        padding: 40px 20px;
        max-width: 1300px;
        margin: auto;
    }

    h2 {
        color: #0a7cff;
        margin-bottom: 25px;
        font-size: 32px;
        font-weight: 700;
    }

    /* Tabela moderna */
    .tabela {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
    }

    .tabela th,
    .tabela td {
        padding: 16px 14px;
        text-align: left;
    }

    .tabela th {
        background: linear-gradient(135deg, #0a7cff, #0056b3);
        color: #fff;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .tabela tr {
        transition: background 0.3s, transform 0.2s;
    }

    .tabela tr:hover {
        background: #f1f5fa;
        transform: translateY(-2px);
    }

    td p {
        margin: 2px 0;
        font-size: 14px;
        color: #555;
    }

    td {
        font-size: 15px;
        color: #333;
    }

    /* Select estilizado */
    select {
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 14px;
        background: #fff;
        transition: 0.3s;
    }

    select:focus {
        border-color: #0a7cff;
        box-shadow: 0 0 5px rgba(10,124,255,0.4);
        outline: none;
    }

    /* Botões modernos */
    button {
        padding: 8px 14px;
        background: #0a7cff;
        color: #fff;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: 0.3s;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    button:hover {
        background: #065abf;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.12);
    }

    form {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .btn-print {
        background: #28a745;
        margin-left: 5px;
    }

    .btn-print:hover {
        background: #1e7e34;
        transform: translateY(-2px);
    }

    /* Responsivo */
    @media (max-width: 1024px) {
        .container {
            padding: 20px 15px;
        }

        h2 {
            font-size: 28px;
        }

        .tabela th, .tabela td {
            padding: 12px 10px;
        }

        button, select {
            font-size: 13px;
            padding: 6px 10px;
        }
    }

    @media (max-width: 768px) {
        .tabela, .tabela tr, .tabela th, .tabela td {
            display: block;
            width: 100%;
        }

        .tabela tr {
            margin-bottom: 15px;
        }

        .tabela th {
            display: none;
        }

        td {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #0a7cff;
        }

        form {
            flex-direction: column;
            gap: 5px;
        }
    }

</style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h2>Gestão de Pedidos</h2>
        <table class="tabela">
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Status</th>
                <th>Data</th>
                <th>Ações</th>
            </tr>
            <?php foreach ($pedidos as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td>
                        <p><?= htmlspecialchars($p['cliente_nome']) ?></p>
                        <p><?= htmlspecialchars($p['cliente_email']) ?></p>
                    </td>
                    <td>€ <?= number_format($p['total'], 2, ',', '.') ?></td>
                    <td><?= $p['status'] ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($p['criado_em'])) ?></td>
                    <td>
                        <form method="POST" style="display:inline-flex; gap:5px;">
                            <input type="hidden" name="pedido_id" value="<?= $p['id'] ?>">
                            <select name="status">
                                <option value="pendente" <?= $p['status'] == "pendente" ? "selected" : "" ?>>Pendente</option>
                                <option value="em preparo" <?= $p['status'] == "em preparo" ? "selected" : "" ?>>Em Preparo</option>
                                <option value="entregue" <?= $p['status'] == "entregue" ? "selected" : "" ?>>Entregue</option>
                            </select>
                            <button type="submit">Atualizar</button>
                        </form>
                        <button class="btn-print" onclick="window.open('fatura.php?id=<?= $p['id'] ?>','_blank')">Imprimir</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>

</html>
