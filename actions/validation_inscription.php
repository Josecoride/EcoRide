<?php 
session_start();
require '../includes/db_connection.php';

$nom = trim(filter_var($_POST['nom'] ?? '', FILTER_SANITIZE_STRING));
$courriel = trim(filter_var($_POST['courriel'] ?? '', FILTER_VALIDATE_EMAIL));
$mot_de_passe = $_POST['mot_de_passe'] ?? '';
$genre = $_POST['genre'] ?? '';
$telephone = trim($_POST['telephone'] ?? '');
$adresse = trim(filter_var($_POST['adresse'] ?? '', FILTER_SANITIZE_STRING));
$role = $_POST['role'] ?? 'passager';

if (!$nom || !$courriel || !$mot_de_passe || !$genre || !$telephone || !$adresse || !in_array($role, ['passager', 'conducteur'])) {
    $_SESSION['erreur'] = "Merci de remplir tous les champs correctement.";
    header("Location: ../actions/inscription.php");
    exit();
}

if (strlen($mot_de_passe) < 6 || !preg_match('/[A-Z]/', $mot_de_passe) || !preg_match('/[0-9]/', $mot_de_passe)) {
    $_SESSION['erreur'] = "Mot de passe trop faible. (min. 6 caractères, 1 majuscule, 1 chiffre)";
    header("Location: ../actions/inscription.php");
    exit();
}


$stmt = $pdo->prepare("SELECT 1 FROM utilisateurs WHERE courriel = ?");
$stmt->execute([$courriel]);
if ($stmt->fetch()) {
    $_SESSION['erreur'] = "Un compte existe déjà avec cet e-mail.";
    header("Location: ../actions/inscription.php");
    exit();
}

try {
    $pdo->beginTransaction();

    
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (courriel, mot_de_passe, credits) VALUES (?, ?, 20)");
    $stmt->execute([$courriel, password_hash($mot_de_passe, PASSWORD_DEFAULT)]);

    $stmt = $pdo->prepare("INSERT INTO profils (courriel, nom, genre, telephone, adresse, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$courriel, $nom, $genre, $telephone, $adresse, $role]);

    $pdo->commit();

    $_SESSION['success'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
    header("Location: ../actions/connexion.php");
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['erreur'] = "Erreur serveur : " . $e->getMessage();
    header("Location: ../actions/inscription.php");
    exit();
}
