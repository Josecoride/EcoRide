<?php
session_start();
require '../includes/db_connection.php';

if (!isset($_SESSION['courriel'])) {
    header('Location: ../actions/connexion.php');
    exit();
}

$courriel = $_SESSION['courriel'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ../pages/reservations.php');
    exit();
}

$id_confirmation = (int) $_GET['id'];


$stmt = $pdo->prepare('SELECT * FROM trajets_confirmes WHERE id_confirmation = ? AND courriel_passager = ?');
$stmt->execute([$id_confirmation, $courriel]);
$reservation = $stmt->fetch();

if (!$reservation) {
    $_SESSION['erreur_reservation'] = "Réservation non trouvée ou accès refusé.";
    header('Location: ../pages/reservations.php');
    exit();
}

$erreur = '';
$succes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (!isset($_POST['supprimer'])) {
        $nouv_places = intval($_POST['places_reservees'] ?? 0);

        if ($nouv_places < 1) {
            $erreur = "Le nombre de places doit être au moins 1.";
        } else {
            $stmt = $pdo->prepare('SELECT places_disponibles FROM trajets_proposes WHERE id_trajet = ?');
            $stmt->execute([$reservation['id_trajet']]);
            $trajet = $stmt->fetch();

            if (!$trajet) {
                $erreur = "Le trajet n'existe plus.";
            } else {
                $places_dispo = $trajet['places_disponibles'] + $reservation['places_reservees'];

                if ($nouv_places > $places_dispo) {
                    $erreur = "Le nombre de places demandé dépasse les places disponibles.";
                } else {
                    try {
                        $pdo->beginTransaction();

                        
                        $stmt = $pdo->prepare('UPDATE trajets_confirmes SET places_reservees = ? WHERE id_confirmation = ?');
                        $stmt->execute([$nouv_places, $id_confirmation]);

                       
                        $new_dispo = $places_dispo - $nouv_places;
                        $stmt = $pdo->prepare('UPDATE trajets_proposes SET places_disponibles = ? WHERE id_trajet = ?');
                        $stmt->execute([$new_dispo, $reservation['id_trajet']]);

                        $pdo->commit();
                        $succes = "Réservation mise à jour avec succès.";
                        $reservation['places_reservees'] = $nouv_places;
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        $erreur = "Erreur serveur, veuillez réessayer.";
                    }
                }
            }
        }
    }

   
    if (isset($_POST['supprimer'])) {
        try {
            $pdo->beginTransaction();

            
            $stmt = $pdo->prepare('UPDATE trajets_proposes SET places_disponibles = places_disponibles + ? WHERE id_trajet = ?');
            $stmt->execute([$reservation['places_reservees'], $reservation['id_trajet']]);

           
            $stmt = $pdo->prepare('DELETE FROM trajets_confirmes WHERE id_confirmation = ?');
            $stmt->execute([$id_confirmation]);

            $pdo->commit();
            $_SESSION['succes_reservation'] = "Réservation supprimée avec succès.";
            header('Location: ../pages/reservations.php');
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $erreur = "Erreur lors de la suppression, veuillez réessayer.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Modifier ou annuler réservation - Ecoride</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="container mt-5" style="max-width: 600px;">
  <h2>Modifier ou annuler la réservation</h2>

  <?php if ($erreur): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
  <?php elseif ($succes): ?>
    <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
  <?php endif; ?>

  <div class="mb-3">
    <strong>Trajet :</strong> <?= htmlspecialchars($reservation['depart']) ?> → <?= htmlspecialchars($reservation['destination']) ?><br/>
    <strong>Date :</strong> <?= htmlspecialchars($reservation['date_trajet']) ?><br/>
    <strong>Heure :</strong> <?= htmlspecialchars(substr($reservation['heure_depart'], 0, 5)) ?><br/>
    <strong>Véhicule :</strong> <?= htmlspecialchars($reservation['vehicule']) ?><br/>
  </div>

  <form method="POST" novalidate>
    <div class="mb-3">
      <label for="places_reservees" class="form-label">Nombre de places réservées</label>
      <input type="number" class="form-control" id="places_reservees" name="places_reservees" min="1" required value="<?= htmlspecialchars($reservation['places_reservees']) ?>" />
    </div>
    <button type="submit" class="btn btn-primary">Mettre à jour</button>
    <button type="submit" name="supprimer" value="1" onclick="return confirm('Voulez-vous vraiment annuler cette réservation ?');" class="btn btn-danger ms-2">Annuler la réservation</button>
    <a href="reservations.php" class="btn btn-secondary ms-2">Retour</a>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
