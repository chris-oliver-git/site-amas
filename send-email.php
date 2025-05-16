<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Inclui o autoload do Composer

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = $_POST['nome'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $email = $_POST['email'] ?? '';
    $mensagem = $_POST['mensagem'] ?? '';

    $mail = new PHPMailer(true);

    try {
        // Configuração do servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'nao-responda@cartaoamas.com.br'; // Seu email
        $mail->Password = 'duggqkspkneghtue'; // Sua senha de app (não a do Gmail normal)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Remetente e destinatário
        $mail->setFrom('nao-responda@cartaoamas.com.br', 'Cartão Amas - Formulário');
        $mail->addAddress('faleconosco@cartaoamas.com.br'); // Destinatário

        // Conteúdo
        $mail->isHTML(true);
        $mail->Subject = 'Nova mensagem do site Cartão Amas';
        $mail->Body = "
            <strong>Nome:</strong> {$nome}<br>
            <strong>Telefone:</strong> {$telefone}<br>
            <strong>E-mail:</strong> {$email}<br><br>
            <strong>Mensagem:</strong><br>{$mensagem}
        ";

        $mail->send();

        echo "<script>alert('Mensagem enviada com sucesso!'); window.history.back();</script>";
    } catch (Exception $e) {
        echo "<script>alert('Aconteceu algum erro na comunicação com o servidor. Tente novamente.'); window.history.back();</script>";
    }
} else {
    echo "Acesso não permitido.";
}
