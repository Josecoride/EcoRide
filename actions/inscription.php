<?php
session_start();
$messageErreur = $_SESSION['erreur'] ?? '';
$messageSuccess = $_SESSION['success'] ?? '';
unset($_SESSION['erreur'], $_SESSION['success']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Inscription - EcoRide</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="../assets/css/style.css" rel="stylesheet" />
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="container mt-5" style="max-width: 500px;">
  <h2 class="mb-4 text-center">Créer un compte</h2>

  <?php if ($messageErreur): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($messageErreur) ?></div>
  <?php endif; ?>
  
  <?php if ($messageSuccess): ?>
    <div class="alert alert-success"><?= htmlspecialchars($messageSuccess) ?></div>
  <?php endif; ?>

  <form action="validation_inscription.php" method="POST" novalidate>
    <div class="mb-3">
      <label for="nom" class="form-label">Nom complet</label>
      <input type="text" class="form-control" id="nom" name="nom" required />
    </div>

    <div class="mb-3">
      <label for="genre" class="form-label">Genre</label>
      <select class="form-select" id="genre" name="genre" required>
        <option value="" selected disabled>Choisissez</option>
        <option value="M">Homme</option>
        <option value="F">Femme</option>
        <option value="Autre">Autre</option>
      </select>
    </div>

    <div class="mb-3">
      <label for="telephone" class="form-label">Téléphone</label>
      <input type="tel" class="form-control" id="telephone" name="telephone" required pattern="^\+?[0-9\s\-]{6,15}$" />
    </div>

    <div class="mb-3">
      <label for="courriel" class="form-label">Adresse e-mail</label>
      <input type="email" class="form-control" id="courriel" name="courriel" required />
    </div>

    <div class="mb-3">
      <label for="adresse" class="form-label">Adresse</label>
      <textarea class="form-control" id="adresse" name="adresse" rows="2" required></textarea>
    </div>

    <div class="mb-3">
      <label for="mot_de_passe" class="form-label">Mot de passe</label>
      <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" minlength="6" required />
    </div>

    <div class="mb-3">
      <label for="mot_de_passe_confirm" class="form-label">Confirmer le mot de passe</label>
      <input type="password" class="form-control" id="mot_de_passe_confirm" name="mot_de_passe_confirm" minlength="6" required />
    </div>

    <div class="mb-3">
      <label for="role" class="form-label">Vous vous inscrivez en tant que :</label>
      <select class="form-select" id="role" name="role" required>
        <option value="" selected disabled>Choisissez un rôle</option>
        <option value="conducteur">Conducteur</option>
        <option value="passager">Passager</option>
      </select>
    </div>

    <button type="submit" class="btn btn-success w-100">S'inscrire</button>
  </form>
</div>

<script>
  
  (() => {
    const form = document.querySelector('form');
    const mdp = document.getElementById('mot_de_passe');
    const confirm = document.getElementById('mot_de_passe_confirm');

    form.addEventListener('submit', e => {
      if (mdp.value !== confirm.value) {
        confirm.setCustomValidity("Les mots de passe ne correspondent pas.");
        e.preventDefault();
        e.stopPropagation();
      } else {
        confirm.setCustomValidity('');
      }

      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
      }

      form.classList.add('was-validated');
    });
  })();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
