<?php
require "../includes/db.php";
session_start();

// ================= SEGURANÇA =================
// Verifica se usuário está logado e é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel_acesso'] !== 'admin') {
    header("Location: login.php");
    exit();
}
// ============================================

// Pedidos mensais
$pedidos = $pdo->query("SELECT MONTH(data) AS mes, COUNT(*) AS total FROM pedidos GROUP BY MONTH(data)")->fetchAll(PDO::FETCH_ASSOC);

// Vendas mensais
$vendas = $pdo->query("SELECT MONTH(data) AS mes, SUM(total) AS total FROM pedidos WHERE status='entregue' GROUP BY MONTH(data)")->fetchAll(PDO::FETCH_ASSOC);

// Produtos mais vendidos
$produtos = $pdo->query("
    SELECT p.nome, SUM(pe.quantidade) AS total 
    FROM pedido_itens pe
    JOIN produtos p ON p.id = pe.produto_id
    GROUP BY pe.produto_id
    ORDER BY total DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Contadores
$totalPedidos = $pdo->query("SELECT COUNT(*) FROM pedidos")->fetchColumn();
$totalVendas = $pdo->query("SELECT SUM(total) FROM pedidos WHERE status='entregue'")->fetchColumn();
$totalProdutos = $pdo->query("SELECT COUNT(*) FROM produtos")->fetchColumn();
$totalClientes = $pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Restaurante</title>
    <link rel="stylesheet" href="dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
        }

        .container {
            padding: 20px;
            max-width: 1200px;
            margin: auto;
        }

        .cards {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .card {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            flex: 1;
            min-width: 150px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
        }

        .graficos {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .grafico-item {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            flex: 1;
            min-width: 300px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        h1, h3 {
            margin: 0 0 10px;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h1>📊 Dashboard do Restaurante</h1>

        <!-- CARDS RESUMO -->
        <div class="cards">
            <div class="card">
                <h3>Total Pedidos</h3>
                <p><?= $totalPedidos ?></p>
            </div>
            <div class="card">
                <h3>Vendas (€)</h3>
                <p><?= number_format($totalVendas ?? 0, 2, ',', '.') ?></p>
            </div>
            <div class="card">
                <h3>Produtos</h3>
                <p><?= $totalProdutos ?></p>
            </div>
            <div class="card">
                <h3>Clientes</h3>
                <p><?= $totalClientes ?></p>
            </div>
        </div>

        <!-- GRÁFICOS -->
        <div class="graficos">
            <div class="grafico-item">
                <h3>Pedidos por Mês</h3>
                <canvas id="graficoPedidos"></canvas>
            </div>
            <div class="grafico-item">
                <h3>Vendas por Mês (€)</h3>
                <canvas id="graficoVendas"></canvas>
            </div>
            <div class="grafico-item">
                <h3>Top 5 Produtos Mais Vendidos</h3>
                <canvas id="graficoProdutos"></canvas>
            </div>
        </div>
    </div>

    <script>
        const pedidos = <?= json_encode($pedidos) ?>;
        const vendas = <?= json_encode($vendas) ?>;
        const produtos = <?= json_encode($produtos) ?>;

        function mesesArray(data) {
            const meses = ["Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez"];
            return data.map(item => meses[item.mes - 1] || "Desconhecido");
        }

        function valoresArray(data, campo) {
            return data.map(item => Number(item[campo]) || 0);
        }

        // GRÁFICO DE PEDIDOS
        new Chart(document.getElementById("graficoPedidos"), {
            type: "line",
            data: {
                labels: mesesArray(pedidos),
                datasets: [{
                    label: "Pedidos",
                    data: valoresArray(pedidos, "total"),
                    borderWidth: 3,
                    borderColor: "#0a7cff",
                    backgroundColor: "rgba(10,124,255,0.2)",
                    fill: true,
                    tension: 0.4
                }]
            },
            options: { responsive: true }
        });

        // GRÁFICO DE VENDAS
        new Chart(document.getElementById("graficoVendas"), {
            type: "bar",
            data: {
                labels: mesesArray(vendas),
                datasets: [{
                    label: "Vendas (€)",
                    data: valoresArray(vendas, "total"),
                    backgroundColor: "#0a7cff"
                }]
            },
            options: { responsive: true }
        });

        // GRÁFICO PRODUTOS MAIS VENDIDOS
        new Chart(document.getElementById("graficoProdutos"), {
            type: "doughnut",
            data: {
                labels: produtos.map(p => p.nome),
                datasets: [{
                    label: "Quantidade",
                    data: produtos.map(p => Number(p.total) || 0),
                    backgroundColor: ["#0a7cff","#ff5733","#33ff57","#f1c40f","#9b59b6"]
                }]
            },
            options: { responsive: true }
        });
    </script>
</body>
</html>
