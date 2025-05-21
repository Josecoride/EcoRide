<?php 
session_start();
require '../includes/db_connection.php';

if (!isset($_SESSION['courriel'])) {
    header("Location: ../actions/connexion.php");
    exit();
}

$courriel = $_SESSION['courriel'];
$id_confirmation = $_POST['id_confirmation'] ?? null;
$note = intval($_POST['note'] ?? 0);
$commentaire = trim($_POST['commentaire'] ?? '');
$validation = $_POST['validation'] ?? '';

if (!$id_confirmation || !in_array($validation, ['ok', 'nok']) || $note < 1 || $note > 5) {
    $_SESSION['erreur_note'] = "Formulaire invalide.";
    header("Location: ../pages/mes_trajets.php");
    exit();
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT * FROM trajets_confirmes WHERE id_confirmation = ? AND courriel_passager = ?");
    $stmt->execute([$id_confirmation, $courriel]);
    $trajet = $stmt->fetch();

    if (!$trajet) {
        throw new Exception("Trajet non trouvé ou non autorisé.");
    }

    $mauvais = ($validation === 'nok') ? 1 : 0;

    
    $stmt_insert = $pdo->prepare("INSERT INTO avis (id_confirmation, note, commentaire, mauvais) VALUES (?, ?, ?, ?)");
    $stmt_insert->execute([$id_confirmation, $note, $commentaire, $mauvais]);

    if ($mauvais === 0) {
        $credits = (int) $trajet['places_reservees'];

        $stmt_credit = $pdo->prepare("UPDATE utilisateurs SET credits = credits + ? WHERE courriel = ?");
        $stmt_credit->execute([$credits, $trajet['courriel_conducteur']]);
    }

    $pdo->commit();

    $_SESSION['succes_note'] = "Merci pour votre retour.";
    header("Location: ../pages/mes_trajets.php");
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['erreur_note'] = "Erreur : " . $e->getMessage();
    header("Location: ../pages/mes_trajets.php");
    exit();
}
