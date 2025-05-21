<?php
session_start();
require '../includes/db_connection.php';

if (!isset($_SESSION['courriel'])) {
    header("Location: connexion.php");
    exit();
}

$courriel = $_SESSION['courriel'];

$depart = trim($_POST['depart'] ?? '');
$destination = trim($_POST['destination'] ?? '');
$date = $_POST['date_trajet'] ?? '';
$heure_depart = $_POST['heure_depart'] ?? '';
$heure_arrivee = $_POST['heure_arrivee'] ?? null;
$prix = floatval($_POST['prix'] ?? 0);
$places_totales = intval($_POST['places_totales'] ?? 0);
$vehicule = intval($_POST['vehicule'] ?? 0);
$preferences = trim($_POST['preferences'] ?? '');

if (!$depart || !$destination || !$date || !$heure_depart || $prix <= 0 || $places_totales < 1 || !$vehicule) {
    $_SESSION['message'] = "Tous les champs requis doivent être remplis correctement.";
    header("Location: ../pages/ajouter_trajet.php");
    exit();
}

try {
    $stmt = $pdo->prepare("INSERT INTO trajets_proposes (
        courriel_conducteur, depart, destination, date_trajet,
        heure_depart, heure_arrivee, prix, places_totales,
        places_disponibles, vehicule, preferences
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $courriel,
        $depart,
        $destination,
        $date,
        $heure_depart,
        $heure_arrivee ?: null,
        $prix,
        $places_totales,
        $places_totales,
        $vehicule,
        $preferences
    ]);

    $_SESSION['message'] = " Trajet ajouté avec succès !";
    header("Location: ../pages/tableau_de_bord.php");
    exit();

} catch (Exception $e) {
    $_SESSION['message'] = "Erreur lors de l'ajout du trajet : " . $e->getMessage();
    header("Location: ../pages/ajouter_trajet.php");
    exit();
}
