# Todo N' co


[![Codacy Badge](https://app.codacy.com/project/badge/Grade/6b14d199106b4844ad86bb868d48a637)](https://www.codacy.com/gh/JoDeyme/todoNco/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=JoDeyme/todoNco&amp;utm_campaign=Badge_Grade)

# README

## Description

Projet 8 de la formation Développeur web Symfony d'OpenClassrooms.

### Prérequis

- PHP 7.1.3 ou supérieur
- Composer
- Symfony 4.1.3 ou supérieur
- MySQL 5.7 ou supérieur

### Installation

- Cloner le projet
- Installer les dépendances avec la commande `composer install`
- Créer la base de données avec la commande `php bin/console doctrine:database:create`
- Créer les tables avec la commande `php bin/console doctrine:schema:update --force`
- Charger les fixtures avec la commande `php bin/console doctrine:fixtures:load`
- Lancer le serveur avec la commande `symfony server:start`




## Utilisation

### Créer un compte

**Uniquement accessible aux utilisateurs avec le rôle "ROLE_ADMIN".**

- Se rendre sur la page d'accueil
- Cliquer sur le bouton "Créer un utilisateur"
- Remplir le formulaire
- Cliquer sur le bouton "Ajouter"

### Se connecter

- Se rendre sur la page d'accueil
- Cliquer sur le bouton "Se connecter"
- Remplir le formulaire
- Cliquer sur le bouton "Se connecter"

### Créer une tâche

- Se rendre sur la page d'accueil
- Cliquer sur le bouton "Créer une tâche"
- Remplir le formulaire
- Cliquer sur le bouton "Créer"

### Modifier une tâche

- Se rendre sur la liste des tâches
- Cliquer sur le titre d'une tâche
- Remplir le formulaire
- Cliquer sur le bouton "Modifier"

### Déconnexion

- Cliquer sur le bouton "Se déconnecter"

### Administration

Uniquement accessible aux utilisateurs avec le rôle "ROLE_ADMIN".

Permet de gérer les utilisateurs (création, modification, suppression).

## Auteur

- [**Jordan DEYME**]
