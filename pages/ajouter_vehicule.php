<?php
session_start();
require_once '../includes/db_connection.php';

if (!isset($_SESSION['courriel'])) {
    header("Location: ../actions/connexion.php");
    exit();
}

$courriel = $_SESSION['courriel'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marque = trim($_POST['marque']);
    $modele = trim($_POST['modele']);
    $energie = $_POST['energie'];
    $couleur = trim($_POST['couleur']);
    $plaque = trim($_POST['plaque']);
    $date_immatriculation = $_POST['date_immatriculation'];
    $preferences = trim($_POST['preferences']);

    $stmt = $pdo->prepare("INSERT INTO vehicules (courriel_conducteur, marque, modele, energie, couleur, plaque, date_immatriculation, preferences) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$courriel, $marque, $modele, $energie, $couleur, $plaque, $date_immatriculation, $preferences]);

    $message = "Véhicule ajouté avec succès !";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Ajouter un véhicule - EcoRide</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="container mt-5" style="max-width: 700px;">
  <h2>Ajouter un véhicule</h2>

  <?php if ($message): ?>
    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Marque</label>
      <input type="text" name="marque" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Modèle</label>
      <input type="text" name="modele" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Énergie</label>
      <select name="energie" class="form-select" required>
        <option value="essence">Essence</option>
        <option value="diesel">Diesel</option>
        <option value="electrique">Électrique</option>
        <option value="hybride">Hybride</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Couleur</label>
      <input type="text" name="couleur" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Plaque</label>
      <input type="text" name="plaque" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Date d'immatriculation</label>
      <input type="date" name="date_immatriculation" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Préférences (optionnel)</label>
      <textarea name="preferences" class="form-control"></textarea>
    </div>
    <button type="submit" class="btn btn-success">Ajouter le véhicule</button>
  </form>
</div>

</body>
</html>
