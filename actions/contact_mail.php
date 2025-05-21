<?php
session_start();

$nom = trim($_POST['nom'] ?? '');
$courriel = trim($_POST['courriel'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$nom || !$courriel || !$message || !filter_var($courriel, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['contact_message'] = "Veuillez remplir correctement tous les champs.";
    header("Location: ../pages/contact.php");  
    exit();
}

$to = "contact@ecoride.fr";
$sujet = "Nouveau message de contact - EcoRide";
$contenu = "Nom : $nom\nEmail : $courriel\n\nMessage :\n$message";
$headers = "From: $courriel\r\nReply-To: $courriel\r\nContent-Type: text/plain; charset=UTF-8";

@mail($to, $sujet, $contenu, $headers);

header("Location: ../pages/contact_confirmation.php");
exit();
