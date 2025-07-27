<script setup>
import { ref } from 'vue'
import api from '../services/api'  // Seu axios configurado

const carregando = ref(false)
const mensagem = ref('')

async function sincronizar() {
  carregando.value = true
  mensagem.value = ''

  try {
    const response = await api.post('/sincronizar-deputados')
    mensagem.value = response.data.message || 'Sincronização iniciada com sucesso!'
  } catch (error) {
    mensagem.value = 'Erro ao iniciar sincronização.'
    console.error(error)
  } finally {
    carregando.value = false
  }
}
</script>

<template>
  <div>
    <button @click="sincronizar" :disabled="carregando">
      {{ carregando ? 'Sincronizando...' : 'Sincronizar Deputados' }}
    </button>

    <p v-if="mensagem">{{ mensagem }}</p>
  </div>
</template>

<style scoped>
button {
  padding: 8px 16px;
  font-weight: bold;
  cursor: pointer;
}
button[disabled] {
  cursor: not-allowed;
  opacity: 0.6;
}
</style>
