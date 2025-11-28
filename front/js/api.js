/**
 * API Client - Simple JavaScript
 * Gestion des appels API avec fetch
 */

const API_URL = 'http://localhost:8000/api';

// Fonction helper pour les appels API
async function apiCall(endpoint, options = {}) {
    const token = localStorage.getItem('token');

    const headers = {
        'Content-Type': 'application/json',
        ...options.headers
    };

    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }

    try {
        const response = await fetch(`${API_URL}${endpoint}`, {
            ...options,
            headers
        });

        // Gestion des erreurs 401
        if (response.status === 401) {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = 'login.html';
            throw new Error('Non authentifié');
        }

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Erreur API');
        }

        return data;
    } catch (error) {
        throw error;
    }
}

// Fonctions API pour l'authentification
const authAPI = {
    async login(email, password) {
        return await apiCall('/auth/login', {
            method: 'POST',
            body: JSON.stringify({ email, password })
        });
    },

    async register(username, email, password) {
        return await apiCall('/auth/register', {
            method: 'POST',
            body: JSON.stringify({ username, email, password })
        });
    },

    async me() {
        return await apiCall('/auth/me');
    }
};

// Fonctions API pour les builds
const buildsAPI = {
    async getAll(params = '') {
        return await apiCall(`/builds${params}`);
    },

    async getById(id) {
        return await apiCall(`/builds/${id}`);
    },

    async create(buildData) {
        return await apiCall('/builds', {
            method: 'POST',
            body: JSON.stringify(buildData)
        });
    },

    async delete(id) {
        return await apiCall(`/builds/${id}`, {
            method: 'DELETE'
        });
    }
};

// Fonctions API pour les personnages
const charactersAPI = {
    async getAll(params = '') {
        return await apiCall(`/characters${params}`);
    },

    async getById(id) {
        return await apiCall(`/characters/${id}`);
    },

    async getBuilds(id) {
        return await apiCall(`/characters/${id}/builds`);
    }
};

// Fonctions API pour les favoris
const favoritesAPI = {
    async getAll() {
        return await apiCall('/favorites');
    },

    async toggle(buildId) {
        return await apiCall('/favorites/toggle', {
            method: 'POST',
            body: JSON.stringify({ build_id: buildId })
        });
    }
};
