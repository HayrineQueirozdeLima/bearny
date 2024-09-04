<!-- delete_record.php -->
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

// Exclui o registro do banco de dados
$stmt = $conn->prepare("DELETE FROM user_profile WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $record_id, $user_id);

if ($stmt->execute()) {
    // Redireciona para o dashboard após a exclusão
    header("Location: dashboard.php?message=Registro excluído com sucesso");
    exit();
} else {
    echo "Ocorreu um erro ao tentar excluir o registro. Tente novamente.";
}

$stmt->close();
$conn->close();
?>
