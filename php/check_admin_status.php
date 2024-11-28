<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['is_admin' => false]);
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

    // Verifica o status de administrador
    $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['is_admin'] == 1) { // Verifica se é admin
        echo json_encode(['is_admin' => true]);
    } else {
        echo json_encode(['is_admin' => false]);
    }
} catch (PDOException $e) {
    echo json_encode(['is_admin' => false, 'error' => $e->getMessage()]);
}
