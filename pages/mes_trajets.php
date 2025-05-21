<?php
session_start();
require_once '../includes/db_connection.php';

if (!isset($_SESSION['courriel'])) {
    header("Location: ../actions/connexion.php");
    exit();
}

$courriel = $_SESSION['courriel'];


$stmt = $pdo->prepare("SELECT role FROM profils WHERE courriel = ?");
$stmt->execute([$courriel]);
$role = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mes trajets - EcoRide</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="container mt-5">
  <h2 class="mb-4"> Mes trajets</h2>

  <?php if (isset($_SESSION['succes_trajet'])): ?>
    <div class="alert alert-success"><?= $_SESSION['succes_trajet']; unset($_SESSION['succes_trajet']); ?></div>
  <?php endif; ?>
  <?php if (isset($_SESSION['erreur_trajet'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['erreur_trajet']; unset($_SESSION['erreur_trajet']); ?></div>
  <?php endif; ?>

  <?php if (in_array($role, ['passager', 'les deux'])): ?>
    <h4 class="mt-4"> En tant que passager</h4>
    <?php
    $stmt = $pdo->prepare("
      SELECT tc.*, tp.statut
      FROM trajets_confirmes tc
      JOIN trajets_proposes tp ON tc.id_trajet = tp.id_trajet
      WHERE tc.courriel_passager = ?
      ORDER BY tc.date_trajet DESC
    ");
    $stmt->execute([$courriel]);
    $reservations = $stmt->fetchAll();

    if ($reservations):
      foreach ($reservations as $res): ?>
        <div class="border rounded p-3 mb-3">
          <strong><?= htmlspecialchars($res['depart']) ?> → <?= htmlspecialchars($res['destination']) ?></strong><br>
          Date : <?= $res['date_trajet'] ?> à <?= $res['heure_depart'] ?><br>
          Véhicule : <?= $res['vehicule'] ?><br>
          Places réservées : <?= $res['places_reservees'] ?><br>
          Statut :
          <span class="badge bg-<?= 
            $res['statut'] === 'terminé' ? 'success' : (
            $res['statut'] === 'en_cours' ? 'warning text-dark' : 'secondary') ?>">
            <?= ucfirst($res['statut']) ?>
          </span><br>

          <?php if ($res['statut'] === 'confirmé'): ?>
            <a href="../actions/annuler_reservation.php?id=<?= $res['id_confirmation'] ?>" class="btn btn-sm btn-danger mt-2">❌ Annuler</a>
          <?php endif; ?>

          <?php if ($res['statut'] === 'terminé'): ?>
            <a href="../actions/laisser_avis.php?id=<?= $res['id_trajet'] ?>" class="btn btn-sm btn-outline-warning mt-2">✍️ Laisser un avis</a>
          <?php endif; ?>
        </div>
      <?php endforeach;
    else:
      echo "<p>Aucune réservation effectuée.</p>";
    endif;
    ?>
  <?php endif; ?>

  <?php if (in_array($role, ['conducteur', 'les deux'])): ?>
    <h4 class="mt-5"> En tant que conducteur</h4>
    <?php
    $stmt = $pdo->prepare("
      SELECT tc.*, tp.statut
      FROM trajets_confirmes tc
      JOIN trajets_proposes tp ON tc.id_trajet = tp.id_trajet
      WHERE tc.courriel_conducteur = ?
      ORDER BY tc.date_trajet DESC
    ");
    $stmt->execute([$courriel]);
    $mes_trajets = $stmt->fetchAll();

    if ($mes_trajets):
      foreach ($mes_trajets as $trajet): ?>
        <div class="border rounded p-3 mb-3">
          <strong><?= htmlspecialchars($trajet['depart']) ?> → <?= htmlspecialchars($trajet['destination']) ?></strong><br>
          Date : <?= $trajet['date_trajet'] ?> à <?= $trajet['heure_depart'] ?><br>
          Passager : <?= $trajet['courriel_passager'] ?><br>
          Statut :
          <span class="badge bg-<?= 
            $trajet['statut'] === 'terminé' ? 'success' : (
            $trajet['statut'] === 'en_cours' ? 'warning text-dark' : 'secondary') ?>">
            <?= ucfirst($trajet['statut']) ?>
          </span><br>

          <?php if ($trajet['statut'] === 'confirmé'): ?>
            <a href="../actions/demarrer_trajet.php?id=<?= $trajet['id_confirmation'] ?>" class="btn btn-sm btn-warning mt-2">▶️ Démarrer</a>
          <?php elseif ($trajet['statut'] === 'en_cours'): ?>
            <a href="../actions/terminer_trajet.php?id=<?= $trajet['id_confirmation'] ?>" class="btn btn-sm btn-success mt-2">✅ Terminer</a>
          <?php endif; ?>
        </div>
      <?php endforeach;
    else:
      echo "<p>Aucun trajet proposé n’a encore été réservé.</p>";
    endif;
    ?>
  <?php endif; ?>

</div>

</body>
</html>
