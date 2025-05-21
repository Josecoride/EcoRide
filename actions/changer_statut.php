<?php
session_start();
require '../includes/db_connection.php';

if (!isset($_SESSION['courriel']) || !in_array($_SESSION['role'], ['conducteur', 'les deux'])) {
    header('Location: connexion.php');
    exit();
}

$courriel = $_SESSION['courriel'];

if (!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['action'])) {
    $_SESSION['erreur'] = "Requête invalide.";
    header('Location: ../pages/trajets_conducteur.php');
    exit();
}

$id_trajet = (int) $_GET['id'];
$action = $_GET['action'];


$stmt = $pdo->prepare("SELECT statut FROM trajets_proposes WHERE id_trajet = ? AND courriel_conducteur = ?");
$stmt->execute([$id_trajet, $courriel]);
$trajet = $stmt->fetch();

if (!$trajet) {
    $_SESSION['erreur'] = "Trajet introuvable ou non autorisé.";
    header('Location: ../pages/trajets_conducteur.php');
    exit();
}

$statut_actuel = $trajet['statut'];
$nouveau_statut = '';

if ($action === 'demarrer' && $statut_actuel === 'prévu') {
    $nouveau_statut = 'en_cours';
} elseif ($action === 'terminer' && $statut_actuel === 'en_cours') {
    $nouveau_statut = 'terminé';
} else {
    $_SESSION['erreur'] = "Action non autorisée pour ce trajet.";
    header('Location: ../pages/trajets_conducteur.php');
    exit();
}

$stmt = $pdo->prepare("UPDATE trajets_proposes SET statut = ? WHERE id_trajet = ?");
$stmt->execute([$nouveau_statut, $id_trajet]);

$_SESSION['message'] = "Le trajet a été mis à jour en statut : $nouveau_statut.";
header('Location: ../pages/trajets_conducteur.php');
exit();
