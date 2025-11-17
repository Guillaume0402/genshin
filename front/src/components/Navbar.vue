<template>
  <nav class="navbar">
    <div class="nav-container">
      <router-link to="/" class="nav-logo">
        <h2>Genshin Build Manager</h2>
      </router-link>

      <div class="nav-menu">
        <router-link to="/" class="nav-link">Accueil</router-link>
        <router-link to="/builds" class="nav-link">Builds</router-link>
        <router-link to="/characters" class="nav-link">Personnages</router-link>

        <template v-if="authStore.isAuthenticated">
          <router-link to="/favorites" class="nav-link">Favoris</router-link>
          <router-link to="/profile" class="nav-link">Profil</router-link>
          <button @click="handleLogout" class="btn btn-secondary">Déconnexion</button>
        </template>
        <template v-else>
          <router-link to="/login" class="btn btn-primary">Connexion</router-link>
          <router-link to="/register" class="btn btn-secondary">Inscription</router-link>
        </template>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>

<style scoped>
.navbar {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  padding: 1rem 0;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.nav-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.nav-logo h2 {
  color: #667eea;
  text-decoration: none;
}

.nav-menu {
  display: flex;
  gap: 20px;
  align-items: center;
}

.nav-link {
  color: #333;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s;
}

.nav-link:hover,
.nav-link.router-link-active {
  color: #667eea;
}

.btn {
  text-decoration: none;
  display: inline-block;
}
</style>
