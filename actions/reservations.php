<?php
session_start();
require '../includes/db_connection.php';  

if (!isset($_SESSION['courriel'])) {
    header('Location: ../actions/connexion.php');  
    exit();
}

$courriel = $_SESSION['courriel'];

$stmt = $pdo->prepare('SELECT * FROM trajets_confirmes WHERE courriel_passager = ? ORDER BY date_trajet DESC, heure_depart DESC');
$stmt->execute([$courriel]);
$reservations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mes réservations - Ecoride</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="container mt-5">
  <h2>Mes réservations</h2>

  <?php if (empty($reservations)): ?>
    <p>Vous n'avez pas encore réservé de trajet.</p>
    <a href="../pages/rechercher_trajet.php" class="btn btn-primary">Rechercher un trajet</a>
  <?php else: ?>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Date</th>
          <th>Départ</th>
          <th>Destination</th>
          <th>Véhicule</th>
          <th>Places réservées</th>
          <th>Heure</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($reservations as $res): ?>
          <tr>
            <td><?= htmlspecialchars($res['date_trajet']) ?></td>
            <td><?= htmlspecialchars($res['depart']) ?></td>
            <td><?= htmlspecialchars($res['destination']) ?></td>
            <td><?= htmlspecialchars($res['vehicule']) ?></td>
            <td><?= htmlspecialchars($res['places_reservees']) ?></td>
            <td><?= htmlspecialchars(substr($res['heure_depart'], 0, 5)) ?></td>
            <td>
              <a href="modifier_reservation.php?id=<?= $res['id_confirmation'] ?>" class="btn btn-sm btn-outline-primary">Modifier</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
