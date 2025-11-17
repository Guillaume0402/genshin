<template>
  <div class="auth-page">
    <div class="auth-card card">
      <h1>Inscription</h1>

      <div v-if="authStore.error" class="error">{{ authStore.error }}</div>

      <form @submit.prevent="handleRegister">
        <div class="form-group">
          <label for="username">Nom d'utilisateur</label>
          <input
            id="username"
            v-model="form.username"
            type="text"
            required
            placeholder="Votre pseudo"
          />
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            required
            placeholder="votre@email.com"
          />
        </div>

        <div class="form-group">
          <label for="password">Mot de passe</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            required
            minlength="8"
            placeholder="••••••••"
          />
        </div>

        <div class="form-group">
          <label for="confirmPassword">Confirmer le mot de passe</label>
          <input
            id="confirmPassword"
            v-model="form.confirmPassword"
            type="password"
            required
            minlength="8"
            placeholder="••••••••"
          />
        </div>

        <div v-if="passwordMismatch" class="error">Les mots de passe ne correspondent pas</div>

        <button type="submit" class="btn btn-primary btn-block" :disabled="authStore.loading || passwordMismatch">
          {{ authStore.loading ? 'Inscription...' : 'S\'inscrire' }}
        </button>
      </form>

      <p class="auth-footer">
        Déjà un compte ? <router-link to="/login">Connectez-vous</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({
  username: '',
  email: '',
  password: '',
  confirmPassword: ''
})

const passwordMismatch = computed(() => {
  return form.password && form.confirmPassword && form.password !== form.confirmPassword
})

const handleRegister = async () => {
  if (passwordMismatch.value) return

  try {
    await authStore.register({
      username: form.username,
      email: form.email,
      password: form.password
    })
    router.push('/')
  } catch (error) {
    console.error('Registration error:', error)
  }
}
</script>

<style scoped>
.auth-page {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: calc(100vh - 200px);
}

.auth-card {
  width: 100%;
  max-width: 400px;
}

.auth-card h1 {
  text-align: center;
  color: #667eea;
  margin-bottom: 30px;
}

.btn-block {
  width: 100%;
}

.auth-footer {
  text-align: center;
  margin-top: 20px;
  color: #666;
}

.auth-footer a {
  color: #667eea;
  text-decoration: none;
  font-weight: 600;
}

.auth-footer a:hover {
  text-decoration: underline;
}
</style>
