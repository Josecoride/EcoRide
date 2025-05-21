<?php session_start(); ?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Accueil – EcoRide</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <link href="/EcoRide/assets/css/style.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body>

<?php include 'includes/nav.php'; ?>

<section class="hero">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-6 text-center text-md-start">
        <h1 class="hero-title mb-3">Réduisez votre impact, voyagez vert 🌍</h1>
        <p class="lead">Partagez vos trajets pour un avenir durable avec <strong>EcoRide</strong>.</p>
        <a href="actions/inscription.php" class="hero-btn btn">Créer un compte</a>
      </div>
      <div class="col-md-6 text-center">
        <img src="assets/images/voiture-eco.jpg" class="img-fluid" alt="Voiture écologique" />
      </div>
    </div>
  </div>
</section>

<section class="features">
  <div class="container">
    <h2>Trouvez un itinéraire</h2>
    <form action="pages/resultats_recherche.php" method="get" class="row justify-content-center">
      <div class="col-md-3 mb-2">
        <input type="text" name="depart" placeholder="Départ" class="form-control" required>
      </div>
      <div class="col-md-3 mb-2">
        <input type="text" name="destination" placeholder="Destination" class="form-control" required>
      </div>
      <div class="col-md-3 mb-2">
        <input type="date" name="date_trajet" class="form-control" required> 
      </div>
      <div class="col-md-2 mb-2">
        <button type="submit" class="btn btn-success w-100">Rechercher</button>
      </div>
    </form>
  </div>
</section>

<section class="about container mt-5 mb-5">
  <div class="row align-items-center">
    <div class="col-md-6">
      <img src="assets/images/nature.jpg" alt="Nature & écologie" class="img-fluid">
    </div>
    <div class="col-md-6">
      <h3>Notre mission</h3>
      <p>EcoRide est une plateforme française de covoiturage dédiée à l'écologie et à l'économie. Nous encourageons l’utilisation de véhicules électriques et favorisons la mobilité verte.</p>
    </div>
  </div>
</section>
<footer class="footer mt-5 text-center text-white py-4" style="background-color: #4CAF50;">
  <div class="container">
    <p class="mb-1">
      Contact : <a href="mailto:contact@ecoride.fr" class="text-white text-decoration-none">contact@ecoride.fr</a>
    </p>
    <p class="mb-1">
      <a href="pages/mentions_legales.php" class="text-white text-decoration-underline">Mentions légales</a>
    </p>
    <p class="mb-0">&copy; <?= date('Y') ?> EcoRide. Tous droits réservés.</p>
  </div>
</footer>



</body>
</html>
