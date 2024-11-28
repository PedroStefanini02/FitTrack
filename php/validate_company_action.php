<?php
session_start();

// Verifica se o usuário está logado e tem permissão
if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Acesso negado
    echo json_encode(["error" => "Acesso negado. Usuário não autenticado."]);
    exit();
}

// Configuração do banco de dados
$localhost = "localhost";
$user = "root";
$passw = "";
$banco = "FitTrack";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userID = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
    $action = $_POST['action'];

    try {
        $pdo = new PDO("mysql:dbname=" . $banco . ";host=" . $localhost, $user, $passw);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($action === "approve") {
            // Aprova a empresa (1 = aprovado)
            $stmt = $pdo->prepare("UPDATE users SET is_validated = 1 WHERE user_ID = :userID");
            $stmt->bindParam(':userID', $userID);
            $stmt->execute();

            echo json_encode(["success" => true, "message" => "Empresa aprovada com sucesso!"]);
        } elseif ($action === "reject") {
            // Rejeita a empresa (2 = rejeitado)
            $stmt = $pdo->prepare("UPDATE users SET is_validated = 2 WHERE user_ID = :userID");
            $stmt->bindParam(':userID', $userID);
            $stmt->execute();

            echo json_encode(["success" => true, "message" => "Empresa rejeitada com sucesso!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Ação inválida."]);
        }
    } catch (PDOException $e) {
        http_response_code(500); // Erro interno do servidor
        echo json_encode(["success" => false, "error" => "Erro de conexão: " . $e->getMessage()]);
    }
} else {
    http_response_code(405); // Método não permitido
    echo json_encode(["error" => "Método não permitido."]);
}
