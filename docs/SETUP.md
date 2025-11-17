# Guide d'installation - Genshin Build Manager

Ce guide explique comment installer et lancer l'application Genshin Build Manager en utilisant Docker.

---

## Prérequis

- **Docker** & **Docker Compose** installés
- **Git** (pour cloner le projet)
- Ports disponibles : `5173` (front), `8000` (back), `3306` (db)

---

## Installation

### 1. Cloner le projet

```bash
cd Desktop/Genshin
# Le projet est déjà cloné
```

### 2. Structure du projet

```
Genshin/
├── back/                  # Backend PHP
│   ├── app/
│   │   ├── Controllers/  # Contrôleurs REST
│   │   ├── Models/       # Models (CRUD)
│   │   ├── Middleware/   # JWT Auth
│   │   ├── Database/     # Connexion BDD
│   │   ├── Router/       # Système de routing
│   │   └── Routes/       # Définition des routes
│   ├── database/
│   │   └── schema.sql    # Schéma MySQL
│   ├── public/
│   │   └── index.php     # Point d'entrée
│   └── .env              # Configuration
├── front/                 # Frontend Vue 3
│   ├── src/
│   │   ├── api/          # Configuration Axios
│   │   ├── components/   # Composants Vue
│   │   ├── views/        # Pages Vue
│   │   ├── stores/       # Stores Pinia
│   │   ├── services/     # Services API
│   │   ├── router/       # Vue Router
│   │   ├── App.vue       # Composant principal
│   │   └── main.js       # Point d'entrée
│   └── package.json      # Dépendances npm
├── docker/
│   ├── Dockerfile.back   # Image PHP 8.2 + Apache
│   └── Dockerfile.front  # Image Node.js + Vite
├── docker-compose.yml     # Orchestration Docker
└── docs/                  # Documentation
```

---

## Démarrage avec Docker

### 1. Lancer les containers Docker

```bash
cd c:\Users\yomgu\Desktop\Genshin
docker-compose up -d
```

Cela démarre 3 services :
- **front** : Vue 3 sur `http://localhost:5173`
- **back** : PHP 8.2 + Apache sur `http://localhost:8000`
- **db** : MySQL 8 sur `localhost:3306`

### 2. Vérifier que les containers fonctionnent

```bash
docker-compose ps
```

Vous devriez voir 3 containers actifs :
```
genshin-front-1    running    0.0.0.0:5173->5173/tcp
genshin-back-1     running    0.0.0.0:8000->80/tcp
genshin-db-1       running    0.0.0.0:3306->3306/tcp
```

---

## Configuration de la base de données

### 1. Importer le schéma SQL

**Option A : Via Docker exec (recommandé)**
```bash
docker exec -i genshin-db-1 mysql -uroot -prootpassword genshin < back/database/schema.sql
```

**Option B : Via ligne de commande MySQL**
```bash
mysql -h 127.0.0.1 -u root -prootpassword genshin < back/database/schema.sql
```

**Option C : Via phpMyAdmin ou MySQL Workbench**
1. Connectez-vous à `localhost:3306`
2. Username: `root`
3. Password: `rootpassword`
4. Base de données: `genshin`
5. Importez le fichier `back/database/schema.sql`

### 2. Vérifier l'import

```bash
docker exec -it genshin-db-1 mysql -uroot -prootpassword -e "USE genshin; SHOW TABLES;"
```

Vous devriez voir :
```
+-------------------+
| Tables_in_genshin |
+-------------------+
| builds            |
| characters        |
| favorites         |
| users             |
+-------------------+
```

---

## Installation des dépendances Frontend

### 1. Entrer dans le container front

```bash
docker exec -it genshin-front-1 sh
```

### 2. Installer les dépendances npm

```bash
npm install
```

### 3. Sortir du container

```bash
exit
```

### 4. Redémarrer le container front

```bash
docker-compose restart front
```

---

## Accéder à l'application

