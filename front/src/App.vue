<template>
  <div id="app">
    <Navbar />
    <main class="container">
      <router-view />
    </main>
    <Footer />
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from './stores/auth'
import Navbar from './components/Navbar.vue'
import Footer from './components/Footer.vue'

const authStore = useAuthStore()

onMounted(() => {
  // Si l'utilisateur est connecté, récupérer ses données à jour
  if (authStore.isAuthenticated) {
    authStore.fetchUser().catch(() => {
      // Si la requête échoue (token expiré), déconnecter
      authStore.logout()
    })
  }
})
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  min-height: 100vh;
  color: #333;
}

#app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
  flex: 1;
}

/* Boutons */
.btn {
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
  transition: all 0.3s;
}

.btn-primary {
  background: #667eea;
  color: white;
}

.btn-primary:hover {
  background: #5568d3;
  transform: translateY(-2px);
}

.btn-secondary {
  background: #764ba2;
  color: white;
}

.btn-secondary:hover {
  background: #5e3a82;
}

.btn-danger {
  background: #f56565;
  color: white;
}

.btn-danger:hover {
  background: #e53e3e;
}

/* Cards */
.card {
  background: white;
  border-radius: 10px;
  padding: 20px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
}

/* Forms */
.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
}

.form-group input,
.form-group textarea,
.form-group select {
  width: 100%;
  padding: 10px;
  border: 2px solid #e2e8f0;
  border-radius: 5px;
  font-size: 16px;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
  outline: none;
  border-color: #667eea;
}

/* Loading */
.loading {
  text-align: center;
  padding: 40px;
  font-size: 18px;
  color: #667eea;
}

/* Error */
.error {
  background: #fed7d7;
  color: #9b2c2c;
  padding: 15px;
  border-radius: 5px;
  margin-bottom: 20px;
}

/* Success */
.success {
  background: #c6f6d5;
  color: #22543d;
  padding: 15px;
  border-radius: 5px;
  margin-bottom: 20px;
}

/* Grid */
.grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

/* Utility Classes */
.text-center {
  text-align: center;
}

.mt-20 {
  margin-top: 20px;
}

.mb-20 {
  margin-bottom: 20px;
}
</style>
