<?php
$reservas = require './data/reservas.php';

$erro = '';
$sucesso = '';

$horariosDisponiveis = [
    "12:00",
    "12:30",
    "13:00",
    "13:30",
    "18:00",
    "18:30",
    "19:00",
    "19:30",
    "20:00"
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservar'])) {

    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $data = trim($_POST['data'] ?? '');
    $horario = trim($_POST['horario'] ?? '');
    $pessoas = (int)($_POST['pessoas'] ?? 0);
    $mensagem = trim($_POST['mensagem'] ?? '');

    if (!$nome || !$telefone || !$data || !$horario || $pessoas <= 0) {

        $erro = "Preencha todos os campos obrigatórios corretamente.";
    } elseif (!in_array($horario, $horariosDisponiveis)) {

        $erro = "Horário inválido.";
    } else {

        $novaReserva = [
            'cliente' => $nome,
            'email' => $email,
            'telefone' => $telefone,
            'data' => $data,
            'hora' => $horario,
            'pessoas' => $pessoas,
            'mensagem' => $mensagem,
            'status' => 'Pendente'
        ];

        $reservas[] = $novaReserva;

        file_put_contents(
            './data/reservas.php',
            "<?php\nreturn " . var_export($reservas, true) . ";"
        );

        $sucesso = "Reserva realizada com sucesso!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Reserva</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        :root {
            --primary: #0a7cff;
            --primary-dark: #0047ab;
            --bg-main: #f8fafc;
            --bg-card: #fff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --radius-md: 14px;
            --radius-lg: 20px;
            --shadow-sm: 0 8px 24px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 18px 45px rgba(0, 0, 0, 0.14);
        }

        body {
            background: var(--bg-main);
            color: var(--text-main);
            line-height: 1.6;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* ================= HEADER ================= */

        .header {
            position: fixed;
            top: 0;
            width: 100%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: 15px 20px;
            z-index: 1000;
            box-shadow: var(--shadow-sm);
        }

        .header-content {
            max-width: 1200px;
            margin: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            height: 50px;
        }

        .nav ul {
            display: flex;
            list-style: none;
            gap: 20px;
        }

        .nav a {
            color: #fff;
            font-weight: 600;
            padding: 8px 14px;
            border-radius: 8px;
            transition: 0.3s;
        }

        .nav a:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        /* ================= CONTAINER ================= */

        .container {
            max-width: 520px;
            margin: 170px auto 50px;
            padding: 0 20px;
        }

        h1 {
            text-align: center;
            color: var(--primary);
            margin-bottom: 8px;
            font-size: 2.2rem;
        }

        .subtitle {
            text-align: center;
            color: var(--text-muted);
            margin-bottom: 30px;
        }

        .card {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            padding: 30px;
            box-shadow: var(--shadow-md);
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-4px);
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 0.9rem;
            font-weight: 600;
            color: #555;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 14px;
            border-radius: var(--radius-md);
            border: 1px solid #ccc;
            margin-bottom: 16px;
            font-size: 0.95rem;
            transition: 0.3s;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 4px 10px rgba(10, 124, 255, 0.25);
        }

        textarea {
            resize: none;
            min-height: 120px;
        }

        button {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: var(--radius-md);
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        /* ================= ALERTAS ================= */

        .msg-erro,
        .msg-sucesso {
            padding: 16px;
            border-radius: var(--radius-md);
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
            box-shadow: var(--shadow-sm);
        }

        .msg-erro {
            background: #ffeaea;
            color: #b30000;
        }

        .msg-sucesso {
            background: #e9ffef;
            color: #1c7c38;
        }

        /* ================= FOOTER ================= */

        .footer {
            text-align: center;
            padding: 28px 20px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            margin-top: 60px;
        }

        /* ================= RESPONSIVO ================= */

        @media(max-width:768px) {

            .header-content {
                flex-direction: column;
                gap: 15px;
            }

            .nav ul {
                flex-wrap: wrap;
                justify-content: center;
                gap: 10px;
            }

            .container {
                margin-top: 220px;
            }

            h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <header class="header">

        <div class="header-content">

            <img src="assets/img/logo1.png" class="logo" alt="Logo">

            <nav class="nav" id="navMenu">
                <ul>
                    <li><a href="index.php">Início</a></li>
                    <li><a href="cardapio.php">Cardápio</a></li>
                    <li><a href="pedido.php">Pedido</a></li>
                    <li><a href="reserva.php">Reserva</a></li>
                    <li><a href="contato.php">Contato</a></li>
                </ul>
            </nav>

        </div>

    </header>

    <!-- CONTEÚDO -->
    <div class="container">

        <h1>Reserva de Mesa</h1>
        <p class="subtitle">Garanta a sua mesa com rapidez e conforto</p>

        <?php if ($erro): ?>
            <div class="msg-erro">
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>

        <?php if ($sucesso): ?>
            <div class="msg-sucesso">
                <?= htmlspecialchars($sucesso) ?>
            </div>
        <?php endif; ?>

        <div class="card">

            <form method="POST">

                <label>Nome</label>
                <input type="text" name="nome" required>

                <label>Email</label>
                <input type="email" name="email">

                <label>Telefone</label>
                <input type="text" name="telefone" required>

                <label>Data</label>
                <input
                    type="date"
                    name="data"
                    min="<?= date('Y-m-d') ?>"
                    required>

                <label>Horário</label>

                <select name="horario" required>

                    <option value="">Selecione</option>

                    <?php foreach ($horariosDisponiveis as $h): ?>

                        <option value="<?= $h ?>">
                            <?= $h ?>
                        </option>

                    <?php endforeach; ?>

                </select>

                <label>Número de Pessoas</label>
                <input
                    type="number"
                    name="pessoas"
                    min="1"
                    required>

                <label>Observações</label>
                <textarea name="mensagem"></textarea>

                <button type="submit" name="reservar">
                    Confirmar Reserva
                </button>

            </form>

        </div>

    </div>

    <!-- FOOTER -->
    <footer class="footer">
        <p>&copy; 2026 Nelson Jaime - Todos os direitos reservados.</p>
    </footer>

</body>

</html>