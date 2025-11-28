# Genshin Build Manager

Application de gestion de builds pour Genshin Impact - Projet DWWM (Développeur Web et Web Mobile)

## Description

Genshin Build Manager est une application web full-stack permettant aux joueurs de Genshin Impact de créer, partager et découvrir les meilleurs builds pour leurs personnages préférés.

### Fonctionnalités principales

- 🔐 **Authentification JWT** - Inscription, connexion sécurisée
- 📖 **Gestion des Builds** - Création, consultation, suppression de builds
- 🎭 **Personnages** - Base de données complète avec filtres (élément, rareté)
- 🔍 **Recherche** - Recherche de builds par nom
- 📊 **Statistiques** - Vues, notes, popularité des builds

## Stack Technique

### Backend
- **PHP 8.2** - Langage serveur
- **Architecture MVC** - Models, Controllers, API REST
- **MySQL 8** - Base de données relationnelle
- **JWT** - Authentification stateless
- **PDO** - Accès sécurisé à la base de données
- **Apache 2.4** - Serveur web

### Frontend
- **HTML5** - Structure des pages
- **CSS3** - Styles et design responsive
- **JavaScript vanilla** - Logique côté client
- **Fetch API** - Communication avec l'API REST

### DevOps
- **Docker** - Containerisation complète
- **Docker Compose** - Orchestration multi-containers
- **Apache HTTP Server** - Serveur web pour le frontend

## Structure du Projet

```
genshin/
├── back/                       # Backend PHP
│   ├── app/
│   │   ├── Controllers/        # Contrôleurs REST API
│   │   ├── Models/             # Models CRUD
│   │   ├── Middleware/         # Middleware JWT
│   │   ├── Database/           # Connexion BDD
│   │   ├── Router/             # Système de routing
│   │   └── Routes/             # Définition des routes
│   ├── database/
│   │   └── schema.sql          # Schéma SQL
│   └── public/
│       └── index.php           # Point d'entrée API
├── front/                      # Frontend HTML/CSS/JS
│   ├── css/
│   │   └── style.css           # Styles globaux
│   ├── js/
│   │   ├── api.js              # Client API
│   │   └── auth.js             # Gestion auth
│   ├── index.html              # Page d'accueil
│   ├── login.html              # Page de connexion
│   ├── register.html           # Page d'inscription
│   ├── builds.html             # Liste des builds
│   ├── build-detail.html       # Détail d'un build
│   └── characters.html         # Liste des personnages
├── docker/
│   ├── Dockerfile.back         # Image PHP + Apache
│   └── Dockerfile.front        # Image Apache HTTP
└── docker-compose.yml          # Orchestration Docker
```

## Images des Personnages

**Important** : Les images des personnages ne sont pas incluses dans ce projet pour des raisons de droits d'auteur.

Pour ajouter les images :
1. Téléchargez les images officielles des personnages Genshin Impact
2. Placez-les dans le dossier `front/images/`
3. Nommez-les selon le nom du personnage (ex: `Hu_Tao.png`, `Ganyu.png`)

Voir [front/images/README.md](front/images/README.md) pour plus de détails.

## Installation et Démarrage

### Prérequis

- Docker et Docker Compose installés
- Ports disponibles : 3000 (frontend), 8000 (API), 3306 (MySQL)

### Démarrage rapide

```bash
# 1. Cloner/se placer dans le projet
cd c:\Users\gmaig\Desktop\genshin

# 2. Lancer Docker Compose
docker-compose up -d

# 3. Importer le schéma SQL
docker exec -i genshin-db-1 mysql -uroot -prootpassword genshin < back/database/schema.sql

# 4. Accéder à l'application
# Frontend : http://localhost:3000
# API : http://localhost:8000/api
```

### Arrêter l'application

```bash
docker-compose down
```

## Comptes de test

Deux utilisateurs de test sont disponibles après l'import du schéma :

**Utilisateur 1 :**
- Email : `admin@genshin.com`
- Mot de passe : `password123`

**Utilisateur 2 :**
- Email : `test@genshin.com`
- Mot de passe : `password123`

## Endpoints API principaux

```
# Authentification
POST   /api/auth/register       # Inscription
POST   /api/auth/login          # Connexion
GET    /api/auth/me             # Profil utilisateur

# Builds
GET    /api/builds              # Liste des builds
POST   /api/builds              # Créer un build
GET    /api/builds/:id          # Détails d'un build
PUT    /api/builds/:id          # Modifier un build
DELETE /api/builds/:id          # Supprimer un build

# Personnages
GET    /api/characters          # Liste des personnages
GET    /api/characters/:id      # Détails d'un personnage
GET    /api/characters/:id/builds # Builds d'un personnage

# Favoris
GET    /api/favorites           # Liste des favoris
POST   /api/favorites/toggle    # Ajouter/Retirer un favori
```

## Architecture

### Backend (MVC)

- **Models** : Couche d'accès aux données (CRUD avec PDO)
- **Controllers** : Logique métier et validation
- **Router** : Routing RESTful avec paramètres dynamiques
- **Middleware** : Authentification JWT

### Frontend (Pages HTML)

- **Pages HTML** : Structure sémantique HTML5
- **CSS Global** : Styles cohérents et responsive
- **JavaScript** : Gestion des appels API et de l'authentification
- **Navigation** : Liens classiques entre pages

## Sécurité

- ✅ **JWT** : Authentification stateless avec tokens
- ✅ **Bcrypt** : Hash des mots de passe
- ✅ **PDO Prepared Statements** : Protection contre les injections SQL
- ✅ **CORS** : Configuration des origines autorisées
- ✅ **Validation** : Validation côté serveur et client

## Base de données

4 tables principales :
- **users** : Utilisateurs (id, username, email, password_hash, avatar)
- **characters** : Personnages Genshin (id, name, element, weapon_type, rarity, region)
- **builds** : Builds créés (id, user_id, character_id, title, artifacts, weapon, talents, etc.)
- **favorites** : Système de favoris (user_id, build_id)

## Technologies utilisées

| Catégorie | Technologies |
|-----------|-------------|
| Backend | PHP 8.2, Apache 2.4, MySQL 8 |
| Frontend | HTML5, CSS3, JavaScript (Fetch API) |
| Architecture | MVC, REST API, Multi-pages |
| Sécurité | JWT, Bcrypt, PDO, CORS |
| DevOps | Docker, Docker Compose |

## Commandes Docker utiles

```bash
# Démarrer les containers
docker-compose up -d

# Voir les logs
docker-compose logs -f

# Voir les logs d'un service spécifique
docker-compose logs -f front

# Arrêter les containers
docker-compose stop

# Redémarrer les containers
docker-compose restart

# Supprimer les containers
docker-compose down

# Supprimer les containers et les volumes
docker-compose down -v
```

## Tests API avec curl

```bash
# Health check
curl http://localhost:8000/api/health

# Register
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"username":"test","email":"test@test.com","password":"password123"}'

# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","password":"password123"}'

# Get builds (pas d'auth requise)
curl http://localhost:8000/api/builds

# Get characters
curl http://localhost:8000/api/characters
```

## Auteur

Projet DWWM - Titre Professionnel Développeur Web et Web Mobile

## Licence

Projet éducatif - 2024

---


