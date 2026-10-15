<?php

require 'includes/db.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizar'])) {

    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $tel = trim($_POST['telefone'] ?? '');
    $end = trim($_POST['endereco'] ?? '');

    $produtos_post = $_POST['produto_id'] ?? [];
    $quantidades_post = $_POST['quantidade'] ?? [];
    $precos_post = $_POST['preco'] ?? [];

    if (empty($nome) || empty($tel) || empty($end)) {

        $erro = "Por favor, preencha nome, telefone e endereço.";

    } else {

        $itens = [];

        for ($i = 0; $i < count($produtos_post); $i++) {

            $pid = (int) $produtos_post[$i];
            $qtd = (int) ($quantidades_post[$i] ?? 0);
            $pre = (float) ($precos_post[$i] ?? 0);

            if ($pid > 0 && $qtd > 0) {
                $itens[] = [
                    'produto_id' => $pid,
                    'quantidade' => $qtd,
                    'preco' => $pre
                ];
            }
        }

        if (!$itens) {

            $erro = "Selecione pelo menos um produto.";

        } else {

            try {

                $pdo->beginTransaction();

                $stmtCliente = $pdo->prepare(
                    "INSERT INTO clientes (nome, telefone, email, endereco)
                     VALUES (?, ?, ?, ?)"
                );

                $stmtCliente->execute([$nome, $tel, $email, $end]);

                $cliente_id = $pdo->lastInsertId();

                $total = 0;

                foreach ($itens as $it) {
                    $total += $it['preco'] * $it['quantidade'];
                }

                $stmtPedido = $pdo->prepare(
                    "INSERT INTO pedidos (cliente_id, total, status)
                     VALUES (?, ?, 'pendente')"
                );

                $stmtPedido->execute([$cliente_id, $total]);

                $pedido_id = $pdo->lastInsertId();

                $stmtItem = $pdo->prepare(
                    "INSERT INTO pedido_itens
                     (pedido_id, produto_id, quantidade, preco)
                     VALUES (?, ?, ?, ?)"
                );

                foreach ($itens as $it) {

                    $stmtItem->execute([
                        $pedido_id,
                        $it['produto_id'],
                        $it['quantidade'],
                        $it['preco']
                    ]);
                }

                $pdo->commit();

                $sucesso = "Pedido realizado com sucesso! Nº #{$pedido_id}";

            } catch (Exception $e) {

                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                $erro = "Não foi possível concluir o pedido. " . $e->getMessage();
            }
        }
    }
}

/*
|--------------------------------------------------------------------------
| CARREGAR PRODUTOS
|--------------------------------------------------------------------------
| Correção: utilizamos a coluna imagem, em vez da coluna foto.
*/

