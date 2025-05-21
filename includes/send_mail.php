<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . '/../vendor/autoload.php';


// chemin selon ton organisation

function envoyerEmail($to, $subject, $body, $from = 'noreply@tondomaine.com', $fromName = 'Ecoride') {
    $mail = new PHPMailer(true);

    try {
        // Configuration SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Remplace par ton serveur SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'ton.email@gmail.com'; // Ton adresse SMTP
        $mail->Password = 'ton_mot_de_passe_app'; // Ton mot de passe ou token SMTP
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Expéditeur et destinataire
        $mail->setFrom($from, $fromName);
        $mail->addAddress($to);

        // Contenu<
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Erreur mail: " . $mail->ErrorInfo);
        return false;
    }
}
