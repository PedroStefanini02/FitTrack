<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $price = $_POST["price"];
    $market = $_POST["market"];
    $address = $_POST["address"];
    $user_id = $_SESSION['user_id']; // Obtém o user_id da sessão
    $image = $_FILES['image']; // Obtém o arquivo enviado

    // Conecta ao banco de dados
    $localhost = "localhost";
    $user = "root";
    $passw = "";
    $banco = "FitTrack";

    // Diretório para salvar as imagens
    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Cria o diretório se não existir
    }

    // Define o caminho completo para salvar a imagem
    $imagePath = $uploadDir . basename($image['name']);

    try {
        // Verifica se a imagem foi carregada corretamente
        if (move_uploaded_file($image['tmp_name'], $imagePath)) {
            $pdo = new PDO("mysql:dbname=".$banco.";host=".$localhost, $user, $passw);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Prepara a consulta SQL para inserir o novo alimento com user_id e o caminho da imagem
            $stmt = $pdo->prepare("INSERT INTO foods (name, price, market, address, user_id, image_path) VALUES (:name, :price, :market, :address, :user_id, :image_path)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':market', $market);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':image_path', $imagePath);

            if ($stmt->execute()) {
                echo "Alimento cadastrado com sucesso!";
            } else {
                echo "Falha ao cadastrar o alimento.";
            }
        } else {
            echo "Falha ao carregar a imagem.";
        }
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }
}
?>
