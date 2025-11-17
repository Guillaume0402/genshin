# Genshin Build Manager

Application complète de gestion de builds pour Genshin Impact - Projet DWWM (Développeur Web et Web Mobile)

## Description

Genshin Build Manager est une application full-stack permettant aux joueurs de Genshin Impact de créer, partager et découvrir les meilleurs builds pour leurs personnages préférés.

### Fonctionnalités principales

- 🔐 **Authentification JWT** - Inscription, connexion, gestion de profil
- 📖 **Gestion des Builds** - CRUD complet avec artefacts, armes, talents, équipe
- 🎭 **Personnages** - Base de données complète avec filtres (élément, arme, rareté)
- ❤️ **Système de Favoris** - Sauvegarde et gestion des builds favoris
- 🔍 **Recherche avancée** - Filtres multiples et recherche textuelle
- 📊 **Statistiques** - Vues, notes, builds populaires

## Stack Technique

### Backend
- **PHP 8.2** - Langage serveur
- **Architecture MVC** - Models, Views (API REST), Controllers
- **MySQL 8** - Base de données relationnelle
- **JWT** - Authentification stateless
- **PDO** - Accès sécurisé à la base de données
- **Apache 2.4** - Serveur web

### Frontend
- **Vue 3** - Framework JavaScript progressif (Composition API)
- **Vite** - Build tool et dev server ultra-rapide
- **Vue Router** - Routing SPA
- **Pinia** - State management
- **Axios** - Client HTTP pour les appels API

### DevOps
- **Docker** - Containerisation complète
- **Docker Compose** - Orchestration multi-containers

## Structure du Projet

```
Genshin/
├── back/                      # Backend PHP
│   ├── app/
│   │   ├── Controllers/      # Contrôleurs REST API
│   │   │   ├── BaseController.php
│   │   │   ├── AuthController.php
│   │   │   ├── BuildController.php
│   │   │   ├── CharacterController.php
│   │   │   └── FavoriteController.php
│   │   ├── Models/           # Models CRUD
│   │   │   ├── BaseModel.php
│   │   │   ├── User.php
│   │   │   ├── Build.php
│   │   │   ├── Character.php
│   │   │   └── Favorite.php
│   │   ├── Middleware/       # Middleware JWT
│   │   │   └── Auth.php
│   │   ├── Database/         # Connexion BDD
│   │   │   └── Database.php
│   │   ├── Router/           # Système de routing
│   │   │   └── Router.php
│   │   └── Routes/           # Définition des routes
│   │       ├── web.php
│   │       └── api.php
│   ├── database/
│   │   └── schema.sql        # Schéma SQL complet
│   ├── public/
│   │   └── index.php         # Point d'entrée
│   └── .env                  # Configuration
├── front/                     # Frontend Vue 3
│   ├── src/
│   │   ├── api/              # Configuration Axios
│   │   │   └── axios.js
│   │   ├── components/       # Composants réutilisables
│   │   │   ├── Navbar.vue
│   │   │   ├── Footer.vue
│   │   │   ├── BuildCard.vue
│   │   │   └── CharacterCard.vue
│   │   ├── views/            # Pages de l'application
│   │   │   ├── Home.vue
│   │   │   ├── Login.vue
│   │   │   ├── Register.vue
│   │   │   ├── Builds.vue
│   │   │   ├── BuildDetail.vue
│   │   │   ├── BuildCreate.vue
│   │   │   ├── BuildEdit.vue
│   │   │   ├── Characters.vue
│   │   │   ├── CharacterDetail.vue
│   │   │   ├── Favorites.vue
│   │   │   ├── Profile.vue
│   │   │   └── NotFound.vue
│   │   ├── stores/           # Stores Pinia
│   │   │   ├── auth.js
│   │   │   ├── builds.js
│   │   │   ├── characters.js
│   │   │   └── favorites.js
│   │   ├── services/         # Services API
│   │   │   ├── authService.js
│   │   │   ├── buildService.js
│   │   │   ├── characterService.js
│   │   │   └── favoriteService.js
│   │   ├── router/           # Vue Router
│   │   │   └── index.js
│   │   ├── App.vue           # Composant racine
│   │   ├── main.js           # Point d'entrée
│   │   └── style.css         # Styles globaux
│   └── package.json          # Dépendances npm
├── docker/
│   ├── Dockerfile.back       # Image PHP 8.2 + Apache
│   └── Dockerfile.front      # Image Node.js + Vite
├── docker-compose.yml         # Orchestration Docker
└── docs/                      # Documentation
    ├── API.md                # Documentation API REST
    ├── SETUP.md              # Guide d'installation
    └── DATABASE_SCHEMA.md    # Schéma de la BDD
```

