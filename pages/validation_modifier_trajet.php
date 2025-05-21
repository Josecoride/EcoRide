<?php
session_start();
require_once '../includes/db_connection.php';

if (!isset($_SESSION['courriel'])) {
    header('Location: ../connexion.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courriel = $_SESSION['courriel'];
    $date_trajet = $_POST['date_trajet'] ?? '';
    $depart = trim($_POST['depart'] ?? '');
    $destination = trim($_POST['destination'] ?? '');
    $vehicule = $_POST['vehicule'] ?? '';
    $places_disponibles = intval($_POST['places_disponibles'] ?? 0);
    $places_totales = intval($_POST['places_totales'] ?? 0);
    $heure_depart = $_POST['heure_depart'] ?? '';
    $heure_arrivee = $_POST['heure_arrivee'] ?? null;
    $prix = floatval($_POST['prix'] ?? 0);
    $description = trim($_POST['description'] ?? '');

    if (
        !$date_trajet || !$depart || !$destination || !$vehicule ||
        $places_disponibles < 1 || $places_totales < 1 || $places_disponibles > $places_totales ||
        !$heure_depart || $prix <= 0
    ) {
        $_SESSION['erreur_trajet'] = "Veuillez remplir correctement tous les champs obligatoires.";
        header('Location: ../pages/proposer_trajet.php');
        exit();
    }

    try {
        
        $stmt_credit = $pdo->prepare('SELECT credits FROM utilisateurs WHERE courriel = ?');
        $stmt_credit->execute([$courriel]);
        $credits = $stmt_credit->fetchColumn();

        if ($credits < 2) {
            $_SESSION['erreur_trajet'] = "Vous n'avez pas assez de crédits pour proposer un trajet.";
            header('Location: ../pages/proposer_trajet.php');
            exit();
        }

        $stmt = $pdo->prepare('INSERT INTO trajets_proposes (
            courriel_conducteur, date_trajet, depart, destination, vehicule,
            prix, description, places_disponibles, places_totales,
            heure_depart, heure_arrivee
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');

        $stmt->execute([
            $courriel,
            $date_trajet,
            $depart,
            $destination,
            $vehicule,
            $prix,
            $description !== '' ? $description : null,
            $places_disponibles,
            $places_totales,
            $heure_depart,
            $heure_arrivee !== '' ? $heure_arrivee : null
        ]);

       
        $pdo->prepare('UPDATE utilisateurs SET credits = credits - 2 WHERE courriel = ?')
            ->execute([$courriel]);

      
        $pdo->prepare('INSERT INTO transactions (date_transaction, credits_gagnes, type) VALUES (CURDATE(), 2, "trajet")')
            ->execute();

        $_SESSION['succes_trajet'] = "Trajet publié avec succès.";
        header('Location: ../pages/mes_trajets.php');
        exit();

    } catch (PDOException $e) {
        $_SESSION['erreur_trajet'] = "Erreur serveur : " . $e->getMessage();
        header('Location: ../pages/proposer_trajet.php');
        exit();
    }

} else {
    header('Location: ../pages/mes_trajets.php');
    exit();
}
