<?php
require '../includes/db.php';
session_start();

// Se já estiver logado, redireciona para o dashboard
if (isset($_SESSION['usuario_id']) && $_SESSION['nivel_acesso'] === 'admin') {
    header("Location: dashboard.php");
    exit;
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Busca usuário pelo email
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Verifica senha e se é admin
    if ($user && password_verify($senha, $user['senha'])) {
        $_SESSION['usuario_id'] = $user['id'];       // id do usuário
        $_SESSION['nivel_acesso'] = $user['role'];  // deve ser 'admin' para acessar o painel

        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Email ou senha incorretos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f5f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-form {
            background: #fff;
            padding: 40px 30px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            transition: 0.3s;
        }

        .login-form:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .login-form h2 {
            margin-bottom: 30px;
            color: #0a7cff;
            font-size: 28px;
        }

        .login-form input {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 16px;
            transition: 0.3s;
        }

        .login-form input:focus {
            border-color: #0a7cff;
            outline: none;
        }

        .login-form button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            background: #0a7cff;
            color: #fff;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.3s;
        }

        .login-form button:hover {
            background: #0066d6;
        }

        .login-form p.error {
            color: #ff4d4f;
            margin-bottom: 20px;
            font-weight: 500;
        }

        @media(max-width:480px) {
            .login-form {
                padding: 30px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="login-form">
        <h2>👤 Login Administrador</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit" name="login">Entrar</button>
        </form>
    </div>
</body>

</html>