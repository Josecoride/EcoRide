<?php 
session_start();
require '../includes/db_connection.php';

if (!isset($_SESSION['courriel'])) {
    header("Location: ../actions/connexion.php");
    exit();
}

$courriel = $_SESSION['courriel'];
$stmt = $pdo->prepare("SELECT nom, role FROM profils WHERE courriel = ?");
$stmt->execute([$courriel]);
$profil = $stmt->fetch();

$nom = htmlspecialchars($profil['nom']);
$role = $profil['role'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Tableau de bord - EcoRide</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="container mt-5">
  <h2 class="mb-4">Bienvenue, <?= $nom ?> !</h2>

  <?php if (!empty($_SESSION['message'])): ?>
    <div class="alert alert-success">
      <?= $_SESSION['message']; unset($_SESSION['message']); ?>
    </div>
  <?php endif; ?>

  <div class="row">

    <?php if (in_array($role, ['passager', 'les deux'])): ?>
      <div class="col-md-6 mb-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <h5 class="card-title"> Côté Passager</h5>
            <ul class="list-unstyled">
              <li><a href="../pages/rechercher_trajet.php" class="btn btn-outline-success w-100 my-1"> Rechercher un trajet</a></li>
               <li><a href="../pages/mes_trajets.php" class="btn btn-outline-info w-100 my-1">Mes trajets réservés</a></li>
              <li><a href="../actions/laisser_avis.php" class="btn btn-outline-warning w-100 my-1"> Laisser un avis</a></li>
              <li><a href="../pages/profil_utilisateur.php" class="btn btn-outline-dark w-100 my-1"> Mon profil</a></li>
            </ul>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <?php if (in_array($role, ['conducteur', 'les deux'])): ?>
      <div class="col-md-6 mb-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <h5 class="card-title"> Côté Conducteur</h5>
            <ul class="list-unstyled">
              <li><a href="../pages/proposer_trajet.php" class="btn btn-outline-success w-100 my-1">Proposer un trajet</a></li>
              <li><a href="../pages/trajets_conducteur.php" class="btn btn-outline-primary w-100 my-1"> Trajets proposés</a></li>
              <li><a href="../actions/passagers.php" class="btn btn-outline-info w-100 my-1"> Voir les passagers</a></li>
              <li><a href="../pages/profil_utilisateur.php" class="btn btn-outline-dark w-100 my-1"> Mon profil</a></li>
            </ul>
          </div>
        </div>
      </div>
    <?php endif; ?>

  </div>
</div>

</body>
</html>
