<?php require 'includes/db.php';
$produtos = $pdo->query("SELECT * FROM produtos ORDER BY nome ASC")->fetchAll(); ?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Cardápio - Restaurante Moderno</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* ===============================
   RESET & VARIÁVEIS
================================ */
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
    --text-main: #1f2937;
    --text-muted: #6b7280;
    --bg-main: #f8fafc;
    --bg-card: #ffffff;
    --radius-lg: 18px;
    --radius-xl: 26px;
    --shadow-sm: 0 6px 18px rgba(0, 0, 0, 0.08);
    --shadow-md: 0 18px 40px rgba(0, 0, 0, 0.14);
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

/* ===============================
   LAYOUT
================================ */
.container {
    max-width: 1200px;
    margin: auto;
    padding: 0 20px;
}

/* ===============================
   HEADER
================================ */
.header {
    width: 100%;
    position: fixed;
    inset: 0 0 auto 0;
    z-index: 1000;
    padding: 16px 0;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    box-shadow: var(--shadow-sm);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    height: 46px;
}

/* ===============================
   NAV
================================ */
.nav ul {
    list-style: none;
    display: flex;
    gap: 28px;
}

.nav ul li a {
    color: #ffffff;
    font-weight: 500;
    position: relative;
    transition: color 0.3s ease;
}

.nav ul li a::after {
    content: "";
    position: absolute;
    left: 0;
    bottom: -6px;
    width: 0;
    height: 2px;
    background: var(--accent);
    transition: width 0.3s ease;
}

.nav ul li a:hover {
    color: var(--accent);
}

.nav ul li a:hover::after {
    width: 100%;
}

/* ===============================
   HAMBURGER
================================ */
.hamburger {
    display: none;
    flex-direction: column;
    gap: 6px;
    cursor: pointer;
    z-index: 1100;
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

/* ===============================
   CARDÁPIO
================================ */
.cardapio-section {
    padding: 120px 0 80px;
}

.titulo-section {
    text-align: center;
    font-size: 2.3rem;
    font-weight: 700;
    margin-bottom: 60px;
    color: var(--primary);
}

.cardapio-grade {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 32px;
}

/* ===============================
   CARD PRODUTO
================================ */
.card-produto {
    background: var(--bg-card);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: all 0.35s ease;
    cursor: pointer;
    display: flex;
    flex-direction: column;
}

.card-produto:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-md);
}

.card-produto img {
    width: 100%;
    height: 190px;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.card-produto:hover img {
    transform: scale(1.08);
}

.card-info {
    padding: 22px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.card-info h3 {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--primary);
}

.card-info p {
    font-size: 0.95rem;
    color: var(--text-muted);
    flex-grow: 1;
}

/* ===============================
   INFO EXTRA
================================ */
.produto-info-extra {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 12px;
}

.preco {
    font-weight: 700;
    font-size: 1.1rem;
    color: var(--danger);
}

.quantidade {
    font-size: 0.85rem;
    color: #16a34a;
    background: #ecfdf5;
    padding: 4px 10px;
    border-radius: 999px;
}

/* ===============================
   FOOTER
================================ */
.footer {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    text-align: center;
    padding: 28px 20px;
    color: #ffffff;
    margin-top: 60px;
    font-size: 0.95rem;
}

/* ===============================
   MOBILE
================================ */
@media (max-width: 768px) {
    .hamburger {
        display: flex;
    }

    .nav {
        position: absolute;
        top: 78px;
        right: 20px;
        background: rgba(10, 124, 255, 0.98);
        width: 230px;
        border-radius: var(--radius-xl);
        transform: translateX(120%);
        transition: transform 0.35s ease;
        box-shadow: var(--shadow-md);
    }

    .nav.active {
        transform: translateX(0);
    }

    .nav ul {
        flex-direction: column;
        padding: 22px;
        gap: 18px;
    }
}

    </style>
</head>

<body>
    <header class="header">
        <div class="container header-content"> <img src="assets/img/logo1.png" class="logo">
            <div class="hamburger" id="hamburger"> <span></span> <span></span> <span></span> </div>
            <nav class="nav" id="nav">
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
    <section class="cardapio-section">
        <div class="container">
            <h2 class="titulo-section">Nosso Cardápio</h2>
            <div class="cardapio-grade"> <?php foreach ($produtos as $p): ?> <div class="card-produto"> <img src="assets/img/<?= $p['imagem'] ?? 'placeholder.jpg' ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
                        <div class="card-info">
                            <h3><?= htmlspecialchars($p['nome']) ?></h3>
                            <p><?= htmlspecialchars($p['descricao']) ?></p>
                            <div class="produto-info-extra"> <span class="preco">€ <?= number_format($p['preco'], 2, ',', '.') ?></span> <span class="quantidade">🔥 Stock: <?= $p['quantidade'] ?></span> </div>
                        </div>
                    </div> <?php endforeach; ?> </div>
        </div>
    </section>
    <footer class="footer">
        <p>&copy; 2026 Nelson Jaime — Todos os direitos reservados</p>
    </footer>
    <script>
        const hamburger = document.getElementById('hamburger');
        const nav = document.getElementById('nav');
        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            nav.classList.toggle('active');
        });
    </script>
</body>

</html>