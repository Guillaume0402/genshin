<template>
  <div class="characters-page">
    <div class="page-header">
      <h1>Personnages</h1>
    </div>

    <div class="filters">
      <select v-model="selectedElement" @change="handleFilter" class="filter-select">
        <option value="">Tous les éléments</option>
        <option value="Pyro">Pyro</option>
        <option value="Hydro">Hydro</option>
        <option value="Anemo">Anemo</option>
        <option value="Electro">Electro</option>
        <option value="Dendro">Dendro</option>
        <option value="Cryo">Cryo</option>
        <option value="Geo">Geo</option>
      </select>

      <select v-model="selectedRarity" @change="handleFilter" class="filter-select">
        <option value="">Toutes les raretés</option>
        <option value="4">4 étoiles</option>
        <option value="5">5 étoiles</option>
      </select>
    </div>

    <div v-if="charactersStore.loading" class="loading">Chargement...</div>
    <div v-else-if="charactersStore.error" class="error">{{ charactersStore.error }}</div>
    <div v-else>
      <div v-if="charactersStore.characters.length === 0" class="empty">
        Aucun personnage trouvé
      </div>
      <div v-else class="grid">
        <CharacterCard v-for="character in charactersStore.characters" :key="character.id" :character="character" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useCharactersStore } from '../stores/characters'
import CharacterCard from '../components/CharacterCard.vue'

const charactersStore = useCharactersStore()
const selectedElement = ref('')
const selectedRarity = ref('')

onMounted(() => {
  charactersStore.fetchAll()
})

const handleFilter = () => {
  const filters = {}
  if (selectedElement.value) filters.element = selectedElement.value
  if (selectedRarity.value) filters.rarity = selectedRarity.value

  charactersStore.fetchAll(filters)
}
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

.filters {
  display: flex;
  gap: 15px;
  margin-bottom: 30px;
}

.filter-select {
  padding: 10px 15px;
  border: 2px solid #e2e8f0;
  border-radius: 5px;
  font-size: 16px;
  background: white;
  cursor: pointer;
}

.filter-select:focus {
  outline: none;
  border-color: #667eea;
}

.empty {
  text-align: center;
  padding: 40px;
  background: white;
  border-radius: 10px;
  color: #666;
}
</style>
