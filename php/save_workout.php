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
        echo "Você precisa estar logado para salvar o treino.";
        exit();
    }

    $userId = $_SESSION['user_id']; // Obtém o user_id da sessão
    $day = $_POST['dia'];
    $muscle = $_POST['musculo'];
    $exercise = $_POST['exercicio'];
    $sets = $_POST['series'];
    $reps = $_POST['repeticoes'];

    $stmt = $pdo->prepare("INSERT INTO workout (user_id, day, muscle, exercise, sets, reps) VALUES (:user_id, :day, :muscle, :exercise, :sets, :reps)");
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':day', $day);
    $stmt->bindParam(':muscle', $muscle);
    $stmt->bindParam(':exercise', $exercise);
    $stmt->bindParam(':sets', $sets);
    $stmt->bindParam(':reps', $reps);
    $stmt->execute();

    echo "Workout saved successfully!";
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>
