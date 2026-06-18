<?php
require '../includes/db.php';
require '../includes/functions.php';
check_login();

$periodo = $_GET['periodo'] ?? 'mes';

// Buscar colaboradores
$colaboradores = $pdo->query("SELECT id, nome FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);

// Arrays de totais e lucros
$totais = [];
$lucros = [];

foreach ($colaboradores as $c) {
    $id = $c['id'];

    switch ($periodo) {
        case 'diario':
            $inicio = $fim = date('Y-m-d');
            break;
        case 'semana':
            $inicio = date('Y-m-d', strtotime('monday this week'));
            $fim = date('Y-m-d', strtotime('sunday this week'));
            break;
        case 'ano':
            $inicio = date('Y-01-01');
            $fim = date('Y-12-31');
            break;
        default:
            $inicio = date('Y-m-01');
            $fim = date('Y-m-t');
            break;
    }

    $stmt = $pdo->prepare("SELECT SUM(total) as total FROM pedidos WHERE funcionario_id=? AND DATE(criado_em) BETWEEN ? AND ?");
    $stmt->execute([$id, $inicio, $fim]);
    $total = (float)$stmt->fetchColumn();
    $totais[$id] = $total;
    $lucros[$id] = $total * 0.3;
}

// Gerar HTML da tabela
ob_start();
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Colaboradores - PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #0a7cff;
            color: #fff;
        }

        h2 {
            color: #0a7cff;
        }
    </style>
</head>

<body>
    <h2>Dashboard de Colaboradores - <?= ucfirst($periodo) ?></h2>
    <table>
        <tr>
            <th>Colaborador</th>
            <th>Total Arrecadado (€)</th>
            <th>Lucro Estimado (€)</th>
        </tr>
        <?php foreach ($colaboradores as $c):
            $id = $c['id']; ?>
            <tr>
                <td><?= htmlspecialchars($c['nome']) ?></td>
                <td><?= number_format($totais[$id] ?? 0, 2, ',', '.') ?></td>
                <td><?= number_format($lucros[$id] ?? 0, 2, ',', '.') ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>
<?php
$html = ob_get_clean();

// Forçar download como PDF
header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=dashboard_colaboradores_{$periodo}.pdf");
echo $html;
exit;
?>