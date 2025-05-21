<?php
session_start();
require '../includes/db_connection.php';

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!isset($_SESSION['courriel'])) {
    header('Location: ../actions/connexion.php');
    exit();
}

$courriel = $_SESSION['courriel'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['erreur_trajet'] = "ID de réservation invalide.";
    header('Location: ../pages/mes_trajets.php');
    exit();
}

$id_confirmation = (int) $_GET['id'];

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare('SELECT * FROM trajets_confirmes WHERE id_confirmation = ? AND courriel_passager = ?');
    $stmt->execute([$id_confirmation, $courriel]);
    $reservation = $stmt->fetch();

    if (!$reservation) {
        throw new Exception("Réservation introuvable ou accès refusé.");
    }

    $places = $reservation['places_reservees'];
    $id_trajet = $reservation['id_trajet'];

    
    $stmt = $pdo->prepare('UPDATE trajets_proposes SET places_disponibles = places_disponibles + ? WHERE id_trajet = ?');
    $stmt->execute([$places, $id_trajet]);

    $stmt = $pdo->prepare('DELETE FROM trajets_confirmes WHERE id_confirmation = ?');
    $stmt->execute([$id_confirmation]);

    $stmt = $pdo->prepare('UPDATE utilisateurs SET credits = credits + ? WHERE courriel = ?');
    $stmt->execute([$places, $courriel]);

    $stmt = $pdo->prepare('INSERT INTO transactions (date_transaction, credits_gagnes, type, id_trajet) VALUES (NOW(), 0, "annulation", ?)');
    $stmt->execute([$id_trajet]);

    $pdo->commit();

    $_SESSION['message'] = " Réservation annulée avec succès.";
    header('Location: ../pages/mes_trajets.php');
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['erreur_trajet'] = "Erreur lors de l'annulation : " . $e->getMessage();
    header('Location: ../pages/mes_trajets.php');
    exit();
}
