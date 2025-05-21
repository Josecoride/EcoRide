<?php
session_start();
include '../includes/nav.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Mot de passe oublié - Ecoride</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<div class="container mt-5" style="max-width: 400px;">
  <h2>Réinitialisation du mot de passe</h2>

  <?php
  if (isset($_SESSION['message'])) {
      echo '<div class="alert alert-info">' . htmlspecialchars($_SESSION['message']) . '</div>';
      unset($_SESSION['message']);
  }
  ?>

  <form method="POST" action="mdp_reset_envoi.php" novalidate>
    <div class="mb-3">
      <label for="email" class="form-label">Votre adresse e-mail</label>
      <input type="email" class="form-control" id="email" name="email" required />
      <div class="invalid-feedback">Veuillez saisir une adresse e-mail valide.</div>
    </div>
    <button type="submit" class="btn btn-primary">Envoyer le lien de réinitialisation</button>
  </form>
</div>

<script>
(() => {
  'use strict';
  const form = document.querySelector('form');
  form.addEventListener('submit', event => {
    if (!form.checkValidity()) {
      event.preventDefault();
      event.stopPropagation();
    }
    form.classList.add('was-validated');
  }, false);
})();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
