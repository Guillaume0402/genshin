<template>
  <div class="build-detail">
    <div v-if="buildsStore.loading" class="loading">Chargement...</div>
    <div v-else-if="buildsStore.error" class="error">{{ buildsStore.error }}</div>
    <div v-else-if="buildsStore.currentBuild" class="card">
      <h1>{{ buildsStore.currentBuild.title }}</h1>
      <p>{{ buildsStore.currentBuild.description }}</p>
      <!-- TODO: Ajouter les détails complets du build -->
      <router-link to="/builds" class="btn btn-secondary">Retour</router-link>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useBuildsStore } from '../stores/builds'

const route = useRoute()
const buildsStore = useBuildsStore()

onMounted(() => {
  buildsStore.fetchById(parseInt(route.params.id))
})
</script>
