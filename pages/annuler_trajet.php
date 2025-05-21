<?php
session_start();
require '../includes/db_connection.php';

if (!isset($_SESSION['courriel']) || !in_array($_SESSION['role'], ['conducteur', 'les deux'])) {
    header('Location: ../actions/connexion.php');
    exit();
}

$courriel = $_SESSION['courriel'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['erreur'] = "Trajet invalide.";
    header('Location: trajets_conducteur.php');
    exit();
}

$id_trajet = (int) $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM trajets_proposes WHERE id_trajet = ? AND courriel_conducteur = ?");
$stmt->execute([$id_trajet, $courriel]);
$trajet = $stmt->fetch();

if (!$trajet) {
    $_SESSION['erreur'] = "Trajet non trouvé ou accès refusé.";
    header('Location: trajets_conducteur.php');
    exit();
}

try {
    
    $stmt = $pdo->prepare("DELETE FROM trajets_confirmes WHERE id_trajet = ?");
    $stmt->execute([$id_trajet]);

    
    $stmt = $pdo->prepare("DELETE FROM trajets_proposes WHERE id_trajet = ?");
    $stmt->execute([$id_trajet]);

    $_SESSION['message'] = " Trajet annulé avec succès.";
} catch (Exception $e) {
    $_SESSION['erreur'] = "Erreur lors de l’annulation : " . $e->getMessage();
}

header('Location: trajets_conducteur.php');
exit();
