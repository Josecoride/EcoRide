<?php
session_start();
require_once '../includes/db_connection.php'; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $courriel = trim($_POST['courriel'] ?? '');
    $motDePasse = $_POST['mot_de_passe'] ?? '';
    $roleChoisi = $_POST['role'] ?? '';

    $roles_valides = ['conducteur', 'passager'];

    
    if (!$courriel || !$motDePasse || !$roleChoisi) {
        $_SESSION['erreur_connexion'] = "Tous les champs sont requis.";
        header("Location: connexion.php");
        exit();
    }

    if (!in_array($roleChoisi, $roles_valides)) {
        $_SESSION['erreur_connexion'] = "Rôle invalide.";
        header("Location: connexion.php");
        exit();
    }

    $stmt = $pdo->prepare("
        SELECT u.mot_de_passe, p.role
        FROM utilisateurs u
        JOIN profils p ON u.courriel = p.courriel
        WHERE u.courriel = ?
    ");
    $stmt->execute([$courriel]);
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($utilisateur && password_verify($motDePasse, $utilisateur['mot_de_passe'])) {
        if ($utilisateur['role'] === $roleChoisi || $utilisateur['role'] === 'les deux') {
            session_regenerate_id(true);
            $_SESSION['courriel'] = $courriel;
            $_SESSION['role'] = $roleChoisi;

            header("Location: ../pages/tableau_de_bord.php");
            exit();
        } else {
            $_SESSION['erreur_connexion'] = "Le rôle sélectionné ne correspond pas à votre profil.";
        }
    } else {
        $_SESSION['erreur_connexion'] = "Courriel ou mot de passe incorrect.";
    }

    header("Location: connexion.php");
    exit();
}
