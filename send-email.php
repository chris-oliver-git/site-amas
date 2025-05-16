<?php
// Importar as classes do PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Caminho para os arquivos do PHPMailer
// Se você instalou via Composer:
require '../vendor/autoload.php';
// Se você baixou os arquivos manualmente, descomente estas linhas e ajuste os caminhos:
// require 'PHPMailer/src/Exception.php';
// require 'PHPMailer/src/PHPMailer.php';
// require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitização básica dos dados do formulário
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $mensagem = filter_input(INPUT_POST, 'mensagem', FILTER_SANITIZE_STRING);

    // Validação básica
    if (empty($nome) || empty($telefone) || empty($email) || empty($mensagem) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Por favor, preencha todos os campos corretamente.'); window.history.back();</script>";
        exit;
    }

    // Extrair os dois primeiros nomes para o assunto
    $partesNome = explode(' ', trim($nome));
    $primeirosNomes = $partesNome[0] . (isset($partesNome[1]) ? ' ' . $partesNome[1] : '');

    $mail = new PHPMailer(true);

    try {
        // Configurações do servidor SMTP
        $mail->isSMTP();                                      // Usar SMTP
        $mail->Host       = 'smtp.gmail.com';                 // Servidor SMTP do Gmail
        $mail->SMTPAuth   = true;                             // Habilitar autenticação SMTP
        $mail->Username   = 'nao-responda@cartaoamas.com.br'; // Seu email (remetente)
        $mail->Password   = 'duggqkspkneghtue';               // Senha de app do Gmail (não a senha normal)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // Habilitar criptografia TLS
        $mail->Port       = 587;                              // Porta TCP para conexão
        $mail->CharSet    = 'UTF-8';                          // Definir charset como UTF-8

        // Configurações do e-mail
        $mail->setFrom('nao-responda@cartaoamas.com.br', 'Cartão Amas - Formulário');
        $mail->addAddress('faleconosco@cartaoamas.com.br');   // Email do setor de vendas
        $mail->addReplyTo($email, $nome);                     // Responder para o email do cliente

        // Assunto conforme solicitado
        $mail->Subject = 'Fale Conosco - ' . $primeirosNomes;

        // Corpo do e-mail com a máscara solicitada
        $mail->isHTML(true);
        $mail->Body = "
            <p><strong>Vim do site Cartão Amas e tenho uma dúvida!</strong></p>
            <hr>
            <p><strong>Dados do Cliente:</strong></p>
            <p><strong>Nome:</strong> {$nome}</p>
            <p><strong>Telefone:</strong> {$telefone}</p>
            <p><strong>E-mail:</strong> {$email}</p>
            <hr>
            <p><strong>Mensagem:</strong></p>
            <p>" . nl2br($mensagem) . "</p>
        ";

        // Versão em texto plano para clientes de email que não suportam HTML
        $mail->AltBody = "Vim do site Cartão Amas e tenho uma dúvida!\n\n" .
                        "Dados do Cliente:\n" .
                        "Nome: {$nome}\n" .
                        "Telefone: {$telefone}\n" .
                        "E-mail: {$email}\n\n" .
                        "Mensagem:\n{$mensagem}";

        $mail->send();
        echo "<script>alert('Mensagem enviada com sucesso! Entraremos em contato em breve.'); window.location.href='fale-conosco.html';</script>";
    } catch (Exception $e) {
        // Log do erro para depuração (em ambiente de produção, use um arquivo de log)
        error_log("Erro no envio de e-mail: " . $mail->ErrorInfo);
        echo "<script>alert('Não foi possível enviar sua mensagem. Por favor, tente novamente mais tarde ou entre em contato por telefone.'); window.history.back();</script>";
    }
} else {
    // Acesso direto ao script sem POST
    header("Location: fale-conosco.html");
    exit;
}
