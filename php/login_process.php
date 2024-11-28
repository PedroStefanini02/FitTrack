<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Conecta ao banco de dados
    $localhost = "localhost";
    $user = "root";
    $passw = "";
    $banco = "FitTrack";

    try {
        $pdo = new PDO("mysql:dbname=" . $banco . ";host=" . $localhost, $user, $passw);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Verifica se o e-mail existe
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verifica se o e-mail foi confirmado
            if ($user['email_verified'] == 0) {
                // E-mail não confirmado
                $error = "email_not_confirmed";
                header("Location: ../index.html?error=" . $error . "&modal=open");
                exit();
            }

            // Verifica se a senha está correta
            if (password_verify($password, $user['password'])) {
                // Login bem-sucedido
                $_SESSION["user_id"] = $user['user_id']; // Armazena o user_id na sessão

                // Atualiza o last_login para a data e hora atuais
                $updateQuery = "UPDATE users SET last_login = NOW() WHERE user_id = :user_id";
                $updateStmt = $pdo->prepare($updateQuery);
                $updateStmt->bindParam(':user_id', $user['user_id']);
                $updateStmt->execute();

                // Verifica se a sessão está ativa e o user_id está correto
                if (isset($_SESSION["user_id"])) {
                    echo "User ID in session: " . $_SESSION["user_id"];
                } else {
                    echo "User ID not set in session.";
                }

                header("Location: ../indexlogado.html");
                exit();
            } else {
                // Senha incorreta
                $error = "invalid_password";
                header("Location: ../index.html?error=" . $error . "&modal=open");
                exit();
            }
        } else {
            // Email não encontrado
            $error = "invalid_email";
            header("Location: ../index.html?error=" . $error . "&modal=open");
            exit();
        }
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }
}
