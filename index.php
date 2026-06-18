<?php
// Carrega os pratos populares (sem base de dados)
$produtos = require __DIR__ . '/data/pratos_populares.php';
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Restaurante Moderno</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        /* ===============================
   RESET & BASE
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
    --shadow-sm: 0 6px 20px rgba(0, 0, 0, 0.08);
    --shadow-md: 0 14px 40px rgba(0, 0, 0, 0.12);
}

body {
    background: var(--bg-main);
    color: var(--text-main);
    line-height: 1.6;
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
    position: fixed;
    inset: 0 0 auto 0;
    z-index: 1000;
    padding: 16px 0;
    transition: all 0.35s ease;
}

.header.scrolled {
    background: rgba(10, 124, 255, 0.96);
    backdrop-filter: blur(12px);
    box-shadow: var(--shadow-sm);
}

.header-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.logo {
    height: 46px;
}

/* ===============================
   NAVIGATION
================================ */
.nav ul {
    list-style: none;
    display: flex;
    gap: 28px;
}

.nav ul li a {
    color: #ffffff;
    text-decoration: none;
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
   HERO
================================ */
.hero {
    height: 90vh;
    background: url('assets/img/hero-bg.jpg') center / cover no-repeat;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    position: relative;
    color: #ffffff;
    padding: 20px;
}

.hero::after {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(
        rgba(0, 0, 0, 0.55),
        rgba(0, 0, 0, 0.65)
    );
}

.hero-content {
    position: relative;
    z-index: 1;
    max-width: 700px;
}

.hero h1 {
    font-size: clamp(2.4rem, 5vw, 3.4rem);
    font-weight: 700;
    margin-bottom: 20px;
}

.hero p {
    font-size: 1.15rem;
    color: #e5e7eb;
    margin-bottom: 36px;
}

/* ===============================
   BUTTON
================================ */
.btn-hero {
    display: inline-block;
    padding: 15px 42px;
    background: var(--accent);
    color: var(--primary);
    font-weight: 600;
    border-radius: 999px;
    text-decoration: none;
    box-shadow: var(--shadow-sm);
    transition: all 0.35s ease;
}

.btn-hero:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-md);
}

/* ===============================
   PRODUTOS
================================ */
.produtos-destaque {
    padding: 110px 0 80px;
}

.titulo-section {
    text-align: center;
    font-size: 2.3rem;
    font-weight: 700;
    margin-bottom: 60px;
    color: var(--primary);
}

.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 32px;
}

.card {
    background: var(--bg-card);
    border-radius: var(--radius-lg);
    padding: 28px;
    box-shadow: var(--shadow-sm);
    transition: all 0.35s ease;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.card:hover {
    transform: translateY(-6px);
    box-shadow: var(--shadow-md);
}

.card h3 {
    font-size: 1.25rem;
    color: var(--primary);
}

.card p {
    color: var(--text-muted);
    font-size: 0.95rem;
}

.preco {
    margin-top: 8px;
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--danger);
}

/* ===============================
   FOOTER
================================ */
.footer {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    padding: 28px 20px;
    text-align: center;
    color: #ffffff;
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

    .hero {
        height: 85vh;
    }

    .cards {
        gap: 24px;
    }
}

    </style>
</head>

<body>

    <header class="header">
        <div class="container header-content">
            <img src="assets/img/logo1.png" class="logo">
            <div class="hamburger" id="hamburger">
                <span></span><span></span><span></span>
            </div>
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

    <section class="hero">
        <div class="hero-content">
            <h1>Restaurante Moderno</h1>
            <p>Sabores incríveis preparados para você</p>
            <a href="cardapio.php" class="btn-hero">Ver Cardápio</a>
        </div>
    </section>

    <section class="produtos-destaque">
        <div class="container">
            <h2 class="titulo-section">Pratos Populares</h2>
            <div class="cards">
                <?php foreach ($produtos as $p): ?>
                    <div class="card">
                        <h3><?= htmlspecialchars($p['nome']) ?></h3>
                        <p><?= htmlspecialchars($p['descricao']) ?></p>
                        <span class="preco"><?= number_format($p['preco'], 2, ',', '.') ?> €</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <footer class="footer">
        <p>&copy; 2026 NexaTech — Todos os direitos reservados</p>
    </footer>

    <script>
        // HEADER SCROLL
        const header = document.querySelector('.header');
        window.addEventListener('scroll', () => {
            header.classList.toggle('scrolled', window.scrollY > 50);
        });

        // MENU HAMBURGER MOBILE
        const hamburger = document.getElementById('hamburger');
        const nav = document.getElementById('nav');
        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            nav.classList.toggle('active');
        });
    </script>

</body>

</html>