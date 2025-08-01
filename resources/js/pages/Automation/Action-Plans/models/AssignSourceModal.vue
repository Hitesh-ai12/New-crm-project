<template>
  <div class="modal-overlay">
    <div class="modal-content shadow-lg">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">
          <transition name="fade" mode="out-in">
            <span v-if="!showAddSourceForm">🚩 Assign Source</span>
            <span v-else>➕ Add New Source</span>
          </transition>
        </h5>
        <button type="button" class="btn-close" @click="$emit('close')" aria-label="Close"></button>
      </div>

      <div class="modal-body p-4">
        <transition name="fade" mode="out-in">
          <div v-if="!showAddSourceForm" key="assign">
            <div class="input-group mb-3">
              <span class="input-group-text bg-light border-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                  <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                </svg>
              </span>
              <input
                v-model="searchQuery"
                type="text"
                class="form-control border-0 bg-light"
                placeholder="Search for sources..."
              />
            </div>

            <div class="source-list-container">
              <p v-if="!filteredSources.length" class="text-muted text-center mt-4">No sources found.</p>
              <div v-else class="list-group">
                <button
                  v-for="source in filteredSources"
                  :key="source.id"
                  type="button"
                  class="list-group-item list-group-item-action"
                  :class="{ 'active': source.id === selectedSource }"
                  @click="selectedSource = source.id"
                >
                  {{ source.name }}
                </button>
              </div>
            </div>

            <div class="d-grid mt-3">
              <button class="btn btn-light btn-sm" @click="showAddSourceForm = true">
                + Add New
              </button>
            </div>
          </div>

          <div v-else key="add">
            <div class="mb-3">
              <label for="sourceName" class="form-label fw-semibold">Source Name</label>
              <input
                v-model.trim="newSource"
                id="sourceName"
                type="text"
                class="form-control"
                placeholder="e.g., Social Media"
              />
            </div>
          </div>
        </transition>
      </div>

      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary" @click="handleCancel">
          {{ showAddSourceForm ? 'Back' : 'Cancel' }}
        </button>
        <transition name="fade" mode="out-in">
          <button
            v-if="!showAddSourceForm"
            key="apply"
            type="button"
            class="btn btn-primary"
            :disabled="!selectedSource"
            @click="assignSource"
          >
            Apply
          </button>
          <button
            v-else
            key="addSource"
            type="button"
            class="btn btn-success"
            :disabled="!newSource.trim()"
            @click="addSource"
          >
            Add Source
          </button>
        </transition>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios';
import Swal from 'sweetalert2'; // Import SweetAlert2
import { computed, onMounted, ref } from 'vue';

const emit = defineEmits(['close', 'source-assigned'])

// State
const sources = ref([])
const selectedSource = ref(null)
const searchQuery = ref('')
const newSource = ref('')
const showAddSourceForm = ref(false)

// Helper function to show toast messages
const showToastMessage = (title, icon = 'success') => {
  Swal.fire({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    icon: icon,
    title: title,
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer)
      toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
  });
}

// Fetch all sources on mount
onMounted(async () => {
  await fetchSources()
})

const fetchSources = async () => {
  try {
    const token = localStorage.getItem('auth_token')
    const res = await axios.get('/api/items?type=source', {
      headers: { Authorization: `Bearer ${token}` }
    })
    sources.value = res.data.sources || []
  } catch (err) {
    console.error('Failed to fetch sources:', err)
    showToastMessage('Failed to fetch sources.', 'error') // Use toast for errors
  }
}

const filteredSources = computed(() =>
  sources.value.filter(source =>
    source.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
)

// Add new source
const addSource = async () => {
  if (!newSource.value.trim()) return // Use .trim() for validation
  try {
    const token = localStorage.getItem('auth_token')
    const res = await axios.post(
      '/api/items',
      { name: newSource.value, type: 'source' },
      { headers: { Authorization: `Bearer ${token}` } }
    )
    const newItem = res.data.item // Get the new item from the response
    sources.value.push(newItem)
    selectedSource.value = newItem.id // Automatically select the new source
    newSource.value = ''
    showAddSourceForm.value = false
    showToastMessage('Source added successfully!')
  } catch (err) {
    console.error('Failed to add source:', err)
    showToastMessage('Failed to add source.', 'error')
  }
}

// Apply selected source
const assignSource = () => {
  emit('source-assigned', selectedSource.value)
  showToastMessage('Source assigned successfully!') // Add success toast
  emit('close')
}

// Cancel handler
const handleCancel = () => {
  if (showAddSourceForm.value) {
    newSource.value = ''
    showAddSourceForm.value = false
  } else {
    emit('close')
  }
}
</script>

<style scoped>
/* These styles are identical to the previous component and work perfectly here */
.modal-overlay {
  position: fixed;
  z-index: 1050;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 50%);
  inset: 0;
}

.modal-content {
  display: flex;
  flex-direction: column;
  border-radius: 0.5rem;
  background: white;
  inline-size: 450px;
  max-inline-size: 95%;
}

.source-list-container {
  max-block-size: 250px; /* Adjusted from 300px to match the stage component's list height visually */
  overflow-y: auto;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.modal-body > div > .d-grid {
  margin-inline: -5px;
}
</style>
