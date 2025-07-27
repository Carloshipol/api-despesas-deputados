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

let jaTentouBuscar = false

async function buscarDeputados() {
  const temFiltro = filtros.value.nome || filtros.value.sigla_partido || filtros.value.sigla_uf

  if (!temFiltro) {
    if (jaTentouBuscar) {
      toast.warning('Por favor, informe pelo menos um critério de busca (nome, partido ou UF).')
    }
    jaTentouBuscar = true
    return
  }

  try {
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
    toast.error('Erro ao buscar deputados. Tente novamente mais tarde.')
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
    toast.success('Despesas sincronizadas com sucesso!')
  } catch (e) {
    toast.error('Falha ao sincronizar despesas. Verifique sua conexão.')
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
  <div class="deputados-container">
    <header class="header">
      <h1 class="title">Deputados Federais</h1>
      <p class="subtitle">Consulte informações sobre deputados e suas despesas</p>
    </header>

    <div class="filtros-wrapper border border-gray-200 rounded-lg p-4 bg-white shadow-sm mb-8">
      <div class="filtros-content">
        <div class="filtro-group">
          <label for="nome" class="filtro-label">Nome</label>
          <input 
            id="nome"
            v-model="filtros.nome" 
            placeholder="Digite o nome"
            class="filtro-input"
          />
        </div>
        
        <div class="filtro-group">
          <label for="partido" class="filtro-label">Partido</label>
          <input 
            id="partido"
            v-model="filtros.sigla_partido" 
            placeholder="Sigla"
            class="filtro-input"
          />
        </div>
        
        <div class="filtro-group">
          <label for="uf" class="filtro-label">UF</label>
          <input 
            id="uf"
            v-model="filtros.sigla_uf" 
            placeholder="UF"
            maxlength="2"
            class="filtro-input uppercase"
          />
        </div>
        
        <button 
          @click="aplicarFiltro" 
          class="filtro-button self-end"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
          </svg>
          Buscar
        </button>
      </div>
    </div>

    <div v-if="carregado && deputados.length === 0" class="empty-state">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <h3 class="empty-title">Nenhum deputado encontrado</h3>
      <p class="empty-text">Tente ajustar os filtros de busca</p>
    </div>

    <div v-else class="deputados-grid">
      <div v-for="dep in deputados" :key="dep.id" class="deputado-card">
        <div class="deputado-header">
          <h2 class="deputado-nome">{{ dep.nome }}</h2>
          <div class="deputado-info">
            <span class="deputado-partido">{{ dep.sigla_partido }}</span>
            <span class="deputado-uf">{{ dep.sigla_uf }}</span>
          </div>
        </div>

        <div class="deputado-actions">
          <button 
            @click="verDespesas(dep.id)" 
            class="action-button primary"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            Ver Despesas
          </button>
          
          <button
            @click="sincronizarDespesas(dep.id)"
            :disabled="sincronizando === dep.id"
            class="action-button success"
          >
            <svg v-if="sincronizando !== dep.id" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <svg v-else class="animate-spin h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ sincronizando === dep.id ? 'Sincronizando...' : 'Sincronizar' }}
          </button>
        </div>
      </div>
    </div>

    <div v-if="deputados.length > 0" class="paginacao">
      <button 
        @click="pagina--; buscarDeputados()" 
        :disabled="pagina === 1" 
        class="paginacao-button"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
        Anterior
      </button>
      
      <div class="paginacao-info">
        Página {{ pagina }} de {{ totalPaginas }}
      </div>
      
      <button 
        @click="pagina++; buscarDeputados()" 
        :disabled="pagina === totalPaginas" 
        class="paginacao-button"
      >
        Próxima
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
        </svg>
      </button>
    </div>

    <Modal v-if="showModal" :title="'Despesas do Deputado'" @close="showModal = false">
      <DespesasDeputado :deputadoId="deputadoSelecionado" />
    </Modal>
  </div>
</template>

<style scoped>
.deputados-container {
  max-width: 80rem;
  margin-left: auto;
  margin-right: auto;
  padding-left: 1rem;
  padding-right: 1rem;
  padding-top: 2rem;
  padding-bottom: 2rem;
}

.header {
  margin-bottom: 2rem;
  text-align: center;
}

.title {
  font-size: 1.875rem;
  font-weight: 700;
  color: #1a202c;
}

.subtitle {
  margin-top: 0.5rem;
  font-size: 1.125rem;
  color: #4b5563;
}

.filtros-wrapper {
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  padding: 1rem;
  background-color: #fff;
  box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);
  margin-bottom: 2rem;
}

.filtros-content {
  display: flex;
  flex-wrap: wrap;
  align-items: flex-end;
  gap: 1rem;
}

.filtro-group {
  flex: 1;
  min-width: 150px;
}

.filtro-label {
  margin-bottom: 0.25rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
}

.filtro-input {
  width: 100%;
  padding: 0.75rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);
  outline: none;
}

