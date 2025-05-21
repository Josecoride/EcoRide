<?php 
session_start();
require '../includes/db_connection.php';

$id_trajet = $_GET['id'] ?? null;

if (!$id_trajet || !is_numeric($id_trajet)) {
    header('Location: rechercher_trajet.php');
    exit();
}

$sql = "SELECT tp.*, p.nom AS pseudo, p.photo, p.note, v.marque, v.modele, v.energie, v.preferences
        FROM trajets_proposes tp
        JOIN profils p ON tp.courriel_conducteur = p.courriel
        JOIN vehicules v ON tp.vehicule = v.id
        WHERE tp.id_trajet = :id_trajet";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id_trajet' => $id_trajet]);
$trajet = $stmt->fetch();

if (!$trajet) {
    header('Location: rechercher_trajet.php');
    exit();
}

$is_ecologique = strtolower($trajet['energie']) === 'electrique';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Détail du trajet - EcoRide</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    .photo-profil {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
    }
    .eco-label {
      color: green;
      font-weight: bold;
    }
    .card-body {
      background-color: #f9f9f9;
    }
  </style>
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="container mt-4">
  <a href="rechercher_trajet.php" class="btn btn-sm btn-outline-secondary mb-3">&larr; Retour</a>

  <h2>Détail du trajet</h2>

  <div class="card mt-3">
    <div class="card-body">
      <h4><?= htmlspecialchars($trajet['depart']) ?> → <?= htmlspecialchars($trajet['destination']) ?></h4>
      <p>
        <strong>Date :</strong> <?= htmlspecialchars($trajet['date_trajet']) ?><br>
        <strong>Heure de départ :</strong> <?= substr($trajet['heure_depart'], 0, 5) ?><br>
        <strong>Heure d'arrivée :</strong> <?= $trajet['heure_arrivee'] ? substr($trajet['heure_arrivee'], 0, 5) : 'Non précisée' ?><br>
        <strong>Prix :</strong> <?= $trajet['prix'] ?> €<br>
        <strong>Places restantes :</strong> 
        <span class="badge bg-success"><?= $trajet['places_disponibles'] ?></span> / <?= $trajet['places_totales'] ?><br>
        <?php if ($is_ecologique): ?>
          <span class="eco-label">🚗 Ce trajet est écologique !</span><br>
        <?php endif; ?>
      </p>

      <hr>
      <h5>Informations sur le conducteur</h5>
      <div class="d-flex align-items-center">
        <img src="<?= $trajet['photo'] ? htmlspecialchars($trajet['photo']) : '../assets/images/default_user.png' ?>" alt="Photo conducteur" class="photo-profil me-3">
        <div>
          <strong><?= htmlspecialchars($trajet['pseudo']) ?></strong><br>
          Note : <?= $trajet['note'] ?? 'Non noté' ?>/5
        </div>
      </div>

      <hr>
      <h5>Véhicule</h5>
      <p>
        Marque : <?= htmlspecialchars($trajet['marque']) ?><br>
        Modèle : <?= htmlspecialchars($trajet['modele']) ?><br>
        Énergie : <?= htmlspecialchars($trajet['energie']) ?>
      </p>

      <hr>
      <h5>Préférences conducteur</h5>
      <p><?= nl2br(htmlspecialchars($trajet['preferences'])) ?></p>

      <?php if ($trajet['places_disponibles'] > 0): ?>
        <?php if (isset($_SESSION['courriel'])): ?>
          <a href="../actions/reserver_place.php?id=<?= $id_trajet ?>" class="btn btn-success">Participer</a>
        <?php else: ?>
          <a href="../actions/connexion.php" class="btn btn-outline-primary">Connectez-vous pour participer</a>
        <?php endif; ?>
      <?php else: ?>
        <div class="alert alert-warning">Ce trajet est complet.</div>
      <?php endif; ?>
    </div>
  </div>
