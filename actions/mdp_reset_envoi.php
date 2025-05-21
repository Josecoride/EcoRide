<?php
session_start();
require '../includes/db_connection.php';
require '../includes/send_mail.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Adresse e-mail invalide.";
        header('Location: ../actions/motdepasse_oublie.php');
        exit();
    }

   
    $stmt = $pdo->prepare('SELECT * FROM profils WHERE courriel = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    $_SESSION['message'] = "Un lien de réinitialisation a été envoyé si l’adresse est valide.";
    
    if (!$user) {
        header('Location: ../actions/motdepasse_oublie.php');
        exit();
    }

    
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

  
    $stmt = $pdo->prepare('INSERT INTO reset_tokens (courriel, token, expires_at) VALUES (?, ?, ?)');
    $stmt->execute([$email, $token, $expires]);

    $baseUrl = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
    $resetPath = dirname($_SERVER['SCRIPT_NAME'], 1); 
    $url = $baseUrl . $resetPath . "/reinitialiser_motdepasse.php?token=" . urlencode($token);

    $subject = "Réinitialisation de votre mot de passe Ecoride";
    $body = "<p>Bonjour,</p>
             <p>Vous avez demandé à réinitialiser votre mot de passe. Cliquez sur le lien ci-dessous pour définir un nouveau mot de passe (valable 1 heure) :</p>
             <p><a href=\"" . htmlspecialchars($url) . "\">Réinitialiser mon mot de passe</a></p>
             <p>Si vous n’avez pas demandé cette réinitialisation, ignorez cet email.</p>";

    envoyerEmail($email, $subject, $body);

    header('Location: ../actions/motdepasse_oublie.php');
    exit();
} else {
    header('Location: ../actions/motdepasse_oublie.php');
    exit();
}
