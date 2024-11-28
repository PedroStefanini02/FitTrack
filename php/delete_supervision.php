<?php
$localhost = "localhost";
$user = "root";
$passw = "";
$banco = "FitTrack";

try {
    $pdo = new PDO("mysql:dbname=".$banco.";host=".$localhost, $user, $passw);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = $_POST['id'];

    $stmt = $pdo->prepare("DELETE FROM supervision WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    echo "Supervision deleted successfully!";
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>