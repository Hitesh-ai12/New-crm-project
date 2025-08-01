<template>
  <div class="modal-overlay">
    <div class="modal-content shadow-lg">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">
          <transition name="fade" mode="out-in">
            <span v-if="!showAddStageForm">🚩 Assign Stage</span>
            <span v-else>➕ Add New Stage</span>
          </transition>
        </h5>
        <button type="button" class="btn-close" @click="$emit('close')" aria-label="Close"></button>
      </div>

      <div class="modal-body p-4">
        <transition name="fade" mode="out-in">
          <div v-if="!showAddStageForm" key="assign">
            <div class="input-group mb-3">
              <span class="input-group-text bg-light border-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>
              </span>
              <input v-model="searchQuery" type="text" class="form-control border-0 bg-light" placeholder="Search for stages...">
            </div>

            <div class="stage-list-container">
               <p v-if="!filteredStages.length" class="text-muted text-center mt-4">No stages found.</p>
               <div v-else class="list-group">
<button
  v-for="stage in filteredStages"
  :key="stage.id"
  type="button"
  class="list-group-item list-group-item-action"
  :class="{ 'active': selectedStages.includes(stage.id) }"
  @click="toggleStageSelection(stage.id)"
>
  {{ stage.name }}
</button>

               </div>
            </div>

            <div class="d-grid mt-3">
               <button class="btn btn-light btn-sm" @click="showAddStageForm = true">
                 + Add New
               </button>
            </div>
          </div>

          <div v-else key="add">
            <div class="mb-3">
              <label for="stageName" class="form-label fw-semibold">Stage Name</label>
              <input v-model.trim="newStage" id="stageName" type="text" class="form-control" placeholder="e.g., In Progress">
            </div>
          </div>
        </transition>
      </div>

      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary" @click="handleCancel">
          {{ showAddStageForm ? 'Back' : 'Cancel' }}
        </button>
        <transition name="fade" mode="out-in">
          <button
            v-if="!showAddStageForm"
            key="apply"
            type="button"
            class="btn btn-primary"
            :disabled="!selectedStage"
            @click="assignStage"
          >
            Apply
          </button>
          <button
            v-else
            key="addStage"
            type="button"
            class="btn btn-success"
            :disabled="!newStage"
            @click="addStage"
          >
            Add Stage
          </button>
        </transition>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import Swal from 'sweetalert2'
import { computed, onMounted, ref } from 'vue'

const emit = defineEmits(['close', 'stage-assigned'])

// State
const stages = ref([])
const selectedStage = ref(null)
const searchQuery = ref('')
const newStage = ref('')
const showAddStageForm = ref(false)
const selectedStages = ref([]) 


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

// Fetch all stages on mount
onMounted(async () => {
  await loadStages()
})

const loadStages = async () => {
  try {
    const token = localStorage.getItem('auth_token')
    const res = await axios.get('/api/items?type=stage', {
      headers: { Authorization: `Bearer ${token}` }
    })
    stages.value = res.data.stages || []
  } catch (err) {
    console.error('Failed to load stages:', err)
    showToastMessage('Failed to load stages.', 'error')
  }
}

const filteredStages = computed(() =>
  stages.value.filter(stage =>
    stage.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
)

const toggleStageSelection = (stageId) => {
  const index = selectedStages.value.indexOf(stageId)
  if (index > -1) {
    selectedStages.value.splice(index, 1) // Remove if already selected
  } else {
    selectedStages.value.push(stageId) // Add if not selected
  }
}

// Add new stage
const addStage = async () => {
  if (!newStage.value) return
  try {
    const token = localStorage.getItem('auth_token')
    const res = await axios.post(
      '/api/items',
      { name: newStage.value, type: 'stage' },
      { headers: { Authorization: `Bearer ${token}` } }
    )
    const newItem = res.data.item
    stages.value.push(newItem)
    // 4. Automatically select the new stage for better UX
    selectedStage.value = newItem.id 
    newStage.value = ''
    showAddStageForm.value = false
    showToastMessage('Stage added successfully!')
  } catch (err) {
    console.error('Failed to add stage:', err)
    showToastMessage('Failed to add stage.', 'error')
  }
}

// Apply selected stage
const assignStage = () => {
  emit('stage-assigned', selectedStage.value)
  showToastMessage('Stage assigned successfully!')
  emit('close')
}

// Cancel handler
const handleCancel = () => {
  if (showAddStageForm.value) {
    newStage.value = ''
    showAddStageForm.value = false
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

.stage-list-container {
  max-block-size: 250px;
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
