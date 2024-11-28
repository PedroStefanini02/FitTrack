<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"];
    $name = $_POST["name"];
    $price = $_POST["price"];
    $market = $_POST["market"];
    $address = $_POST["address"];
    $user_id = $_SESSION['user_id'];

    // Conecta ao banco de dados
    $localhost = "localhost";
    $user = "root";
    $passw = "";
    $banco = "FitTrack";

    try {
        $pdo = new PDO("mysql:dbname=".$banco.";host=".$localhost, $user, $passw);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Verifica se uma nova imagem foi enviada
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            $imagePath = $uploadDir . basename($_FILES['image']['name']);

            // Move o arquivo enviado para o diretório de uploads
            if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                // Inclui o campo de imagem na consulta de atualização
                $stmt = $pdo->prepare("UPDATE foods SET name = :name, price = :price, market = :market, address = :address, image_path = :image_path WHERE id = :id AND user_id = :user_id");
                $stmt->bindParam(':image_path', $imagePath);
            } else {
                echo "Erro ao fazer upload da imagem.";
                exit;
            }
        } else {
            // Atualização sem alterar o campo de imagem
            $stmt = $pdo->prepare("UPDATE foods SET name = :name, price = :price, market = :market, address = :address WHERE id = :id AND user_id = :user_id");
        }

        // Parâmetros comuns para ambas as consultas
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':market', $market);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $user_id);

        if ($stmt->execute()) {
            echo "Alimento atualizado com sucesso!";
        } else {
            echo "Falha ao atualizar o alimento.";
        }
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }
}
?>
