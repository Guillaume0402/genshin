# Démarrage Rapide - Genshin Build Manager

Ce guide permet de démarrer l'application en 5 minutes.

## Étapes de démarrage

### 1. Démarrer Docker Compose

```bash
cd c:\Users\yomgu\Desktop\Genshin
docker-compose up -d
```

Attendez que les 3 containers démarrent (front, back, db).

### 2. Importer le schéma SQL

```bash
docker exec -i genshin-db-1 mysql -uroot -prootpassword genshin < back/database/schema.sql
```

### 3. Installer les dépendances npm

```bash
docker exec -it genshin-front-1 npm install
docker-compose restart front
```

### 4. Accéder à l'application

- **Frontend** : [http://localhost:5173](http://localhost:5173)
- **API** : [http://localhost:8000/api/health](http://localhost:8000/api/health)

### 5. Se connecter avec un compte de test

**Email :** `admin@genshin.com`
**Mot de passe :** `password123`

---

## Vérifications

### Container Docker actifs

```bash
docker-compose ps
```

Vous devriez voir 3 containers en état "running".

### Base de données

```bash
docker exec -it genshin-db-1 mysql -uroot -prootpassword -e "USE genshin; SHOW TABLES;"
```

Voir 4 tables : users, characters, builds, favorites.

### API fonctionnelle

Testez le health check :
```bash
curl http://localhost:8000/api/health
```

Réponse attendue :
```json
{
  "success": true,
  "message": "API is running",
  "version": "1.0.0",
  "timestamp": "..."
}
```

---

## Problèmes courants

### Les ports sont déjà utilisés

Si les ports 5173, 8000 ou 3306 sont déjà utilisés :

1. Vérifier les processus qui utilisent ces ports :
   ```bash
   netstat -ano | findstr :5173
   netstat -ano | findstr :8000
   netstat -ano | findstr :3306
   ```

2. Arrêter les processus ou modifier les ports dans `docker-compose.yml`

### Le frontend ne démarre pas

```bash
# Voir les logs
docker-compose logs front

# Réinstaller les dépendances
docker exec -it genshin-front-1 rm -rf node_modules package-lock.json
docker exec -it genshin-front-1 npm install
docker-compose restart front
```

### Erreurs API 500

```bash
# Voir les logs du backend
docker-compose logs back

# Vérifier que la base de données est accessible
docker exec -it genshin-back-1 php -r "new PDO('mysql:host=db;dbname=genshin', 'root', 'rootpassword');"
```

---

## Commandes utiles

```bash
# Voir les logs en temps réel
docker-compose logs -f

# Redémarrer tous les services
docker-compose restart

# Arrêter tous les services
docker-compose stop

# Supprimer tous les containers
docker-compose down

# Supprimer les containers ET les données
docker-compose down -v
```

---

## Prochaines étapes

1. **Explorez l'application** - Créez un compte, naviguez dans les personnages et builds
2. **Testez l'API** - Utilisez curl ou Postman pour tester les endpoints ([docs/API.md](docs/API.md))
3. **Complétez les pages manquantes** - BuildDetail, BuildCreate, etc. (templates fournis)
4. **Personnalisez** - Modifiez les styles, ajoutez des fonctionnalités

---

## Documentation complète

- [README.md](README.md) - Vue d'ensemble du projet
- [docs/SETUP.md](docs/SETUP.md) - Guide d'installation détaillé
- [docs/API.md](docs/API.md) - Documentation complète de l'API
- [docs/DATABASE_SCHEMA.md](docs/DATABASE_SCHEMA.md) - Schéma de la base de données

---

## Support

En cas de problème :
1. Consultez les logs : `docker-compose logs`
2. Vérifiez la documentation : dossier `docs/`
3. Vérifiez que Docker et Docker Compose sont bien installés

---


