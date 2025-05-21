<?php 
session_start();
require '../includes/db_connection.php';

if (!isset($_SESSION['courriel'])) {
    header('Location: ../actions/connexion.php');
    exit();
}

$courriel = $_SESSION['courriel'];

$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);

$stmt = $pdo->prepare('SELECT * FROM profils WHERE courriel = ?');
$stmt->execute([$courriel]);
$profil = $stmt->fetch();

$erreur = '';
$succes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $genre = $_POST['genre'] ?? '';
    $telephone = trim($_POST['telephone'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $role = $_POST['role'] ?? '';

    if (!$nom || !$genre || !$telephone || !$adresse || !in_array($role, ['conducteur', 'passager', 'les deux'])) {
        $erreur = "Veuillez remplir tous les champs correctement.";
    } else {
        $pdo->prepare('UPDATE profils SET nom = ?, genre = ?, telephone = ?, adresse = ?, role = ? WHERE courriel = ?')
            ->execute([$nom, $genre, $telephone, $adresse, $role, $courriel]);
        $succes = "Profil mis à jour avec succès.";
    }

    if (in_array($role, ['conducteur', 'les deux']) && isset($_POST['ajouter_vehicule'])) {
        $marque = trim($_POST['marque'] ?? '');
        $modele = trim($_POST['modele'] ?? '');
        $energie = $_POST['energie'] ?? '';
        $couleur = trim($_POST['couleur'] ?? '');
        $plaque = trim($_POST['plaque'] ?? '');
        $date_immat = $_POST['date_immat'] ?? '';
        $places = intval($_POST['places'] ?? 1);
        $preferences = trim($_POST['preferences'] ?? '');

        if ($marque && $modele && $energie && $plaque && $date_immat && $places > 0) {
            $pdo->prepare("INSERT INTO vehicules (courriel_conducteur, marque, modele, energie, couleur, plaque, date_immatriculation, preferences)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)")
                ->execute([$courriel, $marque, $modele, $energie, $couleur, $plaque, $date_immat, $preferences]);
            $succes .= "<br>Véhicule ajouté.";
        } else {
            $erreur .= "<br>Informations véhicule incomplètes.";
        }
    }

    $stmt = $pdo->prepare('SELECT * FROM profils WHERE courriel = ?');
    $stmt->execute([$courriel]);
    $profil = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Mon profil - EcoRide</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script>
    function toggleVehiculeForm() {
      const role = document.querySelector('select[name="role"]').value;
      document.getElementById('vehiculeForm').style.display = (role === 'conducteur' || role === 'les deux') ? 'block' : 'none';
    }
  </script>
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="container mt-5" style="max-width: 800px;">
  <h2> Mon profil</h2>

  <?php if ($message): ?>
    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <?php if ($erreur): ?>
    <div class="alert alert-danger"><?= $erreur ?></div>
  <?php elseif ($succes): ?>
    <div class="alert alert-success"><?= $succes ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="row mb-3">
      <div class="col">
        <label>Nom complet</label>
        <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($profil['nom'] ?? '') ?>" required />
      </div>
      <div class="col">
        <label>Genre</label>
        <select name="genre" class="form-control" required>
          <option value="H" <?= ($profil['genre'] ?? '') === 'H' ? 'selected' : '' ?>>H</option>
          <option value="F" <?= ($profil['genre'] ?? '') === 'F' ? 'selected' : '' ?>>F</option>
          <option value="Autre" <?= ($profil['genre'] ?? '') === 'Autre' ? 'selected' : '' ?>>Autre</option>
        </select>
      </div>
    </div>

    <div class="mb-3">
      <label>Adresse</label>
      <input type="text" name="adresse" class="form-control" value="<?= htmlspecialchars($profil['adresse'] ?? '') ?>" required />
    </div>

    <div class="mb-3">
      <label>Téléphone</label>
      <input type="tel" name="telephone" class="form-control" value="<?= htmlspecialchars($profil['telephone'] ?? '') ?>" required />
    </div>

    <div class="mb-3">
      <label>Rôle</label>
      <select name="role" class="form-control" onchange="toggleVehiculeForm()" required>
        <option value="passager" <?= $profil['role'] === 'passager' ? 'selected' : '' ?>>Passager</option>
        <option value="conducteur" <?= $profil['role'] === 'conducteur' ? 'selected' : '' ?>>Conducteur</option>
        <option value="les deux" <?= $profil['role'] === 'les deux' ? 'selected' : '' ?>>Les deux</option>
      </select>
    </div>

    <button type="submit" class="btn btn-primary">Mettre à jour mon profil</button>
<?php if (in_array($profil['role'], ['conducteur', 'les deux'])): ?>
  <div class="mb-3 text-center">
    <a href="ajouter_vehicule.php" class="btn btn-primary btn-lg">Ajouter un véhicule</a>
  </div>
<?php endif; ?>


</div>

</body>
</html>
