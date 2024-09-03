<?php
// Inicia a sessão
session_start();

// Inclui a conexão com o banco de dados
include 'db_connect.php';

// Inicializa variáveis de erro
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Validação básica
    if (empty($email) || empty($password)) {
        $error = "Por favor, preencha todos os campos.";
    } else {
        // Busca o usuário no banco de dados
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // Verifica se o usuário existe
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $hashed_password);
            $stmt->fetch();

            // Verifica a senha
            if (password_verify($password, $hashed_password)) {
                // Armazena dados na sessão
                $_SESSION["user_id"] = $id;

                // Verifica se o perfil do usuário está completo
                $stmt = $conn->prepare("SELECT COUNT(*) FROM user_profile WHERE user_id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->bind_result($profile_count);
                $stmt->fetch();
                $stmt->close();

                // Se o perfil estiver preenchido, redireciona para o dashboard, caso contrário, redireciona para o profile.php
                if ($profile_count > 0) {
                    header("Location: dashboard.php");
                } else {
                    header("Location: profile.php");
                }
                exit();
            } else {
                $error = "Senha incorreta.";
            }
        } else {
            $error = "Nenhuma conta encontrada com este e-mail.";
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bearny</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php
        if (!empty($error)) {
            echo '<div class="error">'.$error.'</div>';
        }
        ?>
        <form action="login.php" method="POST">
            <label for="email">E-mail:</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Senha:</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Entrar</button>
        </form>
        <p>Não tem uma conta? <a href="register.php">Registre-se aqui</a>.</p>
    </div>
</body>
</html>
