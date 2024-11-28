<?php
session_start();

// Verifica se o método é POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recebe os dados do formulário
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];

    // Verifica se os campos estão preenchidos
    if (empty($email) || empty($password) || empty($confirmPassword)) {
        header("Location: ../reset_password.html?error=empty_fields");
        exit();
    }

    // Verifica se as senhas coincidem
    if ($password !== $confirmPassword) {
        header("Location: ../reset_password.html?error=password_mismatch");
        exit();
    }

    // Conecta ao banco de dados
    $localhost = "localhost";
    $user = "root";
    $passw = "";
    $banco = "FitTrack";

    try {
        $pdo = new PDO("mysql:dbname=" . $banco . ";host=" . $localhost, $user, $passw);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Verifica se o e-mail existe
        $stmt = $pdo->prepare("SELECT * FROM USERS WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            header("Location: ../reset_password.html?error=email_not_found");
            exit();
        }

        // Atualiza a senha do usuário
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE USERS SET password = :password WHERE email = :email");
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':email', $email);

        if ($stmt->execute()) {
            header("Location: ../reset_password.html?status=success");
            exit();
        } else {
            header("Location: ../reset_password.html?error=reset_failed");
            exit();
        }
    } catch (PDOException $e) {
        echo 'Erro de conexão com o banco de dados: ' . $e->getMessage();
    }
} else {
    header("Location: ../reset_password.html?error=invalid_request");
    exit();
}
