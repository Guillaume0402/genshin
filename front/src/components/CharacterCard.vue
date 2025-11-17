<template>
  <div class="character-card card">
    <img :src="character.icon_url || '/placeholder.png'" :alt="character.name" class="character-image" />

    <div class="character-content">
      <h3>{{ character.name }}</h3>

      <div class="character-details">
        <span class="badge" :class="`element-${character.element?.toLowerCase()}`">
          {{ character.element }}
        </span>
        <span class="badge weapon">{{ character.weapon_type }}</span>
        <span class="badge rarity">{{ '⭐'.repeat(character.rarity) }}</span>
      </div>

      <p class="character-region">{{ character.region }}</p>

      <div class="character-stats" v-if="character.builds_count !== undefined">
        <span>📖 {{ character.builds_count }} builds</span>
      </div>

      <router-link :to="`/characters/${character.id}`" class="btn btn-primary btn-block">
        Voir les builds
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'

defineProps({
  character: {
    type: Object,
    required: true
  }
})
</script>

<style scoped>
.character-card {
  text-align: center;
}

.character-image {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 10px 10px 0 0;
  margin: -20px -20px 20px -20px;
}

.character-content h3 {
  color: #667eea;
  margin-bottom: 15px;
}

.character-details {
  display: flex;
  gap: 10px;
  justify-content: center;
  flex-wrap: wrap;
  margin-bottom: 15px;
}

.badge {
  padding: 5px 10px;
  border-radius: 15px;
  font-size: 12px;
  font-weight: 600;
  color: white;
}

.element-pyro { background: #ff6b6b; }
.element-hydro { background: #4dabf7; }
.element-anemo { background: #74c0fc; }
.element-electro { background: #b197fc; }
.element-dendro { background: #51cf66; }
.element-cryo { background: #91d7ff; }
.element-geo { background: #ffa94d; }
.weapon { background: #868e96; }
.rarity { background: #ffd43b; color: #333; }

.character-region {
  color: #666;
  font-size: 14px;
  margin-bottom: 15px;
}

.character-stats {
  font-size: 14px;
  color: #888;
  margin-bottom: 15px;
}

.btn-block {
  width: 100%;
  text-align: center;
  text-decoration: none;
}
</style>
