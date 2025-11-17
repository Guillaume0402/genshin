<?php
/**
 * Favorite Controller
 *
 * Gère le système de favoris pour les builds
 *
 * @package App\Controllers
 * @author DWWM Project
 * @version 1.0
 */

namespace App\Controllers;

use App\Models\Favorite;
use App\Models\Build;
use App\Middleware\Auth;

class FavoriteController extends BaseController
{
    /**
     * @var Favorite Model favorite
     */
    private Favorite $favoriteModel;

    /**
     * @var Build Model build
     */
    private Build $buildModel;

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
        $this->favoriteModel = new Favorite();
        $this->buildModel = new Build();
        $this->auth = new Auth();
    }

    /**
     * Liste tous les favoris de l'utilisateur connecté
     * GET /api/favorites
     *
     * @return void
     */
    public function index(): void
    {
        // Authentification requise
        $authData = $this->auth->authenticate();

        if (!$authData) {
            return;
        }

        $pagination = $this->getPaginationParams();

        $favorites = $this->favoriteModel->getUserFavorites(
            $authData['user_id'],
            $pagination['limit'],
            $pagination['offset']
        );

        $total = $this->favoriteModel->countUserFavorites($authData['user_id']);

        $this->sendSuccess([
            'favorites' => $favorites,
            'pagination' => [
                'total' => $total,
                'page' => $pagination['page'],
                'limit' => $pagination['limit'],
                'pages' => ceil($total / $pagination['limit'])
            ]
        ]);
    }

    /**
     * Ajoute un build aux favoris
     * POST /api/favorites
     *
     * @return void
     */
    public function add(): void
    {
        // Authentification requise
        $authData = $this->auth->authenticate();

        if (!$authData) {
            return;
        }

        $data = $this->getRequestBody();

        if (!$data || !isset($data['build_id'])) {
            $this->sendError('build_id requis', 400);
        }

        $buildId = (int)$data['build_id'];

        // Vérification de l'existence du build
        $build = $this->buildModel->findById($buildId);

        if (!$build) {
            $this->sendError('Build introuvable', 404);
        }

        // Vérification que le build est public ou appartient à l'utilisateur
        if (!$build['is_public'] && $build['user_id'] != $authData['user_id']) {
            $this->sendError('Ce build n\'est pas accessible', 403);
        }

        // Ajout aux favoris
        $favoriteId = $this->favoriteModel->addFavorite($authData['user_id'], $buildId);

        if ($favoriteId === false) {
            $this->sendError('Ce build est déjà dans vos favoris', 409);
        }

        // Mise à jour du compteur de favoris du build
        $this->buildModel->updateFavoritesCount($buildId);

        $this->sendSuccess([
            'favorite_id' => $favoriteId,
            'build_id' => $buildId
        ], 'Build ajouté aux favoris', 201);
    }

    /**
     * Retire un build des favoris
     * DELETE /api/favorites/:buildId
     *
     * @param int $buildId L'ID du build
     * @return void
     */
    public function remove(int $buildId): void
    {
        // Authentification requise
        $authData = $this->auth->authenticate();

        if (!$authData) {
            return;
        }

        // Vérification que le build existe
        $build = $this->buildModel->findById($buildId);

        if (!$build) {
            $this->sendError('Build introuvable', 404);
        }

        // Vérification que le favori existe
        if (!$this->favoriteModel->isFavorite($authData['user_id'], $buildId)) {
            $this->sendError('Ce build n\'est pas dans vos favoris', 404);
        }

        // Suppression du favori
        $success = $this->favoriteModel->removeFavorite($authData['user_id'], $buildId);

        if (!$success) {
            $this->sendError('Erreur lors de la suppression', 500);
        }

        // Mise à jour du compteur de favoris du build
        $this->buildModel->updateFavoritesCount($buildId);

        $this->sendSuccess(null, 'Build retiré des favoris');
    }

    /**
     * Toggle le statut favori d'un build (ajoute si absent, retire si présent)
     * POST /api/favorites/toggle
     *
     * @return void
     */
    public function toggle(): void
    {
        // Authentification requise
        $authData = $this->auth->authenticate();

        if (!$authData) {
            return;
        }

        $data = $this->getRequestBody();

        if (!$data || !isset($data['build_id'])) {
            $this->sendError('build_id requis', 400);
        }

        $buildId = (int)$data['build_id'];

        // Vérification de l'existence du build
        $build = $this->buildModel->findById($buildId);

        if (!$build) {
            $this->sendError('Build introuvable', 404);
        }

        // Vérification que le build est public ou appartient à l'utilisateur
        if (!$build['is_public'] && $build['user_id'] != $authData['user_id']) {
            $this->sendError('Ce build n\'est pas accessible', 403);
        }

        // Toggle du favori
        $result = $this->favoriteModel->toggleFavorite($authData['user_id'], $buildId);

        if (!$result['success']) {
            $this->sendError('Erreur lors de la mise à jour', 500);
        }

        // Mise à jour du compteur de favoris du build
        $this->buildModel->updateFavoritesCount($buildId);

        $message = $result['action'] === 'added' ? 'Build ajouté aux favoris' : 'Build retiré des favoris';

        $this->sendSuccess([
            'action' => $result['action'],
            'is_favorite' => $result['is_favorite'],
            'build_id' => $buildId
        ], $message);
    }

    /**
     * Vérifie si un build est en favori
     * GET /api/favorites/check/:buildId
     *
     * @param int $buildId L'ID du build
     * @return void
     */
    public function check(int $buildId): void
    {
        // Authentification requise
        $authData = $this->auth->authenticate();

        if (!$authData) {
            return;
        }

        $isFavorite = $this->favoriteModel->isFavorite($authData['user_id'], $buildId);

        $this->sendSuccess([
            'build_id' => $buildId,
            'is_favorite' => $isFavorite
        ]);
    }

    /**
     * Récupère les IDs de tous les builds favoris de l'utilisateur
     * GET /api/favorites/ids
     *
     * @return void
     */
    public function getFavoriteIds(): void
    {
        // Authentification requise
        $authData = $this->auth->authenticate();

        if (!$authData) {
            return;
        }

        $ids = $this->favoriteModel->getUserFavoriteBuildIds($authData['user_id']);

        $this->sendSuccess([
            'favorite_build_ids' => $ids,
            'total' => count($ids)
        ]);
    }
}
