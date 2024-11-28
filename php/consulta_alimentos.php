<?php
// consulta_alimentos.php

// Configurações do banco de dados
$servername = "localhost";
$username = "root"; // Ajuste conforme necessário
$password = "";     // Ajuste conforme necessário
$dbname = "FitTrack"; // Nome do banco de dados

// Criação da conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Consultar todos os alimentos, incluindo a categoria
$sql = "SELECT nome, calorias_por_100g, proteinas_por_100g, carboidratos_por_100g, gorduras_por_100g, fibras_por_100g, categoria FROM alimentos";
$result = $conn->query($sql);

$alimentos = [];

if ($result->num_rows > 0) {
    // Guardar os resultados em um array
    while ($row = $result->fetch_assoc()) {
        $alimentos[] = $row;
    }
}

// Retornar dados em formato JSON
header('Content-Type: application/json');
echo json_encode($alimentos);

$conn->close();
