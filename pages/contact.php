<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Contact - EcoRide</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="container mt-5" style="max-width: 600px;">
  <h2>Contactez-nous</h2>

  <?php if (isset($_SESSION['contact_message'])): ?>
    <div class="alert alert-info"><?= htmlspecialchars($_SESSION['contact_message']); ?></div>
    <?php unset($_SESSION['contact_message']); ?>
  <?php endif; ?>

  <form action="../actions/contact_mail.php" method="POST" novalidate>
    <div class="mb-3">
      <label for="nom" class="form-label">Nom complet</label>
      <input type="text" class="form-control" id="nom" name="nom" required />
      <div class="invalid-feedback">Veuillez saisir votre nom.</div>
    </div>

    <div class="mb-3">
      <label for="courriel" class="form-label">Adresse e-mail</label>
      <input type="email" class="form-control" id="courriel" name="courriel" required />
      <div class="invalid-feedback">Veuillez saisir une adresse e-mail valide.</div>
    </div>

    <div class="mb-3">
      <label for="message" class="form-label">Message</label>
      <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
      <div class="invalid-feedback">Veuillez saisir un message.</div>
    </div>

    <button type="submit" class="btn btn-success">Envoyer</button>
  </form>
</div>

<script>
(() => {
  'use strict';
  const forms = document.querySelectorAll('form');
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });
})();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
