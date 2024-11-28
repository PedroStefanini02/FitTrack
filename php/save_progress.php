<?php
session_start();

$localhost = "localhost";
$user = "root";
$passw = "";
$banco = "FitTrack";

try {
    $pdo = new PDO("mysql:dbname=" . $banco . ";host=" . $localhost, $user, $passw);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verifica se o usuário está logado
    if (!isset($_SESSION['user_id'])) {
        echo "Você precisa estar logado para salvar o progresso.";
        exit();
    }

    $userId = $_SESSION['user_id']; // Obtém o user_id da sessão

    // Dados enviados via AJAX
    $date = $_POST['data'];
    $weight = $_POST['peso'];
    $bodyFat = $_POST['percentualGordura'];
    $muscleMass = $_POST['musculatura'];

    // Insere os dados no banco de dados
    $stmt = $pdo->prepare("INSERT INTO progress (user_id, date, weight, body_fat, muscle_mass) VALUES (:user_id, :date, :weight, :body_fat, :muscle_mass)");
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':weight', $weight);
    $stmt->bindParam(':body_fat', $bodyFat);
    $stmt->bindParam(':muscle_mass', $muscleMass);

    if ($stmt->execute()) {
        echo "Progress saved successfully!";
    } else {
        echo "Erro ao salvar o progresso.";
    }
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
