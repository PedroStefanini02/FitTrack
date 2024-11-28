<?php
session_start();
header('Content-Type: application/json'); // Define o cabeçalho JSON

require_once 'phpmailer/src/Exception.php';
require_once 'phpmailer/src/PHPMailer.php';
require_once 'phpmailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $userName = $_POST["user_name"];
    $cnpj = $_POST["cnpj"] ?? null;
    $description = $_POST["description"] ?? null;
    $acceptedTerms = isset($_POST["accept_terms"]) ? 1 : 0; // Verifica se os termos foram aceitos

    $localhost = "localhost";
    $user = "root";
    $passw = "";
    $banco = "FitTrack";

    try {
        $pdo = new PDO("mysql:dbname=" . $banco . ";host=" . $localhost, $user, $passw);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Verifica se o e-mail já está cadastrado
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM USERS WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row['count'] > 0) {
            echo json_encode(['success' => false, 'message' => 'Este email já está cadastrado. Por favor, use um email diferente.']);
            exit();
        }

        // Valida se o CNPJ foi preenchido sem uma descrição
        if ($cnpj && !$description) {
            echo json_encode(['success' => false, 'message' => 'Por favor, preencha a carta de apresentação para empresas.']);
            exit();
        }

        // Valida se os termos foram aceitos
        if (!$acceptedTerms) {
            echo json_encode(['success' => false, 'message' => 'Você deve aceitar os termos de uso para continuar.']);
            exit();
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(16));

        // Insere o usuário na tabela
        $stmt = $pdo->prepare("INSERT INTO USERS (email, password, user_name, creation_date, cnpj, user_type, description, confirmation_token, accepted_terms) 
                               VALUES (:email, :password, :user_name, :creation_date, :cnpj, :user_type, :description, :confirmation_token, :accepted_terms)");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':user_name', $userName);
        $creationDate = date('Y-m-d H:i:s');
        $stmt->bindParam(':creation_date', $creationDate);
        $stmt->bindParam(':cnpj', $cnpj);
        $userType = $cnpj ? 'market' : 'common';
        $stmt->bindParam(':user_type', $userType);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':confirmation_token', $token);
        $stmt->bindParam(':accepted_terms', $acceptedTerms, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'suportefitrack@gmail.com';
                $mail->Password = 'phud exjy rsnj zayl';
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->setFrom('suportefitrack@gmail.com', 'FitTrack Support');
                $mail->addAddress($email, $userName);
                $mail->isHTML(true);
                $mail->Subject = 'Confirme seu e-mail - FitTrack';
                $mail->Body = "Bem-vindo(a) $userName, <br> Por favor, confirme seu e-mail clicando no link abaixo:<br><br>
                               <a href='http://localhost/fittrack/php/confirm_email.php?token=$token'>Confirmar E-mail</a><br><br>Obrigado, Equipe FitTrack";

                $mail->send();
                echo json_encode(['success' => true, 'message' => 'Cadastro realizado! Por favor, verifique seu e-mail para confirmar o cadastro.']);
                exit();
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => "Erro ao enviar e-mail de confirmação: {$mail->ErrorInfo}"]);
                exit();
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao realizar o cadastro. Tente novamente.']);
            exit();
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro de conexão: ' . $e->getMessage()]);
        exit();
    }
}
?>