$produtos = $pdo->query(
    "SELECT id, nome, descricao, preco, imagem, quantidade
     FROM produtos
     WHERE quantidade > 0"
)->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Finalizar Pedido</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        /* RESET E VARIÁVEIS */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
        }

        :root {
            --primary: #0a7cff;
            --primary-dark: #0047ab;
            --accent: #ffdd57;
            --danger: #ff5733;
            --bg-main: #f8fafc;
            --bg-card: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --radius-md: 14px;
            --radius-lg: 20px;
            --radius-xl: 28px;
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

        /* CONTAINER */

        .container {
            max-width: 1200px;
            margin: 140px auto 40px;
            padding: 0 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: var(--primary);
            font-size: clamp(2rem, 4vw, 2.5rem);
        }

        /* HEADER */

        .header {
            position: fixed;
            top: 0;
            width: 100%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: 16px 0;
            z-index: 1000;
            box-shadow: var(--shadow-sm);
        }

        .header-content {
            max-width: 1200px;
            margin: auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            height: 46px;
        }

        /* NAVEGAÇÃO */

        .nav ul {
            list-style: none;
            display: flex;
            gap: 28px;
        }

        .nav a {
            color: #ffffff;
            font-weight: 500;
            position: relative;
            transition: color 0.3s ease;
        }

        .nav a::after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--accent);
            transition: width 0.3s ease;
        }

        .nav a:hover {
            color: var(--accent);
        }

        .nav a:hover::after {
            width: 100%;
        }

        /* MENU MOBILE */

        .hamburger {
            display: none;
            flex-direction: column;
            gap: 6px;
            cursor: pointer;
        }

        .hamburger span {
            width: 28px;
            height: 3px;
            background: #ffffff;
            border-radius: 10px;
            transition: all 0.35s ease;
        }

        .hamburger.active span:nth-child(1) {
            transform: rotate(45deg) translate(6px, 6px);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(6px, -6px);
        }

        /* MENSAGENS */

        .msg-erro,
        .msg-sucesso {
            padding: 16px;
            border-radius: var(--radius-md);
            margin-bottom: 20px;
            font-weight: 500;
            text-align: center;
            box-shadow: var(--shadow-sm);
        }

        .msg-erro {
            background: #ffeaea;
            color: #a80000;
        }

        .msg-sucesso {
            background: #e8ffe9;
            color: #217a39;
        }

        /* CARTÕES DOS PRODUTOS */

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 32px;
            margin-bottom: 30px;
        }

        .card {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: all 0.4s ease;
            display: flex;
            flex-direction: column;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-md);
        }

        .card img {
            width: 100%;
            height: 190px;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .card:hover img {
            transform: scale(1.04);
        }

        .card-body {
            padding: 22px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .card h3 {
            font-size: 1.2rem;
            color: var(--primary);
        }

        .card p {
            font-size: 0.95rem;
            color: var(--text-muted);
        }

        .preco {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--danger);
        }

        .stock {
            font-size: 0.85rem;
            color: #9ca3af;
        }

        input,
        textarea {
            width: 100%;
            padding: 14px;
            border-radius: var(--radius-md);
            border: 1px solid #ccc;
            margin-top: 10px;
            font-size: 0.95rem;
            transition: border 0.3s ease, box-shadow 0.3s ease;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 4px 10px rgba(10, 124, 255, 0.25);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        /* TOTAL E BOTÃO */

        .total {
            text-align: right;
            font-size: 22px;
            font-weight: 700;
            color: var(--primary);
            margin: 25px 0;
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
            transition: all 0.3s ease;
        }

        button:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        /* RODAPÉ */

        .footer {
            text-align: center;
            padding: 28px 20px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #ffffff;
            margin-top: 60px;
            font-size: 0.95rem;
        }

        /* RESPONSIVIDADE */

        @media (max-width: 768px) {
            .hamburger {
                display: flex;
                margin-right: 12px;
            }

            .nav {
                position: absolute;
                top: 78px;
                right: 20px;
                width: 230px;
                background: rgba(10, 124, 255, 0.98);
                border-radius: var(--radius-xl);
                transform: translateX(120%);
                transition: transform 0.35s ease;
                padding: 20px;
                box-shadow: var(--shadow-md);
            }

            .nav.active {
                transform: translateX(0);
            }

            .nav ul {
                flex-direction: column;
                gap: 18px;
            }

            .cards {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .total {
                text-align: center;
            }
        }
    </style>
</head>

<body>

    <header class="header">
        <div class="header-content">

            <img
                src="/gestao_restaurante/assets/img/logo1.png"
                class="logo"
                alt="Logótipo do restaurante"
            >

            <div class="hamburger" id="menuToggle">
                <span></span>
                <span></span>
                <span></span>
            </div>

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

    <div class="container">

        <h1>Finalizar Pedido</h1>

        <?php if ($erro): ?>
            <div class="msg-erro">
                <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if ($sucesso): ?>
            <div class="msg-sucesso">
                <?= htmlspecialchars($sucesso, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="post">

            <div class="cards">

                <?php foreach ($produtos as $p): ?>

                    <div class="card">

                        <img
                            src="/gestao_restaurante/assets/img/<?= htmlspecialchars($p['imagem'] ?? 'placeholder.jpg', ENT_QUOTES, 'UTF-8') ?>"
                            alt="<?= htmlspecialchars($p['nome'], ENT_QUOTES, 'UTF-8') ?>"
                        >

                        <div class="card-body">

                            <h3>
                                <?= htmlspecialchars($p['nome'], ENT_QUOTES, 'UTF-8') ?>
                            </h3>

                            <p>
                                <?= htmlspecialchars($p['descricao'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </p>

                            <div class="preco">
                                € <?= number_format((float) $p['preco'], 2, ',', '.') ?>
                            </div>

                            <div class="stock">
                                Disponível: <?= (int) $p['quantidade'] ?>
                            </div>

                            <input
                                type="number"
                                name="quantidade[]"
                                min="0"
                                max="<?= (int) $p['quantidade'] ?>"
                                value="0"
                            >

                            <input
                                type="hidden"
                                name="produto_id[]"
                                value="<?= (int) $p['id'] ?>"
                            >

                            <input
                                type="hidden"
                                name="preco[]"
                                value="<?= htmlspecialchars((string) $p['preco'], ENT_QUOTES, 'UTF-8') ?>"
                            >

                        </div>
                    </div>

                <?php endforeach; ?>

            </div>

            <div class="total">
                Total € <span id="total">0,00</span>
            </div>

            <input
                type="text"
                name="nome"
                placeholder="Nome completo"
                required
            >

            <input
                type="tel"
                name="telefone"
                placeholder="Telefone"
                required
            >

            <input
                type="email"
                name="email"
                placeholder="Email"
            >

            <textarea
                name="endereco"
                placeholder="Endereço completo"
                required
            ></textarea>

            <button type="submit" name="finalizar" value="1">
                Concluir Pedido
            </button>

        </form>

    </div>

    <footer class="footer">
        <p>&copy; 2026 Nelson Jaime — Todos os direitos reservados</p>
    </footer>

    <script>
        const toggle = document.getElementById("menuToggle");
        const nav = document.getElementById("navMenu");

        toggle.onclick = () => {
            toggle.classList.toggle("active");
            nav.classList.toggle("active");
        };

        function calc() {
            let total = 0;

            const quantidades = document.querySelectorAll(
                'input[name="quantidade[]"]'
            );

            const precos = document.querySelectorAll(
                'input[name="preco[]"]'
            );

            quantidades.forEach((q, i) => {
                const quantidade = parseInt(q.value, 10) || 0;
                const preco = parseFloat(precos[i].value) || 0;

                total += quantidade * preco;
            });

            document.getElementById("total").innerText =
                total.toFixed(2).replace(".", ",");
        }

        document.querySelectorAll(
            'input[name="quantidade[]"]'
        ).forEach(q => {
            q.addEventListener("input", calc);
        });

        calc();
    </script>

</body>
</html>
