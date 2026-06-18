<?php
require '../includes/db.php';
require '../includes/functions.php';

// ADICIONAR CATEGORIA
if (isset($_POST['add'])) {
    $nome = $_POST['nome'];
    $stmt = $pdo->prepare("INSERT INTO categorias (nome) VALUES (?)");
    $stmt->execute([$nome]);
}

// APAGAR CATEGORIA
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM categorias WHERE id=?")->execute([$id]);
}

// BUSCAR TODAS CATEGORIAS
$categorias = $pdo->query("SELECT * FROM categorias ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Categorias</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
    /* RESET & FONT */
    * { margin:0; padding:0; box-sizing:border-box; }
    body { 
        font-family:'Inter', sans-serif; 
        background:#f4f6f9; 
        color:#333; 
    }

    /* CONTAINER */
    .container { 
        max-width: 1300px; 
        margin: 50px auto; 
        padding: 0 20px; 
    }

    h2 { 
        margin-bottom: 30px; 
        color:#0a7cff; 
        font-size:32px; 
        font-weight:700; 
    }

    /* FORMULÁRIO MODERNO */
    .form-box {
        display: flex;
        gap: 12px;
        margin-bottom: 40px;
        flex-wrap: wrap;
    }

    .form-box input[type="text"] {
        flex: 1;
        padding: 14px 16px;
        border-radius: 12px;
        border: 1px solid #ccc;
        font-size:16px;
        transition:0.3s;
        background:#fff;
        box-shadow:0 2px 8px rgba(0,0,0,0.05);
    }

    .form-box input[type="text"]:focus {
        border-color:#0a7cff;
        box-shadow:0 0 6px rgba(10,124,255,0.3);
        outline:none;
    }

    .form-box button {
        padding:14px 22px;
        background:linear-gradient(135deg, #0a7cff, #0056b3);
        color:#fff;
        border:none;
        border-radius:12px;
        cursor:pointer;
        font-weight:600;
        transition:0.3s;
        box-shadow:0 4px 14px rgba(0,0,0,0.1);
    }

    .form-box button:hover { 
        background:linear-gradient(135deg, #065abf, #003f7f);
        transform: translateY(-2px);
        box-shadow:0 6px 18px rgba(0,0,0,0.15);
    }

    /* CARDS DE CATEGORIAS */
    .cards { 
        display:flex; 
        flex-wrap:wrap; 
        gap:20px; 
    }

    .card {
        background:#fff;
        border-radius:16px;
        box-shadow:0 6px 20px rgba(0,0,0,0.08);
        padding:22px;
        flex:1 1 220px;
        display:flex;
        flex-direction:column;
        justify-content:space-between;
        transition:0.3s;
    }

    .card:hover { 
        transform:translateY(-5px); 
        box-shadow:0 12px 28px rgba(0,0,0,0.12); 
    }

    .card h3 { 
        font-size:22px; 
        color:#0a7cff; 
        margin-bottom:12px; 
        font-weight:600;
    }

    .card p { 
        margin-bottom:18px; 
        font-size:16px; 
        color:#555;
    }

    .card a {
        display:inline-block;
        padding:12px 16px;
        background:#ff4d4f;
        color:#fff;
        border-radius:10px;
        text-decoration:none;
        font-weight:500;
        text-align:center;
        transition:0.3s;
        box-shadow:0 4px 12px rgba(0,0,0,0.08);
    }

    .card a:hover { 
        background:#d9363e; 
        transform: translateY(-2px);
        box-shadow:0 6px 16px rgba(0,0,0,0.12);
    }

    /* RESPONSIVO */
    @media(max-width:1024px){
        .form-box { gap:10px; }
        .form-box input[type="text"], .form-box button { font-size:15px; padding:12px 16px; }
        .card { flex:1 1 45%; }
    }

    @media(max-width:768px){
        .form-box { flex-direction:column; }
        .form-box button { width:100%; }
        .cards { flex-direction:column; }
        .card { flex:1 1 100%; }
    }
</style>

</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <h2>📁 Gestão de Categorias</h2>

    <!-- FORMULÁRIO -->
    <form method="POST" class="form-box">
        <input type="text" name="nome" placeholder="Nome da categoria" required>
        <button type="submit" name="add">Adicionar Categoria</button>
    </form>

    <!-- CARDS -->
    <div class="cards">
        <?php foreach($categorias as $c): ?>
        <div class="card">
            <h3><?= htmlspecialchars($c['nome']) ?></h3>
            <p>ID: <?= $c['id'] ?></p>
            <a href="?delete=<?= $c['id'] ?>" onclick="return confirm('Tem certeza que deseja remover esta categoria?')">Apagar</a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
