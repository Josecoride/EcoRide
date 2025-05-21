<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Rechercher un trajet - EcoRide</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="container mt-5" style="max-width: 800px;">
  <h2 class="mb-4"> Rechercher un covoiturage</h2>

  <?php if (!empty($_SESSION['erreur'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['erreur']) ?></div>
    <?php unset($_SESSION['erreur']); ?>
  <?php endif; ?>

  <form method="GET" action="resultats_recherche.php">
    <div class="row mb-3">
      <div class="col-md-6">
        <label for="depart" class="form-label">Ville de départ</label>
        <input type="text" name="depart" id="depart" class="form-control" list="villes-list" required>
      </div>
      <div class="col-md-6">
        <label for="destination" class="form-label">Ville d’arrivée</label>
        <input type="text" name="destination" id="destination" class="form-control" list="villes-list" required>
      </div>
    </div>

    <datalist id="villes-list"></datalist>

    <div class="mb-3">
      <label for="date_trajet" class="form-label">Date du trajet</label>
      <input type="date" name="date_trajet" id="date_trajet" class="form-control" required>
    </div>

    <h5 class="mt-4"> Filtres facultatifs</h5>

    <div class="row mb-3">
      <div class="col-md-4">
        <label for="ecologique" class="form-label">Type de véhicule</label>
        <select name="ecologique" id="ecologique" class="form-select">
          <option value="">Tous</option>
          <option value="1">Électrique uniquement</option>
          <option value="0">Exclure électrique</option>
        </select>
      </div>
      <div class="col-md-4">
        <label for="prix_max" class="form-label">Prix max (€)</label>
        <input type="number" name="prix_max" id="prix_max" step="0.1" class="form-control">
      </div>
      <div class="col-md-4">
        <label for="note_min" class="form-label">Note min (/5)</label>
        <input type="number" name="note_min" id="note_min" step="0.1" class="form-control">
      </div>
    </div>

    <div class="mb-3">
      <label for="duree_max" class="form-label">Durée maximale (minutes)</label>
      <input type="number" name="duree_max" id="duree_max" class="form-control">
    </div>

    <div class="text-center">
      <button type="submit" class="btn btn-success w-50">Rechercher</button>
    </div>
  </form>
</div>

<script>
fetch('../assets/data/villes.json')
  .then(response => response.json())
  .then(villes => {
    const datalist = document.getElementById('villes-list');
    villes.forEach(ville => {
      const option = document.createElement('option');
      option.value = ville;
      datalist.appendChild(option);
    });
  });
</script>

</body>
</html>
