#  Mini Projet Symfony - Blog

Ce Projet a etait réalisé dans le cadre du module Symfony.

Ce projet est un blog développé avec Symfony qui permet les:

- Gestion des utilisateurs (inscription / connexion)
- Gestion des rôles (ROLE_USER / ROLE_ADMIN)
- Création, modification et suppression d’articles
- Upload d’images pour les articles
- Ajout de commentaires
- Validation des commentaires par un administrateur
- Activation / désactivation des utilisateurs
- Sécurisation des routes d’administration

---

## 🛠 Technologies utilisées

- PHP 
- Symfony 
- Doctrine ORM
- Twig
- Bootstrap 
- MySQL


## ⚙ Installation du projet

### 1️ Cloner le repository

```bash
git clone https://github.com/VOTRE-USERNAME/mini-projet-symfony-blog.git
cd mini-projet-symfony-blog

# 2 Installer les dépendances

composer install

# 3️ Configurer la base de données

Créer un fichier :

.env.local


Puis configurer :

DATABASE_URL="mysql://user:password@127.0.0.1:3306/nom_de_la_base"

# 4️ Créer la base de données
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# 5️ Lancer le serveur
symfony server:start

# 6 Sécurité

Accès /admin réservé aux utilisateurs ayant ROLE_ADMIN

Blocage des utilisateurs désactivés

Validation des commentaires avant publication

 Fonctionnalités principales
Utilisateurs

Inscription

Connexion

Gestion des rôles

Activation / désactivation

# 7 Articles

CRUD complet

Upload image

Auteur et date affichés

# 8 Commentaires

Ajout par utilisateur connecté

Statut pending

Validation par admin

# 9 Interface

Design moderne et responsive utilisant Bootstrap avec améliorations visuelles personnalisées.