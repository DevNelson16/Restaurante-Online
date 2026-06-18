<?php
$pratos = require '../data/pratos_populares.php';

if ($_POST) {
    file_put_contents(
        '../data/pratos_populares.php',
        "<?php\nreturn " . var_export($_POST['pratos'], true) . ";"
    );
    header("Location: pratos_populares.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Admin • Pratos Populares</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
            box-sizing: border-box;
            font-family: 'Inter', system-ui, sans-serif;
        }

        body {
            margin: 0;
            background: #f4f6fb;
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            padding: 30px;
        }

        h1 {
            margin-bottom: 10px;
            color: #0a7cff;
        }

        .subtitle {
            color: #666;
            margin-bottom: 30px;
        }

        .card {
            background: #fff;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
            position: relative;
        }

        .card h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #0a7cff;
        }

        label {
            display: block;
            font-weight: 500;
            margin-bottom: 6px;
        }

        input,
        textarea {
            width: 100%;
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid #ddd;
            margin-bottom: 15px;
            font-size: .95rem;
            transition: .3s;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: #0a7cff;
            box-shadow: 0 0 0 3px rgba(10, 124, 255, .15);
        }

        .price {
            max-width: 200px;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 30px;
            gap: 10px;
            flex-wrap: wrap;
        }

        button {
            background: linear-gradient(135deg, #0a7cff, #0047ab);
            color: #fff;
            border: none;
            padding: 14px 30px;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(10, 124, 255, .35);
            transition: .3s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(10, 124, 255, .45);
        }

        #addPrato {
            margin-right: 15px;
            background: #fff;
            color: #0a7cff;
            border: 2px dashed #0a7cff;
        }

        .removeBtn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #ff4d4d;
            border: none;
            border-radius: 8px;
            color: #fff;
            padding: 6px 12px;
            cursor: pointer;
            font-size: 0.85rem;
        }

        .removeBtn:hover {
            background: #e60000;
        }

        footer {
            text-align: center;
            font-size: .85rem;
            color: #888;
            margin-top: 40px;
        }

        @media (max-width:600px) {
            .container {
                margin: 20px;
                padding: 20px;
            }

            .actions {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <h1>Pratos Populares</h1>
        <p class="subtitle">Edite os pratos exibidos na página inicial</p>

        <form method="post">
            <?php foreach ($pratos as $i => $p): ?>
                <div class="card">
                    <h3>Prato <?= $i + 1 ?></h3>

                    <label>Nome do prato</label>
                    <input
                        name="pratos[<?= $i ?>][nome]"
                        value="<?= htmlspecialchars($p['nome']) ?>"
                        required>

                    <label>Descrição</label>
                    <textarea
                        name="pratos[<?= $i ?>][descricao]"
                        required><?= htmlspecialchars($p['descricao']) ?></textarea>

                    <label>Preço (€)</label>
                    <input
                        class="price"
                        type="number"
                        step="0.01"
                        name="pratos[<?= $i ?>][preco]"
                        value="<?= htmlspecialchars($p['preco']) ?>"
                        required>

                    <!-- Botão remover prato -->
                    <button type="button" class="removeBtn">Remover</button>
                </div>
            <?php endforeach; ?>

            <div class="actions">
                <button type="button" id="addPrato">+ Adicionar prato</button>
                <button type="submit">Guardar alterações</button>
            </div>
        </form>

        <footer>
            Painel administrativo • Restaurante Moderno
        </footer>
    </div>

    <script>
        // Adicionar prato
        document.getElementById('addPrato').addEventListener('click', () => {
            const container = document.querySelector('form');
            const total = document.querySelectorAll('.card').length + 1;

            const card = document.createElement('div');
            card.className = 'card';

            card.innerHTML = `
        <h3>Prato ${total}</h3>

        <label>Nome do prato</label>
        <input name="pratos[${total - 1}][nome]" required>

        <label>Descrição</label>
        <textarea name="pratos[${total - 1}][descricao]" required></textarea>

        <label>Preço (€)</label>
        <input class="price" type="number" step="0.01" name="pratos[${total - 1}][preco]" required>

        <button type="button" class="removeBtn">Remover</button>
    `;

            container.insertBefore(card, document.querySelector('.actions'));
        });

        // Remover prato
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('removeBtn')) {
                const card = e.target.closest('.card');
                card.remove();
                // Atualiza os nomes dos inputs para manter o índice correto
                const cards = document.querySelectorAll('.card');
                cards.forEach((c, i) => {
                    c.querySelector('h3').textContent = 'Prato ' + (i + 1);
                    c.querySelectorAll('input, textarea').forEach(inp => {
                        const name = inp.getAttribute('name');
                        const newName = name.replace(/\d+/, i);
                        inp.setAttribute('name', newName);
                    });
                });
            }
        });
    </script>

</body>

</html>