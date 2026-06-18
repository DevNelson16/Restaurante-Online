<?php
require '../includes/db.php';
require '../includes/functions.php';


// Buscar produtos
$produtos = $pdo->query("SELECT * FROM produtos ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Produtos</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
    body {
        font-family: 'Inter', sans-serif;
        margin:0;
        padding:0;
        background:#f4f6f9;
        color:#333;
    }
    * { box-sizing:border-box; }

    /* HEADER */
    .header {
        background:#0a7cff;
        padding:10px 25px;
        display:flex;
        align-items:center;
        justify-content:space-between;
        color:#fff;
        box-shadow:0 4px 12px rgba(0,0,0,0.15);
        flex-wrap:wrap;
    }
    .header .logo { height:40px; flex-shrink:0; }
    .header > div { display:flex; align-items:center; gap:15px; }

    /* CONTAINER */
    .container { max-width:1300px; margin:30px auto; padding:0 25px; }

    h2 {
        font-size:32px;
        color:#0a7cff;
        font-weight:700;
        margin-bottom:30px;
    }

    /* FORMULÁRIO ADICIONAR PRODUTO */
    .form-add {
        background:#fff;
        padding:20px;
        border-radius:14px;
        display:flex;
        gap:15px;
        flex-wrap:wrap;
        margin-bottom:30px;
        box-shadow:0 8px 20px rgba(0,0,0,0.08);
        transition:0.3s;
    }
    .form-add:hover { box-shadow:0 12px 28px rgba(0,0,0,0.12); }

    .form-add input {
        padding:12px;
        flex:1;
        border:1px solid #cdd5e0;
        border-radius:12px;
        transition:0.3s;
        background:#f9fbff;
    }
    .form-add input:focus {
        border-color:#0a7cff;
        background:#fff;
        outline:none;
        box-shadow:0 0 0 3px rgba(10,124,255,0.15);
    }

    .form-add button {
        background:linear-gradient(135deg, #0a7cff, #0056b3);
        color:#fff;
        border:none;
        padding:12px 25px;
        border-radius:12px;
        cursor:pointer;
        font-weight:600;
        transition:0.3s;
        box-shadow:0 4px 14px rgba(0,0,0,0.1);
    }
    .form-add button:hover {
        background:linear-gradient(135deg, #0563d3, #003f7f);
        transform:translateY(-2px);
        box-shadow:0 6px 18px rgba(0,0,0,0.15);
    }

    /* TABELA PRODUTOS */
    table {
        width:100%;
        border-collapse:collapse;
        background:#fff;
        border-radius:14px;
        overflow:hidden;
        box-shadow:0 12px 28px rgba(0,0,0,0.08);
        transition:0.3s;
    }
    table:hover { box-shadow:0 14px 32px rgba(0,0,0,0.12); }

    th {
        background:linear-gradient(135deg,#0a7cff,#0056b3);
        color:#fff;
        padding:16px;
        font-size:15px;
        text-transform:uppercase;
        letter-spacing:0.5px;
    }

    td {
        padding:14px 12px;
        font-size:15px;
        border-bottom:1px solid #e5e9f2;
    }

    tr:hover { background:#f1f7ff; }

    td input {
        padding:8px;
        width:100%;
        border-radius:10px;
        border:1px solid #cdd5e0;
        background:#f9fbff;
        transition:0.3s;
    }
    td input:focus {
        border-color:#0a7cff;
        box-shadow:0 0 0 3px rgba(10,124,255,0.12);
        outline:none;
        background:#fff;
    }

    /* BOTÕES DA TABELA */
    .btn-save, .btn-remove {
        padding:8px 14px;
        border:none;
        border-radius:10px;
        color:#fff;
        cursor:pointer;
        font-weight:600;
        transition:0.3s;
        margin-right:5px;
    }
    .btn-save {
        background:#28a745;
        box-shadow:0 4px 12px rgba(40,167,69,0.2);
    }
    .btn-save:hover {
        background:#1e7e34;
        transform:translateY(-2px);
        box-shadow:0 6px 16px rgba(40,167,69,0.3);
    }
    .btn-remove {
        background:#dc3545;
        box-shadow:0 4px 12px rgba(220,53,69,0.2);
    }
    .btn-remove:hover {
        background:#b51f2d;
        transform:translateY(-2px);
        box-shadow:0 6px 16px rgba(220,53,69,0.3);
    }

    /* RESPONSIVO */
    @media(max-width:1024px){
        .form-add { gap:12px; }
        .form-add input, .form-add button { font-size:15px; padding:10px 12px; }
    }
    @media(max-width:768px){
        .form-add { flex-direction:column; }
        .form-add button { width:100%; }
        table, thead, tbody, th, td, tr { display:block; }
        thead tr { display:none; }
        tr { margin-bottom:15px; border-bottom:1px solid #ddd; }
        td { text-align:right; padding-left:50%; position:relative; }
        td::before {
            content: attr(data-label);
            position:absolute;
            left:15px;
            width:45%;
            text-align:left;
            font-weight:600;
            color:#0a7cff;
        }
        td input { width:90%; }
    }
</style>

    
</head>
<body>
<?php include 'header.php'; ?>
<div class="container">
    <h2>Gestão de Produtos</h2>

    <!-- FORMULÁRIO PARA ADICIONAR PRODUTO -->
    <div class="form-add">
        <input type="text" id="nome" placeholder="Nome do Produto">
        <input type="number" id="preco" step="0.01" placeholder="Preço (€)">
        <input type="number" id="quantidade" placeholder="Quantidade inicial">
        <button onclick="addProduct()">Adicionar Produto</button>
    </div>

    <!-- TABELA -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Produto</th>
                <th>Preço (€)</th>
                <th>Quantidade</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($produtos as $p): ?>
            <tr id="produto-<?= $p['id'] ?>">
                <td><?= $p['id'] ?></td>
                <td><input type="text" id="nome-<?= $p['id'] ?>" value="<?= htmlspecialchars($p['nome']) ?>"></td>
                <td><input type="number" step="0.01" id="preco-<?= $p['id'] ?>" value="<?= $p['preco'] ?>"></td>
                <td><input type="number" id="quant-<?= $p['id'] ?>" value="<?= $p['quantidade'] ?>"></td>
                <td>
                    <button class="btn-save" onclick="saveProduct(<?= $p['id'] ?>)">Guardar</button>
                    <button class="btn-remove" onclick="deleteProduct(<?= $p['id'] ?>)">Remover</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>

<script>
// AJAX genérico
async function ajax(data) {
    const res = await fetch("../api/estoque_api.php", {
        method: "POST",
        headers: {"Content-Type":"application/x-www-form-urlencoded"},
        body: new URLSearchParams(data)
    });
    return res.json();
}

// ADICIONAR PRODUTO
function addProduct(){
    let nome = document.querySelector("#nome").value.trim();
    let preco = document.querySelector("#preco").value;
    let quantidade = document.querySelector("#quantidade").value;

    if(!nome || !preco || !quantidade){
        alert("Preencha todos os campos!");
        return;
    }

    ajax({action:"add", nome, preco, quantidade})
    .then(res=>{
        if(res.success){
            alert("Produto adicionado!");
            location.reload();
        } else alert("Erro: "+res.message);
    });
}

// EDITAR PRODUTO
function saveProduct(id){
    let nome = document.querySelector("#nome-" + id).value;
    let preco = document.querySelector("#preco-" + id).value;
    let quantidade = document.querySelector("#quant-" + id).value;

    ajax({
        action:"updateFull",
        id,
        nome,
        preco,
        quantidade
    }).then(res=>{
        if(res.success){
            alert("Produto atualizado com sucesso!");
        } else alert("Erro: " + res.message);
    });
}

// REMOVER PRODUTO
function deleteProduct(id){
    if(!confirm("Deseja remover este produto?")) return;

    ajax({action:"delete", id})
    .then(res=>{
        if(res.success){
            document.querySelector("#produto-"+id).remove();
            alert("Removido!");
        } else alert("Erro: " + res.message);
    });
}
</script>

</body>
</html>
