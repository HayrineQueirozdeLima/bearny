<!-- reports.php -->
<?php
session_start();
include 'db_connect.php';
include 'functions.php';  // Inclui as funções de cálculo

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Recupera o ID do usuário logado
$user_id = $_SESSION['user_id'];

// Consulta para recuperar os registros do perfil do usuário
$sql = "SELECT * FROM user_profile WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - Bearny</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Relatórios de Dados do Usuário</h2>
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Idade</th>
                    <th>Peso</th>
                    <th>Altura</th>
                    <th>IMC</th>
                    <th>TMB</th>
                    <th>Déficit Calórico</th>
                    <th>Superávit Calórico</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <?php
                    // Calcula a idade
                    $age = (new DateTime())->diff(new DateTime($row['birthdate']))->y;

                    // Calcula IMC
                    $imc = calculateIMC($row['weight'], $row['height']);

                    // Calcula TMB
                    $tmb = calculateTMB($row['weight'], $row['height'], $age, $row['sex'],  $row['activity_level']);


                    // Calcula Déficit e Superávit Calórico
                    $deficit_calories = calculateDeficitCalories($tmb);
                    $surplus_calories = calculateSurplusCalories($tmb);
                    ?>
                    <tr>
                        <td><?= $row['created_at'] ?></td>
                        <td><?= $age ?> anos</td>
                        <td><?= $row['weight'] ?> kg</td>
                        <td><?= $row['height'] ?> cm</td>
                        <td><?= number_format($imc, 2) ?></td>
                        <td><?= number_format($tmb, 2) ?> kcal</td>
                        <td><?= number_format($deficit_calories, 2) ?> kcal</td>
                        <td><?= number_format($surplus_calories, 2) ?> kcal</td>
                        <td>
                            <a href="edit_record.php?id=<?= $row['id'] ?>">Editar</a> |
                            <a href="delete_record.php?id=<?= $row['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir este registro?');">Excluir</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="dashboard.php">Voltar ao Dashboard</a>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
