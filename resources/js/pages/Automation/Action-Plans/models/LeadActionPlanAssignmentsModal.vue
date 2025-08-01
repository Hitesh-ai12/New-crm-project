<template>
  <div class="modal-overlay-custom">
    <div class="modal-content-custom-wide">
      <div class="modal-header-custom">
        <h5 class="modal-title-custom">Assigned Action Plans to Leads</h5>
        <button type="button" class="close-button-custom" @click="$emit('close')">
          &times;
        </button>
      </div>
      <div class="modal-body-custom">
        <div class="table-responsive">
          <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>Lead Name</th>
                <th>Action Plan</th>
                <th>Status</th>
                <th>Current Action</th>
                <th>Next Due Date</th>
                <th>Assigned At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading">
                <td colspan="7" class="text-center py-4">Loading assignments...</td>
              </tr>
              <tr v-else-if="assignments.length === 0">
                <td colspan="7" class="text-center py-4">No action plans assigned to leads yet.</td>
              </tr>
              <tr v-else v-for="assignment in assignments" :key="assignment.id">
                <td>{{ assignment.lead?.first_name }} {{ assignment.lead?.last_name }}</td>
                <td>{{ assignment.action_plan?.name }}</td>
                <td>
                  <span :class="getStatusBadgeClass(assignment.status)">
                    {{ assignment.status }}
                  </span>
                </td>
                <td>
                  {{ assignment.current_action?.type || 'N/A' }}
                  <span v-if="assignment.current_action?.task_name"> ({{ assignment.current_action.task_name }})</span>
                  <span v-else-if="assignment.current_action?.note_content"> (Note)</span>
                </td>
                <td>{{ formatDateTime(assignment.next_action_due_at) }}</td>
                <td>{{ formatDateTime(assignment.assigned_at) }}</td>
                <td>
                  <button
                    v-if="assignment.status === 'active'"
                    class="btn btn-sm btn-warning"
                    @click="toggleAssignmentStatus(assignment, 'paused')"
                    :disabled="assignment.isUpdatingStatus"
                  >
                    <span v-if="assignment.isUpdatingStatus" class="spinner-border spinner-border-sm me-1"></span>
                    Pause
                  </button>
                  <button
                    v-else-if="assignment.status === 'paused'"
                    class="btn btn-sm btn-success"
                    @click="toggleAssignmentStatus(assignment, 'active')"
                    :disabled="assignment.isUpdatingStatus"
                  >
                    <span v-if="assignment.isUpdatingStatus" class="spinner-border spinner-border-sm me-1"></span>
                    Resume
                  </button>
                  <span v-else>N/A</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer-custom">
        <button type="button" class="btn btn-secondary-custom" @click="$emit('close')">
          Close
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';

const emit = defineEmits(['close']);

const assignments = ref([]);
const loading = ref(true);

const token = localStorage.getItem('auth_token'); // Get token once

// Utility to show toast messages
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

const fetchAssignments = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/lead-action-plan-assignments', {
      headers: { Authorization: `Bearer ${token}` },
    });
    assignments.value = (response.data.data || []).map(assign => ({
      ...assign,
      isUpdatingStatus: false
    }));
  } catch (error) {
    console.error('Failed to load assignments:', error);
    showToastMessage('Failed to load assigned action plans.', 'error');
    assignments.value = [];
  } finally {
    loading.value = false;
  }
};

const toggleAssignmentStatus = async (assignment, newStatus) => {
  assignment.isUpdatingStatus = true;
  try {
    const response = await axios.post(`/api/lead-action-plan-assignments/${assignment.id}/status`, {
      status: newStatus,
    }, {
      headers: { Authorization: `Bearer ${token}` },
    });

    const updatedAssignment = response.data.data;
    const index = assignments.value.findIndex(a => a.id === updatedAssignment.id);
    if (index !== -1) {
      assignments.value[index].status = updatedAssignment.status;
      assignments.value[index].next_action_due_at = updatedAssignment.next_action_due_at;
    }
    showToastMessage(`Action Plan status updated to '${newStatus}' successfully!`);
  } catch (error) {
    console.error(`Failed to update status for assignment ${assignment.id}:`, error);
    const message = error.response?.data?.message || `Failed to update status to '${newStatus}'.`;
    showToastMessage(message, 'error');
  } finally {
    assignment.isUpdatingStatus = false;
  }
};

