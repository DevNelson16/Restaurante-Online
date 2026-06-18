<?php
require '../includes/db.php';
require '../includes/functions.php';


// Configuração de período
$periodo = $_GET['periodo'] ?? 'mes'; // diario, semana, mes, ano

// Buscar colaboradores
$colaboradores = $pdo->query("SELECT id, nome FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);

// Arrays para gráficos e cards
$labels = [];
$totais = [];
$lucros = [];

foreach ($colaboradores as $c) {
    $id = $c['id'];
    $labels[] = $c['nome'];

    // Calcular período
    switch ($periodo) {
        case 'diario':
            $inicio = date('Y-m-d');
            $fim    = date('Y-m-d');
            break;
        case 'semana':
            $inicio = date('Y-m-d', strtotime('monday this week'));
            $fim    = date('Y-m-d', strtotime('sunday this week'));
            break;
        case 'ano':
            $inicio = date('Y-01-01');
            $fim    = date('Y-12-31');
            break;
        case 'mes':
        default:
            $inicio = date('Y-m-01');
            $fim    = date('Y-m-t');
            break;
    }

    // Total arrecadado
    $stmt = $pdo->prepare("SELECT SUM(total) as total FROM pedidos WHERE funcionario_id=? AND DATE(criado_em) BETWEEN ? AND ?");
    $stmt->execute([$id, $inicio, $fim]);
    $total = (float)$stmt->fetchColumn();
    $totais[] = $total;

    // Lucro estimado 30%
    $lucros[] = $total * 0.3;
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Colaboradores</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #f4f6f9;
            color: #333;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 220px;
            background: linear-gradient(180deg, #0a7cff, #0563d3);
            color: #fff;
            display: flex;
            flex-direction: column;
            padding-top: 20px;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.1);
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
            font-size: 20px;
        }

        .sidebar a {
            padding: 12px 20px;
            display: block;
            margin: 5px 10px;
            border-radius: 8px;
            font-weight: 500;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(3px);
        }

        /* MAIN */
        .main {
            margin-left: 240px;
            padding: 30px;
        }

        h2 {
            margin-bottom: 15px;
            color: #0a7cff;
        }

        h3 {
            margin-bottom: 10px;
        }

        /* FILTRO */
        .form-filtro {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 25px;
            align-items: center;
        }

        .form-filtro select,
        .form-filtro input {
            padding: 8px 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        .btn {
            background: #0a7cff;
            color: #fff;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: .3s;
        }

        .btn:hover {
            background: #065abf;
        }

        .btn-export {
            background: #28a745;
        }

        .btn-export:hover {
            background: #1e7e34;
        }

        /* CARDS */
        .cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 35px;
        }

        .card {
            background: #fff;
            flex: 1;
            min-width: 260px;
            padding: 20px;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            transition: .3s;
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        }

        .card h3 {
            font-size: 18px;
            color: #0a7cff;
            margin-bottom: 10px;
        }

        .card p {
            font-size: 15px;
            margin: 4px 0;
        }

        canvas {
            background: #fff;
            border-radius: 14px;
            padding: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            margin-bottom: 40px;
        }

        /* RESPONSIVO */
        @media(max-width:900px) {
            .main {
                margin-left: 0;
                padding: 20px;
            }

            .sidebar {
                position: relative;
                width: 100%;
                flex-direction: row;
                overflow-x: auto;
            }

            .sidebar a {
                margin: 0 10px;
            }
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h2>Restaurante</h2>
        <a href="dashboard.php">Home</a>
        <a href="pedidos.php">Pedidos</a>
        <a href="categorias.php">Categorias</a>
        <a href="produtos.php">Produtos</a>
        <a href="dashboard_colaboradores.php">Colaboradores</a>
        <a href="logout.php">Sair</a>
    </div>

    <div class="main">
        <h2>Dashboard de Colaboradores</h2>

        <!-- FILTRO -->
        <form method="GET" class="form-filtro">
            <label>Período:
                <select name="periodo">
                    <option value="diario" <?= $periodo == 'diario' ? 'selected' : '' ?>>Diário</option>
                    <option value="semana" <?= $periodo == 'semana' ? 'selected' : '' ?>>Semanal</option>
                    <option value="mes" <?= $periodo == 'mes' ? 'selected' : '' ?>>Mensal</option>
                    <option value="ano" <?= $periodo == 'ano' ? 'selected' : '' ?>>Anual</option>
                </select>
            </label>
            <button type="submit" class="btn">Filtrar</button>
            <a href="export_excel.php?periodo=<?= $periodo ?>" class="btn btn-export">Exportar Excel</a>
            <a href="export_pdf.php?periodo=<?= $periodo ?>" class="btn btn-export">Exportar PDF</a>

        </form>

        <!-- CARDS -->
        <div class="cards">
            <?php foreach ($colaboradores as $i => $c): ?>
                <div class="card">
                    <h3><?= htmlspecialchars($c['nome']) ?></h3>
                    <p>Total arrecadado: € <?= number_format($totais[$i] ?? 0, 2, ',', '.') ?></p>
                    <p>Lucro estimado: € <?= number_format($lucros[$i] ?? 0, 2, ',', '.') ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- GRÁFICO -->
        <h3>Lucro por Colaborador</h3>
        <canvas id="graficoLucro"></canvas>
    </div>

    <script>
        const labels = <?= json_encode($labels) ?>;
        const lucros = <?= json_encode($lucros) ?>;

        new Chart(document.getElementById('graficoLucro'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Lucro (€)',
                    data: lucros,
                    backgroundColor: 'rgba(10,124,255,0.8)',
                    borderColor: '#0a7cff',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 50
                        }
                    },
                    x: {
                        ticks: {
                            color: '#333',
                            font: {
                                weight: '500'
                            }
                        }
                    }
                }
            }
        });
    </script>

</body>

</html>