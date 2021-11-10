# SnowTricks !
*Projet 6 du parcours "Développeur d'applications PHP/Symfony" formation Openclassrooms*

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/68a35d8ffdc0495e87637b5cd6abf9a7)](https://app.codacy.com/manual/alexdev06/SnowTricks?utm_source=github.com&utm_medium=referral&utm_content=alexdev06/SnowTricks&utm_campaign=Badge_Grade_Dashboard)

### Lien vers le site : https://snowtricks.alexandremanteaux.fr

## Contexte
Jimmy Sweat est un entrepreneur ambitieux passionné de snowboard. Son objectif est la création d'un site collaboratif pour faire connaître ce sport auprès du grand public et aider à l'apprentissage des figures (tricks).

Il souhaite capitaliser sur du contenu apporté par les internautes afin de développer un contenu riche et suscitant l’intérêt des utilisateurs du site. Par la suite, Jimmy souhaite développer un business de mise en relation avec les marques de snowboard grâce au trafic que le contenu aura généré.

Pour ce projet, nous allons nous concentrer sur la création technique du site pour Jimmy.

Votre mission : créer un site communautaire pour apprendre les figures de snowboard
Votre mission : créer un site communautaire pour apprendre les figures de snowboard
Description du besoin
Vous êtes chargé de développer le site répondant aux besoins de Jimmy. Vous devez ainsi implémenter les fonctionnalités suivantes : 

* un annuaire des figures de snowboard. Vous pouvez vous inspirer de la liste des figures sur Wikipédia. Contentez-vous d'intégrer 10 figures, le reste sera saisi par les internautes ;
* la gestion des figures (création, modification, consultation) ;
* un espace de discussion commun à toutes les figures.

Pour implémenter ces fonctionnalités, vous devez créer les pages suivantes :

* la page d’accueil où figurera la liste des figures ; 
* la page de création d'une nouvelle figure ;
* la page de modification d'une figure ;
* la page de présentation d’une figure (contenant l’espace de discussion commun autour d’une figure).
* L’ensemble des spécifications détaillées pour les pages à développer est accessible ici : Spécifications détaillées.

### Nota bene
Il faut que les URL de page permettent une compréhension rapide de ce que la page représente et que le référencement naturel soit facilité.

L’utilisation de bundles tiers est interdite sauf pour les données initiales. Vous utiliserez les compétences acquises jusqu’ici ainsi que la documentation officielle afin de remplir les objectifs donnés.

Le design du site web est laissé complètement libre, attention cependant à respecter les wireframes fournis pour le gabarit de vos pages. Néanmoins, il faudra que le site soit consultable aussi bien sur un ordinateur que sur mobile (téléphone mobile, tablette, phablette…).

### Livrables

* L’ensemble des diagrammes demandés (modèles de données, classes, use cases, séquentiels)
* Les issues sur le repository GitHub
* Les instructions pour installer le projet (dans un fichier README à la racine du projet)
* Jeu de données initiales avec l’ensemble des figures de snowboard
* Lien vers les analyses SensioLabsInsight, Codacy ou Codeclimate (via une médaille dans le README, par exemple).

## Bibliothèques utilisées:
- Symfony 5.2
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

