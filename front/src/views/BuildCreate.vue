<template>
  <div class="build-create">
    <div class="card">
      <h1>Créer un Build</h1>
      <form @submit.prevent="handleSubmit">
        <div class="form-group">
          <label>Personnage</label>
          <select v-model="form.character_id" required>
            <option value="">Sélectionner un personnage</option>
            <!-- TODO: Charger la liste des personnages -->
          </select>
        </div>
        <div class="form-group">
          <label>Titre</label>
          <input v-model="form.title" type="text" required />
        </div>
        <div class="form-group">
          <label>Description</label>
          <textarea v-model="form.description" rows="4"></textarea>
        </div>
        <!-- TODO: Ajouter tous les champs du build -->
        <button type="submit" class="btn btn-primary" :disabled="buildsStore.loading">
          {{ buildsStore.loading ? 'Création...' : 'Créer' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useBuildsStore } from '../stores/builds'

const router = useRouter()
const buildsStore = useBuildsStore()

const form = reactive({
  character_id: '',
  title: '',
  description: ''
})

const handleSubmit = async () => {
  try {
    await buildsStore.create(form)
    router.push('/builds')
  } catch (error) {
    console.error('Error creating build:', error)
  }
}
</script>
