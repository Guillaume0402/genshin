<template>
  <div class="not-found">
    <div class="card error-card">
      <h1>404</h1>
      <h2>Page non trouvée</h2>
      <p>La page que vous recherchez n'existe pas.</p>
      <router-link to="/" class="btn btn-primary btn-lg">
        Retour à l'accueil
      </router-link>
    </div>
  </div>
</template>

<style scoped>
.not-found {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: calc(100vh - 200px);
}

.error-card {
  text-align: center;
  max-width: 500px;
}

.error-card h1 {
  font-size: 6rem;
  color: #667eea;
  margin-bottom: 20px;
}

.error-card h2 {
  color: #333;
  margin-bottom: 15px;
}

.error-card p {
  color: #666;
  margin-bottom: 30px;
}

.btn-lg {
  text-decoration: none;
  padding: 15px 30px;
  font-size: 18px;
}
</style>
