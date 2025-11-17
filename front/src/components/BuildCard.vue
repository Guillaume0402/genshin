<template>
  <div class="build-card card">
    <div class="build-header">
      <img :src="build.character_icon || '/placeholder.png'" :alt="build.character_name" class="character-icon" />
      <div class="build-info">
        <h3>{{ build.title }}</h3>
        <p class="character-name">{{ build.character_name }} • {{ build.element }}</p>
      </div>
    </div>

    <p class="build-description">{{ truncateText(build.description, 100) }}</p>

    <div class="build-meta">
      <span>⭐ {{ build.rating }}</span>
      <span>👁 {{ build.views_count }}</span>
      <span>❤ {{ build.favorites_count }}</span>
    </div>

    <div class="build-footer">
      <span class="author">Par: {{ build.author }}</span>
      <router-link :to="`/builds/${build.id}`" class="btn btn-primary btn-sm">Voir</router-link>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'

defineProps({
  build: {
    type: Object,
    required: true
  }
})

const truncateText = (text, length) => {
  if (!text) return ''
  return text.length > length ? text.substring(0, length) + '...' : text
}
</script>

<style scoped>
.build-card {
  height: 100%;
  display: flex;
  flex-direction: column;
}

.build-header {
  display: flex;
  gap: 15px;
  margin-bottom: 15px;
}

.character-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  object-fit: cover;
}

.build-info h3 {
  margin: 0 0 5px 0;
  color: #667eea;
}

.character-name {
  color: #666;
  font-size: 14px;
}

.build-description {
  color: #555;
  margin-bottom: 15px;
  flex: 1;
}

.build-meta {
  display: flex;
  gap: 15px;
  margin-bottom: 15px;
  font-size: 14px;
  color: #888;
}

.build-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 15px;
  border-top: 1px solid #eee;
}

.author {
  font-size: 14px;
  color: #888;
}

.btn-sm {
  padding: 8px 16px;
  font-size: 14px;
}
</style>
