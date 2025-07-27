<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({ deputadoId: Number })

const despesas = ref([])
const loading = ref(false)
const erro = ref(false)

onMounted(async () => {
  loading.value = true
  erro.value = false
  try {
    const { data } = await axios.get(`http://localhost:8000/api/deputados/${props.deputadoId}/despesas`)
    despesas.value = data?.data ?? data
  } catch (error) {
    console.error('Erro ao carregar despesas:', error)
    erro.value = true
  } finally {
    loading.value = false
  }
})

const formatarValor = (valor) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
    minimumFractionDigits: 2,
  }).format(Number(valor));
}
</script>

<template>
  <div class="despesas-container">
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <span>Carregando despesas...</span>
    </div>
    
    <div v-else-if="erro" class="error-state">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
      </svg>
      <span>Erro ao carregar despesas. Tente novamente mais tarde.</span>
    </div>
    
    <div v-else-if="despesas.length === 0" class="empty-state">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd" />
      </svg>
      <span>Nenhuma despesa encontrada para este deputado.</span>
    </div>
    
    <div v-else class="table-container">
      <table class="despesas-table">
        <thead>
          <tr>
            <th>Ano</th>
            <th>Mês</th>
            <th>Fornecedor</th>
            <th>Valor</th>
            <th>Tipo</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(d, i) in despesas" :key="i">
            <td>{{ d.ano }}</td>
            <td>{{ d.mes }}</td>
            <td class="fornecedor-cell">{{ d.fornecedor }}</td>
            <td :class="{'valor-cell': true, 'valor-alto': Number(d.valor_documento) > 10000}">
              {{ formatarValor(d.valor_documento) }}
            </td>
            <td>{{ d.tipo_despesa }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<style scoped>
.despesas-container {
  width: 100%;
}

.loading-state,
.error-state,
.empty-state {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 1rem;
  border-radius: 0.5rem;
}

.loading-state {
  background-color: #eff6ff;
  color: #1e40af;
}

.error-state {
  background-color: #fee2e2;
  color: #b91c1c;
}

.empty-state {
  background-color: #f9fafb;
  color: #4b5563;
}

.spinner {
  animation: spin 1s linear infinite;
  height: 1.25rem;
  width: 1.25rem;
  border: 2px solid #3b82f6;
  border-top-color: transparent;
  border-radius: 9999px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.table-container {
  overflow-x: auto;
  box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);
  border-radius: 0.5rem;
}

.despesas-table {
  min-width: 100%;
  border-collapse: collapse;
}

.despesas-table thead {
  background-color: #f9fafb;
}

.despesas-table th {
  padding: 0.75rem 1.5rem;
  text-align: left;
  font-size: 0.75rem;
  font-weight: 500;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.despesas-table tbody {
  background-color: #fff;
}

.despesas-table td {
  padding: 1rem 1.5rem;
  white-space: nowrap;
  font-size: 0.875rem;
}

.fornecedor-cell {
  max-width: 20rem;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.valor-cell {
  font-weight: 500;
}

.valor-alto {
  color: #dc2626;
}

.despesas-table tr:hover td {
  background-color: #f9fafb;
}
</style>