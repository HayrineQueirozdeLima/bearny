<!-- dashboard.php -->
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
    <link rel="stylesheet" href="css/styles_dash.css">
</head>
<body>
    <div class="dashboard-container">
        <?php if ($row): ?>
            <div class="dashboard-header">
                <?php if ($sex === 'Feminino'): ?>
                    <h2>Bem-vinda, <?php echo htmlspecialchars($name); ?>!</h2>
                <?php else: ?>
                    <h2>Bem-vindo, <?php echo htmlspecialchars($name); ?>!</h2>
                <?php endif; ?>
            </div>
                
            <div class="dashboard-content">
                <div class="proile-info">
                    <h2>Seu Perfil</h2>
                    <p>Com <?= htmlspecialchars($age) ?> anos</p>
                    <p>Pesando <?= htmlspecialchars($weight) ?> kg</p>
                    <p>Medindo <?= htmlspecialchars($height) ?> cm de altura</p>
                    <p>Nível de atividade física: <?= htmlspecialchars($activity_level) ?></p>
                    <p>Seu Índice de Massa Corporal atual é <?= number_format($imc, 2) ?></p>
                    <p>Sua Taxa Metabólica Basal atual(quantidade de calorias que seu corpo gasta para se manter vivo) é <?= number_format($tmb, 2) ?> kcal por dia</p>
                    <p>Déficit Calórico de 500 calorias para perca de peso: <?= number_format($deficit_calories, 2) ?> kcal por dia</p>
                    <p>Superávit Calórico de 500 calorias para ganho de peso: <?= number_format($surplus_calories, 2) ?> kcal por dia</p>
                </div>
            </div>
        <?php else: ?>
            <p>Nenhum dado disponível.</p>
        <?php endif; ?>
        <div class="dashboard-actions">
            <a href="profile.php" class="btn">Editar Perfil</a>  
            <a href="reports.php" class="btn">Relatórios</a>
            <a href="logout.php" class="btn">Logout</a>
        </div>
    </div>
</body>
</html>