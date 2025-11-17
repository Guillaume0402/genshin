# Documentation API - Genshin Build Manager

## Base URL
```
http://localhost:8000/api
```

## Authentification
L'API utilise JWT (JSON Web Tokens) pour l'authentification.
**Header requis pour les routes protégées :**
```
Authorization: Bearer <token>
```

---

## Endpoints API

### 🔐 AUTHENTIFICATION

#### POST /api/auth/register
Inscription d'un nouvel utilisateur

**Body:**
```json
{
  "username": "string",
  "email": "string",
  "password": "string"
}
```

**Réponse (201):**
```json
{
  "success": true,
  "message": "Compte créé avec succès",
  "data": {
    "user": { "id": 1, "username": "...", "email": "..." },
    "token": "eyJ0eXAiOiJKV1..."
  }
}
```

---

#### POST /api/auth/login
Connexion d'un utilisateur

**Body:**
```json
{
  "email": "string",
  "password": "string"
}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Connexion réussie",
  "data": {
    "user": { "id": 1, "username": "...", "email": "..." },
    "token": "eyJ0eXAiOiJKV1..."
  }
}
```

---

#### GET /api/auth/me
Récupère les informations de l'utilisateur connecté
**Auth required:** ✅

**Réponse (200):**
```json
{
  "success": true,
  "data": {
    "user": { "id": 1, "username": "...", "email": "..." },
    "stats": {
      "builds_count": 5,
      "favorites_count": 12
    }
  }
}
```

---

#### PUT /api/auth/profile
Mise à jour du profil utilisateur
**Auth required:** ✅

**Body:**
```json
{
  "username": "string (optional)",
  "email": "string (optional)",
  "password": "string (optional)",
  "avatar": "string (optional)"
}
```

---

### 📖 BUILDS

#### GET /api/builds
Liste tous les builds publics

**Query params:**
- `page` (int) : Numéro de page (défaut: 1)
- `limit` (int) : Nombre de résultats par page (défaut: 20, max: 100)
- `character_id` (int) : Filtrer par personnage
- `element` (string) : Filtrer par élément
- `search` (string) : Recherche textuelle
- `sort` (string) : Champ de tri (created_at, rating, views_count, favorites_count)
- `order` (string) : Ordre de tri (ASC, DESC)

**Réponse (200):**
```json
{
  "success": true,
  "data": {
    "builds": [ {...}, {...} ],
    "pagination": {
      "total": 50,
      "page": 1,
      "limit": 20,
      "pages": 3
    }
  }
}
```

---

#### GET /api/builds/:id
Récupère un build par son ID

**Réponse (200):**
```json
{
  "success": true,
  "data": {
    "build": {
      "id": 1,
      "title": "Hu Tao Vaporize DPS",
      "description": "...",
      "character_name": "Hu Tao",
      "element": "Pyro",
      "artifact_set": "Crimson Witch of Flames",
      "weapon_name": "Staff of Homa",
      "rating": 4.75,
      "views_count": 1250,
      "favorites_count": 89,
      "author": "username",
      ...
    }
  }
}
```

---

#### POST /api/builds
Crée un nouveau build
**Auth required:** ✅

**Body:**
```json
{
  "character_id": 1,
  "title": "string",
  "description": "string (optional)",
  "artifact_set": "string (optional)",
  "artifact_main_stats": { ... } (optional),
  "artifact_sub_stats": { ... } (optional),
  "weapon_name": "string (optional)",
  "weapon_refinement": 1-5 (optional),
  "talent_priority": "string (optional)",
  "team_composition": [ ... ] (optional),
  "is_public": true/false (optional),
  "tags": [ ... ] (optional)
}
```

**Réponse (201):**
```json
{
  "success": true,
  "message": "Build créé avec succès",
  "data": { "build": { ... } }
}
```

---

#### PUT /api/builds/:id
Met à jour un build existant
**Auth required:** ✅ (propriétaire uniquement)

**Body:** Mêmes champs que POST (tous optionnels)

---

#### DELETE /api/builds/:id
Supprime un build
**Auth required:** ✅ (propriétaire uniquement)

---

#### GET /api/builds/my-builds
Récupère les builds de l'utilisateur connecté
**Auth required:** ✅

---

#### GET /api/builds/top-rated
Récupère les builds les mieux notés

**Query params:**
- `limit` (int) : Nombre de résultats (défaut: 10, max: 50)

