<?php
//inicia a sessão
session_start();
include 'db_connect.php';
include 'functions.php'; 

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Recupera o ID do usuário logado
$user_id = $_SESSION['user_id'];

// Consulta os dados do perfil do usuário
$sql = "SELECT * FROM user_profile WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Recupera os dados do perfil
    $name = $row['name'];
    $weight = $row['weight'];
    $height = $row['height'];
    $birthdate = $row['birthdate'];
    $sex = $row['sex'];
    $activity_level = $row['activity_level'];

    // Calcula a idade
    $birthdate = new DateTime($birthdate);
    $today = new DateTime('today');
    $age = $birthdate->diff($today)->y;

    // Calcula IMC
    $imc = calculateIMC($weight, $height);

    // Calcula TMB
    $tmb = calculateTMB($weight, $height, $age, $sex,  $activity_level);


    // Calcula Déficit e Superávit Calórico
    $deficit_calories = calculateDeficitCalories($tmb);
    $surplus_calories = calculateSurplusCalories($tmb);
} else {
    $row = null;  // ou redirecionar para uma página de erro
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Bearny</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
    <?php if ($row): ?>
            <?php if ($sex === 'Feminino'): ?>
                <h2>Bem-vinda, <?php echo htmlspecialchars($name); ?>!</h2>
            <?php else: ?>
                <h2>Bem-vindo, <?php echo htmlspecialchars($name); ?>!</h2>
            <?php endif; ?>
            <p>Idade: <?= htmlspecialchars($age) ?></p>
            <p>Peso: <?= htmlspecialchars($weight) ?> kg</p>
            <p>Altura: <?= htmlspecialchars($height) ?> cm</p>
            <p>IMC Atual: <?= number_format($imc, 2) ?></p>
            <p>TMB: <?= number_format($tmb, 2) ?> kcal</p>
            <p>Déficit Calórico: <?= number_format($deficit_calories, 2) ?> kcal</p>
            <p>Superávit Calórico: <?= number_format($surplus_calories, 2) ?> kcal</p>
        <?php else: ?>
            <p>Nenhum dado disponível.</p>
        <?php endif; ?>
        <a href="profile.php">Editar Perfil</a> | 
        <a href="reports.php">Relatórios</a>
    </div>
</body>
</html>