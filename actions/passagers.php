<?php
session_start();
require '../includes/db_connection.php';

if (!isset($_SESSION['courriel']) || !in_array($_SESSION['role'], ['conducteur', 'les deux'])) {
    header('Location: connexion.php');
    exit();
}

$courriel_conducteur = $_SESSION['courriel'];

$stmt = $pdo->prepare("
    SELECT DISTINCT u.courriel, p.nom, p.genre, p.telephone, p.adresse
    FROM trajets_confirmes tc
    JOIN profils p ON tc.courriel_passager = p.courriel
    JOIN utilisateurs u ON p.courriel = u.courriel
    WHERE tc.courriel_conducteur = ?
    ORDER BY p.nom ASC
");
$stmt->execute([$courriel_conducteur]);
$passagers = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Passagers de mes trajets - EcoRide</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="container mt-5">
  <h2> Passagers ayant réservé vos trajets</h2>

  <?php if (empty($passagers)): ?>
    <p class="alert alert-warning">Aucun passager n’a encore réservé vos trajets.</p>
  <?php else: ?>
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <?php foreach ($passagers as $p): ?>
        <div class="col">
          <div class="card h-100 shadow-sm">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($p['nom']) ?></h5>
              <p class="card-text">
                <strong>Genre :</strong> <?= htmlspecialchars($p['genre']) ?><br/>
                <strong>Téléphone :</strong> <?= htmlspecialchars($p['telephone']) ?><br/>
                <strong>Adresse :</strong> <?= nl2br(htmlspecialchars($p['adresse'])) ?><br/>
                <strong>Courriel :</strong> <?= htmlspecialchars($p['courriel']) ?>
              </p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

</body>
</html>
