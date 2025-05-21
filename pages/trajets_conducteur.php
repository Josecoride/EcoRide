<?php
session_start();
require '../includes/db_connection.php';

if (!isset($_SESSION['courriel']) || !in_array($_SESSION['role'], ['conducteur', 'les deux'])) {
    header("Location: ../actions/connexion.php");
    exit();
}

$courriel = $_SESSION['courriel'];

$stmt = $pdo->prepare("
    SELECT tp.id_trajet, tp.date_trajet, tp.depart, tp.destination, tp.heure_depart, 
           tp.places_disponibles, tp.vehicule, tp.statut
    FROM trajets_proposes tp
    WHERE tp.courriel_conducteur = ?
    ORDER BY tp.date_trajet DESC
");
$stmt->execute([$courriel]);
$trajets = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mes trajets proposés - EcoRide</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="container mt-5">
  <h2 class="mb-4"> Mes trajets proposés</h2>

  <?php if (!empty($_SESSION['message'])): ?>
    <div class="alert alert-success"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
  <?php endif; ?>

  <?php if (empty($trajets)): ?>
    <div class="alert alert-info">Aucun trajet proposé pour le moment.</div>
    <a href="proposer_trajet.php" class="btn btn-success"> Ajouter un trajet</a>
  <?php else: ?>
    <table class="table table-bordered mt-3">
      <thead class="table-light">
        <tr>
          <th>Date</th>
          <th>Départ</th>
          <th>Destination</th>
          <th>Heure</th>
          <th>Places</th>
          <th>Statut</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($trajets as $trajet): ?>
          <tr>
            <td><?= htmlspecialchars($trajet['date_trajet']) ?></td>
            <td><?= htmlspecialchars($trajet['depart']) ?></td>
            <td><?= htmlspecialchars($trajet['destination']) ?></td>
            <td><?= htmlspecialchars(substr($trajet['heure_depart'], 0, 5)) ?></td>
            <td><?= htmlspecialchars($trajet['places_disponibles']) ?></td>
            <td>
              <?php if ($trajet['statut'] === 'prévu'): ?>
                <span class="badge bg-secondary">Prévu</span>
              <?php elseif ($trajet['statut'] === 'en_cours'): ?>
                <span class="badge bg-warning text-dark">En cours</span>
              <?php elseif ($trajet['statut'] === 'terminé'): ?>
                <span class="badge bg-success">Terminé</span>
              <?php endif; ?>
            </td>
            <td>
              <a href="modifier_trajet.php?id=<?= $trajet['id_trajet'] ?>" class="btn btn-sm btn-warning">✏️ Modifier</a>
              <a href="annuler_trajet.php?id=<?= $trajet['id_trajet'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce trajet ?')">🗑️ Supprimer</a>
              <a href="../actions/demarrer_trajet.php?id=<?= $trajet['id_trajet'] ?>" class="btn btn-sm btn-outline-success mt-1">▶️ Démarrer</a>
              <a href="../actions/terminer_trajet.php?id=<?= $trajet['id_trajet'] ?>" class="btn btn-sm btn-outline-primary mt-1">✅ Terminer</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

</body>
</html>
