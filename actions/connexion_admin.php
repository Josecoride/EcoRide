<?php 
session_start();
require '../includes/db_connection.php';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courriel = $_POST['courriel'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    // Correction ici : nom de la table = admins
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE courriel = ?");
    $stmt->execute([$courriel]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($mot_de_passe, $admin['mot_de_passe'])) {
        $_SESSION['admin_email'] = $courriel;
        header("Location: ../pages/espace_admin.php");
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
    <title>Connexion Administrateur</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="container mt-5" style="max-width: 400px;">
    <h2>Connexion administrateur</h2>

    <?php if ($erreur): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div class="mb-3">
            <input type="email" name="courriel" placeholder="Adresse email" class="form-control" required />
        </div>
        <div class="mb-3">
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" class="form-control" required />
        </div>
        <button type="submit" class="btn btn-primary w-100">Connexion</button>
    </form>
</div>

</body>
</html>