---

#### GET /api/builds/recent
Récupère les builds les plus récents

**Query params:**
- `limit` (int) : Nombre de résultats (défaut: 10, max: 50)

---

#### GET /api/builds/search
Recherche de builds

**Query params:**
- `q` (string, required) : Terme de recherche (min 2 caractères)
- `limit` (int) : Nombre de résultats (défaut: 20, max: 100)

---

### 🎭 PERSONNAGES

#### GET /api/characters
Liste tous les personnages

**Query params:**
- `page`, `limit` : Pagination
- `element` (string) : Filtrer par élément
- `weapon_type` (string) : Filtrer par type d'arme
- `rarity` (int) : Filtrer par rareté (4 ou 5)
- `region` (string) : Filtrer par région
- `search` (string) : Recherche textuelle

---

#### GET /api/characters/:id
Récupère un personnage par son ID

---

#### GET /api/characters/:id/builds
Récupère les builds d'un personnage

---

#### GET /api/characters/element/:element
Récupère les personnages par élément
**Éléments valides:** Pyro, Hydro, Anemo, Electro, Dendro, Cryo, Geo

---

#### GET /api/characters/weapon/:weaponType
Récupère les personnages par type d'arme
**Types valides:** Sword, Claymore, Polearm, Bow, Catalyst

---

#### GET /api/characters/rarity/:rarity
Récupère les personnages par rareté
**Raretés valides:** 4, 5

---

#### GET /api/characters/popular
Récupère les personnages les plus populaires

**Query params:**
- `limit` (int) : Nombre de résultats (défaut: 10, max: 50)

---

#### GET /api/characters/search
Recherche de personnages

**Query params:**
- `q` (string, required) : Terme de recherche (min 2 caractères)

---

### ❤️ FAVORIS

#### GET /api/favorites
Liste tous les favoris de l'utilisateur
**Auth required:** ✅

**Query params:**
- `page`, `limit` : Pagination

---

#### POST /api/favorites
Ajoute un build aux favoris
**Auth required:** ✅

**Body:**
```json
{
  "build_id": 1
}
```

---

#### DELETE /api/favorites/:buildId
Retire un build des favoris
**Auth required:** ✅

---

#### POST /api/favorites/toggle
Toggle le statut favori d'un build
**Auth required:** ✅

**Body:**
```json
{
  "build_id": 1
}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Build ajouté aux favoris" ou "Build retiré des favoris",
  "data": {
    "action": "added" ou "removed",
    "is_favorite": true ou false,
    "build_id": 1
  }
}
```

---

#### GET /api/favorites/check/:buildId
Vérifie si un build est en favori
**Auth required:** ✅

---

#### GET /api/favorites/ids
Récupère les IDs de tous les builds favoris
**Auth required:** ✅

**Réponse (200):**
```json
{
  "success": true,
  "data": {
    "favorite_build_ids": [1, 5, 12, 23],
    "total": 4
  }
}
```

---

### 🔧 UTILITAIRES

#### GET /api/health
Health check de l'API

**Réponse (200):**
```json
{
  "success": true,
  "message": "API is running",
  "version": "1.0.0",
  "timestamp": "2024-01-15 10:30:00"
}
```

---

## Codes d'erreur HTTP

- `200` - OK
- `201` - Created
- `400` - Bad Request (données invalides)
- `401` - Unauthorized (authentification requise)
- `403` - Forbidden (accès refusé)
- `404` - Not Found (ressource introuvable)
- `409` - Conflict (conflit, ex: email déjà utilisé)
- `422` - Unprocessable Entity (validation échouée)
- `500` - Internal Server Error (erreur serveur)

---

## Format des erreurs

```json
{
  "success": false,
  "message": "Description de l'erreur",
  "errors": {
    "field_name": "Message d'erreur spécifique"
  }
}
```

---

## Notes importantes

1. **CORS** : L'API est configurée pour accepter les requêtes depuis `http://localhost:5173` (configurable dans `.env`)
2. **Rate Limiting** : Aucun rate limiting n'est implémenté dans cette version
3. **Pagination** : La pagination par défaut est de 20 éléments, maximum 100
4. **JWT Expiration** : Les tokens JWT expirent après 24 heures (configurable dans `.env`)
5. **JSON uniquement** : L'API accepte et retourne uniquement du JSON
