<?php
session_start();
require '../includes/db_connection.php';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courriel = $_POST['courriel'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM employes WHERE courriel = ?");
    $stmt->execute([$courriel]);
    $employe = $stmt->fetch();

    if ($employe && password_verify($mot_de_passe, $employe['mot_de_passe'])) {
        $_SESSION['employe'] = $courriel;
        header("Location: ../pages/espace_employe.php");
        exit();
    } else {
        $erreur = "Identifiants incorrects.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Employé</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="container mt-5" style="max-width: 400px;">
    <h2>Connexion employé</h2>

    <?php if ($erreur): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div class="mb-3">
            <label for="courriel" class="form-label">Adresse e-mail</label>
            <input type="email" name="courriel" id="courriel" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="mot_de_passe" class="form-label">Mot de passe</label>
            <input type="password" name="mot_de_passe" id="mot_de_passe" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Connexion</button>
    </form>
</div>

</body>
</html>
