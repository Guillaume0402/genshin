<?php
/**
 * Build Controller
 *
 * Gère les opérations CRUD pour les builds de personnages
 *
 * @package App\Controllers
 * @author DWWM Project
 * @version 1.0
 */

namespace App\Controllers;

use App\Models\Build;
use App\Models\Character;
use App\Middleware\Auth;

class BuildController extends BaseController
{
    /**
     * @var Build Model build
     */
    private Build $buildModel;

    /**
     * @var Character Model character
     */
    private Character $characterModel;

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
        $this->buildModel = new Build();
        $this->characterModel = new Character();
        $this->auth = new Auth();
    }

    /**
     * Liste tous les builds publics avec pagination et filtres
     * GET /api/builds
     *
     * @return void
     */
    public function index(): void
    {
        $pagination = $this->getPaginationParams();

        $filters = [
            'character_id' => $this->getParam('character_id'),
            'element' => $this->getParam('element'),
            'search' => $this->getParam('search'),
            'order_by' => $this->getParam('sort', 'created_at'),
            'order_dir' => $this->getParam('order', 'DESC')
        ];

        // Suppression des filtres vides
        $filters = array_filter($filters);

        $builds = $this->buildModel->getAllBuilds(
            $pagination['limit'],
            $pagination['offset'],
            $filters
        );

        $total = $this->buildModel->count(['is_public' => 1]);

        $this->sendSuccess([
            'builds' => $builds,
            'pagination' => [
                'total' => $total,
                'page' => $pagination['page'],
                'limit' => $pagination['limit'],
                'pages' => ceil($total / $pagination['limit'])
            ]
        ]);
    }

    /**
     * Récupère un build par son ID
     * GET /api/builds/:id
     *
     * @param int $id L'ID du build
     * @return void
     */
    public function show(int $id): void
    {
        $build = $this->buildModel->getBuildDetails($id);

        if (!$build) {
            $this->sendError('Build introuvable', 404);
        }

        // Vérification de la visibilité (si privé, seul le propriétaire peut voir)
        if (!$build['is_public']) {
            $authData = $this->auth->user();
            if (!$authData || $authData['user_id'] != $build['user_id']) {
                $this->sendError('Accès non autorisé', 403);
            }
        }

        // Incrémentation du compteur de vues
        $this->buildModel->incrementViews($id);

        $this->sendSuccess(['build' => $build]);
    }

    /**
     * Crée un nouveau build
     * POST /api/builds
     *
     * @return void
     */
    public function create(): void
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

        // Validation des champs requis
        $errors = $this->validateRequired($data, ['character_id', 'title']);
        if ($errors) {
            $this->sendError('Champs manquants', 422, $errors);
        }

        // Vérification de l'existence du personnage
        $character = $this->characterModel->findById($data['character_id']);
        if (!$character) {
            $this->sendError('Personnage introuvable', 404);
        }

        // Préparation des données
        $buildData = [
            'user_id' => $authData['user_id'],
            'character_id' => (int)$data['character_id'],
            'title' => $this->sanitize($data['title']),
            'description' => isset($data['description']) ? $this->sanitize($data['description']) : null,
            'artifact_set' => $data['artifact_set'] ?? null,
            'artifact_main_stats' => isset($data['artifact_main_stats']) ? json_encode($data['artifact_main_stats']) : null,
            'artifact_sub_stats' => isset($data['artifact_sub_stats']) ? json_encode($data['artifact_sub_stats']) : null,
            'weapon_name' => $data['weapon_name'] ?? null,
            'weapon_refinement' => isset($data['weapon_refinement']) ? (int)$data['weapon_refinement'] : 1,
            'talent_priority' => $data['talent_priority'] ?? null,
            'team_composition' => isset($data['team_composition']) ? json_encode($data['team_composition']) : null,
            'is_public' => isset($data['is_public']) ? (bool)$data['is_public'] : true,
            'tags' => isset($data['tags']) ? json_encode($data['tags']) : null
        ];

        $buildId = $this->buildModel->create($buildData);

        if (!$buildId) {
            $this->sendError('Erreur lors de la création du build', 500);
        }

        $build = $this->buildModel->getBuildDetails($buildId);

        $this->sendSuccess(['build' => $build], 'Build créé avec succès', 201);
    }

    /**
     * Met à jour un build
     * PUT /api/builds/:id
     *
     * @param int $id L'ID du build
     * @return void
     */
    public function update(int $id): void
    {
        // Authentification requise
        $authData = $this->auth->authenticate();

        if (!$authData) {
            return;
        }

        // Vérification de l'existence du build
        $build = $this->buildModel->findById($id);

        if (!$build) {
            $this->sendError('Build introuvable', 404);
        }

        // Vérification de la propriété
        if (!$this->buildModel->isOwner($id, $authData['user_id'])) {
            $this->sendError('Vous n\'êtes pas autorisé à modifier ce build', 403);
        }

        $data = $this->getRequestBody();

        if (!$data) {
            $this->sendError('Données invalides', 400);
        }

        // Préparation des données à mettre à jour
        $updateData = [];

        if (isset($data['title'])) {
            $updateData['title'] = $this->sanitize($data['title']);
        }

        if (isset($data['description'])) {
            $updateData['description'] = $this->sanitize($data['description']);
        }

        if (isset($data['artifact_set'])) {
            $updateData['artifact_set'] = $data['artifact_set'];
        }

        if (isset($data['artifact_main_stats'])) {
            $updateData['artifact_main_stats'] = json_encode($data['artifact_main_stats']);
        }

        if (isset($data['artifact_sub_stats'])) {
            $updateData['artifact_sub_stats'] = json_encode($data['artifact_sub_stats']);
        }

        if (isset($data['weapon_name'])) {
            $updateData['weapon_name'] = $data['weapon_name'];
        }

        if (isset($data['weapon_refinement'])) {
            $updateData['weapon_refinement'] = (int)$data['weapon_refinement'];
        }

        if (isset($data['talent_priority'])) {
            $updateData['talent_priority'] = $data['talent_priority'];
        }

        if (isset($data['team_composition'])) {
            $updateData['team_composition'] = json_encode($data['team_composition']);
        }

        if (isset($data['is_public'])) {
            $updateData['is_public'] = (bool)$data['is_public'];
        }

        if (isset($data['tags'])) {
            $updateData['tags'] = json_encode($data['tags']);
        }

        if (empty($updateData)) {
            $this->sendError('Aucune donnée à mettre à jour', 400);
        }

        $success = $this->buildModel->update($id, $updateData);

        if (!$success) {
            $this->sendError('Erreur lors de la mise à jour du build', 500);
        }

        $updatedBuild = $this->buildModel->getBuildDetails($id);

        $this->sendSuccess(['build' => $updatedBuild], 'Build mis à jour avec succès');
    }

    /**
     * Supprime un build
     * DELETE /api/builds/:id
     *
     * @param int $id L'ID du build
     * @return void
     */
    public function delete(int $id): void
    {
        // Authentification requise
        $authData = $this->auth->authenticate();

        if (!$authData) {
            return;
        }

        // Vérification de l'existence du build
        $build = $this->buildModel->findById($id);

        if (!$build) {
            $this->sendError('Build introuvable', 404);
        }

        // Vérification de la propriété
        if (!$this->buildModel->isOwner($id, $authData['user_id'])) {
            $this->sendError('Vous n\'êtes pas autorisé à supprimer ce build', 403);
        }

        $success = $this->buildModel->delete($id);

        if (!$success) {
            $this->sendError('Erreur lors de la suppression du build', 500);
        }

        $this->sendSuccess(null, 'Build supprimé avec succès');
    }

    /**
     * Récupère les builds de l'utilisateur connecté
     * GET /api/builds/my-builds
     *
     * @return void
     */
    public function myBuilds(): void
    {
        // Authentification requise
        $authData = $this->auth->authenticate();

        if (!$authData) {
            return;
        }

        $builds = $this->buildModel->getUserBuilds($authData['user_id'], true);

        $this->sendSuccess([
            'builds' => $builds,
            'total' => count($builds)
        ]);
    }

    /**
     * Récupère les builds les mieux notés
     * GET /api/builds/top-rated
     *
     * @return void
     */
    public function topRated(): void
    {
        $limit = min(50, max(1, (int)$this->getParam('limit', 10)));
        $builds = $this->buildModel->getTopRated($limit);

        $this->sendSuccess(['builds' => $builds]);
    }

    /**
     * Récupère les builds les plus récents
     * GET /api/builds/recent
     *
     * @return void
     */
    public function recent(): void
    {
        $limit = min(50, max(1, (int)$this->getParam('limit', 10)));
        $builds = $this->buildModel->getRecent($limit);

        $this->sendSuccess(['builds' => $builds]);
    }

    /**
     * Recherche de builds
     * GET /api/builds/search
     *
     * @return void
     */
    public function search(): void
    {
        $query = $this->getParam('q', '');

        if (strlen($query) < 2) {
            $this->sendError('La recherche doit contenir au moins 2 caractères', 400);
        }

        $limit = min(100, max(1, (int)$this->getParam('limit', 20)));
        $builds = $this->buildModel->search($query, $limit);

        $this->sendSuccess([
            'builds' => $builds,
            'query' => $query,
            'total' => count($builds)
        ]);
    }
}