## Installation

### Prérequis

- Docker & Docker Compose installés
- Ports disponibles : 5173 (front), 8000 (back), 3306 (db)

### Démarrage rapide

```bash
# 1. Cloner le projet (déjà fait)
cd c:\Users\yomgu\Desktop\Genshin

# 2. Lancer Docker Compose
docker-compose up -d

# 3. Importer le schéma SQL
docker exec -i genshin-db-1 mysql -uroot -prootpassword genshin < back/database/schema.sql

# 4. Installer les dépendances npm du frontend
docker exec -it genshin-front-1 npm install
docker-compose restart front

# 5. Accéder à l'application
# Frontend : http://localhost:5173
# API : http://localhost:8000/api
```

Pour plus de détails, consultez [docs/SETUP.md](docs/SETUP.md)

## Documentation

- **[API.md](docs/API.md)** - Documentation complète de l'API REST (tous les endpoints)
- **[SETUP.md](docs/SETUP.md)** - Guide d'installation et de configuration
- **[DATABASE_SCHEMA.md](docs/DATABASE_SCHEMA.md)** - Schéma détaillé de la base de données

## Comptes de test

Deux utilisateurs de test sont disponibles :

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

Voir [docs/API.md](docs/API.md) pour la documentation complète.

## Architecture

### Backend (MVC)

- **Models** : Couche d'accès aux données (CRUD avec PDO)
- **Controllers** : Logique métier et validation
- **Router** : Routing RESTful avec paramètres dynamiques
- **Middleware** : Authentification JWT

### Frontend (Composition API)

- **Components** : Composants Vue réutilisables
- **Views** : Pages de l'application
- **Stores** : État global avec Pinia
- **Services** : Appels API avec Axios
- **Router** : Navigation SPA avec guards

## Sécurité

- ✅ **JWT** : Authentification stateless avec tokens
- ✅ **Bcrypt** : Hash des mots de passe (10 rounds)
- ✅ **PDO Prepared Statements** : Protection contre les injections SQL
- ✅ **CORS** : Configuration des origines autorisées
- ✅ **Validation** : Validation côté serveur et client
- ✅ **Sanitization** : Nettoyage des entrées utilisateur

## Base de données

4 tables principales :
- **users** : Utilisateurs (id, username, email, password_hash, avatar)
- **characters** : Personnages Genshin (id, name, element, weapon_type, rarity, region)
- **builds** : Builds créés (id, user_id, character_id, title, artifacts, weapon, talents, etc.)
- **favorites** : Système de favoris (user_id, build_id)

Voir [docs/DATABASE_SCHEMA.md](docs/DATABASE_SCHEMA.md) pour le schéma complet.

## Technologies utilisées

| Catégorie | Technologies |
|-----------|-------------|
| Backend | PHP 8.2, Apache 2.4, MySQL 8 |
| Frontend | Vue 3, Vite, Pinia, Vue Router, Axios |
| Architecture | MVC, REST API, SPA |
| Sécurité | JWT, Bcrypt, PDO, CORS |
| DevOps | Docker, Docker Compose |

## Commandes Docker

```bash
# Démarrer les containers
docker-compose up -d

# Voir les logs
docker-compose logs -f

# Arrêter les containers
docker-compose stop

# Redémarrer les containers
docker-compose restart

# Supprimer les containers
docker-compose down
```

## Tests API

### Avec curl

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
```

## Auteur

Projet DWWM - Titre Professionnel Développeur Web et Web Mobile

## Licence

Projet éducatif - 2024

---

**Note importante :** Certaines pages Vue (BuildDetail, BuildCreate, BuildEdit, CharacterDetail) sont des templates à compléter. Les fonctionnalités principales sont opérationnelles, mais vous pouvez les enrichir selon vos besoins.

Pour toute question, consultez la documentation dans le dossier [docs/](docs/).