.filtro-input:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 2px #6366f1;
}

.uppercase {
  text-transform: uppercase;
}

.filtro-button {
  height: 42px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.5rem 1rem;
  border: 1px solid transparent;
  font-size: 0.875rem;
  font-weight: 500;
  border-radius: 0.375rem;
  box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);
  color: #fff;
  background-color: #4f46e5;
  cursor: pointer;
}

.filtro-button:hover {
  background-color: #4338ca;
}

.filtro-button:focus {
  outline: none;
  box-shadow: 0 0 0 2px #6366f1;
}

.empty-state {
  text-align: center;
  padding-top: 3rem;
  padding-bottom: 3rem;
}

.empty-title {
  margin-top: 0.5rem;
  font-size: 1.125rem;
  font-weight: 500;
  color: #1a202c;
}

.empty-text {
  margin-top: 0.25rem;
  font-size: 0.875rem;
  color: #6b7280;
}

.deputados-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;
}

@media (min-width: 640px) {
  .deputados-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
@media (min-width: 1024px) {
  .deputados-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

.deputado-card {
  background-color: #fff;
  overflow: hidden;
  box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);
  border-radius: 0.5rem;
  display: flex;
  flex-direction: column;
  border: 1px solid #e5e7eb;
}

.deputado-header {
  padding: 1.25rem 1rem;
}

@media (min-width: 640px) {
  .deputado-header {
    padding: 1.5rem;
  }
}

.deputado-nome {
  font-size: 1.125rem;
  font-weight: 500;
  color: #1a202c;
}

.deputado-info {
  margin-top: 0.25rem;
  display: flex;
  gap: 0.5rem;
}

.deputado-partido {
  padding: 0.25rem 0.5rem;
  display: inline-flex;
  font-size: 0.75rem;
  line-height: 1.25rem;
  font-weight: 600;
  border-radius: 9999px;
  background-color: #dbeafe;
  color: #1e40af;
}

.deputado-uf {
  padding: 0.25rem 0.5rem;
  display: inline-flex;
  font-size: 0.75rem;
  line-height: 1.25rem;
  font-weight: 600;
  border-radius: 9999px;
  background-color: #d1fae5;
  color: #065f46;
}

.deputado-actions {
  padding: 1rem 1rem;
  display: flex;
  justify-content: space-between;
}

@media (min-width: 640px) {
  .deputado-actions {
    padding: 1rem 1.5rem;
  }
}

.action-button {
  display: inline-flex;
  align-items: center;
  padding: 0.25rem 0.75rem;
  border: 1px solid transparent;
  font-size: 0.75rem;
  font-weight: 500;
  border-radius: 0.25rem;
  box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);
  outline: none;
  cursor: pointer;
}

.action-button:focus {
  box-shadow: 0 0 0 2px #6366f1;
}

.action-button.primary {
  color: #4338ca;
  background-color: #e0e7ff;
}

.action-button.primary:hover {
  background-color: #c7d2fe;
}

.action-button.success {
  color: #047857;
  background-color: #d1fae5;
}

.action-button.success:hover {
  background-color: #a7f3d0;
}

.action-button.success:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.paginacao {
  margin-top: 2rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.paginacao-button {
  position: relative;
  display: inline-flex;
  align-items: center;
  padding: 0.5rem 1rem;
  border: 1px solid #d1d5db;
  font-size: 0.875rem;
  font-weight: 500;
  border-radius: 0.375rem;
  color: #374151;
  background-color: #fff;
  cursor: pointer;
}

.paginacao-button:hover {
  background-color: #f9fafb;
}

.paginacao-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.paginacao-info {
  font-size: 0.875rem;
  color: #374151;
}
</style>