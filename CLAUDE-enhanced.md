Contexte : Tu travailles sur un projet DWWM complet (titre professionnel).
Infrastructure Docker est opérationnelle avec :
- Front : Vue 3 (Vite) sur localhost:5173
- Back : PHP 8.2 (Apache) sur localhost:8000
- DB : MySQL 8 sur localhost:3306

📄 Utilise le fichier CLAUDE-enhanced.md que je fournis en contexte.
Cela décrit précisément :
- L'arborescence complète
- Le schema MySQL
- Les endpoints API REST
- Les pages Vue et composants
- Les conventions de code
- La checklist de génération

TÂCHE :
Génère TOUT le code manquant (Backend + Frontend + Documentation) dans cet ordre :

1. **Schema MySQL** : Crée les 4 tables (users, builds, characters, favorites)
2. **Backend PHP** : Controllers, Models, Routes, Middleware, Database.php, .env
3. **Frontend Vue** : Stores Pinia, Services API, Router, Pages, Composants
4. **Documentation** : API.md, SETUP.md, DATABASE_SCHEMA.md

Le code doit être :
- Production-ready (pas d'erreurs)
- Bien commenté (PHPDoc, Vue comments)
- Sécurisé (JWT, CORS, validation, SQL injection prevention)
- Respecter l'architecture MVC
- Compatible Docker sans modifications

Va-y, génère tout le code ! 🚀
