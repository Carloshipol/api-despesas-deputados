<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="close">
    <div class="bg-white p-6 rounded-2xl shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">{{ title }}</h2>
        <button @click="close" class="text-gray-500 hover:text-red-600 text-2xl">&times;</button>
      </div>
      <slot />
    </div>
  </div>
</template>

<script setup>
const emit = defineEmits(['close'])
defineProps({ title: String })

function close() {
  emit('close')
}
</script>
