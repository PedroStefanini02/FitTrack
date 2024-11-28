<?php
session_start(); // Inicia a sessão

$localhost = "localhost";
$user = "root";
$passw = "";
$banco = "FitTrack";

try {
    $pdo = new PDO("mysql:dbname=".$banco.";host=".$localhost, $user, $passw);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!isset($_SESSION['user_id'])) {
        echo "Você precisa estar logado para remover um produto.";
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
        $productId = $_POST['id'];

        $stmt = $pdo->prepare("DELETE FROM foods WHERE id = :id AND user_id = :user_id");
        $stmt->bindParam(':id', $productId);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        if ($stmt->execute()) {
            echo "Produto removido com sucesso!";
        } else {
            echo "Erro ao remover o produto.";
        }
    } else {
        echo "Dados inválidos.";
    }
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>
