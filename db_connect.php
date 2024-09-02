<?php
// db_connect.php

$servername = "localhost";
$username = "root";
$password = "";
$database = "bearny";

// Criando a conexão
$conn = new mysqli($servername, $username, $password, $database);

// Verificando a conexão
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

