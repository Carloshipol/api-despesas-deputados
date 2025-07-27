<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'
import { useToast } from 'vue-toastification'
import Modal from './BaseModal.vue'
import DespesasDeputado from './DespesasDeputado.vue'

const toast = useToast()

const deputados = ref([])
const filtros = ref({ nome: '', sigla_partido: '', sigla_uf: '' })
const pagina = ref(1)
const totalPaginas = ref(1)
const carregado = ref(false)

const deputadoSelecionado = ref(null)
const showModal = ref(false)
const sincronizando = ref(null)

async function buscarDeputados() {
  try {
    const temFiltro = filtros.value.nome || filtros.value.sigla_partido || filtros.value.sigla_uf
    if (!temFiltro) {
      toast.warning('Informe pelo menos um filtro para buscar.')
      return
    }

    const params = {
      pagina: pagina.value,
      nome: filtros.value.nome || undefined,
      sigla_partido: filtros.value.sigla_partido || undefined,
      sigla_uf: filtros.value.sigla_uf || undefined,
    }

    const response = await api.get('/deputados', { params })
    deputados.value = response.data.data
    totalPaginas.value = response.data.last_page
    carregado.value = true
  } catch (error) {
    toast.error('Erro ao buscar deputados.')
    console.error(error)
  }
}



function verDespesas(id) {
  deputadoSelecionado.value = id
  showModal.value = true
}

async function sincronizarDespesas(id) {
  sincronizando.value = id
  try {
    await api.post(`/deputados/${id}/sincronizar-despesas`)
    toast.success('Despesas sincronizadas com sucesso.')
  } catch (e) {
    toast.error('Erro ao sincronizar despesas.')
    console.error(e)
  } finally {
    sincronizando.value = null
  }
}

function aplicarFiltro() {
  pagina.value = 1
  buscarDeputados()
}

onMounted(() => buscarDeputados())
</script>

<template>
  <div class="space-y-6 p-6 max-w-6xl mx-auto">
    <h1 class="text-2xl font-bold">📋 Deputados</h1>

    <div class="flex flex-wrap gap-3">
      <input v-model="filtros.nome" placeholder="Nome" class="border p-2 rounded w-full sm:w-auto" />
      <input v-model="filtros.sigla_partido" placeholder="Partido" class="border p-2 rounded w-full sm:w-auto" />
      <input v-model="filtros.sigla_uf" placeholder="UF" maxlength="2" class="border p-2 rounded w-full sm:w-auto" />
      <button @click="aplicarFiltro" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Filtrar</button>
    </div>

    <div v-if="carregado && deputados.length === 0" class="text-gray-500 mt-4">Nenhum deputado encontrado.</div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="dep in deputados" :key="dep.id" class="border p-4 rounded-xl shadow bg-white flex flex-col justify-between">
        <div>
          <h2 class="font-semibold text-lg">{{ dep.nome }}</h2>
          <p class="text-sm text-gray-500">{{ dep.sigla_partido }} - {{ dep.sigla_uf }}</p>
        </div>

        <div class="mt-4 space-x-3">
          <button @click="verDespesas(dep.id)" class="text-blue-600 hover:underline">Ver Despesas</button>
          <button
            @click="sincronizarDespesas(dep.id)"
            :disabled="sincronizando === dep.id"
            class="text-sm bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 disabled:opacity-50"
          >
            {{ sincronizando === dep.id ? 'Sincronizando...' : 'Sincronizar Despesas' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Paginação -->
    <div class="flex justify-between items-center mt-6" v-if="deputados.length > 0">
      <button @click="pagina--; buscarDeputados()" :disabled="pagina === 1" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50">
        ⬅ Anterior
      </button>
      <span>Página {{ pagina }} de {{ totalPaginas }}</span>
      <button @click="pagina++; buscarDeputados()" :disabled="pagina === totalPaginas" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50">
        Próxima ➡
      </button>
    </div>

    <!-- Modal -->
    <Modal v-if="showModal" :title="'Despesas do Deputado'" @close="showModal = false">
      <DespesasDeputado :deputadoId="deputadoSelecionado" />
    </Modal>
  </div>
</template>
