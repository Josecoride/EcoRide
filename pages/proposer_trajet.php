<?php
session_start();
require_once '../includes/db_connection.php';

if (!isset($_SESSION['courriel'])) {
    header('Location: ../actions/connexion.php');
    exit();   
}

$courriel = $_SESSION['courriel'];


$stmt = $pdo->prepare("SELECT role FROM profils WHERE courriel = ?");
$stmt->execute([$courriel]);
$role = $stmt->fetchColumn();

if (!in_array($role, ['conducteur', 'les deux'])) {
    $_SESSION['erreur_trajet'] = "Seuls les conducteurs peuvent proposer un trajet.";
    header('Location: tableau_de_bord.php');
    exit();
}


$stmt = $pdo->prepare("SELECT id, marque, modele FROM vehicules WHERE courriel_conducteur = ?");
$stmt->execute([$courriel]);
$vehicules = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Proposer un trajet - EcoRide</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="container mt-5" style="max-width: 600px;">
  <h2 class="mb-4">Proposer un trajet</h2>

  <?php if (!empty($_SESSION['erreur_trajet'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['erreur_trajet']) ?></div>
    <?php unset($_SESSION['erreur_trajet']); ?>
  <?php endif; ?>

  <?php if (!empty($_SESSION['succes_trajet'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_SESSION['succes_trajet']) ?></div>
    <?php unset($_SESSION['succes_trajet']); ?>
  <?php endif; ?>

  <form action="validation_proposer_trajet.php" method="POST" novalidate>
    <div class="mb-3">
      <label for="date_trajet" class="form-label">Date du trajet</label>
      <input type="date" class="form-control" id="date_trajet" name="date_trajet" required />
    </div>

    <div class="mb-3">
      <label for="depart" class="form-label">Lieu de départ</label>
      <input type="text" class="form-control" id="depart" name="depart" required />
    </div>

    <div class="mb-3">
      <label for="destination" class="form-label">Destination</label>
      <input type="text" class="form-control" id="destination" name="destination" required />
    </div>

    <div class="mb-3">
      <label for="vehicule" class="form-label">Véhicule</label>
      <select class="form-select" id="vehicule" name="vehicule" required>
        <option value="">-- Sélectionner un véhicule --</option>
        <?php foreach ($vehicules as $v): ?>
          <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['marque'] . ' ' . $v['modele']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label for="places_totales" class="form-label">Places totales</label>
        <input type="number" class="form-control" id="places_totales" name="places_totales" min="1" max="10" required />
      </div>

      <div class="col-md-6 mb-3">
        <label for="places_disponibles" class="form-label">Places disponibles</label>
        <input type="number" class="form-control" id="places_disponibles" name="places_disponibles" min="1" max="10" required />
      </div>
    </div>

    <div class="mb-3">
      <label for="prix" class="form-label">Prix (€)</label>
      <input type="number" step="0.01" class="form-control" id="prix" name="prix" required />
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label for="heure_depart" class="form-label">Heure de départ</label>
        <input type="time" class="form-control" id="heure_depart" name="heure_depart" required />
      </div>
      <div class="col-md-6 mb-3">
        <label for="heure_arrivee" class="form-label">Heure d'arrivée (facultative)</label>
        <input type="time" class="form-control" id="heure_arrivee" name="heure_arrivee" />
      </div>
    </div>

    <div class="mb-3">
      <label for="description" class="form-label">Description (facultative)</label>
      <textarea class="form-control" id="description" name="description" rows="3"></textarea>
    </div>

    <button type="submit" class="btn btn-success w-100">Publier le trajet</button>
  </form>
</div>

</body>
</html>
