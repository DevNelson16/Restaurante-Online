<?php
$reservas = require '../data/reservas.php';

/* Ações do Admin */
if (isset($_GET['acao'], $_GET['id'])) {
    $id = (int) $_GET['id'];

    if (isset($reservas[$id])) {
        if ($_GET['acao'] === 'confirmar') $reservas[$id]['status'] = 'Confirmada';
        if ($_GET['acao'] === 'concluir')  $reservas[$id]['status'] = 'Concluída';
        if ($_GET['acao'] === 'cancelar')  $reservas[$id]['status'] = 'Cancelada';
        if ($_GET['acao'] === 'apagar')    unset($reservas[$id]);

        file_put_contents(
            '../data/reservas.php',
            "<?php\nreturn " . var_export(array_values($reservas), true) . ";"
        );

        header("Location: reservas.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Gestão de Reservas</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding-top: 70px;
            background: #f4f6f9;
            color: #333;
        }

        .container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 0 25px;
        }

        h2 {
            font-size: 32px;
            color: #0a7cff;
            font-weight: 700;
            margin-bottom: 25px;
        }

        /* TABELA */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
        }

        th,
        td {
            padding: 14px 12px;
            font-size: 15px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background: linear-gradient(135deg, #0a7cff, #0056b3);
            color: #fff;
            font-weight: 600;
        }

        tr:hover {
            background: #f1f5fa;
        }

        /* BOTÕES */
        .btn {
            padding: 6px 12px;
            border-radius: 8px;
            color: #fff;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            margin: 2px 2px 2px 0;
            display: inline-block;
            transition: 0.3s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .confirmar {
            background: #27ae60;
        }

        .concluir {
            background: #0a7cff;
        }

        .cancelar {
            background: #f39c12;
        }

        .apagar {
            background: #c0392b;
        }

        /* STATUS */
        .status {
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 8px;
            display: inline-block;
            min-width: 90px;
            text-align: center;
        }

        .confirmada {
            background: #27ae60;
            color: #fff;
        }

        .concluida {
            background: #2980b9;
            color: #fff;
        }

        .cancelada {
            background: #e74c3c;
            color: #fff;
        }

        /* RESPONSIVO */
        @media(max-width:768px) {

            table,
            thead,
            tbody,
            th,
            td,
            tr {
                display: block;
            }

            thead tr {
                display: none;
            }

            tr {
                margin-bottom: 20px;
                border-bottom: 1px solid #e0e0e0;
            }

            td {
                text-align: left;
                padding: 8px 12px;
                display: flex;
                flex-direction: column;
                gap: 6px;
            }

            td:last-child {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
            }
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <h2>📅 Gestão de Reservas</h2>

        <table>
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Telefone</th>
                    <th>Data</th>
                    <th>Hora</th>
                    <th>Pessoas</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservas as $i => $r):
                    $status = $r['status'] ?? 'Pendente';
                    $classeStatus = match ($status) {
                        'Confirmada' => 'confirmada',
                        'Cancelada' => 'cancelada',
                        'Concluída' => 'concluida',
                        default => ''
                    };
                ?>
                    <tr>
                        <td><?= htmlspecialchars($r['cliente']) ?></td>
                        <td><?= htmlspecialchars($r['telefone']) ?></td>
                        <td><?= htmlspecialchars($r['data']) ?></td>
                        <td><?= htmlspecialchars($r['hora']) ?></td>
                        <td><?= htmlspecialchars($r['pessoas']) ?></td>
                        <td class="status <?= $classeStatus ?>"><?= $status ?></td>
                        <td>
                            <a class="btn confirmar" href="?acao=confirmar&id=<?= $i ?>">Confirmar</a>
                            <a class="btn concluir" href="?acao=concluir&id=<?= $i ?>">Concluir</a>
                            <a class="btn cancelar" href="?acao=cancelar&id=<?= $i ?>">Cancelar</a>
                            <a class="btn apagar" href="?acao=apagar&id=<?= $i ?>" onclick="return confirm('Apagar reserva?')">Apagar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>