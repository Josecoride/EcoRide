<?php
session_start();
require_once '../includes/db_connection.php';

if (!isset($_SESSION['admin_email'])) {
    header('Location: ../actions/connexion_admin.php');
    exit();
}

$statsTrajets = $pdo->query("SELECT date_trajet, COUNT(*) AS nb FROM trajets_confirmes GROUP BY date_trajet")->fetchAll();
$statsCredits = $pdo->query("SELECT date_prise, SUM(credits_preleves) AS total FROM logs_credits GROUP BY date_prise")->fetchAll();
$totalCredits = $pdo->query("SELECT SUM(credits_preleves) AS total FROM logs_credits")->fetchColumn();

$utilisateurs = $pdo->query("SELECT courriel, actif FROM utilisateurs")->fetchAll();
$employes = $pdo->query("SELECT courriel FROM employes")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Espace Administrateur - EcoRide</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="container mt-5">

  <?php if (isset($_GET['suppr'])): ?>
    <?php if ($_GET['suppr'] === 'ok'): ?>
      <div class="alert alert-success">Suppression réussie.</div>
    <?php elseif ($_GET['suppr'] === 'introuvable'): ?>
      <div class="alert alert-warning">Aucun compte trouvé avec ce courriel.</div>
    <?php elseif ($_GET['suppr'] === 'erreur_format'): ?>
      <div class="alert alert-danger">Format d'e-mail invalide.</div>
    <?php elseif ($_GET['suppr'] === 'erreur_sql'): ?>
      <div class="alert alert-danger">Erreur SQL lors de la suppression.</div>
    <?php else: ?>
      <div class="alert alert-danger">Action non autorisée.</div>
    <?php endif; ?>
  <?php endif; ?>

  <?php if (isset($_GET['etat']) && $_GET['etat'] === 'ok'): ?>
    <div class="alert alert-success">Modification du statut effectuée.</div>
  <?php elseif (isset($_GET['etat']) && $_GET['etat'] === 'erreur'): ?>
    <div class="alert alert-danger">Erreur lors de la mise à jour du statut.</div>
  <?php endif; ?>

  <?php if (isset($_GET['ajout']) && $_GET['ajout'] === 'ok'): ?>
    <div class="alert alert-success">Employé ajouté avec succès.</div>
  <?php elseif (isset($_GET['ajout']) && $_GET['ajout'] === 'erreur'): ?>
    <div class="alert alert-danger">Erreur : employé déjà existant ou champ invalide.</div>
  <?php endif; ?>

  <h1>Bienvenue, Administrateur</h1>

  <section class="mt-4">
    <h3>Crédits totaux gagnés :</h3>
    <p class="fw-bold"><?= $totalCredits ?> crédits</p>
  </section>

  <section class="mt-4">
    <h3>Trajets par jour :</h3>
    <ul class="list-group">
      <?php foreach ($statsTrajets as $s): ?>
        <li class="list-group-item"><?= htmlspecialchars($s['date_trajet']) ?> : <?= $s['nb'] ?> trajets</li>
      <?php endforeach; ?>
    </ul>
  </section>

  <section class="mt-4">
    <h3>Crédits gagnés par jour :</h3>
    <ul class="list-group">
      <?php foreach ($statsCredits as $c): ?>
        <li class="list-group-item"><?= htmlspecialchars($c['date_prise']) ?> : <?= $c['total'] ?> crédits</li>
      <?php endforeach; ?>
    </ul>
  </section>

 
  <section class="mt-5">
    <h3>Gestion des utilisateurs</h3>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Courriel</th>
          <th>Statut</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($utilisateurs as $u): ?>
          <tr>
            <td><?= htmlspecialchars($u['courriel']) ?></td>
            <td><?= $u['actif'] ? 'Actif' : 'Suspendu' ?></td>
            <td>
              <?php if ($u['actif']): ?>
                <a href="../actions/suspendre_utilisateur.php?courriel=<?= urlencode($u['courriel']) ?>" class="btn btn-warning btn-sm">Suspendre</a>
              <?php else: ?>
                <a href="../actions/reactiver_utilisateur.php?courriel=<?= urlencode($u['courriel']) ?>" class="btn btn-success btn-sm">Réactiver</a>
              <?php endif; ?>
              <a href="../actions/supprimer_utilisateur.php?courriel=<?= urlencode($u['courriel']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer définitivement cet utilisateur ?')">Supprimer</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>

  <section class="mt-5">
    <h3>Ajouter un employé</h3>
    <form action="../actions/ajouter_employe.php" method="POST" class="row g-3" style="max-width: 500px;">
      <div class="col-12">
        <label for="courriel" class="form-label">Adresse e-mail</label>
        <input type="email" name="courriel" id="courriel" class="form-control" required>
      </div>
      <div class="col-12">
        <label for="mot_de_passe" class="form-label">Mot de passe</label>
        <input type="password" name="mot_de_passe" id="mot_de_passe" class="form-control" required>
      </div>
      <div class="col-12">
        <button type="submit" class="btn btn-primary">Ajouter</button>
      </div>
    </form>
  </section>

  <section class="mt-5">
    <h3>Liste des employés</h3>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Courriel</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($employes as $e): ?>
          <tr>
            <td><?= htmlspecialchars($e['courriel']) ?></td>
            <td>
              <a href="../actions/supprimer_utilisateur.php?courriel=<?= urlencode($e['courriel']) ?>&type=employe"
                 class="btn btn-danger btn-sm"
                 onclick="return confirm('Supprimer cet employé ?')">
                 Supprimer
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>

  <div class="mt-5">
    <a href="../actions/deconnexion.php" class="btn btn-danger">Déconnexion</a>
  </div>
</div>

</body>
</html>
