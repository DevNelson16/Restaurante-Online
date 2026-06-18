<header class="header">
    <h2>Dashboard Restaurante</h2>
    <nav>
        <a href="dashboard.php">Home</a>
        <a href="pedidos.php">Pedidos</a>
        <a href="categorias.php">Categorias</a>
        <a href="produtos.php">Produtos</a>
        <a href="pratos_populares.php">Atualizar Pratos</a>
        <a href="reservas.php">Reservas</a>
        <a href="dashboard_colaboradores_profissional.php">Colaboradores</a>
        <a href="logout.php">Sair</a>
    </nav>
</header>

<style>
    /* =========================
HEADER MODERNO FIXO
========================= */
    .header {
        position: fixed;
        /* fixo no topo */
        top: 0;
        left: 0;
        width: 100%;
        /* ocupa toda largura */
        background: #0a7cff;
        color: #fff;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
        z-index: 1000;
        transition: all 0.3s;
    }

    .header h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .header nav {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }

    .header nav a {
        color: #fff;
        text-decoration: none;
        font-weight: 500;
        padding: 8px 14px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.05);
        transition: all 0.3s;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .header nav a:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    /* Espaço para o conteúdo não ficar atrás do header */
    body {
        padding-top: 80px;
    }

    /* Responsividade */
    @media (max-width: 900px) {
        .header {
            flex-direction: column;
            align-items: flex-start;
            padding: 15px 20px;
        }

        .header nav {
            width: 100%;
            justify-content: flex-start;
            gap: 10px;
            margin-top: 10px;
        }
    }

    @media (max-width: 500px) {
        .header h2 {
            font-size: 20px;
        }

        .header nav a {
            padding: 6px 10px;
            font-size: 14px;
        }
    }
</style>