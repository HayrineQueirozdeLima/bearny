<?php
// Inicia a sessão
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Inclui a conexão com o banco de dados
include 'db_connect.php';

// Inicializa variáveis de erro e sucesso
$success = "";
$error = "";

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $birthdate = $_POST["birthdate"];
    $weight = floatval($_POST["weight"]);
    $height = floatval($_POST["height"]);
    $activity_level = $_POST["activity_level"];
    $sex = $_POST["sex"];
    $user_id = $_SESSION["user_id"];

    // Validação básica
    if (empty($name) || empty($birthdate) || empty($weight) || empty($height) || empty($activity_level) || empty($sex)) {
        $error = "Por favor, preencha todos os campos.";
    } else {
        // Verifica se o perfil já existe
        $stmt = $conn->prepare("SELECT id FROM user_profile WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Atualiza o perfil existente
            $stmt = $conn->prepare("UPDATE user_profile SET name = ?, birthdate = ?, weight = ?, height = ?, activity_level = ?, sex = ? WHERE user_id = ?");
            $stmt->bind_param("ssddssi", $name, $birthdate, $weight, $height, $activity_level, $sex, $user_id);
            $success = "Perfil atualizado com sucesso!";
        } else {
            // Insere o novo perfil no banco de dados
            $stmt = $conn->prepare("INSERT INTO user_profile (user_id, name, birthdate, weight, height, activity_level, sex) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issddss", $user_id, $name, $birthdate, $weight, $height, $activity_level, $sex);
            $success = "Perfil criado com sucesso!";
        }

        if (!$stmt->execute()) {
            $error = "Ocorreu um erro ao salvar o perfil. Tente novamente.";
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
    <title>Perfil - Bearny</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Preencha seu Perfil</h2>
        <?php
        if (!empty($error)) {
            echo '<div class="error">'.$error.'</div>';
        }
        if (!empty($success)) {
            echo '<div class="success">'.$success.'</div>';
        }
        ?>
        <form action="profile.php" method="POST">
            <label for="name">Nome:</label>
            <input type="text" name="name" id="name" required>

            <label for="birthdate">Data de Nascimento:</label>
            <input type="date" name="birthdate" id="birthdate" required>

            <label for="weight">Peso (kg):</label>
            <input type="number" step="0.1" name="weight" id="weight" required>

            <label for="height">Altura (cm):</label>
            <input type="number" step="0.1" name="height" id="height" required>

            <label for="activity_level">Nível de Atividade Física:</label>
            <select name="activity_level" id="activity_level" required>
                <option value="Sedentário">Sedentário</option>
                <option value="Levemente Ativo">Levemente Ativo</option>
                <option value="Moderadamente Ativo">Moderadamente Ativo</option>
                <option value="Muito Ativo">Muito Ativo</option>
                <option value="Extremamente Ativo">Extremamente Ativo</option>
            </select>

            <label for="sex">Sexo:</label>
            <select name="sex" id="sex" required>
                <option value="Masculino">Masculino</option>
                <option value="Feminino">Feminino</option>
            </select>

            <button type="submit">Salvar</button>
        </form>
    </div>
</body>
</html>
