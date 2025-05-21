<?php
session_start();
require_once '../includes/db_connection.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courriel = $_POST['courriel'] ?? '';
    $motdepasse = $_POST['motdepasse'] ?? '';

    if (!filter_var($courriel, FILTER_VALIDATE_EMAIL)) {
        $message = "Adresse e-mail invalide.";
    } else {
       $stmt = $pdo->prepare("
            SELECT u.*, p.nom, p.role 
            FROM utilisateurs u 
            LEFT JOIN profils p ON u.courriel = p.courriel 
            WHERE u.courriel = ?
        ");
        $stmt->execute([$courriel]);
        $utilisateur = $stmt->fetch();

        if ($utilisateur && password_verify($motdepasse, $utilisateur['mot_de_passe'])) {
            if (!$utilisateur['actif']) {
                $message = "Votre compte a été suspendu. Contactez un administrateur.";
            } else {
                $_SESSION['courriel'] = $utilisateur['courriel'];
                $_SESSION['role'] = $utilisateur['role'] ?? 'passager'; 
                $_SESSION['nom'] = $utilisateur['nom'] ?? '';
                header('Location: ../pages/tableau_de_bord.php');
                exit();
            }
        } else {
            $message = "Identifiants incorrects.";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Connexion – EcoRide</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="../assets/css/style.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="container mt-5" style="max-width: 400px;">
    <h2 class="mb-4 text-center">Connexion</h2>

    <?php if ($message): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" action="connexion.php" class="mb-3">
        <div class="mb-3">
            <label for="courriel" class="form-label">Adresse e-mail</label>
            <input type="email" id="courriel" name="courriel" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="motdepasse" class="form-label">Mot de passe</label>
            <input type="password" id="motdepasse" name="motdepasse" class="form-control" required>
        </div>

        <div class="mb-3 text-end">
            <a href="mot_de_passe_oublie.php" class="small">Mot de passe oublié ?</a>
        </div>

        <button type="submit" class="btn btn-success w-100">Se connecter</button>
    </form>

    <hr>

    <div class="text-center mb-3">
        <p>Ou connectez-vous en tant que :</p>
        <a href="connexion_admin.php" class="btn btn-outline-primary me-2">Admin</a>
        <a href="connexion_employe.php" class="btn btn-outline-secondary">Employé</a>
    </div>

    <p class="text-center">
        Pas encore de compte ? <a href="inscription.php">Inscrivez-vous ici</a>.
    </p>
</div>

</body>
</html>
