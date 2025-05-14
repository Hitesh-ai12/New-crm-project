<template>
  <div class="p-6 bg-gray-50 min-h-screen">
    <h1 class="text-2xl font-bold mb-4">Page Title</h1>

    <!-- Example content -->
    <div class="bg-white shadow-md rounded p-4">
      <p class="text-gray-700">This is a basic Vue.js page template.</p>
      <button @click="handleClick" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
        Click Me
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const message = ref('Hello from Vue!')

function handleClick() {
  alert(message.value)
}
</script>

<style scoped>
/* Add custom styles here if not using Tailwind */
</style>