</div><?php 
session_start();
require '../includes/db_connection.php';

$id_trajet = $_GET['id'] ?? null;

if (!$id_trajet || !is_numeric($id_trajet)) {
    header('Location: ../pages/rechercher_trajet.php');
    exit();
}

$sql = "SELECT tp.*, p.nom AS pseudo, p.photo, p.note, v.marque, v.modele, v.energie, v.preferences
        FROM trajets_proposes tp
        JOIN profils p ON tp.courriel_conducteur = p.courriel
        JOIN vehicules v ON tp.vehicule = v.id
        WHERE tp.id_trajet = :id_trajet";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id_trajet' => $id_trajet]);
$trajet = $stmt->fetch();

if (!$trajet) {
    header('Location: ../pages/rechercher_trajet.php');
    exit();
}

$is_ecologique = strtolower($trajet['energie']) === 'electrique';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Détail du trajet - EcoRide</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    .photo-profil {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
    }
    .eco-label {
      color: green;
      font-weight: bold;
    }
    .card-body {
      background-color: #f9f9f9;
    }
  </style>
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="container mt-4">
  <a href="../pages/rechercher_trajet.php" class="btn btn-sm btn-outline-secondary mb-3">&larr; Retour</a>

  <h2>Détail du trajet</h2>

  <div class="card mt-3">
    <div class="card-body">
      <h4><?= htmlspecialchars($trajet['depart']) ?> → <?= htmlspecialchars($trajet['destination']) ?></h4>
      <p>
        <strong>Date :</strong> <?= htmlspecialchars($trajet['date_trajet']) ?><br>
        <strong>Heure de départ :</strong> <?= substr($trajet['heure_depart'], 0, 5) ?><br>
        <strong>Heure d'arrivée :</strong> <?= $trajet['heure_arrivee'] ? substr($trajet['heure_arrivee'], 0, 5) : 'Non précisée' ?><br>
        <strong>Prix :</strong> <?= $trajet['prix'] ?> €<br>
        <strong>Places restantes :</strong> 
        <span class="badge bg-success"><?= $trajet['places_disponibles'] ?></span> / <?= $trajet['places_totales'] ?><br>
        <?php if ($is_ecologique): ?>
          <span class="eco-label">🚗 Ce trajet est écologique !</span><br>
        <?php endif; ?>
      </p>

      <hr>
      <h5>Informations sur le conducteur</h5>
      <div class="d-flex align-items-center">
        <img src="<?= $trajet['photo'] ? htmlspecialchars($trajet['photo']) : '../assets/images/default_user.png' ?>" alt="Photo conducteur" class="photo-profil me-3">
        <div>
          <strong><?= htmlspecialchars($trajet['pseudo']) ?></strong><br>
          Note : <?= $trajet['note'] ?? 'Non noté' ?>/5
        </div>
      </div>

      <hr>
      <h5>Véhicule</h5>
      <p>
        Marque : <?= htmlspecialchars($trajet['marque']) ?><br>
        Modèle : <?= htmlspecialchars($trajet['modele']) ?><br>
        Énergie : <?= htmlspecialchars($trajet['energie']) ?>
      </p>

      <hr>
      <h5>Préférences conducteur</h5>
      <p><?= nl2br(htmlspecialchars($trajet['preferences'])) ?></p>

      <?php if ($trajet['places_disponibles'] > 0): ?>
        <?php if (isset($_SESSION['courriel'])): ?>
          <a href="../actions/reserver_place.php?id=<?= $id_trajet ?>" class="btn btn-success">Participer</a>
        <?php else: ?>
          <a href="../actions/connexion.php" class="btn btn-outline-primary">Connectez-vous pour participer</a>
        <?php endif; ?>
      <?php else: ?>
        <div class="alert alert-warning">Ce trajet est complet.</div>
      <?php endif; ?>
    </div>
  </div>
</div>

</body>
</html>


</body>
</html>
