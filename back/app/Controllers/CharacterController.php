<?php
/**
 * Character Controller
 *
 * Gère les opérations sur les personnages Genshin Impact
 *
 * @package App\Controllers
 * @author DWWM Project
 * @version 1.0
 */

namespace App\Controllers;

use App\Models\Character;

class CharacterController extends BaseController
{
    /**
     * @var Character Model character
     */
    private Character $characterModel;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->handleCors();
        $this->characterModel = new Character();
    }

    /**
     * Liste tous les personnages avec pagination et filtres
     * GET /api/characters
     *
     * @return void
     */
    public function index(): void
    {
        $pagination = $this->getPaginationParams();

        $filters = [
            'element' => $this->getParam('element'),
            'weapon_type' => $this->getParam('weapon_type'),
            'rarity' => $this->getParam('rarity'),
            'region' => $this->getParam('region'),
            'search' => $this->getParam('search')
        ];

        // Suppression des filtres vides
        $filters = array_filter($filters);

        $characters = $this->characterModel->getAllCharacters(
            $pagination['limit'],
            $pagination['offset'],
            $filters
        );

        $total = $this->characterModel->count();

        $this->sendSuccess([
            'characters' => $characters,
            'pagination' => [
                'total' => $total,
                'page' => $pagination['page'],
                'limit' => $pagination['limit'],
                'pages' => ceil($total / $pagination['limit'])
            ]
        ]);
    }

    /**
     * Récupère un personnage par son ID
     * GET /api/characters/:id
     *
     * @param int $id L'ID du personnage
     * @return void
     */
    public function show(int $id): void
    {
        $character = $this->characterModel->findById($id);

        if (!$character) {
            $this->sendError('Personnage introuvable', 404);
        }

        // Récupération des builds associés
        $buildsCount = $this->characterModel->countBuilds($id);

        $this->sendSuccess([
            'character' => $character,
            'stats' => [
                'builds_count' => $buildsCount
            ]
        ]);
    }

    /**
     * Récupère les builds d'un personnage
     * GET /api/characters/:id/builds
     *
     * @param int $id L'ID du personnage
     * @return void
     */
    public function builds(int $id): void
    {
        $character = $this->characterModel->findById($id);

        if (!$character) {
            $this->sendError('Personnage introuvable', 404);
        }

        $builds = $this->characterModel->getCharacterBuilds($id, true);

        $this->sendSuccess([
            'character' => $character,
            'builds' => $builds,
            'total' => count($builds)
        ]);
    }

    /**
     * Récupère les personnages par élément
     * GET /api/characters/element/:element
     *
     * @param string $element L'élément (Pyro, Hydro, etc.)
     * @return void
     */
    public function byElement(string $element): void
    {
        $validElements = ['Pyro', 'Hydro', 'Anemo', 'Electro', 'Dendro', 'Cryo', 'Geo'];

        if (!in_array($element, $validElements)) {
            $this->sendError('Élément invalide', 400);
        }

        $characters = $this->characterModel->getByElement($element);

        $this->sendSuccess([
            'element' => $element,
            'characters' => $characters,
            'total' => count($characters)
        ]);
    }

    /**
     * Récupère les personnages par type d'arme
     * GET /api/characters/weapon/:weaponType
     *
     * @param string $weaponType Le type d'arme
     * @return void
     */
    public function byWeapon(string $weaponType): void
    {
        $validWeapons = ['Sword', 'Claymore', 'Polearm', 'Bow', 'Catalyst'];

        if (!in_array($weaponType, $validWeapons)) {
            $this->sendError('Type d\'arme invalide', 400);
        }

        $characters = $this->characterModel->getByWeaponType($weaponType);

        $this->sendSuccess([
            'weapon_type' => $weaponType,
            'characters' => $characters,
            'total' => count($characters)
        ]);
    }

    /**
     * Récupère les personnages par rareté
     * GET /api/characters/rarity/:rarity
     *
     * @param int $rarity La rareté (4 ou 5)
     * @return void
     */
    public function byRarity(int $rarity): void
    {
        if (!in_array($rarity, [4, 5])) {
            $this->sendError('Rareté invalide (4 ou 5 étoiles)', 400);
        }

        $characters = $this->characterModel->getByRarity($rarity);

        $this->sendSuccess([
            'rarity' => $rarity,
            'characters' => $characters,
            'total' => count($characters)
        ]);
    }

    /**
     * Récupère les personnages les plus populaires
     * GET /api/characters/popular
     *
     * @return void
     */
    public function popular(): void
    {
        $limit = min(50, max(1, (int)$this->getParam('limit', 10)));
        $characters = $this->characterModel->getMostPopular($limit);

        $this->sendSuccess([
            'characters' => $characters,
            'total' => count($characters)
        ]);
    }

    /**
     * Recherche de personnages
     * GET /api/characters/search
     *
     * @return void
     */
    public function search(): void
    {
        $query = $this->getParam('q', '');

        if (strlen($query) < 2) {
            $this->sendError('La recherche doit contenir au moins 2 caractères', 400);
        }

        $characters = $this->characterModel->getAllCharacters(100, 0, ['search' => $query]);

        $this->sendSuccess([
            'characters' => $characters,
            'query' => $query,
            'total' => count($characters)
        ]);
    }
}
