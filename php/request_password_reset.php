<?php
require_once 'phpmailer/src/PHPMailer.php';
require_once 'phpmailer/src/SMTP.php';
require_once 'phpmailer/src/Exception.php';

header('Content-Type: application/json; charset=UTF-8'); // Define o cabeçalho como JSON UTF-8

function sendPasswordResetEmail($email) {
    $localhost = "localhost";
    $user = "root";
    $passw = "";
    $banco = "FitTrack";

    try {
        // Conexão com o banco de dados
        $pdo = new PDO("mysql:dbname=" . $banco . ";host=" . $localhost, $user, $passw);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Verificar se o e-mail existe
        $stmt = $pdo->prepare("SELECT user_name FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'Email não encontrado']);
            return;
        }

        $userName = $user['user_name'];
        $token = bin2hex(random_bytes(16)); // Geração do token
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // Expira em 1 hora

        // Salvar o token no banco
        $stmt = $pdo->prepare("UPDATE users SET reset_token = :token, reset_token_expiry = :expiry WHERE email = :email");
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expiry', $expiry);
        $stmt->bindParam(':email', $email);

        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'message' => 'Erro ao salvar o token no banco']);
            return;
        }

        // Enviar o e-mail
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'suportefitrack@gmail.com'; // Seu e-mail SMTP
            $mail->Password = 'phud exjy rsnj zayl'; // Substitua pela senha do aplicativo
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Configuração de charset para evitar problemas de acentuação
            $mail->CharSet = 'UTF-8';

            $mail->setFrom('suportefitrack@gmail.com', 'FitTrack Support');
            $mail->addAddress($email, $userName); // Enviar para o destinatário
            $mail->isHTML(true);
            $mail->Subject = 'Redefinição de Senha - FitTrack';
            $mail->Body = "Olá $userName,<br><br>
                           Recebemos uma solicitação para redefinir sua senha.<br>
                           Por favor, clique no link abaixo para redefinir sua senha:<br><br>
                           <a href='http://localhost/fittrack/reset_password.html?token=$token'>Redefinir Senha</a><br><br>
                           Este link é válido por 1 hora.<br><br>
                           Caso você não tenha solicitado a redefinição, ignore este e-mail.";

            $mail->send();
            echo json_encode(['success' => true, 'message' => 'E-mail de redefinição enviado com sucesso!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => "Erro ao enviar e-mail: {$mail->ErrorInfo}"]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro de conexão com o banco de dados: ' . $e->getMessage()]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    sendPasswordResetEmail($email);
}
