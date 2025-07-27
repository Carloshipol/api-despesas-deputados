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
  <div>
    <div v-if="loading" class="text-gray-600 text-sm">🔄 Carregando despesas...</div>
    <div v-else-if="erro" class="text-red-600">❌ Erro ao carregar despesas.</div>
    <div v-else-if="despesas.length === 0" class="text-gray-600">ℹ️ Nenhuma despesa encontrada.</div>
    
    <table v-else class="min-w-full border text-sm mt-4">
      <thead class="bg-gray-100 text-left">
        <tr>
          <th class="border p-2">Ano</th>
          <th class="border p-2">Mês</th>
          <th class="border p-2">Fornecedor</th>
          <th class="border p-2">Valor</th>
          <th class="border p-2">Tipo</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(d, i) in despesas" :key="i" class="hover:bg-gray-50">
          <td class="border p-2">{{ d.ano }}</td>
          <td class="border p-2">{{ d.mes }}</td>
          <td class="border p-2">{{ d.fornecedor }}</td>
          <td class="border p-2">{{ formatarValor(d.valor_documento) }}</td>
          <td class="border p-2">{{ d.tipo_despesa }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
