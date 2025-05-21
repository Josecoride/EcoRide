<?php
session_start();
require_once '../includes/db_connection.php';

if (!isset($_SESSION['courriel'])) {
    header('Location: connexion.php');
    exit();
}

$courriel = $_SESSION['courriel'];
$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    $_SESSION['message'] = "ID du trajet invalide.";
    header('Location: ../pages/trajets_conducteur.php');
    exit();
}

$stmt = $pdo->prepare("UPDATE trajets_proposes SET statut = 'en_cours' 
                       WHERE id_trajet = ? AND courriel_conducteur = ?");
$success = $stmt->execute([$id, $courriel]);

$_SESSION['message'] = $success ? " Trajet démarré." : "Erreur lors du démarrage.";
header('Location: ../pages/trajets_conducteur.php');
exit();
