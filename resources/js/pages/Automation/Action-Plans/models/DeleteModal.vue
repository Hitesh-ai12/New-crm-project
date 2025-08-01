<template>
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="modal-header">
        <h5>Confirm Delete</h5>
        <button class="btn-close" @click="$emit('close')"></button>
      </div>

      <div class="modal-body">
        <p>Are you sure you want to delete <strong>{{ selected.length }}</strong> selected lead(s)? This action cannot be undone.</p>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" @click="$emit('close')">Cancel</button>
        <button class="btn btn-danger" @click="deleteLeads" :disabled="loading">
          <span v-if="loading" class="spinner-border spinner-border-sm me-1"></span>
          Delete
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { ref } from 'vue'

const props = defineProps({
  selected: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['close'])

const loading = ref(false)

const deleteLeads = async () => {
  try {
    loading.value = true
    const token = localStorage.getItem('auth_token')
    await axios.post('/api/delete-leads', {
      leads: props.selected
    }, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })
    alert('Leads deleted successfully.')
    emit('close')
  } catch (error) {
    console.error('Delete failed:', error)
    alert('Failed to delete leads.')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  z-index: 1050;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 40%);
  block-size: 100%;
  inline-size: 100%;
  inset-block-start: 0;
  inset-inline-start: 0;
}

.modal-content {
  padding: 20px;
  border-radius: 8px;
  background: #fff;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 30%);
  inline-size: 400px;
  max-inline-size: 90%;
}

.modal-header,
.modal-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.btn-close {
  border: none;
  background: none;
  cursor: pointer;
  font-size: 1.2rem;
}
</style>
