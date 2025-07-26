<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'

const deputados = ref([])
const pagina = ref(1)
const totalPaginas = ref(1)
const nomeFiltro = ref('')
const siglaPartidoFiltro = ref('')
const siglaUfFiltro = ref('')

async function buscarDeputados() {
  try {
    const params = {
      pagina: pagina.value,
      nome: nomeFiltro.value || undefined,
      sigla_partido: siglaPartidoFiltro.value || undefined,
      sigla_uf: siglaUfFiltro.value || undefined,
    }

    const response = await api.get('/deputados', { params })
    deputados.value = response.data.data
    totalPaginas.value = response.data.last_page
  } catch (error) {
    alert('Erro ao buscar deputados')
    console.error(error)
  }
}

onMounted(() => buscarDeputados())

function aplicarFiltro() {
  pagina.value = 1
  buscarDeputados()
}
</script>

<template>
  <div>
    <h1>Deputados</h1>

    <form @submit.prevent="aplicarFiltro">
      <input v-model="nomeFiltro" placeholder="Nome" />
      <input v-model="siglaPartidoFiltro" placeholder="Partido" />
      <input v-model="siglaUfFiltro" placeholder="UF" maxlength="2" />
      <button type="submit">Buscar</button>
    </form>

    <ul>
      <li v-for="dep in deputados" :key="dep.id">
        {{ dep.nome }} - {{ dep.sigla_partido }} / {{ dep.sigla_uf }}
      </li>
    </ul>

    <div style="margin-top: 10px;">
      <button :disabled="pagina === 1" @click="pagina--; buscarDeputados()">Anterior</button>
      <button :disabled="pagina === totalPaginas" @click="pagina++; buscarDeputados()">Próximo</button>
    </div>
  </div>
</template>
