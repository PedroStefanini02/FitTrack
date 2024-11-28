<?php
session_start();

// Configuração do banco de dados
$localhost = "localhost";
$user = "root";
$passw = "";
$banco = "FitTrack";

// Conectar ao banco de dados
$conn = new mysqli($localhost, $user, $passw, $banco);

// Verificar conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "not_logged_in", "message" => "Usuário não está logado."]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Consultar CNPJ e validação
$sql = "SELECT cnpj, is_validated FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo json_encode(["status" => "not_found", "message" => "Usuário não encontrado."]);
} elseif (empty($row['cnpj'])) {
    echo json_encode(["status" => "no_cnpj", "message" => "Apenas usuários com CNPJ podem acessar esta aba."]);
} elseif ($row['is_validated'] !== 1) {
    echo json_encode([
        "status" => "not_approved",
        "message" => "Sua empresa ainda não foi aprovada. Por favor, aguarde nosso feedback por email."
    ]);
} else {
    echo json_encode(["status" => "valid", "message" => "Acesso autorizado."]);
}

$conn->close();
