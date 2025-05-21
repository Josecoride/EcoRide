<?php
session_start();
require_once '../includes/db_connection.php';

if (!isset($_SESSION['admin_email'])) {
    header('Location: ../actions/login_admin.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['courriel'])) {
    $courriel = $_GET['courriel'];
    $type = $_GET['type'] ?? 'utilisateur';

    if (!filter_var($courriel, FILTER_VALIDATE_EMAIL)) {
        die("Adresse e-mail invalide.");
    }

    
    if ($type === 'employe') {
        $stmt = $pdo->prepare("DELETE FROM employes WHERE courriel = ?");
    } else {
        $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE courriel = ?");
    }

    $stmt->execute([$courriel]);

    header('Location: ../pages/espace_admin.php');
    exit();
}

echo "Méthode non autorisée.";