- **Frontend (Vue 3)** : [http://localhost:5173](http://localhost:5173)
- **Backend API** : [http://localhost:8000/api](http://localhost:8000/api)
- **Health Check API** : [http://localhost:8000/api/health](http://localhost:8000/api/health)

---

## Comptes de test

Le schéma SQL inclut 2 utilisateurs de test :

**Utilisateur 1 :**
- Email : `admin@genshin.com`
- Mot de passe : `password123`

**Utilisateur 2 :**
- Email : `test@genshin.com`
- Mot de passe : `password123`

---

## Commandes Docker utiles

### Voir les logs
```bash
# Logs de tous les services
docker-compose logs -f

# Logs d'un service spécifique
docker-compose logs -f front
docker-compose logs -f back
docker-compose logs -f db
```

### Arrêter les containers
```bash
docker-compose stop
```

### Redémarrer les containers
```bash
docker-compose restart
```

### Supprimer les containers
```bash
docker-compose down
```

### Supprimer les containers ET les volumes
```bash
docker-compose down -v
```

---

## Configuration avancée

### Variables d'environnement Backend

Fichier : `back/.env`

```env
# Application
APP_NAME="Genshin Build Manager"
APP_ENV=development
APP_DEBUG=true

# Database
DB_HOST=db
DB_PORT=3306
DB_DATABASE=genshin
DB_USERNAME=root
DB_PASSWORD=rootpassword

# JWT
JWT_SECRET=your-secret-key-change-this-in-production
JWT_EXPIRATION=86400  # 24 heures

# CORS
CORS_ALLOWED_ORIGINS=http://localhost:5173
```

### Variables d'environnement Frontend

Créer un fichier `front/.env` (optionnel) :

```env
VITE_API_URL=http://localhost:8000/api
```

---

## Résolution des problèmes

### Le frontend ne démarre pas

1. Vérifier les logs :
```bash
docker-compose logs front
```

2. Installer manuellement les dépendances :
```bash
docker exec -it genshin-front-1 npm install
docker-compose restart front
```

### L'API retourne des erreurs 500

1. Vérifier les logs du backend :
```bash
docker-compose logs back
```

2. Vérifier que la base de données est accessible :
```bash
docker exec -it genshin-back-1 php -r "new PDO('mysql:host=db;dbname=genshin', 'root', 'rootpassword');"
```

### La base de données ne se connecte pas

1. Vérifier que le container MySQL est actif :
```bash
docker-compose ps db
```

2. Vérifier les credentials :
```bash
docker exec -it genshin-db-1 mysql -uroot -prootpassword -e "SELECT 1;"
```

### Erreurs CORS

1. Vérifier le fichier `back/.env` :
```env
CORS_ALLOWED_ORIGINS=http://localhost:5173
```

2. Vérifier que le frontend tourne bien sur le port 5173

---

## Mode développement

### Backend (PHP)

Les fichiers PHP sont automatiquement rechargés grâce au volume Docker :
```yaml
volumes:
  - ./back:/var/www/html
```

Toute modification dans `back/` est immédiatement visible.

### Frontend (Vue 3)

Le Hot Module Replacement (HMR) de Vite est actif. Les modifications sont visibles en temps réel sans recharger la page.

---

## Mode production

### Build du frontend

```bash
cd front
npm run build
```

Le build est généré dans `front/dist/`.

### Servir le frontend statique

Vous pouvez servir le dossier `dist/` avec Apache, Nginx, ou tout autre serveur web.

---

## Tests

### Tester l'API avec curl

**Health Check :**
```bash
curl http://localhost:8000/api/health
```

**Register :**
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"username":"testuser","email":"test@test.com","password":"password123"}'
```

**Login :**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","password":"password123"}'
```

---

## Support

Pour toute question ou problème :
1. Vérifier les logs Docker : `docker-compose logs`
2. Consulter la documentation API : `docs/API.md`
3. Consulter le schéma de base de données : `docs/DATABASE_SCHEMA.md`

---

## Checklist de démarrage

- [ ] Docker et Docker Compose installés
- [ ] Ports 5173, 8000, 3306 disponibles
- [ ] `docker-compose up -d` exécuté
- [ ] Schéma SQL importé
- [ ] Dépendances npm installées
- [ ] Frontend accessible sur http://localhost:5173
- [ ] API accessible sur http://localhost:8000/api
- [ ] Health check retourne une réponse valide

---

Félicitations ! Votre application Genshin Build Manager est prête. 🎉
