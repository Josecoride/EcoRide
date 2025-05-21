<?php
session_start();
require '../includes/db_connection.php';

$depart = trim($_GET['depart'] ?? '');
$destination = trim($_GET['destination'] ?? '');
$date_trajet = $_GET['date_trajet'] ?? '';
$ecologique = $_GET['ecologique'] ?? '';
$prix_max = $_GET['prix_max'] ?? '';
$duree_max = $_GET['duree_max'] ?? '';
$note_min = $_GET['note_min'] ?? '';
$trajets = [];

if ($depart && $destination && $date_trajet) {
    $sql = "SELECT tp.*, 
                   v.energie, 
                   p.nom AS conducteur_nom, 
                   TIME_TO_SEC(TIMEDIFF(tp.heure_arrivee, tp.heure_depart))/60 AS duree
            FROM trajets_proposes tp
            JOIN vehicules v ON tp.vehicule = v.id
            JOIN profils p ON tp.courriel_conducteur = p.courriel
            WHERE tp.depart LIKE ? 
              AND tp.destination LIKE ? 
              AND tp.date_trajet = ?
              AND tp.places_disponibles > 0";

    $params = ["%$depart%", "%$destination%", $date_trajet];

    if ($ecologique === '1') {
        $sql .= " AND v.energie = 'electrique'";
    } elseif ($ecologique === '0') {
        $sql .= " AND v.energie != 'electrique'";
    }

    if (is_numeric($prix_max)) {
        $sql .= " AND tp.prix <= ?";
        $params[] = $prix_max;
    }

    if (is_numeric($duree_max)) {
        $sql .= " AND tp.heure_arrivee IS NOT NULL 
                  AND TIME_TO_SEC(TIMEDIFF(tp.heure_arrivee, tp.heure_depart)) <= ?";
        $params[] = intval($duree_max) * 60;
    }

    if (is_numeric($note_min)) {
        $sql .= " AND p.note >= ?";
        $params[] = $note_min;
    }

    $sql .= " ORDER BY tp.date_trajet ASC, tp.heure_depart ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $trajets = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Résultats de recherche - EcoRide</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="container mt-5" style="max-width: 800px;">
  <h2 class="mb-4">Résultats de votre recherche</h2>

  <?php if (!empty($trajets)): ?>
    <div class="list-group">
      <?php foreach ($trajets as $trajet): ?>
        <div class="list-group-item mb-3 shadow-sm">
          <strong><?= htmlspecialchars($trajet['depart']) ?> → <?= htmlspecialchars($trajet['destination']) ?></strong><br>
          Date : <?= htmlspecialchars($trajet['date_trajet']) ?> à <?= htmlspecialchars(substr($trajet['heure_depart'], 0, 5)) ?><br>
          Conducteur : <?= htmlspecialchars($trajet['conducteur_nom']) ?><br>
          Véhicule : <?= ucfirst($trajet['energie']) ?> – <?= $trajet['places_disponibles'] ?> place(s)<br>
          Prix : <?= number_format($trajet['prix'], 2) ?> €<br>
          Durée : <?= isset($trajet['duree']) ? intval($trajet['duree']) . ' min' : 'Non précisée' ?><br>
          <div class="mt-2">
            <?php if (isset($_SESSION['courriel'])): ?>
              <a href="../actions/reserver_place.php?id=<?= $trajet['id_trajet'] ?>" class="btn btn-sm btn-primary">Réserver</a>
            <?php else: ?>
              <a href="../actions/connexion.php" class="btn btn-sm btn-outline-secondary">Se connecter pour réserver</a>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="alert alert-warning">Aucun trajet ne correspond à votre recherche.</div>
  <?php endif; ?>
</div>

</body>
</html>
