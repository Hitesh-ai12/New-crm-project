<template>
  <div class="appointment-modal-overlay">
    <div class="appointment-modal-content">
      <div class="appointment-modal-header">
        <h2>Create Appointment</h2>
        <button @click="$emit('close')">×</button>
      </div>

      <div class="appointment-modal-body">
        <div class="appointment-form-group">
          <label>Lead Name:</label>
          <div class="select-lead-wrapper">
            <div class="selected-lead-input" @click="toggleDropdown">
              {{ selectedLeadName || 'Select Lead' }}
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

        <div class="appointment-form-group">
          <label for="appointment-title">Title:</label>
          <input
            type="text"
            id="appointment-title"
            v-model="appointmentData.title"
            placeholder="Enter title"
          />
        </div>

        <div class="appointment-form-group">
          <label for="appointment-description">Description:</label>
          <textarea
            id="appointment-description"
            v-model="appointmentData.description"
            placeholder="Enter description"
          ></textarea>
        </div>

        <div class="appointment-form-group">
          <label for="appointment-location">Location:</label>
          <input
            type="text"
            id="appointment-location"
            v-model="appointmentData.location"
            placeholder="Enter location"
          />
        </div>

        <div class="appointment-form-group">
          <label for="appointment-date">Date:</label>
          <input type="datetime-local" id="appointment-date" v-model="appointmentData.date" />
        </div>

        <div class="appointment-form-actions">
          <button @click="handleFormSubmit">Create Appointment</button>
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

const appointmentData = ref({
  lead: '',
  title: '',
  description: '',
  location: '',
  date: ''
});

const leads = ref([]);
const page = ref(1);
const isLoading = ref(false);
const noMoreLeads = ref(false);
const searchQuery = ref('');
const dropdown = ref(null);
const isDropdownOpen = ref(false);
const selectedLeadName = ref('');

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
    console.error('Error fetching leads', err);
  } finally {
    isLoading.value = false;
  }
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

const onScroll = () => {
  const el = dropdown.value;
  if (el.scrollTop + el.clientHeight >= el.scrollHeight - 10) {
    fetchLeads();
  }
};

const selectLead = (lead) => {
  appointmentData.value.lead = lead.id;
  selectedLeadName.value = `${lead.first_name} ${lead.last_name}`;
  isDropdownOpen.value = false;
};

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

const handleFormSubmit = async () => {
  try {
    if (!appointmentData.value.lead || !appointmentData.value.title || !appointmentData.value.date) {
      showToastMessage('Please fill all required fields.', 'warning');
      return;
    }

    const response = await fetch('/api/appointments', {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
        'Content-Type': 'application/json',
        Accept: 'application/json'
      },
      body: JSON.stringify(appointmentData.value)
    });

    const result = await response.json();

    if (response.ok && result?.appointment) {
      emit('save', {
        id: result.appointment.id,
        title: result.appointment.title,
        type: 'Appointment',
        due_date: result.appointment.date,
        lead_id: result.appointment.lead_id,
        lead_name: result.appointment.lead_name
      });
      showToastMessage('Appointment created successfully!', 'success');
      emit('close');
    } else {
      console.error('❌ Appointment creation failed:', result);
      showToastMessage('Failed to create appointment.', 'error');
    }
  } catch (error) {
    console.error('❌ Error creating appointment:', error);
    showToastMessage('Error creating appointment.', 'error');
  }
};

onMounted(() => {
  fetchLeads();
});
</script>

<style scoped>
.appointment-modal-overlay {
  position: fixed;
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 50%);
  inset: 0;
}

.appointment-modal-content {
  padding: 20px;
  border-radius: 8px;
  background: white;
  inline-size: 400px;
  max-inline-size: 90%;
}

.appointment-modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-block-end: 1px solid #ccc;
  margin-block-end: 10px;
}

.appointment-modal-body {
  display: flex;
  flex-direction: column;
}

.appointment-form-group {
  margin-block-end: 15px;
}

.appointment-form-group label {
  display: block;
  font-weight: 600;
  margin-block-end: 5px;
}

.appointment-form-group input,
.appointment-form-group select,
.appointment-form-group textarea {
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 0.95rem;
  inline-size: 100%;
}

.appointment-form-actions {
  text-align: end;
}

.appointment-form-actions button {
  border: none;
  border-radius: 4px;
  background: #8c57ff;
  color: white;
  cursor: pointer;
  font-size: 0.95rem;
  padding-block: 10px;
  padding-inline: 20px;
}

.appointment-form-actions button:hover {
  background: #6a43d6;
}

/* Lead Dropdown Styles */
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

.dropdown-lead-list {
  position: absolute;
  z-index: 10;
  border: 1px solid #ccc;
  background: white;
  inline-size: 100%;
  margin-block-start: 4px;
  max-block-size: 200px;
  overflow-y: auto;
}

.dropdown-search {
  box-sizing: border-box;
  padding: 8px;
  border-block-end: 1px solid #ccc;
  inline-size: 100%;
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
</style>
