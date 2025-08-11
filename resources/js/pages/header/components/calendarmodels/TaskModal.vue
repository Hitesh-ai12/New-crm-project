<template>
  <div class="task-modal-overlay">
    <div class="task-modal-content">
      <div class="task-modal-header">
        <h2>Add Task</h2>
        <button @click="$emit('close')">×</button>
      </div>
      
      <div class="task-modal-body">

        <div class="task-form-group">
          <label>Select Lead:</label>
          <div class="select-lead-wrapper">
            <div class="selected-lead-input" @click="toggleDropdown">
              {{ selectedLeadName || 'Select a lead' }}
            </div>

            <div v-if="isDropdownOpen" class="dropdown-lead-list" ref="dropdown" @scroll="onScroll">
              <input
                type="text"
                v-model="searchQuery"
                placeholder="Search lead..."
                class="dropdown-search"
                @input="onSearchInput"
              />

              <div
                v-for="lead in leads"
                :key="lead.id"
                class="dropdown-item"
                @click="selectLead(lead)"
              >
                {{ lead.first_name }} {{ lead.last_name }}
              </div>

              <div v-if="isLoading" class="loader-wrapper">
                <div class="loader"></div>
              </div>

              <div v-if="noMoreLeads" class="end-text">No more leads</div>
            </div>
          </div>
        </div>

        <div class="task-form-group">
          <label>Task Title:</label>
          <input type="text" v-model="taskData.title" placeholder="Enter task title" />
        </div>

        <div class="task-form-group">
          <label>Task Type:</label>
          <select v-model="taskData.type">
            <option value="Meeting">Meeting</option>
            <option value="Call">Call</option>
            <option value="Email">Email</option>
            <option value="Text">Text</option>
            <option value="Follow-up">Follow-up</option>
          </select>
        </div>

        <div class="task-form-group">
          <label>Due Date:</label>
          <input type="datetime-local" v-model="taskData.due_date" />
        </div>

        <div class="task-form-actions">
          <button @click="saveTask">Add Task</button>
        </div>
      </div>
    </div>
  </div>
</template>

  <script setup>
  import debounce from 'lodash.debounce';
import Swal from 'sweetalert2';
import { onMounted, ref } from 'vue';

  const emit = defineEmits(['close', 'save']);

  const taskData = ref({
    lead_id: '',
    title: '',
    type: 'Meeting',
    due_date: ''
  });

  const leads = ref([]);
  const searchQuery = ref('');
  const page = ref(1);
  const isLoading = ref(false);
  const noMoreLeads = ref(false);
  const dropdown = ref(null);
  const isDropdownOpen = ref(false);
  const selectedLeadName = ref('');

  const showToastMessage = (title, icon = 'success') => {
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        icon: icon,
        title: title,
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer);
          toast.addEventListener('mouseleave', Swal.resumeTimer);
        },
      });
    }
  };

  const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value;
  };

  const fetchLeads = async () => {
    if (isLoading.value || noMoreLeads.value) return;

    isLoading.value = true;
    try {
      const res = await fetch(`/api/leads?page=${page.value}&search=${searchQuery.value}`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('auth_token')}`
        }
      });
      const data = await res.json();

      if (data.data.length < 20) noMoreLeads.value = true;
      leads.value.push(...data.data);
      page.value += 1;
    } catch (err) {

    } finally {
      isLoading.value = false;
    }
  };

  const onScroll = () => {
    const el = dropdown.value;
    if (el.scrollTop + el.clientHeight >= el.scrollHeight - 10) {
      fetchLeads();
    }
  };

  const selectLead = (lead) => {
    taskData.value.lead_id = lead.id;
    selectedLeadName.value = `${lead.first_name} ${lead.last_name}`;
    isDropdownOpen.value = false;
  };

  const resetLeads = () => {
    leads.value = [];
    page.value = 1;
    noMoreLeads.value = false;
    fetchLeads();
  };

  const onSearchInput = debounce(() => {
    resetLeads();
  }, 300);

  const saveTask = () => {
    if (!taskData.value.lead_id || !taskData.value.title || !taskData.value.due_date) {
      // ❌ Old alert, will be replaced by showToastMessage
      showToastMessage('Please fill all required fields.', 'warning');
      return;
    }

    emit('save', taskData.value);
    emit('close');
  };

  onMounted(() => {
    fetchLeads();
  });
  </script>



<style scoped>
.select-lead-wrapper {
  position: relative;
}

.selected-lead-input {
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  background-color: white;
  cursor: pointer;
}

.dropdown-search {
  padding: 8px;
  border-block-end: 1px solid #ccc;
  inline-size: 100%;
}

.dropdown-lead-list {
  border: 1px solid #ccc;
  margin-block-start: 5px;
  max-block-size: 200px;
  overflow-y: auto;
}

.dropdown-item {
  padding: 8px;
  cursor: pointer;
}

.dropdown-item:hover {
  background-color: #f0f0f0;
}

.loader-wrapper {
  padding: 10px;
  text-align: center;
}

.loader {
  border: 4px solid #ccc;
  border-radius: 50%;
  margin: auto;
  animation: spin 1s linear infinite;
  block-size: 20px;
  border-block-start-color: #25b09b;
  inline-size: 20px;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.end-text {
  padding: 5px;
  color: #999;
  font-size: 12px;
  text-align: center;
}

.task-modal-overlay {
  position: fixed;
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 50%);
  inset: 0;
}

.task-modal-content {
  padding: 20px;
  border-radius: 8px;
  background: white;
  inline-size: 400px;
  max-inline-size: 90%;
}

.task-modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-block-end: 1px solid #ccc;
  margin-block-end: 10px;
}

.task-modal-header h2 {
  margin: 0;
  font-size: 1.25rem;
}

.task-modal-header button {
  border: none;
  background: none;
  color: #888;
  cursor: pointer;
  font-size: 1.5rem;
}

.task-modal-body {
  display: flex;
  flex-direction: column;
}

.task-form-group {
  margin-block-end: 15px;
}

.task-form-group label {
  display: block;
  font-weight: 600;
  margin-block-end: 5px;
}

.task-form-group input,
.task-form-group select,
.task-form-group textarea {
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 0.95rem;
  inline-size: 100%;
}

.task-form-actions {
  text-align: end;
}

.task-form-actions button {
  border: none;
  border-radius: 4px;
  background-color: #8c57ff;
  color: white;
  cursor: pointer;
  font-size: 0.95rem;
  padding-block: 10px;
  padding-inline: 20px;
}

.task-form-actions button:hover {
  background-color: #6a43d6;
}
</style>
