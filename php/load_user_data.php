<?php
session_start(); // Inicia a sessão

$localhost = "localhost";
$user = "root";
$passw = "";
$banco = "FitTrack";

try {
    $pdo = new PDO("mysql:dbname=".$banco.";host=".$localhost, $user, $passw);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verifica se o usuário está logado
    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            'error' => 'Você precisa estar logado para carregar os dados.'
        ]);
        exit();
    }

    $userId = $_SESSION['user_id']; // Obtém o user_id da sessão

    // Carregar dados de progresso
    $stmt = $pdo->prepare("SELECT * FROM progress WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $progressData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Carregar dados de treino
    $stmt = $pdo->prepare("SELECT * FROM workout WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $workoutData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Carregar dados de supervisão
    $stmt = $pdo->prepare("SELECT * FROM supervision WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $supervisionData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'progress' => $progressData,
        'workout' => $workoutData,
        'supervision' => $supervisionData
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'error' => 'Connection failed: ' . $e->getMessage()
    ]);
}
?>
