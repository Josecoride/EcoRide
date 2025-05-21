<?php
session_start();
require '../includes/db_connection.php';

if (!isset($_GET['courriel']) || !filter_var($_GET['courriel'], FILTER_VALIDATE_EMAIL)) {
    header("Location: ../index.php");
    exit();
}

$courriel = $_GET['courriel'];

$stmt = $pdo->prepare("SELECT * FROM profils WHERE courriel = ?");
$stmt->execute([$courriel]);
$profil = $stmt->fetch();

if (!$profil) {
    echo "Conducteur introuvable.";
    exit();
}


$stmt = $pdo->prepare("SELECT a.note, a.commentaire, a.date_avis, u.courriel AS passager_email
                       FROM avis a
                       JOIN utilisateurs u ON a.courriel_passager = u.courriel
                       WHERE a.courriel_conducteur = ? AND a.est_valide = 1
                       ORDER BY a.date_avis DESC");
$stmt->execute([$courriel]);
$avis = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Profil du conducteur - EcoRide</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="container mt-5">
  <h2>Profil de <?= htmlspecialchars($profil['nom']) ?></h2>

  <ul class="list-group mb-4">
    <li class="list-group-item"><strong>Genre :</strong> <?= htmlspecialchars($profil['genre']) ?></li>
    <li class="list-group-item"><strong>Téléphone :</strong> <?= htmlspecialchars($profil['telephone']) ?></li>
    <li class="list-group-item"><strong>Adresse :</strong> <?= nl2br(htmlspecialchars($profil['adresse'])) ?></li>
    <li class="list-group-item"><strong>Note moyenne :</strong> <?= $profil['note'] ?? "Pas encore noté" ?></li>
    <li class="list-group-item"><strong>Rôle :</strong> <?= htmlspecialchars($profil['role']) ?></li>
  </ul>

  <h4> Avis des passagers</h4>

  <?php if (empty($avis)): ?>
    <p>Aucun avis validé pour ce conducteur.</p>
  <?php else: ?>
    <?php foreach ($avis as $a): ?>
      <div class="card mb-3">
        <div class="card-body">
          <h6 class="card-title">Note : <?= (int)$a['note'] ?>/5</h6>
          <p class="card-text"><?= nl2br(htmlspecialchars($a['commentaire'])) ?></p>
          <small class="text-muted">Posté le <?= htmlspecialchars($a['date_avis']) ?> par <?= htmlspecialchars($a['passager_email']) ?></small>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <a href="conducteurs.php" class="btn btn-outline-secondary mt-4">← Retour à la liste</a>
</div>

</body>
</html>
