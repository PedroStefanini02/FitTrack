<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(["error" => "Acesso negado. Usuário não autenticado."]);
    exit();
}

// Configuração do banco de dados
$localhost = "localhost";
$user = "root";
$passw = "";
$banco = "FitTrack";

try {
    $pdo = new PDO("mysql:dbname=" . $banco . ";host=" . $localhost, $user, $passw);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta empresas pendentes
    $stmt = $pdo->prepare("SELECT user_ID, user_name, email, cnpj, description ,`is_validated` FROM users WHERE cnpj IS NOT NULL AND is_validated IS NULL OR is_validated = 0 AND `description` IS NOT NULL AND `is_validated` NOT IN (1,2)");
    $stmt->execute();
    $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retorna os dados como JSON
    header('Content-Type: application/json');
    echo json_encode($companies);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro de conexão: " . $e->getMessage()]);
}
