/**
 * Page Profil - connexion avec le backend
 */

// Protège la page profil : redirige vers login si non connecté
requireAuth();

async function loadProfile() {
    const loading = document.getElementById("profile-loading");
    const errorEl = document.getElementById("profile-error");
    const content = document.getElementById("profile-content");

    if (!loading || !errorEl || !content) return;

    loading.style.display = "block";
    errorEl.style.display = "none";
    content.style.display = "none";

    try {
        const data = await authAPI.me();
        const payload = data.data || data;
        const user = payload.user || payload; // structure: { success, message, data: { user, stats } }

        if (!user) {
            throw new Error("Impossible de charger le profil utilisateur");
        }

        document.getElementById("profile-username").value =
            user.username || user.name || "";
        document.getElementById("profile-email").value = user.email || "";

        const createdAt = user.created_at || user.createdAt;
        if (createdAt) {
            const d = new Date(createdAt);
            document.getElementById("profile-created-at").value =
                d.toLocaleDateString("fr-FR", {
                    year: "numeric",
                    month: "long",
                    day: "numeric",
                });
        } else {
            document.getElementById("profile-created-at").value =
                "Non disponible";
        }

        loading.style.display = "none";
        content.style.display = "block";
    } catch (error) {
        loading.style.display = "none";
        errorEl.style.display = "block";
        errorEl.textContent =
            error.message || "Erreur lors du chargement du profil";
    }
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", loadProfile);
} else {
    loadProfile();
}
