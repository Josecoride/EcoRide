EcoRide - Plateforme de Covoiturage Écoresponsable

Développé dans le cadre du TP Développeur Web & Web Mobile

EcoRide est une application web de covoiturage qui promeut les déplacements écologiques et économiques.  
Le projet vise à proposer une plateforme intuitive permettant de connecter passagers et conducteurs de manière responsable.



 Fonctionnalités principales

 Utilisateur
- Rechercher un trajet avec filtres (prix, note, véhicule écologique…)
- Réserver un trajet avec déduction automatique de crédits
- Créer un compte (passager / conducteur / les deux)
- Consulter son espace personnel, modifier ses informations
- Noter et commenter un trajet effectué

 Conducteur
- Proposer un trajet avec sélection de véhicule
- Gérer les trajets proposés (historique, statut)
- Démarrer / terminer un trajet
- Gérer les préférences de voyage (fumeur, animaux, etc.)

 Employé
- Valider / refuser les avis passagers
- Gérer les litiges (trajets signalés)

 Administrateur
- Visualiser statistiques (trajets/jour, crédits gagnés)
- Créer des comptes employés
- Suspendre / réactiver des comptes



 Technologies utilisées

-Frontend : HTML5, CSS3, Bootstrap 5
- Backend : PHP (procédural avec PDO)
- Base de données relationnelle : MySQL
- Hébergement local : XAMPP ou MAMP



 Structure du projet


EcoRide/

 assets/                Images, CSS, JS
    
 includes/              Fichiers partagés (nav.php, db)
 pages/                 Interfaces : accueil, recherche, profil...
 actions/               Traitements : login, réservation, contact...
 docs/                  README, charte graphique, manuels
 sql/                   Script base de données (ecoride.sql)
 index.php              Point d'entrée principal




  Installation locale

1. Clone ou télécharge ce dépôt :
   
   git clone https://github.com/josecoderide/ecoride.git
   

2. Place le dossier dans `htdocs` (si XAMPP) ou `www` (si MAMP)

3. Importer la base de données :
   - Ouvrir phpMyAdmin
   - Crée une base `ecoride`
   - Importe le fichier `sql/ecoride.sql`

4. Démarre le serveur local :
   - Apache + MySQL
   - Accède à `http://localhost/ecoride/index.php`



  Charte graphique & Maquettes

  Voir `/docs/charte_graphique_ecoride.pdf`
   Inclut :
  - Palette couleur
  - Typographies
  - Grille responsive Bootstrap
  - Maquettes (desktop, mobile, tablette)



Déploiement

 Projet pensé pour serveur local.  
Optionnel : déployable sur Fly.io, Vercel, AlwaysData, etc. avec base MySQL distante.




 Méthodologie : Kanban  






