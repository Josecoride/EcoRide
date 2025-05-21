<?php
session_start();
require '../includes/db_connection.php'; 
include '../includes/nav.php';

if (!isset($_SESSION['employe'])) {
    header("Location: ../actions/connexion_employe.php");
    exit();
}

$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;

if (!$id || !is_numeric($id) || !in_array($action, ['valider', 'refuser'])) {
    header("Location: ../pages/espace_employe.php");
    exit();
}

try {
    if ($action === 'valider') {
        $pdo->beginTransaction();

       
        $stmt = $pdo->prepare("SELECT a.valide, tc.*, a.note FROM avis a
                               JOIN trajets_confirmes tc ON a.id_confirmation = tc.id_confirmation
                               WHERE a.id = ?");
        $stmt->execute([$id]);
        $avis = $stmt->fetch();

        if ($avis) {
            if (!$avis['valide']) {
                $credits = (int) $avis['places_reservees'];
                $stmt2 = $pdo->prepare("UPDATE utilisateurs SET credits = credits + ? WHERE courriel = ?");
                $stmt2->execute([$credits, $avis['courriel_conducteur']]);
            }

            
            $pdo->prepare("UPDATE avis SET valide = 1 WHERE id = ?")->execute([$id]);
        }

        $pdo->commit();
    } else {
        
        $pdo->prepare("DELETE FROM avis WHERE id = ?")->execute([$id]);
    }

    header("Location: ../pages/espace_employe.php");
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['erreur_avis'] = "Erreur : " . $e->getMessage();
    header("Location: ../pages/espace_employe.php");
    exit();
}
?>
