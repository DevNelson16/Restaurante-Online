<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contato - Restaurante Moderno</title>
    <style>
        /* ===============================
           RESET & VARIÁVEIS
        ================================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', system-ui, sans-serif;
        }

        :root {
            --primary: #0a7cff;
            --primary-dark: #0047ab;
            --accent: #ffdd57;
            --bg-main: #f8fafc;
            --bg-card: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --radius-md: 14px;
            --radius-lg: 18px;
            --shadow-sm: 0 5px 18px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 12px 25px rgba(0, 0, 0, 0.14);
        }

        body {
            background: var(--bg-main);
            color: var(--text-main);
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* ===============================
           HEADER
        ================================ */
        .header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: 16px 0;
            position: sticky;
            top: 0;
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

        /* ===============================
           NAV
        ================================ */
        .nav ul {
            list-style: none;
            display: flex;
            gap: 24px;
        }

        .nav a {
            color: #fff;
            font-weight: 600;
            padding: 8px 14px;
            border-radius: var(--radius-md);
            transition: all 0.3s ease;
        }

        .nav a:hover,
        .nav a.active {
            background: rgba(0, 95, 204, 0.9);
        }

        /* ===============================
           HAMBURGER
        ================================ */
        .hamburger {
            display: none;
            flex-direction: column;
            gap: 6px;
            cursor: pointer;
            margin-right: 12px;
            /* afastamento da borda */
        }

        .hamburger span {
            width: 28px;
            height: 3px;
            background: #fff;
            border-radius: 3px;
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
           CONTAINER
        ================================ */
        .container-modern {
            max-width: 1000px;
            margin: 90px auto 50px;
            padding: 0 20px;
        }

        .titulo-modern {
            font-size: clamp(1.8rem, 4vw, 2.2rem);
            font-weight: 800;
            margin-bottom: 25px;
            color: var(--primary);
            text-align: center;
        }

        /* ===============================
           CARDS
        ================================ */
        .card-contato,
        .card-mapa {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            padding: 25px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 35px;
            transition: all 0.3s ease;
        }

        .card-contato:hover,
        .card-mapa:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-md);
        }

        .card-contato p {
            font-size: 17px;
            margin-bottom: 12px;
            color: #444;
        }

        .card-contato span {
            font-weight: 700;
            color: var(--primary);
        }

        .card-mapa iframe {
            width: 100%;
            height: 350px;
            border-radius: var(--radius-md);
            border: none;
        }

        /* ===============================
           FOOTER
        ================================ */
        .footer {
            text-align: center;
            padding: 28px 20px;
            margin-top: 50px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            font-weight: 500;
        }

        /* ===============================
           RESPONSIVO
        ================================ */
        @media(max-width:768px) {
            .hamburger {
                display: flex;
            }

            .nav {
                position: fixed;
                top: 70px;
                right: 20px;
                width: 220px;
                background: rgba(10, 124, 255, 0.98);
                border-radius: 16px;
                transform: translateX(120%);
                transition: transform 0.35s ease;
                z-index: 999;
            }

            .nav.active {
                transform: translateX(0);
            }

            .nav ul {
                flex-direction: column;
                padding: 20px;
                gap: 15px;
            }
        }
    </style>
</head>

<body>

    <header class="header">
        <div class="header-content">
            <img src="assets/img/logo1.png" class="logo" alt="Logo">

            <!-- Menu hambúrguer -->
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>

            <!-- Nav -->
            <nav class="nav" id="navMenu">
                <ul>
                    <li><a href="index.php">Início</a></li>
                    <li><a href="cardapio.php">Cardápio</a></li>
                    <li><a href="pedido.php">Pedido</a></li>
                    <li><a href="reserva.php">Reserva</a></li>
                    <li><a href="contato.php" class="active">Contato</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container-modern">
        <h1 class="titulo-modern">Contato</h1>

        <div class="card-contato">
            <p><span>Endereço:</span> Rua Principal 123, Setúbal</p>
            <p><span>Telefone:</span> +351 999 999 999</p>
            <p><span>Email:</span> restaurante@empresa.com</p>
        </div>

        <h2 class="titulo-modern">Localização</h2>
        <div class="card-mapa">
            <iframe src="https://maps.google.com/maps?q=Setubal&t=&z=13&ie=UTF8&iwloc=&output=embed"></iframe>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2026 NexaTech — Todos os direitos reservados</p>
    </footer>

    <script>
        const hamburger = document.getElementById("hamburger");
        const navMenu = document.getElementById("navMenu");

        hamburger.addEventListener("click", () => {
            hamburger.classList.toggle("active");
            navMenu.classList.toggle("active");
        });
    </script>

</body>

</html>