const formatDateTime = (dateTimeString) => {
  if (!dateTimeString) return 'N/A';
  const date = new Date(dateTimeString);
  return date.toLocaleString();
};

const getStatusBadgeClass = (status) => {
  switch (status) {
    case 'active': return 'badge bg-success';
    case 'paused': return 'badge bg-warning text-dark';
    case 'completed': return 'badge bg-info';
    case 'stopped': return 'badge bg-danger';
    default: return 'badge bg-secondary';
  }
};

onMounted(fetchAssignments);
</script>

<style scoped>
/* Modal Overlay & Content Styling (from your previous modals, adjusted for wider content) */
.modal-overlay-custom {
  position: fixed;
  z-index: 1050;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: rgba(0, 0, 0, 50%);
  block-size: 100%;
  inline-size: 100%;
  inset-block-start: 0;
  inset-inline-start: 0;
}

.modal-content-custom-wide { /* Adjusted for wider content */
  position: relative;
  display: flex;
  flex-direction: column;
  padding: 20px;
  border-radius: 8px;
  background: white;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 15%);
  font-family: Inter, sans-serif;
  inline-size: 95%; /* Wider modal */
  max-block-size: 90vh; /* Allow scrolling for tall content */
  max-inline-size: 1000px; /* Max width for larger screens */
}

.modal-header-custom {
  display: flex;
  flex-shrink: 0; /* Prevent header from shrinking */
  align-items: center;
  justify-content: space-between;
  border-block-end: 1px solid #eee;
  margin-block-end: 20px;
  padding-block-end: 15px;
}

.modal-title-custom {
  color: #333;
  font-size: 1.5rem;
  font-weight: 600;
}

.close-button-custom {
  padding: 0;
  border: none;
  background: none;
  color: #666;
  cursor: pointer;
  font-size: 1.8rem;
  line-height: 1;
}

.modal-body-custom {
  flex-grow: 1; /* Allow body to take available space and scroll */
  margin-block-end: 20px;
  overflow-y: auto; /* Enable scrolling for table content */
  padding-inline-end: 15px; /* Add padding for scrollbar */
}

.modal-footer-custom {
  display: flex;
  flex-shrink: 0; /* Prevent footer from shrinking */
  justify-content: flex-end;
  border-block-start: 1px solid #eee;
  gap: 10px;
  padding-block-start: 15px;
}

.btn-secondary-custom {
  border: none;
  border-radius: 5px;
  background-color: #6c757d;
  color: white;
  cursor: pointer;
  padding-block: 8px;
  padding-inline: 15px;
  transition: background-color 0.2s ease;
}

.btn-secondary-custom:hover {
  background-color: #5a6268;
}

/* Table Specific Styles */
.table th,
.table td {
  padding-block: 12px;
  padding-inline: 15px; /* Consistent padding */
  text-align: center;
  vertical-align: middle;
}

.table thead th {
  background-color: #f8f9fa;
  color: #495057;
  font-weight: 600;
}

.table-hover tbody tr:hover {
  background-color: #f2f2f2;
}

/* Badge Styles */
.badge {
  border-radius: 0.375rem;
  font-size: 0.875em;
  font-weight: 600;
  padding-block: 0.5em;
  padding-inline: 0.75em;
}
.bg-success { background-color: #28a745 !important; color: white; }
.bg-warning { background-color: #ffc107 !important; }
.bg-info { background-color: #17a2b8 !important; color: white; }
.bg-danger { background-color: #dc3545 !important; color: white; }
.bg-secondary { background-color: #6c757d !important; color: white; }

/* Button Styles */
.btn-sm {
  border-radius: 0.2rem;
  font-size: 0.875rem;
  padding-block: 0.25rem;
  padding-inline: 0.5rem;
}

.btn-warning {
  border: none;
  background-color: #ffc107;
  color: #212529;
}

.btn-success {
  border: none;
  background-color: #28a745;
  color: white;
}

.spinner-border-sm {
  border-width: 0.15em;
  block-size: 1rem;
  inline-size: 1rem;
}
</style>
