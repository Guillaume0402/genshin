<template>
  <div class="character-detail">
    <div v-if="charactersStore.loading" class="loading">Chargement...</div>
    <div v-else-if="charactersStore.error" class="error">{{ charactersStore.error }}</div>
    <div v-else-if="charactersStore.currentCharacter" class="card">
      <h1>{{ charactersStore.currentCharacter.name }}</h1>
      <p>{{ charactersStore.currentCharacter.description }}</p>
      <!-- TODO: Afficher tous les détails et les builds du personnage -->
      <router-link to="/characters" class="btn btn-secondary">Retour</router-link>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useCharactersStore } from '../stores/characters'

const route = useRoute()
const charactersStore = useCharactersStore()

onMounted(() => {
  charactersStore.fetchById(parseInt(route.params.id))
})
</script>
