<?php
// Incluindo os arquivos necessários do PHPMailer manualmente
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: text/html; charset=UTF-8'); // Define UTF-8 para o conteúdo HTML

$token = $_GET['token'] ?? ''; // Obtendo o token da URL

if ($token) {
    // Configuração do banco de dados
    $localhost = "localhost";
    $user = "root";
    $passw = "";
    $banco = "FitTrack";

    try {
        // Conectar ao banco de dados com charset UTF-8
        $pdo = new PDO("mysql:dbname=" . $banco . ";host=" . $localhost . ";charset=utf8", $user, $passw);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Verificar o token e atualizar a confirmação de e-mail
        $stmt = $pdo->prepare("SELECT email, user_name FROM USERS WHERE confirmation_token = :token AND email_verified = 0");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Atualizar a confirmação de e-mail
            $stmt = $pdo->prepare("UPDATE USERS SET email_verified = 1 WHERE confirmation_token = :token");
            $stmt->bindParam(':token', $token);
            $stmt->execute();

            // Enviar e-mail de boas-vindas
            $email = $user['email'];
            $userName = $user['user_name'];

            // Instanciando o PHPMailer corretamente
            $mail = new PHPMailer(true);

            try {
                // Configurações do servidor de e-mail
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'suportefitrack@gmail.com';
                $mail->Password = 'phud exjy rsnj zayl';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Configurações do e-mail
                $mail->setFrom('suportefitrack@gmail.com', 'FitTrack Support');
                $mail->addAddress($email, $userName);
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8'; // Define o charset do PHPMailer para UTF-8
                $mail->Subject = 'E-mail confirmado! - FitTrack';
                $mail->Body = 'Seu e-mail foi confirmado com sucesso! Agora você pode acessar sua conta. 
                  <br><br> 
                  Clique <a href="http://localhost/fittrack/index.html?token=' . $token . '">aqui</a> para acessar sua página inicial.';

                // Enviar e-mail
                if ($mail->send()) {
                    header("Location: http://localhost/fittrack/index.html?token=" . $token);
                    exit();
                } else {
                    echo "Erro ao enviar o e-mail de boas-vindas.";
                }
            } catch (Exception $e) {
                echo "Erro ao enviar o e-mail de boas-vindas: {$mail->ErrorInfo}";
            }
        } else {
            echo "Token inválido ou e-mail já confirmado.";
        }
    } catch (PDOException $e) {
        echo 'Erro de conexão: ' . $e->getMessage();
    }
} else {
    echo "Token não fornecido.";
}
