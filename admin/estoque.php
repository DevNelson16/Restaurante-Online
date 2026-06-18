<?php
require '../includes/db.php';
require '../includes/functions.php';
check_login();

$produtos = $pdo->query("SELECT * FROM produtos ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Estoque Moderna</title>

    <style>
        body {font-family: 'Inter', sans-serif; margin:0; padding:0; background:#f4f6f9;}
        * {box-sizing: border-box;}

        .header {background:#0a7cff; padding:15px 30px; display:flex; justify-content:space-between; color:#fff;}
        .logo {height:50px;}
        .nav ul {display:flex; gap:20px; list-style:none; margin:0; padding:0;}
        .nav a {color:#fff; text-decoration:none; font-weight:600;}

        .container {max-width:1100px; margin:30px auto; padding:20px;}

        h2 {color:#0a7cff; margin-bottom:20px;}

        .form-add {background:#fff; padding:15px; border-radius:12px; display:flex; gap:10px; margin-bottom:20px;}
        .form-add input {flex:1; padding:10px; border:1px solid #ccc; border-radius:8px;}
        .form-add button {background:#0a7cff; padding:10px 20px; color:#fff; border:none; border-radius:8px;}

        table {width:100%; border-collapse:collapse; background:#fff; border-radius:12px; overflow:hidden;}
        th {background:#0a7cff; color:#fff; padding:12px;}
        td {padding:12px;}
        tr:hover {background:#eef4ff;}

        .btn-save {background:#28a745; padding:6px 12px; border:none; color:#fff; border-radius:6px;}
        .btn-remove {background:#dc3545; padding:6px 12px; border:none; color:#fff; border-radius:6px;}
    </style>
</head>
<body>

<header class="header">
    <img src="../assets/img/logo.png" class="logo">
    <nav class="nav">
        <ul>
            <li><a href="index.php">Início</a></li>
            <li><a href="estoque.php" class="active">Estoque</a></li>
        </ul>
    </nav>
</header>

<div class="container">
    <h2>Gestão de Estoque</h2>

    <div class="form-add">
        <input type="text" id="nome" placeholder="Nome do Produto">
        <input type="number" step="0.01" id="preco" placeholder="Preço (€)">
        <input type="number" id="quantidade" placeholder="Quantidade">
        <button onclick="addProduct()">Adicionar</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Produto</th>
                <th>Preço</th>
                <th>Quantidade</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="produtos-list">
            <?php foreach($produtos as $p): ?>
            <tr id="produto-<?= $p['id'] ?>">
                <td><?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['nome']) ?></td>
                <td><?= number_format($p['preco'], 2, ',', '.') ?></td>
                <td id="estoque-<?= $p['id'] ?>"><?= $p['quantidade'] ?></td>
                <td>
                    <input type="number" id="input-<?= $p['id'] ?>" value="<?= $p['quantidade'] ?>">
                    <button class="btn-save" onclick="updateStock(<?= $p['id'] ?>)">Salvar</button>
                    <button class="btn-remove" onclick="deleteProduct(<?= $p['id'] ?>)">Remover</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
async function ajax(data) {
    const res = await fetch("../api/estoque_api.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: new URLSearchParams(data)
    });
    return res.json();
}

function updateStock(id) {
    let quantidade = document.querySelector("#input-" + id).value;

    ajax({ action: "update", id, quantidade })
        .then(res => {
            if(res.success){
                document.querySelector("#estoque-" + id).innerText = quantidade;
                alert("Estoque atualizado!");
            } else {
                alert(res.message);
            }
        });
}

function deleteProduct(id){
    if(!confirm("Confirmar remoção?")) return;

    ajax({ action:"delete", id })
    .then(res=>{
        if(res.success){
            document.querySelector("#produto-"+id).remove();
        } else alert(res.message);
    });
}

function addProduct(){
    let nome = nome = document.querySelector("#nome").value.trim();
    let preco = document.querySelector("#preco").value;
    let quantidade = document.querySelector("#quantidade").value;

    ajax({action:"add", nome, preco, quantidade})
    .then(res=>{
        if(res.success){
            alert("Produto adicionado!");
            location.reload();
        } else alert(res.message);
    });
}
</script>

</body>
</html>
