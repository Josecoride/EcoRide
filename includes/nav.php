<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$basePath = '/EcoRide'; 
?>

<nav>
  <a href="<?= $basePath ?>/index.php" class="logo">EcoRide 🌿</a>

  <div class="burger" id="burgerMenu">
    <span></span>
    <span></span>
    <span></span>
  </div>

  <div class="nav-links" id="mainNav">
    <a href="<?= $basePath ?>/index.php">Accueil</a>
    <a href="<?= $basePath ?>/pages/rechercher_trajet.php">Covoiturages</a>
    <a href="<?= $basePath ?>/pages/contact.php">Contact</a>

    <?php if (isset($_SESSION['admin_email'])): ?>
      <a href="<?= $basePath ?>/pages/espace_admin.php">Admin</a>
      <a href="<?= $basePath ?>/actions/deconnexion.php">Déconnexion</a>

    <?php elseif (isset($_SESSION['employe'])): ?>
      <a href="<?= $basePath ?>/pages/espace_employe.php">Employé</a>
      <a href="<?= $basePath ?>/actions/deconnexion.php">Déconnexion</a>

    <?php elseif (isset($_SESSION['courriel'])): ?>
      <a href="<?= $basePath ?>/pages/tableau_de_bord.php">Mon espace</a>
      <a href="<?= $basePath ?>/actions/deconnexion.php">Déconnexion</a>

    <?php else: ?>
      <a href="<?= $basePath ?>/actions/connexion.php">Connexion</a>
      <a href="<?= $basePath ?>/actions/inscription.php">Inscription</a>
    <?php endif; ?>
  </div>
</nav>

<style>
  nav {
    background-color: #4CAF50;
    color: white;
    padding: 12px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    position: relative;
    z-index: 10;
  }

  .logo {
    font-size: 1.4rem;
    font-weight: bold;
    color: white;
    text-decoration: none;
  }

  .burger {
    display: none;
    flex-direction: column;
    gap: 5px;
    cursor: pointer;
  }

  .burger span {
    width: 25px;
    height: 3px;
    background: white;
    border-radius: 2px;
  }

  .nav-links {
    display: flex;
    gap: 15px;
    align-items: center;
  }

  .nav-links a {
    color: white;
    text-decoration: none;
    font-weight: 500;
  }

  @media screen and (max-width: 768px) {
    .burger {
      display: flex;
    }

    .nav-links {
      display: none;
      flex-direction: column;
      width: 100%;
      background-color: #4CAF50;
      position: absolute;
      top: 60px;
      left: 0;
      padding: 10px 20px;
    }

    .nav-links.active {
      display: flex;
    }

    .nav-links a {
      padding: 10px 0;
      width: 100%;
    }
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const burger = document.getElementById('burgerMenu');
    const nav = document.getElementById('mainNav');

    if (burger && nav) {
      burger.addEventListener('click', function () {
        nav.classList.toggle('active');
      });

      window.addEventListener('click', function (e) {
        if (!burger.contains(e.target) && !nav.contains(e.target)) {
          nav.classList.remove('active');
        }
      });
    }
  });
</script>
