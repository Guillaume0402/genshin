<?php
/**
 * Authentication Controller
 *
 * Gère l'authentification des utilisateurs (register, login, me, logout)
 *
 * @package App\Controllers
 * @author DWWM Project
 * @version 1.0
 */

namespace App\Controllers;

use App\Models\User;
use App\Middleware\Auth;

class AuthController extends BaseController
{
    /**
     * @var User Model utilisateur
     */
    private User $userModel;

    /**
     * @var Auth Middleware JWT
     */
    private Auth $auth;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->handleCors();
        $this->userModel = new User();
        $this->auth = new Auth();
    }

    /**
     * Inscription d'un nouvel utilisateur
     * POST /api/auth/register
     *
     * @return void
     */
    public function register(): void
    {
        $data = $this->getRequestBody();

        if (!$data) {
            $this->sendError('Données invalides', 400);
        }

        // Validation des champs requis
        $errors = $this->validateRequired($data, ['username', 'email', 'password']);
        if ($errors) {
            $this->sendError('Champs manquants', 422, $errors);
        }

        // Validation de l'email
        if (!$this->validateEmail($data['email'])) {
            $this->sendError('Email invalide', 422);
        }

        // Validation du mot de passe (min 8 caractères)
        if (strlen($data['password']) < 8) {
            $this->sendError('Le mot de passe doit contenir au moins 8 caractères', 422);
        }

        // Vérification de l'unicité de l'email
        if ($this->userModel->emailExists($data['email'])) {
            $this->sendError('Cet email est déjà utilisé', 409);
        }

        // Vérification de l'unicité du username
        if ($this->userModel->usernameExists($data['username'])) {
            $this->sendError('Ce nom d\'utilisateur est déjà utilisé', 409);
        }

        // Création de l'utilisateur
        $userId = $this->userModel->createUser([
            'username' => $this->sanitize($data['username']),
            'email' => $this->sanitize($data['email']),
            'password' => $data['password'],
            'avatar' => $data['avatar'] ?? null
        ]);

        if (!$userId) {
            $this->sendError('Erreur lors de la création du compte', 500);
        }

        // Récupération de l'utilisateur créé
        $user = $this->userModel->findById($userId);

        // Suppression du mot de passe des données retournées
        unset($user['password']);

        // Génération du token JWT
        $token = $this->auth->generateToken($user);

        $this->sendSuccess([
            'user' => $user,
            'token' => $token
        ], 'Compte créé avec succès', 201);
    }

    /**
     * Connexion d'un utilisateur
     * POST /api/auth/login
     *
     * @return void
     */
    public function login(): void
    {
        $data = $this->getRequestBody();

        if (!$data) {
            $this->sendError('Données invalides', 400);
        }

        // Validation des champs requis
        $errors = $this->validateRequired($data, ['email', 'password']);
        if ($errors) {
            $this->sendError('Champs manquants', 422, $errors);
        }

        // Recherche de l'utilisateur par email
        $user = $this->userModel->findByEmail($data['email']);

        if (!$user) {
            $this->sendError('Email ou mot de passe incorrect', 401);
        }

        // Vérification du mot de passe
        if (!$this->userModel->verifyPassword($data['password'], $user['password'])) {
            $this->sendError('Email ou mot de passe incorrect', 401);
        }

        // Suppression du mot de passe des données retournées
        unset($user['password']);

        // Génération du token JWT
        $token = $this->auth->generateToken($user);

        $this->sendSuccess([
            'user' => $user,
            'token' => $token
        ], 'Connexion réussie');
    }

    /**
     * Récupère les informations de l'utilisateur connecté
     * GET /api/auth/me
     *
     * @return void
     */
    public function me(): void
    {
        // Authentification requise
        $authData = $this->auth->authenticate();

        if (!$authData) {
            return; // Le middleware a déjà envoyé la réponse 401
        }

        // Récupération des données complètes de l'utilisateur
        $user = $this->userModel->findById($authData['user_id']);

        if (!$user) {
            $this->sendError('Utilisateur introuvable', 404);
        }

        // Suppression du mot de passe
        unset($user['password']);

        // Récupération des statistiques
        $buildsCount = count($this->userModel->getUserBuilds($user['id']));
        $favoritesCount = count($this->userModel->getUserFavorites($user['id']));

        $this->sendSuccess([
            'user' => $user,
            'stats' => [
                'builds_count' => $buildsCount,
                'favorites_count' => $favoritesCount
            ]
        ]);
    }

    /**
     * Mise à jour du profil utilisateur
     * PUT /api/auth/profile
     *
     * @return void
     */
    public function updateProfile(): void
    {
        // Authentification requise
        $authData = $this->auth->authenticate();

        if (!$authData) {
            return;
        }

        $data = $this->getRequestBody();

        if (!$data) {
            $this->sendError('Données invalides', 400);
        }

        $userId = $authData['user_id'];
        $updateData = [];

        // Mise à jour du username
        if (isset($data['username']) && trim($data['username']) !== '') {
            if ($this->userModel->usernameExists($data['username'], $userId)) {
                $this->sendError('Ce nom d\'utilisateur est déjà utilisé', 409);
            }
            $updateData['username'] = $this->sanitize($data['username']);
        }

        // Mise à jour de l'email
        if (isset($data['email']) && trim($data['email']) !== '') {
            if (!$this->validateEmail($data['email'])) {
                $this->sendError('Email invalide', 422);
            }
            if ($this->userModel->emailExists($data['email'], $userId)) {
                $this->sendError('Cet email est déjà utilisé', 409);
            }
            $updateData['email'] = $this->sanitize($data['email']);
        }

        // Mise à jour du mot de passe
        if (isset($data['password']) && trim($data['password']) !== '') {
            if (strlen($data['password']) < 8) {
                $this->sendError('Le mot de passe doit contenir au moins 8 caractères', 422);
            }
            $updateData['password'] = $data['password'];
        }

        // Mise à jour de l'avatar
        if (isset($data['avatar'])) {
            $updateData['avatar'] = $this->sanitize($data['avatar']);
        }

        if (empty($updateData)) {
            $this->sendError('Aucune donnée à mettre à jour', 400);
        }

        // Mise à jour
        $success = $this->userModel->updateUser($userId, $updateData);

        if (!$success) {
            $this->sendError('Erreur lors de la mise à jour', 500);
        }

        // Récupération des données mises à jour
        $user = $this->userModel->findById($userId);
        unset($user['password']);

        $this->sendSuccess(['user' => $user], 'Profil mis à jour avec succès');
    }

    /**
     * Déconnexion (côté client principalement)
     * POST /api/auth/logout
     *
     * @return void
     */
    public function logout(): void
    {
        // Avec JWT, la déconnexion est principalement gérée côté client
        // (suppression du token du localStorage)
        $this->sendSuccess(null, 'Déconnexion réussie');
    }
}
