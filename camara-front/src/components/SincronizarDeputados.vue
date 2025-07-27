<script setup>
import { ref } from 'vue'
import api from '../services/api'

const carregando = ref(false)
const mensagem = ref('')

async function sincronizar() {
  carregando.value = true
  mensagem.value = ''

  try {
    const response = await api.post('/sincronizar-deputados')
    mensagem.value = response.data.message || 'Sincronização iniciada com sucesso!'
  } catch (error) {
    mensagem.value = 'Erro ao iniciar sincronização. Verifique sua conexão e tente novamente.'
    console.error(error)
  } finally {
    carregando.value = false
  }
}
</script>

<template>
  <div class="flex flex-col items-start gap-2">
    <button 
      @click="sincronizar" 
      :disabled="carregando"
      class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
    >
      <span v-if="carregando" class="flex items-center">
        <svg class="animate-spin h-3 w-3 mr-1 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Processando...
      </span>
      <span v-else class="flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        Sincronizar Deputados
      </span>
    </button>

    <div 
      v-if="mensagem" 
      class="px-3 py-1.5 rounded-md text-xs"
      :class="{'bg-green-50 text-green-700': !mensagem.includes('Erro'), 'bg-red-50 text-red-700': mensagem.includes('Erro')}"
    >
      {{ mensagem }}
    </div>
  </div>
</template>

<style scoped>
/* No custom CSS needed. Use Tailwind utility classes directly in template elements. */
</style>