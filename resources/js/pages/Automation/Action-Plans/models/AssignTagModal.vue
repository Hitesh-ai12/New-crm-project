<template>
  <div class="modal-overlay">
    <div class="modal-content shadow-lg">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">
          <transition name="fade" mode="out-in">
            <span v-if="!showAddTagForm">🏷️ Assign Tags</span>
            <span v-else>➕ Add New Tag</span>
          </transition>
        </h5>
        <button type="button" class="btn-close" @click="$emit('close')" aria-label="Close"></button>
      </div>

      <div class="modal-body p-4">
        <transition name="fade" mode="out-in">
          <div v-if="!showAddTagForm" key="assign">
            <div class="input-group mb-3">
              <span class="input-group-text bg-light border-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>
              </span>
              <input v-model="searchQuery" type="text" class="form-control border-0 bg-light" placeholder="Search for tags...">
            </div>

            <div class="tag-list-container small">
              <p v-if="!filteredTags.length" class="text-muted text-center mt-4">No tags found.</p>
              <div v-else class="d-flex flex-wrap gap-2">
                <button
                  v-for="tag in filteredTags"
                  :key="tag.id"
                  @click="toggleTag(tag.id)"
                  class="btn btn-sm rounded-pill"
                  :class="selectedTags.includes(tag.id) ? 'btn-primary' : 'btn-outline-secondary'"
                >
                  {{ tag.name }}
                </button>
              </div>
            </div>

            <div class="d-grid mt-3">
                <button class="btn btn-light btn-sm" @click="showAddTagForm = true">
                  + Add New
                </button>
            </div>
          </div>

          <div v-else key="add">
            <div class="mb-3">
              <label for="tagName" class="form-label fw-semibold">Tag Name</label>
              <input v-model.trim="newTag" id="tagName" type="text" class="form-control" placeholder="e.g., Important">
            </div>
          </div>
        </transition>
      </div>

      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary" @click="handleCancel">
          {{ showAddTagForm ? 'Back' : 'Cancel' }}
        </button>
        <transition name="fade" mode="out-in">
          <button
            v-if="!showAddTagForm"
            key="apply"
            type="button"
            class="btn btn-primary"
            :disabled="!selectedTags.length"
            @click="assignTags"
          >
            Apply ({{ selectedTags.length }})
          </button>
          <button
            v-else
            key="addTag"
            type="button"
            class="btn btn-success"
            :disabled="!newTag"
            @click="addTag"
          >
            Add Tag
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

const emit = defineEmits(['close', 'tags-assigned']) // 'tags-assigned' event will be emitted

const tags = ref([])
const selectedTags = ref([])
const searchQuery = ref('')
const newTag = ref('')
const showAddTagForm = ref(false)

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

onMounted(async () => {
  await loadTags()
})

const loadTags = async () => {
  try {
    const token = localStorage.getItem('auth_token')
    const res = await axios.get('/api/items?type=tag', {
      headers: { Authorization: `Bearer ${token}` }
    })
    tags.value = res.data.tags || []
  } catch (err) {
    console.error('Failed to load tags:', err)
    showToastMessage('Failed to load tags.', 'error')
  }
}

const filteredTags = computed(() =>
  tags.value.filter(tag =>
    tag.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
)

const toggleTag = (tagId) => {
  const index = selectedTags.value.indexOf(tagId)
  if (index === -1) {
    selectedTags.value.push(tagId)
  } else {
    selectedTags.value.splice(index, 1)
  }
}

const assignTags = () => {
  // Emit the selected tag IDs to the parent (ActionPlans.vue)
  emit('tags-assigned', selectedTags.value)
  showToastMessage('Tags selected for assignment!', 'info') // Inform user that tags are selected, not yet assigned to plan
  emit('close') // Close the modal
}

const addTag = async () => {
  if (!newTag.value) return
  try {
    const token = localStorage.getItem('auth_token')
    const res = await axios.post(
      '/api/items',
      { name: newTag.value, type: 'tag' },
      { headers: { Authorization: `Bearer ${token}` } }
    )
    tags.value.push(res.data.item)
    selectedTags.value.push(res.data.item.id)
    newTag.value = ''
    showAddTagForm.value = false
    showToastMessage('Tag added successfully!', 'success')
  } catch (err) {
    console.error('Failed to add tag:', err)
    showToastMessage('Failed to add tag.', 'error')
  }
}

const handleCancel = () => {
  if (showAddTagForm.value) {
    showAddTagForm.value = false
    newTag.value = ''
  } else {
    emit('close')
  }
}
</script>


<style scoped>
/* Scoped styles remain the same as the previous professional version */
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

.tag-list-container {
  padding: 5px;
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
