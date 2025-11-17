<template>
  <div class="favorites-page">
    <div class="page-header">
      <h1>Mes Favoris</h1>
    </div>

    <div v-if="favoritesStore.loading" class="loading">Chargement...</div>
    <div v-else-if="favoritesStore.error" class="error">{{ favoritesStore.error }}</div>
    <div v-else>
      <div v-if="favoritesStore.favorites.length === 0" class="empty">
        Vous n'avez pas encore de favoris
      </div>
      <div v-else class="grid">
        <BuildCard v-for="favorite in favoritesStore.favorites" :key="favorite.id" :build="favorite" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useFavoritesStore } from '../stores/favorites'
import BuildCard from '../components/BuildCard.vue'

const favoritesStore = useFavoritesStore()

onMounted(() => {
  favoritesStore.fetchAll()
})
</script>

<style scoped>
.page-header {
  background: white;
  padding: 20px;
  border-radius: 10px;
  margin-bottom: 30px;
}

.page-header h1 {
  color: #667eea;
  margin: 0;
}

.empty {
  text-align: center;
  padding: 40px;
  background: white;
  border-radius: 10px;
  color: #666;
}
</style>
