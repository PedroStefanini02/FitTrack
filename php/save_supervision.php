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
        echo "Você precisa estar logado para salvar a supervisão.";
        exit();
    }

    $userId = $_SESSION['user_id']; // Obtém o user_id da sessão
    $date = $_POST['date'];
    $observations = $_POST['observations'];
    $supervisor = $_POST['supervisor'];

    // Formatar a data para o formato YYYY-MM-DD
    $formattedDate = date('Y-m-d', strtotime($date));

    $stmt = $pdo->prepare("INSERT INTO supervision (user_id, date, observations, supervisor) VALUES (:user_id, :date, :observations, :supervisor)");
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':date', $formattedDate);
    $stmt->bindParam(':observations', $observations);
    $stmt->bindParam(':supervisor', $supervisor);
    $stmt->execute();

    echo "Supervision saved successfully!";
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>
