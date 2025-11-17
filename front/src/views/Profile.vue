<template>
  <div class="profile-page">
    <div class="card">
      <h1>Mon Profil</h1>

      <div v-if="authStore.loading" class="loading">Chargement...</div>
      <div v-else-if="authStore.currentUser">
        <div class="profile-info">
          <h3>{{ authStore.currentUser.username }}</h3>
          <p>{{ authStore.currentUser.email }}</p>
        </div>

        <h2>Mes Builds</h2>
        <div v-if="buildsStore.myBuilds.length === 0" class="empty">
          Vous n'avez pas encore créé de builds
        </div>
        <div v-else class="grid">
          <BuildCard v-for="build in buildsStore.myBuilds" :key="build.id" :build="build" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useBuildsStore } from '../stores/builds'
import BuildCard from '../components/BuildCard.vue'

const authStore = useAuthStore()
const buildsStore = useBuildsStore()

onMounted(() => {
  buildsStore.fetchMyBuilds()
})
</script>

<style scoped>
.profile-info {
  padding: 20px;
  background: #f5f5f5;
  border-radius: 10px;
  margin-bottom: 30px;
}

.profile-info h3 {
  color: #667eea;
  margin-bottom: 10px;
}

.empty {
  text-align: center;
  padding: 40px;
  background: #f5f5f5;
  border-radius: 10px;
  color: #666;
  margin-top: 20px;
}
</style>
