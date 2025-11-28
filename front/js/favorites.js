/**
 * Page Mes favoris - Liste les builds favoris de l'utilisateur
 */

// Protège la page : nécessite d'être connecté
requireAuth();

async function loadFavorites() {
    const loading = document.getElementById("favorites-loading");
    const errorEl = document.getElementById("favorites-error");
    const emptyEl = document.getElementById("favorites-empty");
    const listEl = document.getElementById("favorites-list");

    if (!loading || !errorEl || !emptyEl || !listEl) return;

    loading.style.display = "block";
    errorEl.style.display = "none";
    emptyEl.style.display = "none";
    listEl.style.display = "none";
    listEl.innerHTML = "";

    try {
        const data = await favoritesAPI.getAll();
        const payload = data.data || data; // structure sendSuccess
        const favorites = payload.favorites || payload;

        if (!favorites || favorites.length === 0) {
            loading.style.display = "none";
            emptyEl.style.display = "block";
            return;
        }

        favorites.forEach((fav) => {
            // selon ton API, fav peut déjà contenir le build complet ou seulement build_id + build
            const build = fav.build || fav;
            const card = createFavoriteBuildCard(build);
            listEl.appendChild(card);
        });

        loading.style.display = "none";
        listEl.style.display = "grid";
    } catch (error) {
        loading.style.display = "none";
        errorEl.style.display = "block";
        errorEl.textContent =
            error.message || "Erreur lors du chargement de vos favoris";
    }
}

function createFavoriteBuildCard(build) {
    const card = document.createElement("div");
    card.className = "build-card card";
    card.innerHTML = `
        <div class="build-header">
            <img src="${
                build.character_icon || "images/placeholder.png"
            }" alt="${build.character_name || ""}" class="character-icon">
            <div class="build-info">
                <h3>${build.title}</h3>
                <p class="character-name">${build.character_name || ""} • ${
        build.element || ""
    }</p>
            </div>
        </div>
        <p class="build-description">${truncate(build.description, 100)}</p>
        <div class="build-meta">
            <span>⭐ ${build.rating || 0}</span>
            <span>👁 ${build.views_count || 0}</span>
            <span>❤ ${build.favorites_count || 0}</span>
        </div>
        <div class="build-footer">
            <span class="author">Par: ${build.author || "Anonyme"}</span>
            <a href="build-detail.html?id=${
                build.id
            }" class="btn btn-primary btn-sm">Voir</a>
        </div>
    `;
    return card;
}

// petit helper de tronquage (copié d'index.html)
function truncate(text, length) {
    if (!text) return "";
    return text.length > length ? text.substring(0, length) + "..." : text;
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", loadFavorites);
} else {
    loadFavorites();
}
