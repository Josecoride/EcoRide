<?php
session_start();
require '../includes/db_connection.php';  


if (!isset($_SESSION['courriel'])) {
    header("Location: ../actions/connexion.php");
    exit();
}

$courriel = $_SESSION['courriel'];

$stmt = $pdo->prepare("SELECT role FROM profils WHERE courriel = ?");
$stmt->execute([$courriel]);
$role = $stmt->fetchColumn();

if (!in_array($role, ['passager', 'les deux'])) {
    echo "<div class='alert alert-danger'>Accès non autorisé pour votre rôle.</div>";
    exit();
}


$id_confirmation = $_GET['id'] ?? null;

if (!$id_confirmation || !is_numeric($id_confirmation)) {
    header("Location: mes_trajets.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM trajets_confirmes WHERE id_confirmation = ? AND courriel_passager = ?");
$stmt->execute([$id_confirmation, $courriel]);
$trajet = $stmt->fetch();

if (!$trajet) {
    $_SESSION['erreur_trajet'] = "Trajet introuvable ou accès refusé.";
    header("Location: mes_trajets.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Noter un trajet</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="container mt-5">
    <h2>Noter votre trajet du <?= htmlspecialchars($trajet['date_trajet']) ?></h2>

    <form action="../actions/validation_note.php" method="POST">
        <input type="hidden" name="id_confirmation" value="<?= $id_confirmation ?>">

        <div class="mb-3">
            <label for="note" class="form-label">Note (1 à 5)</label>
            <select name="note" id="note" class="form-select" required>
                <option value="">Choisissez</option>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="commentaire" class="form-label">Commentaire</label>
            <textarea name="commentaire" id="commentaire" class="form-control" rows="4" placeholder="Partagez votre expérience..."></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Votre expérience :</label><br>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="validation" value="ok" id="val_ok" required>
                <label class="form-check-label" for="val_ok">Tout s'est bien passé</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="validation" value="nok" id="val_nok">
                <label class="form-check-label" for="val_nok">Il y a eu un problème</label>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Envoyer</button>
    </form>
</div>

</body>
</html>
