<?php
session_start();
require_once '../includes/db_connection.php';


if (!isset($_SESSION['courriel'])) {
    header('Location: ../actions/connexion.php');
    exit();
}

$courriel = $_SESSION['courriel'];
$id_trajet = $_GET['id'] ?? null;

if (!$id_trajet || !is_numeric($id_trajet)) {
    $_SESSION['erreur'] = "Trajet invalide.";
    header("Location: ../pages/rechercher_trajet.php");
    exit();
}


if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


$stmt = $pdo->prepare("
    SELECT tp.*, v.energie, p.nom AS conducteur_nom, p.photo 
    FROM trajets_proposes tp
    JOIN vehicules v ON tp.vehicule = v.id
    JOIN profils p ON tp.courriel_conducteur = p.courriel
    WHERE tp.id_trajet = ?
");
$stmt->execute([$id_trajet]);
$trajet = $stmt->fetch();

if (!$trajet) {
    $_SESSION['erreur'] = "Trajet introuvable.";
    header("Location: ../pages/rechercher_trajet.php");
    exit();
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM trajets_confirmes WHERE id_trajet = ? AND courriel_passager = ?");
$stmt->execute([$id_trajet, $courriel]);
$deja_reserve = $stmt->fetchColumn() > 0;

$stmt = $pdo->prepare("SELECT credits FROM utilisateurs WHERE courriel = ?");
$stmt->execute([$courriel]);
$credits = $stmt->fetchColumn();

$erreur = '';
$succes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $erreur = "Requête non autorisée.";
    } else {
        $places = max(1, intval($_POST['places'] ?? 1));

        if ($places > $trajet['places_disponibles']) {
            $erreur = "Pas assez de places disponibles.";
        } elseif ($credits < $places) {
            $erreur = "Crédits insuffisants.";
        } elseif ($deja_reserve) {
            $erreur = "Vous avez déjà réservé ce trajet.";
        } else {
            try {
                $pdo->beginTransaction();

                $stmt = $pdo->prepare("
                    INSERT INTO trajets_confirmes (id_trajet, courriel_passager, courriel_conducteur, date_trajet, depart, destination, vehicule, places_reservees, heure_depart)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $trajet['id_trajet'],
                    $courriel,
                    $trajet['courriel_conducteur'],
                    $trajet['date_trajet'],
                    $trajet['depart'],
                    $trajet['destination'],
                    $trajet['vehicule'],
                    $places,
                    $trajet['heure_depart']
                ]);

                
                $stmt = $pdo->prepare("UPDATE utilisateurs SET credits = credits - ? WHERE courriel = ?");
                $stmt->execute([$places, $courriel]);

                
                $stmt = $pdo->prepare("UPDATE trajets_proposes SET places_disponibles = places_disponibles - ? WHERE id_trajet = ?");
                $stmt->execute([$places, $id_trajet]);

                $pdo->commit();

                $_SESSION['message'] = " Réservation réussie pour $places place(s) !";
                unset($_SESSION['csrf_token']);

                header("Location: ../pages/tableau_de_bord.php");
                exit();

            } catch (Exception $e) {
                $pdo->rollBack();
                $erreur = "Erreur serveur : " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Réserver un trajet - EcoRide</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="container mt-5">
  <h2>Réserver un trajet</h2>

  <?php if ($erreur): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
  <?php endif; ?>

  <div class="card mt-4">
    <div class="card-body">
      <h5><?= htmlspecialchars($trajet['depart']) ?> → <?= htmlspecialchars($trajet['destination']) ?></h5>
      <p>
        Date : <?= $trajet['date_trajet'] ?><br>
        Départ : <?= $trajet['heure_depart'] ?><br>
        Conducteur : <?= htmlspecialchars($trajet['conducteur_nom']) ?><br>
        Véhicule : <?= ucfirst($trajet['energie']) ?><br>
        Places dispo : <?= $trajet['places_disponibles'] ?><br>
        Vos crédits : <?= $credits ?>
      </p>

      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <div class="mb-3">
          <label class="form-label">Nombre de places</label>
          <input type="number" name="places" class="form-control" min="1" max="<?= $trajet['places_disponibles'] ?>" value="1" required>
        </div>
        <button type="submit" class="btn btn-success">Confirmer la réservation</button>
      </form>
    </div>
  </div>
</div>

</body>
</html>
