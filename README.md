# SnowTricks !
*Projet 6 du parcours "Développeur d'applications PHP/Symfony" formation Openclassrooms*

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/68a35d8ffdc0495e87637b5cd6abf9a7)](https://app.codacy.com/manual/alexdev06/SnowTricks?utm_source=github.com&utm_medium=referral&utm_content=alexdev06/SnowTricks&utm_campaign=Badge_Grade_Dashboard)

## Bibliothèques utilisées:
- Symfony 5.0.3
- Jquery 3.4.1
- Bootstrap 4.4.1
- Thème Boostrap "Bootswatch Litera"
- PHP 7.3.12
- MySQL 8.0.18
- Apache 2.4.41
- PHPUnit 7.5.20
- Faker 1.9.1
- Slugify 4.0
- Faker 1.9
- Apache pack 1.0
- Axios
- ReCAPTCHA v2

## Procédure d'installation :
1. Importer le repository en le clonant ou en téléchargeant le zip

2. Installer les bibliothèques avec la commande `composer install`

3. Modifier les variables d'environnement dans le fichier .env: 
    * Configurer votre base de données avec son nom et les informations de connexion dans la section `doctrine/doctrine-bundle`:
      * Version du server
      * Login
      * Mot de passe
      * Nom de la base de données
    * Mettre en version de dev(développemnent) ou prod(production)dans la section`symfony/framework-bundle `
    * Configurer un service d'email dans la section `symfony/mailer`
4. Pour créer la base de données avec la commande : `php bin/console doctrine:database:create`

5. Pour installer les tables de données via le système de migrations: `php bin/console doctrine:migrations:migrate`

8. Installer un jeu de données : `php bin/console doctrine:fixtures:load`

9. Dans le dossier `templates/email`, configurer les adresses de liens dans les 2 fichiers 

10. Démarrer le server de Symfony avec la commande `symfony server:start`

11. En cas de bugs, vider les cachez avec les commandes `php bin/console cache:clear` et `php bin/console cache:clear --env=prod`

