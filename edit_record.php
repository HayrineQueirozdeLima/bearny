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

$user_id = $_SESSION["user_id"];
$record_id = $_GET['id'];

// Busca o registro para edição
$stmt = $conn->prepare("SELECT weight, height, activity_level, gender FROM user_profile WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $record_id, $user_id);
$stmt->execute();
$stmt->bind_result($weight, $height, $activity_level, $gender);
$stmt->fetch();
$stmt->close();

$error = "";
$success = "";

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_weight = $_POST["weight"];
    $new_height = $_POST["height"];
    $new_activity_level = $_POST["activity_level"];
    $new_gender = $_POST["gender"];

    // Validação básica
    if (empty($new_weight) || empty($new_height) || empty($new_activity_level) || empty($new_gender)) {
        $error = "Por favor, preencha todos os campos.";
    } else {
        // Atualiza o registro no banco de dados
        $stmt = $conn->prepare("UPDATE user_profile SET weight = ?, height = ?, activity_level = ?, gender = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("dssiii", $new_weight, $new_height, $new_activity_level, $new_gender, $record_id, $user_id);
        
        if ($stmt->execute()) {
            $success = "Registro atualizado com sucesso!";
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Ocorreu um erro ao atualizar o registro. Tente novamente.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Registro - Bearny</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Editar Registro</h2>
        <?php
        if (!empty($error)) {
            echo '<div class="error">'.$error.'</div>';
        }
        if (!empty($success)) {
            echo '<div class="success">'.$success.'</div>';
        }
        ?>
        <form action="edit_record.php?id=<?php echo $record_id; ?>" method="POST">
            <label for="weight">Peso (kg):</label>
            <input type="number" name="weight" id="weight" value="<?php echo htmlspecialchars($weight); ?>" required>

            <label for="height">Altura (cm):</label>
            <input type="number" name="height" id="height" value="<?php echo htmlspecialchars($height); ?>" required>

            <label for="activity_level">Nível de Atividade:</label>
            <select name="activity_level" id="activity_level" required>
                <option value="sedentario" <?php if ($activity_level == 'sedentario') echo 'selected'; ?>>Sedentário</option>
                <option value="leve" <?php if ($activity_level == 'leve') echo 'selected'; ?>>Leve</option>
                <option value="moderado" <?php if ($activity_level == 'moderado') echo 'selected'; ?>>Moderado</option>
                <option value="ativo" <?php if ($activity_level == 'ativo') echo 'selected'; ?>>Ativo</option>
                <option value="muito_ativo" <?php if ($activity_level == 'muito_ativo') echo 'selected'; ?>>Muito Ativo</option>
            </select>

            <label for="gender">Gênero:</label>
            <select name="gender" id="gender" required>
                <option value="masculino" <?php if ($gender == 'masculino') echo 'selected'; ?>>Masculino</option>
                <option value="feminino" <?php if ($gender == 'feminino') echo 'selected'; ?>>Feminino</option>
            </select>

            <button type="submit">Salvar Alterações</button>
        </form>
        <a href="dashboard.php">Voltar</a>
    </div>
</body>
</html>
