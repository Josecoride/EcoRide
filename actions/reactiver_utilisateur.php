<?php
session_start();
require_once '../includes/db_connection.php';

if (!isset($_SESSION['admin_email'])) {
    header('Location: ../actions/login_admin.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['courriel'])) {
    $courriel = $_GET['courriel'];

    if (!filter_var($courriel, FILTER_VALIDATE_EMAIL)) {
        header('Location: ../pages/espace_admin.php?etat=erreur');
        exit();
    }

    try {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET actif = 1 WHERE courriel = ?");
        $stmt->execute([$courriel]);

        header('Location: ../pages/espace_admin.php?etat=ok');
        exit();
    } catch (PDOException $e) {
        header('Location: ../pages/espace_admin.php?etat=erreur');
        exit();
    }
}

header('Location: ../pages/espace_admin.php?etat=erreur');
exit();
