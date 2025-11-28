/**
 * Auth Helper - Simple JavaScript
 * Gestion de l'authentification
 */

// Vérifie si l'utilisateur est connecté
function isAuthenticated() {
    return !!localStorage.getItem('token');
}

// Récupère l'utilisateur connecté
function getCurrentUser() {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
}

// Déconnexion
function logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    window.location.href = 'login.html';
}

// Protège une page (redirige vers login si non connecté)
function requireAuth() {
    if (!isAuthenticated()) {
        window.location.href = 'login.html';
    }
}

// Redirige vers l'accueil si déjà connecté
function redirectIfAuthenticated() {
    if (isAuthenticated()) {
        window.location.href = 'index.html';
    }
}

// Met à jour la navbar en fonction de l'état d'authentification
function updateNavbar() {
    const authLinks = document.getElementById('auth-links');
    if (!authLinks) return;

    if (isAuthenticated()) {
        const user = getCurrentUser();
        authLinks.innerHTML = `
            <a href="favorites.html" class="nav-link">Favoris</a>
            <a href="profile.html" class="nav-link">Profil</a>
            <button onclick="logout()" class="btn btn-secondary">Déconnexion</button>
        `;
    } else {
        authLinks.innerHTML = `
            <a href="login.html" class="btn btn-primary">Connexion</a>
            <a href="register.html" class="btn btn-secondary">Inscription</a>
        `;
    }
}

// Initialise la navbar au chargement de la page
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', updateNavbar);
} else {
    updateNavbar();
}
