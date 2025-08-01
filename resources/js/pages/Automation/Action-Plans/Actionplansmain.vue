<template>
  <div>
    <!-- Action Bar -->
    <div class="action-bar d-flex justify-content-between align-items-center gap-2 my-3 flex-wrap">
      <!-- Left: Action Bar Heading -->
      <div class="fw-bold fs-5 text-dark">Action Bar</div>

      <!-- Right: Action Icons -->
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <button class="btn btn-success" @click="openAddModal" title="Add New Action Plan">
          <i class="fas fa-plus"></i> Add Action Plan
        </button>

        <button
          class="btn btn-warning"
          :disabled="isDisabled"
          @click="showAssignUserModal = true"
          title="Assign to Lead"
        >
          <i class="fas fa-user"></i> Assign User
        </button>

        <button
          class="btn btn-warning"
          :disabled="isDisabled"
          @click="showAssignTagModal = true"
          title="Assign Tag"
        >
          <i class="fas fa-tags"></i> Assign Tag
        </button>

        <button
          class="btn btn-warning"
          :disabled="isDisabled"
          @click="showAssignStageModal = true"
          title="Assign Stage"
        >
          <i class="fas fa-calendar-alt"></i> Assign Stage
        </button>

        <button
          class="btn btn-warning"
          :disabled="isDisabled"
          @click="showAssignSourceModal = true"
          title="Assign Source"
        >
          <i class="fas fa-globe"></i> Assign Source
        </button>

        <!-- New Button to open Assignments Modal -->
        <button
          class="btn btn-info"
          @click="showAssignmentsModal = true"
          title="View Assigned Action Plans"
        >
          <i class="fas fa-list-alt"></i> View Assignments
        </button>

        <!-- Delete button removed as per request -->
      </div>
    </div>

    <!-- Data Table -->
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th><input type="checkbox" @change="toggleAllCheckboxes" :checked="allSelected" /></th>
            <th>Name</th>
            <th>Steps</th>
            <th>Active</th>
            <th>Paused</th>
            <th>Stopped</th>
            <th>Completed</th>
            <th>Created By</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="9" class="text-center py-4">Loading action plans...</td>
          </tr>
          <tr v-else-if="actionPlans.length === 0">
            <td colspan="9" class="text-center py-4">No action plans found.</td>
          </tr>
          <tr v-else v-for="item in actionPlans" :key="item.id">
            <td>
              <input
                type="checkbox"
                :value="item.id"
                v-model="selectedActionPlanIds"
              />
            </td>
            <td>{{ item.name }}</td>
            <td>{{ item.step_count }}</td>
            <td>{{ item.tracking_stats?.active || 0 }}</td>
            <td>{{ item.tracking_stats?.paused || 0 }}</td>
            <td>{{ item.tracking_stats?.stopped || 0 }}</td>
            <td>{{ item.tracking_stats?.completed || 0 }}</td>
            <td>{{ item.creator?.name || 'N/A' }}</td>
            <td>
              <button class="btn btn-sm btn-info" @click="openEditModal(item)" title="Edit Action Plan">
                <i class="fas fa-edit"></i> Edit
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modals -->
    <ActionPlanFormModal
      v-if="showFormModal"
      :initialData="selectedActionPlanForForm"
      @close="closeFormModal"
      @submit="handleFormSubmit"
    />

    <AssignUserModal
      v-if="showAssignUserModal"
      @close="showAssignUserModal = false"
      :selectedActionPlanIds="selectedActionPlanIds"
    />
    <AssignTagModal v-if="showAssignTagModal" @close="showAssignTagModal = false" :selected="selectedActionPlanIds" />
    <AssignStageModal v-if="showAssignStageModal" @close="showAssignStageModal = false" :selected="selectedActionPlanIds" />
    <AssignSourceModal v-if="showAssignSourceModal" @close="showAssignSourceModal = false" :selected="selectedActionPlanIds" />
    
    <!-- New Assigned Action Plans List Modal -->
    <LeadActionPlanAssignmentsModal
      v-if="showAssignmentsModal"
      @close="showAssignmentsModal = false"
    />

  </div>
</template>

<script setup>
import axios from 'axios';
import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import { computed, onMounted, ref } from 'vue';

const token = localStorage.getItem('auth_token');
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

const actionPlans = ref([]);
const loading = ref(true);

const selectedActionPlanIds = ref([]);
const allSelected = computed(() => selectedActionPlanIds.value.length === actionPlans.value.length && actionPlans.value.length > 0);
const toggleAllCheckboxes = () => {
  selectedActionPlanIds.value = allSelected.value ? [] : actionPlans.value.map(item => item.id);
};

const showFormModal = ref(false);
const selectedActionPlanForForm = ref(null);

const showAssignUserModal = ref(false);
const showAssignTagModal = ref(false);
const showAssignStageModal = ref(false);
const showAssignSourceModal = ref(false);

const showAssignmentsModal = ref(false); // New state for the assignments modal

const isDisabled = computed(() => selectedActionPlanIds.value.length === 0);

import ActionPlanFormModal from './models/ActionPlanFormModal.vue';
import AssignSourceModal from './models/AssignSourceModal.vue';
import AssignStageModal from './models/AssignStageModal.vue';
import AssignTagModal from './models/AssignTagModal.vue';
import AssignUserModal from './models/AssignUserModal.vue';
import LeadActionPlanAssignmentsModal from './models/LeadActionPlanAssignmentsModal.vue'; // New import

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

const fetchActionPlans = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/automation/action-plans?include=actions,creator');
    actionPlans.value = response.data.data || [];
  } catch (error) {
    console.error('Failed to load action plans:', error);
    showToastMessage('Failed to load action plans.', 'error');
    actionPlans.value = [];
  } finally {
    loading.value = false;
  }
};

const openAddModal = () => {
  selectedActionPlanForForm.value = null;
  showFormModal.value = true;
};

const openEditModal = (item) => {
  axios.get(`/api/automation/action-plans/${item.id}?include=actions`)
    .then(response => {
      selectedActionPlanForForm.value = response.data.data;
      showFormModal.value = true;
    })
    .catch(error => {
      console.error('Failed to fetch action plan for edit:', error);
      showToastMessage('Failed to load action plan details for editing.', 'error');
    });
};

const handleFormSubmit = async (formData) => {
  try {
    if (formData.id) {
      await axios.put(`/api/automation/action-plans/${formData.id}`, formData);
      showToastMessage('Action Plan updated successfully!');
    } else {
      await axios.post('/api/automation/action-plan', formData);
      showToastMessage('Action Plan added successfully!');
    }
    closeFormModal();
    await fetchActionPlans();
  } catch (error) {
    console.error('Failed to save action plan:', error);
    const message = error.response?.data?.message || 'Something went wrong.';
    showToastMessage(message, 'error');
  }
};

const closeFormModal = () => {
  showFormModal.value = false;
  selectedActionPlanForForm.value = null;
};

onMounted(() => {
  fetchActionPlans();
});
</script>

<style scoped>
/* Your existing CSS styles go here. */
.table th,
.table td {
  text-align: center;
  vertical-align: middle;
}

/* Add custom styles for new button */
.btn-info {
  border: none;
  background-color: #17a2b8;
  color: white;
}

.btn-info:hover {
  background-color: #138496;
}
</style>
