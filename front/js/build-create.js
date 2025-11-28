/**
 * Page Création de build
 */

// Accès réservé aux utilisateurs connectés
requireAuth();

async function initBuildCreatePage() {
    const form = document.getElementById('build-create-form');
    const errorEl = document.getElementById('build-create-error');
    const successEl = document.getElementById('build-create-success');
    const characterSelect = document.getElementById('character_id');

    if (!form || !errorEl || !successEl || !characterSelect) return;

    // Charger la liste des personnages pour le select
    try {
        const response = await charactersAPI.getAll('?limit=200');
        const characters = (response.data && response.data.characters) || [];

        characterSelect.innerHTML = '';
        characters.forEach((c) => {
            const option = document.createElement('option');
            option.value = c.id;
            option.textContent = `${c.name} (${c.element} • ${c.weapon_type})`;
            characterSelect.appendChild(option);
        });
    } catch (error) {
        errorEl.style.display = 'block';
        errorEl.textContent = error.message || "Impossible de charger la liste des personnages";
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        errorEl.style.display = 'none';
        successEl.style.display = 'none';

        const buildData = {
            character_id: parseInt(characterSelect.value, 10),
            title: document.getElementById('title').value.trim(),
            description: document.getElementById('description').value.trim() || null,
            artifact_set: document.getElementById('artifact_set').value.trim() || null,
            weapon_name: document.getElementById('weapon_name').value.trim() || null,
            talent_priority: document.getElementById('talent_priority').value.trim() || null,
            is_public: document.getElementById('is_public').value === '1',
        };

        if (!buildData.title || !buildData.character_id) {
            errorEl.style.display = 'block';
            errorEl.textContent = 'Veuillez renseigner au minimum le personnage et le titre du build.';
            return;
        }

        try {
            const response = await buildsAPI.create(buildData);
            successEl.style.display = 'block';
            successEl.textContent = response.message || 'Build créé avec succès';

            // Rediriger vers la page de détail du build si renvoyé
            const payload = response.data || {};
            if (payload.build && payload.build.id) {
                setTimeout(() => {
                    window.location.href = `build-detail.html?id=${payload.build.id}`;
                }, 800);
            } else {
                // Sinon, retourner à la liste après un court délai
                setTimeout(() => {
                    window.location.href = 'builds.html';
                }, 800);
            }
        } catch (error) {
            errorEl.style.display = 'block';
            errorEl.textContent = error.message || "Erreur lors de la création du build";
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBuildCreatePage);
} else {
    initBuildCreatePage();
}
