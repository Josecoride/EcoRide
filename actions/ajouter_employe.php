<?php
session_start();
require_once '../includes/db_connection.php';

if (!isset($_SESSION['admin_email'])) {
    header('Location: ../actions/connexion_admin.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['courriel'], $_POST['mot_de_passe'])) {
    $courriel = trim($_POST['courriel']);
    $mot_de_passe = $_POST['mot_de_passe'];

    
    if (!filter_var($courriel, FILTER_VALIDATE_EMAIL) || strlen($mot_de_passe) < 6) {
        header('Location: ../pages/espace_admin.php?ajout=erreur');
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM employes WHERE courriel = ?");
    $stmt->execute([$courriel]);
    if ($stmt->fetch()) {
        header('Location: ../pages/espace_admin.php?ajout=erreur');
        exit();
    }

    $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO employes (courriel, mot_de_passe) VALUES (?, ?)");
    $stmt->execute([$courriel, $hash]);

    header('Location: ../pages/espace_admin.php?ajout=ok');
    exit();
}

header('Location: ../pages/espace_admin.php?ajout=erreur');
exit();
