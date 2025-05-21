<?php
session_start();
require '../includes/db_connection.php';

if (!isset($_SESSION['employe'])) {
    header("Location: ../actions/connexion.php");
    exit();
}


$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);

$stmt = $pdo->query("SELECT a.*, p.nom AS passager_nom, pr.nom AS conducteur_nom,
                            tp.depart, tp.destination, tp.date_trajet
                     FROM avis a
                     LEFT JOIN profils p ON a.courriel_passager = p.courriel
                     LEFT JOIN profils pr ON a.courriel_conducteur = pr.courriel
                     LEFT JOIN trajets_proposes tp ON a.id_trajet = tp.id_trajet
                     WHERE a.est_valide = 0
                     ORDER BY a.date_avis DESC");
$avis = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Espace Employé - EcoRide</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="container mt-5">
  <h2 class="mb-4">Espace employé : validation des avis</h2>

  <?php if ($message): ?>
    <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <?php if (empty($avis)): ?>
    <div class="alert alert-success">Aucun avis à valider pour l’instant.</div>
  <?php else: ?>
    <table class="table table-bordered table-hover">
      <thead class="table-light">
        <tr>
          <th>Date</th>
          <th>Note</th>
          <th>Commentaire</th>
          <th>Passager</th>
          <th>Conducteur</th>
          <th>Trajet</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($avis as $a): ?>
          <tr>
            <td><?= htmlspecialchars($a['date_avis']) ?></td>
            <td><?= (int)$a['note'] ?>/5</td>
            <td><?= nl2br(htmlspecialchars($a['commentaire'])) ?></td>
            <td><?= htmlspecialchars($a['passager_nom'] ?? $a['courriel_passager']) ?></td>
            <td><?= htmlspecialchars($a['conducteur_nom'] ?? $a['courriel_conducteur']) ?></td>
            <td>
              <?= htmlspecialchars($a['depart'] ?? '?') ?> →
              <?= htmlspecialchars($a['destination'] ?? '?') ?>
              (<?= htmlspecialchars($a['date_trajet'] ?? '-') ?>)
            </td>
            <td>
              <form method="POST" action="../actions/valider_avis.php" class="d-inline">
                <input type="hidden" name="id_avis" value="<?= $a['id'] ?>">
                <button name="action" value="valider" class="btn btn-success btn-sm">Valider</button>
              </form>
              <form method="POST" action="../actions/valider_avis.php" class="d-inline" onsubmit="return confirm('Supprimer cet avis ?');">
                <input type="hidden" name="id_avis" value="<?= $a['id'] ?>">
                <button name="action" value="supprimer" class="btn btn-danger btn-sm">Supprimer</button>
              </form>
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
