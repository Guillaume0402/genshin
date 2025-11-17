<template>
  <div class="builds-page">
    <div class="page-header">
      <h1>Tous les Builds</h1>
      <router-link v-if="authStore.isAuthenticated" to="/builds/create" class="btn btn-primary">
        + Créer un Build
      </router-link>
    </div>

    <div class="filters">
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Rechercher un build..."
        @input="handleSearch"
        class="search-input"
      />
    </div>

    <div v-if="buildsStore.loading" class="loading">Chargement...</div>
    <div v-else-if="buildsStore.error" class="error">{{ buildsStore.error }}</div>
    <div v-else>
      <div v-if="buildsStore.builds.length === 0" class="empty">
        Aucun build trouvé
      </div>
      <div v-else class="grid">
        <BuildCard v-for="build in buildsStore.builds" :key="build.id" :build="build" />
      </div>

      <div v-if="buildsStore.pagination.pages > 1" class="pagination">
        <button
          @click="changePage(buildsStore.pagination.page - 1)"
          :disabled="buildsStore.pagination.page === 1"
          class="btn btn-secondary"
        >
          Précédent
        </button>
        <span>Page {{ buildsStore.pagination.page }} sur {{ buildsStore.pagination.pages }}</span>
        <button
          @click="changePage(buildsStore.pagination.page + 1)"
          :disabled="buildsStore.pagination.page >= buildsStore.pagination.pages"
          class="btn btn-secondary"
        >
          Suivant
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useBuildsStore } from '../stores/builds'
import { useAuthStore } from '../stores/auth'
import BuildCard from '../components/BuildCard.vue'

const buildsStore = useBuildsStore()
const authStore = useAuthStore()
const searchQuery = ref('')

onMounted(() => {
  buildsStore.fetchAll()
})

const handleSearch = () => {
  if (searchQuery.value.length >= 2) {
    buildsStore.search(searchQuery.value)
  } else if (searchQuery.value === '') {
    buildsStore.fetchAll()
  }
}

const changePage = (page) => {
  buildsStore.fetchAll({ page })
  window.scrollTo(0, 0)
}
</script>

<style scoped>
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  background: white;
  padding: 20px;
  border-radius: 10px;
}

.page-header h1 {
  color: #667eea;
  margin: 0;
}

.filters {
  margin-bottom: 30px;
}

.search-input {
  width: 100%;
  max-width: 500px;
  padding: 15px;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  font-size: 16px;
}

.search-input:focus {
  outline: none;
  border-color: #667eea;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 20px;
  margin-top: 40px;
  padding: 20px;
  background: white;
  border-radius: 10px;
}

.pagination span {
  font-weight: 600;
  color: #667eea;
}

.empty {
  text-align: center;
  padding: 40px;
  background: white;
  border-radius: 10px;
  color: #666;
}
</style>
