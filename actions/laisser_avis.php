<?php
session_start();
require '../includes/db_connection.php';

if (!isset($_SESSION['courriel'])) {
    header('Location: connexion.php');
    exit();
}

$courriel_passager = $_SESSION['courriel'];
$succes = '';
$erreur = '';

// Soumission d'un avis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_trajet = (int)($_POST['id_trajet'] ?? 0);
    $courriel_conducteur = trim($_POST['courriel_conducteur'] ?? '');
    $note = (int)($_POST['note'] ?? 0);
    $commentaire = trim($_POST['commentaire'] ?? '');

    if ($note < 1 || $note > 5) {
        $erreur = "Note invalide (entre 1 et 5).";
    } elseif ($id_trajet <= 0 || empty($courriel_conducteur)) {
        $erreur = "Données invalides.";
    } else {
        // Vérifie si déjà évalué
        $check = $pdo->prepare("SELECT COUNT(*) FROM avis WHERE id_trajet = ? AND courriel_passager = ?");
        $check->execute([$id_trajet, $courriel_passager]);
        if ($check->fetchColumn() > 0) {
            $erreur = "Vous avez déjà laissé un avis pour ce trajet.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO avis (id_trajet, courriel_passager, courriel_conducteur, note, commentaire, est_valide)
                                   VALUES (?, ?, ?, ?, ?, 1)");
            $stmt->execute([$id_trajet, $courriel_passager, $courriel_conducteur, $note, $commentaire]);
            $succes = "Merci pour votre avis !";
        }
    }
}

// Trajets à évaluer
$stmt = $pdo->prepare("
    SELECT tc.id_trajet, tc.date_trajet, tc.depart, tc.destination, tc.courriel_conducteur, p.nom AS conducteur_nom
    FROM trajets_confirmes tc
    JOIN trajets_proposes tp ON tc.id_trajet = tp.id_trajet
    JOIN profils p ON tp.courriel_conducteur = p.courriel
    WHERE tc.courriel_passager = ?
      AND tp.statut = 'terminé'
      AND tc.id_trajet NOT IN (
          SELECT id_trajet FROM avis WHERE courriel_passager = ?
      )
    ORDER BY tc.date_trajet DESC
");
$stmt->execute([$courriel_passager, $courriel_passager]);
$trajets = $stmt->fetchAll();

// Avis déjà envoyés
$stmt = $pdo->prepare("
    SELECT a.note, a.commentaire, a.date_avis, tp.depart, tp.destination, tp.date_trajet, p.nom AS conducteur_nom
    FROM avis a
    JOIN trajets_proposes tp ON a.id_trajet = tp.id_trajet
    JOIN profils p ON a.courriel_conducteur = p.courriel
    WHERE a.courriel_passager = ?
    ORDER BY a.date_avis DESC
");
$stmt->execute([$courriel_passager]);
$avis_envoyes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Laisser un avis</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="container mt-5" style="max-width: 850px;">
  <h2> Laisser un avis</h2>

  <?php if ($succes): ?>
    <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
  <?php elseif ($erreur): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
  <?php endif; ?>

  <?php if (empty($trajets)): ?>
    <div class="alert alert-info">Aucun trajet terminé à évaluer pour le moment.</div>
  <?php else: ?>
    <h4 class="mt-4"> Trajets à évaluer</h4>
    <?php foreach ($trajets as $t): ?>
      <form method="POST" class="border rounded p-3 mb-4 shadow-sm bg-light">
        <h5><?= htmlspecialchars($t['depart']) ?> → <?= htmlspecialchars($t['destination']) ?> (<?= $t['date_trajet'] ?>)</h5>
        <p>Conducteur : <?= htmlspecialchars($t['conducteur_nom']) ?></p>

        <input type="hidden" name="id_trajet" value="<?= $t['id_trajet'] ?>">
        <input type="hidden" name="courriel_conducteur" value="<?= $t['courriel_conducteur'] ?>">

        <div class="mb-2">
          <label>Note (1 à 5)</label>
          <input type="number" name="note" min="1" max="5" class="form-control" required>
        </div>

        <div class="mb-2">
          <label>Commentaire</label>
          <textarea name="commentaire" class="form-control" rows="2"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Envoyer</button>
      </form>
    <?php endforeach; ?>
  <?php endif; ?>

  <hr class="my-5">

  <h4> Mes avis envoyés</h4>
  <?php if (empty($avis_envoyes)): ?>
    <p class="text-muted">Vous n'avez encore laissé aucun avis.</p>
  <?php else: ?>
    <?php foreach ($avis_envoyes as $avis): ?>
      <div class="border rounded p-3 mb-3 bg-white shadow-sm">
        <strong><?= htmlspecialchars($avis['depart']) ?> → <?= htmlspecialchars($avis['destination']) ?> (<?= $avis['date_trajet'] ?>)</strong><br>
        Conducteur : <?= htmlspecialchars($avis['conducteur_nom']) ?><br>
        Note :  <?= $avis['note'] ?>/5<br>
        <?php if (!empty($avis['commentaire'])): ?>
          <em><?= nl2br(htmlspecialchars($avis['commentaire'])) ?></em>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

</body>
</html>
