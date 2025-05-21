<?php
session_start();
require '../includes/db_connection.php';

if (!isset($_SESSION['courriel']) || !in_array($_SESSION['role'], ['conducteur', 'les deux'])) {
    header('Location: ../actions/connexion.php');
    exit();
}

$courriel = $_SESSION['courriel'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: tableau_de_bord.php');
    exit();
}

$id_trajet = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM trajets_proposes WHERE id_trajet = ? AND courriel_conducteur = ?");
$stmt->execute([$id_trajet, $courriel]);
$trajet = $stmt->fetch();

if (!$trajet) {
    $_SESSION['erreur'] = "Trajet introuvable ou non autorisé.";
    header('Location: tableau_de_bord.php');
    exit();
}


$stmtVeh = $pdo->prepare("SELECT id, marque, modele FROM vehicules WHERE courriel_conducteur = ?");
$stmtVeh->execute([$courriel]);
$vehicules = $stmtVeh->fetchAll();

$erreur = '';
$succes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $depart = trim($_POST['depart'] ?? '');
    $destination = trim($_POST['destination'] ?? '');
    $date = $_POST['date'] ?? '';
    $heure = $_POST['heure'] ?? '';
    $vehicule = (int)($_POST['vehicule'] ?? 0);
    $places = (int)($_POST['places_disponibles'] ?? 0);
    $prix = (float)($_POST['prix'] ?? 0);
    $duree = (int)($_POST['duree'] ?? 0);

    if (!$depart || !$destination || !$date || !$heure || $vehicule <= 0 || $places < 1 || $prix < 0 || $duree <= 0) {
        $erreur = "Veuillez remplir correctement tous les champs.";
    } else {
        $stmt = $pdo->prepare("UPDATE trajets_proposes 
            SET depart = ?, destination = ?, date_trajet = ?, heure_depart = ?, vehicule = ?, 
                places_disponibles = ?, prix = ?, duree = ? 
            WHERE id_trajet = ? AND courriel_conducteur = ?");
        $stmt->execute([$depart, $destination, $date, $heure, $vehicule, $places, $prix, $duree, $id_trajet, $courriel]);
        $succes = "Trajet mis à jour avec succès.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Modifier un trajet</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="container mt-4" style="max-width: 700px;">
  <h2>Modifier le trajet</h2>

  <?php if ($erreur): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
  <?php elseif ($succes): ?>
    <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
  <?php endif; ?>

  <form method="POST" novalidate>
    <div class="row mb-3">
      <div class="col-md-6">
        <label for="depart" class="form-label">Départ</label>
        <input type="text" name="depart" id="depart" class="form-control" value="<?= htmlspecialchars($trajet['depart']) ?>" required>
      </div>
      <div class="col-md-6">
        <label for="destination" class="form-label">Destination</label>
        <input type="text" name="destination" id="destination" class="form-control" value="<?= htmlspecialchars($trajet['destination']) ?>" required>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label for="date" class="form-label">Date</label>
        <input type="date" name="date" id="date" class="form-control" value="<?= htmlspecialchars($trajet['date_trajet']) ?>" required>
      </div>
      <div class="col-md-6">
        <label for="heure" class="form-label">Heure de départ</label>
        <input type="time" name="heure" id="heure" class="form-control" value="<?= htmlspecialchars($trajet['heure_depart']) ?>" required>
      </div>
    </div>

    <div class="mb-3">
      <label for="vehicule" class="form-label">Véhicule</label>
      <select name="vehicule" id="vehicule" class="form-select" required>
        <option value="">-- Choisir un véhicule --</option>
        <?php foreach ($vehicules as $v): ?>
          <option value="<?= $v['id'] ?>" <?= $trajet['vehicule'] == $v['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($v['marque'] . ' ' . $v['modele']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="row mb-3">
      <div class="col-md-4">
        <label for="places_disponibles" class="form-label">Places disponibles</label>
        <input type="number" name="places_disponibles" id="places_disponibles" class="form-control" value="<?= htmlspecialchars($trajet['places_disponibles']) ?>" min="1" required>
      </div>
      <div class="col-md-4">
        <label for="prix" class="form-label">Prix (€)</label>
        <input type="number" name="prix" id="prix" class="form-control" value="<?= htmlspecialchars($trajet['prix']) ?>" step="0.1" required>
      </div>
      <div class="col-md-4">
        <label for="duree" class="form-label">Durée estimée (min)</label>
        <input type="number" name="duree" id="duree" class="form-control" value="<?= htmlspecialchars($trajet['duree']) ?>" required>
      </div>
    </div>

    <button type="submit" class="btn btn-primary">Mettre à jour</button>
    <a href="tableau_de_bord.php" class="btn btn-secondary">Annuler</a>
  </form>
</div>

</body>
</html>
