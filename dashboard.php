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

// Obtém o ID do usuário da sessão
$user_id = $_SESSION["user_id"];

// Busca os dados do perfil do usuário
$stmt = $conn->prepare("SELECT name, birthdate, weight, height, activity_level, sex FROM user_profile WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $birthdate, $weight, $height, $activity_level, $sex);
$stmt->fetch();
$stmt->close();

// Calcula o IMC
$height_m = $height / 100; // Converte altura para metros
$imc = $weight / ($height_m * $height_m);

// Calcula a TMB usando a fórmula de Mifflin-St Jeor
if ($sex == 'Masculino') {
    $tmb = 10 * $weight + 6.25 * $height - 5 * (date("Y") - date("Y", strtotime($birthdate))) + 5;
} else {
    $tmb = 10 * $weight + 6.25 * $height - 5 * (date("Y") - date("Y", strtotime($birthdate))) - 161;
}

// Ajusta a TMB para o nível de atividade
switch ($activity_level) {
    case "Sedentário":
        $tmb *= 1.2;
        break;
    case "Levemente Ativo":
        $tmb *= 1.375;
        break;
    case "Moderadamente Ativo":
        $tmb *= 1.55;
        break;
    case "Muito Ativo":
        $tmb *= 1.725;
        break;
    case "Extremamente Ativo":
        $tmb *= 1.9;
        break;
}

// Calcula as calorias para déficit e superávit calórico
$calorias_deficit = $tmb - 500; // Deficit calórico recomendado
$calorias_superavit = $tmb + 500; // Superavit calórico recomendado

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Bearny</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Bem-vindo, <?php echo htmlspecialchars($name); ?>!</h2>

        <div class="profile-info">
            <h3>Suas Informações</h3>
            <p><strong>Nome:</strong> <?php echo htmlspecialchars($name); ?></p>
            <p><strong>Data de Nascimento:</strong> <?php echo date("d/m/Y", strtotime($birthdate)); ?></p>
            <p><strong>Peso:</strong> <?php echo htmlspecialchars($weight); ?> kg</p>
            <p><strong>Altura:</strong> <?php echo htmlspecialchars($height); ?> cm</p>
            <p><strong>Nível de Atividade Física:</strong> <?php echo htmlspecialchars($activity_level); ?></p>
            <p><strong>Sexo:</strong> <?php echo htmlspecialchars($sex); ?></p>
        </div>

        <div class="calculation-results">
            <h3>Seus Cálculos</h3>
            <p><strong>IMC Atual:</strong> <?php echo number_format($imc, 2); ?></p>
            <p><strong>TMB:</strong> <?php echo number_format($tmb, 2); ?> calorias/dia</p>
            <p><strong>Calorias para Déficit Calórico:</strong> <?php echo number_format($calorias_deficit, 2); ?> calorias/dia</p>
            <p><strong>Calorias para Superávit Calórico:</strong> <?php echo number_format($calorias_superavit, 2); ?> calorias/dia</p>
        </div>

        <a href="profile.php" class="btn">Editar Perfil</a>
        <a href="logout.php" class="btn">Sair</a>
    </div>
</body>
</html>
