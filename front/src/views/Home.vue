<template>
  <div class="home">
    <section class="hero">
      <h1>Genshin Build Manager</h1>
      <p>Créez, partagez et découvrez les meilleurs builds pour vos personnages préférés</p>
      <div class="hero-actions">
        <router-link to="/builds" class="btn btn-primary btn-lg">Explorer les Builds</router-link>
        <router-link to="/characters" class="btn btn-secondary btn-lg">Voir les Personnages</router-link>
      </div>
    </section>

    <section class="featured">
      <h2>Builds les mieux notés</h2>
      <div v-if="buildsStore.loading" class="loading">Chargement...</div>
      <div v-else-if="buildsStore.error" class="error">{{ buildsStore.error }}</div>
      <div v-else class="grid">
        <BuildCard v-for="build in buildsStore.topRatedBuilds" :key="build.id" :build="build" />
      </div>
    </section>

    <section class="featured">
      <h2>Personnages populaires</h2>
      <div v-if="charactersStore.loading" class="loading">Chargement...</div>
      <div v-else-if="charactersStore.error" class="error">{{ charactersStore.error }}</div>
      <div v-else class="grid">
        <CharacterCard v-for="character in charactersStore.popularCharacters" :key="character.id" :character="character" />
      </div>
    </section>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useBuildsStore } from '../stores/builds'
import { useCharactersStore } from '../stores/characters'
import BuildCard from '../components/BuildCard.vue'
import CharacterCard from '../components/CharacterCard.vue'

const buildsStore = useBuildsStore()
const charactersStore = useCharactersStore()

onMounted(async () => {
  await buildsStore.fetchTopRated(6)
  await charactersStore.fetchPopular(6)
})
</script>

<style scoped>
.hero {
  text-align: center;
  padding: 60px 20px;
  background: white;
  border-radius: 15px;
  margin-bottom: 40px;
}

.hero h1 {
  font-size: 3rem;
  color: #667eea;
  margin-bottom: 20px;
}

.hero p {
  font-size: 1.5rem;
  color: #666;
  margin-bottom: 30px;
}

.hero-actions {
  display: flex;
  gap: 20px;
  justify-content: center;
}

.btn-lg {
  padding: 15px 30px;
  font-size: 18px;
  text-decoration: none;
}

.featured {
  margin-bottom: 40px;
}

.featured h2 {
  color: white;
  margin-bottom: 20px;
  font-size: 2rem;
}
</style>
