<template>
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="modal-header">
        <h5>Assign Action Plans to Leads</h5>
        <button class="btn-close" @click="$emit('close')"></button>
      </div>

      <div class="modal-body">
        <!-- Display Selected Action Plans (from parent) -->
        <div v-if="selectedActionPlanIds.length > 0" class="mb-3">
          <label class="form-label mb-2">Selected Action Plan(s) to Assign:</label>
          <div class="selected-action-plans-display">
            <span v-for="planId in selectedActionPlanIds" :key="planId" class="badge bg-primary me-1">
              {{ getActionPlanName(planId) }}
            </span>
          </div>
        </div>
        <div v-else class="alert alert-warning mb-3">
          No action plans selected from the table. Please select action plans first.
        </div>

        <label class="form-label mb-2">Select Leads:</label>
        <multiselect
          v-model="selectedLeadsLocal"
          :options="allLeads"
          :multiple="true"
          :close-on-select="false"
          :clear-on-select="false"
          :preserve-search="true"
          placeholder="Search leads"
          label="full_name"
          track-by="id"
          class="lead-multiselect"
        >
          <template #option="{ option, isSelected }">
            <div class="d-flex justify-content-between align-items-center">
              <span>{{ option.full_name }}</span>
              <span v-if="isSelected" class="text-success fw-bold">✔</span>
            </div>
          </template>
          <template #tag="{ option, remove }">
            <div class="selected-lead-tag">
              {{ option.full_name }}
              <span @click.stop="remove(option)">×</span>
            </div>
          </template>
        </multiselect>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" @click="$emit('close')">Cancel</button>
        <button
          class="btn btn-primary"
          @click="assignActionPlansToLeads"
          :disabled="selectedLeadsLocal.length === 0 || selectedActionPlanIds.length === 0 || loading"
        >
          <span v-if="loading" class="spinner-border spinner-border-sm me-1"></span>
          Assign
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios';
import { onMounted, ref, watch } from 'vue'; // Import watch
import Multiselect from 'vue-multiselect';

const props = defineProps({
  selectedActionPlanIds: {
    type: Array,
    required: true,
  },
});

const emit = defineEmits(['close']);

const selectedLeadsLocal = ref([]);
const allLeads = ref([]);
const loading = ref(false);

// To display names of selected action plans
const availableActionPlans = ref([]); // All action plans for name lookup

// Utility to show toast messages (assuming Swal is available globally)
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
  } else {
    console.warn('Swal is not defined. Toast message not shown:', title);
  }
};

const fetchLeads = async () => {
  try {
    const token = localStorage.getItem('auth_token');
    const { data } = await axios.get('/api/leads', {
      headers: { Authorization: `Bearer ${token}` },
    });

    allLeads.value = data.map((lead) => ({
      id: lead.id,
      full_name: `${lead.first_name} ${lead.last_name || ''}`.trim(), // Handle missing last_name
    }));
  } catch (err) {
    console.error('Error fetching leads:', err);
    showToastMessage('Failed to load leads.', 'error');
  }
};

// Fetch all action plans to get their names for display
const fetchAllActionPlansForNames = async () => {
  try {
    const token = localStorage.getItem('auth_token');
    const response = await axios.get('/api/automation/action-plans', {
      headers: { Authorization: `Bearer ${token}` },
    });
    availableActionPlans.value = response.data.data ?? response.data;
  } catch (error) {
    console.error('Failed to load all action plans for name lookup:', error);
  }
};

// Helper to get action plan name from its ID
const getActionPlanName = (planId) => {
  const plan = availableActionPlans.value.find((p) => p.id === planId);
  return plan ? plan.name : `Plan ID: ${planId}`;
};

const assignActionPlansToLeads = async () => {
  loading.value = true;
  try {
    const token = localStorage.getItem('auth_token');
    await axios.post(
      '/api/assign-action-plans', // New API endpoint
      {
        action_plan_ids: props.selectedActionPlanIds, // IDs of plans selected in table
        lead_ids: selectedLeadsLocal.value.map((lead) => lead.id), // IDs of leads selected in modal
      },
      {
        headers: { Authorization: `Bearer ${token}` },
      }
    );

    showToastMessage('Action Plans assigned successfully!');
    emit('close');
  } catch (err) {
    console.error('Assignment error:', err);
    const message = err.response?.data?.message || 'Failed to assign action plans.';
    showToastMessage(message, 'error');
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchLeads();
  fetchAllActionPlansForNames(); // Fetch action plans for name lookup
});

// Watch the prop to ensure initial setup if data arrives later
watch(() => props.selectedActionPlanIds, (newIds) => {
  // You might want to do something here if the selected IDs change while modal is open
  // For this use case, it's primarily for initial display.
}, { immediate: true });
</script>

<style scoped>
@import "vue-multiselect/dist/vue-multiselect.min.css";

.modal-overlay {
  position: fixed;
  z-index: 1050;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 40%);
  inset: 0;
}

.modal-content {
  padding: 20px;
  border-radius: 8px;
  background: #fff;
  inline-size: 500px;
  max-inline-size: 90%;
}

.modal-header,
.modal-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-block-end: 1px solid #eee;
  margin-block-end: 15px;
  padding-block-end: 15px;
}

.modal-footer {
  justify-content: flex-end;
  border-block-start: 1px solid #eee;
  gap: 10px;
  margin-block-start: 15px;
  padding-block-start: 15px;
}

.btn-close {
  border: none;
  background: none;
  cursor: pointer;
  font-size: 1.2rem;
}

.selected-lead-tag {
  display: inline-flex;
  align-items: center;
  border-radius: 16px;
  margin: 3px;
  background: #eee;
  font-size: 14px;
  gap: 6px;
  padding-block: 5px;
  padding-inline: 10px;
}

.selected-lead-tag span {
  cursor: pointer;
  font-weight: bold;
}

.badge {
  border-radius: 0.375rem;
  font-size: 0.875em;
  padding-block: 0.5em;
  padding-inline: 0.75em;
}

.bg-primary {
  background-color: #007bff !important;
  color: white;
}

.alert {
  padding: 1rem;
  border: 1px solid transparent;
  border-radius: 0.25rem;
  margin-block-end: 1rem;
}

.alert-warning {
  border-color: #ffeeba;
  background-color: #fff3cd;
  color: #856404;
}
</style>
