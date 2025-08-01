<template>
  <div class="container my-4">
    <h2 class="mb-4">Assigned Action Plans to Leads</h2>

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
</template>

<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';

const assignments = ref([]);
const loading = ref(true);

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
  }
};

const fetchAssignments = async () => {
  loading.value = true;
  try {
    const token = localStorage.getItem('auth_token');
    const response = await axios.get('/api/lead-action-plan-assignments', {
      headers: { Authorization: `Bearer ${token}` },
    });
    // Add a temporary 'isUpdatingStatus' flag for loading state on buttons
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
  assignment.isUpdatingStatus = true; // Set loading state for this specific assignment
  try {
    const token = localStorage.getItem('auth_token');
    const response = await axios.post(`/api/lead-action-plan-assignments/${assignment.id}/status`, {
      status: newStatus,
    }, {
      headers: { Authorization: `Bearer ${token}` },
    });

    // Update the status in the local assignments list
    const updatedAssignment = response.data.data;
    const index = assignments.value.findIndex(a => a.id === updatedAssignment.id);
    if (index !== -1) {
      assignments.value[index].status = updatedAssignment.status;
      assignments.value[index].next_action_due_at = updatedAssignment.next_action_due_at; // Update due date
    }
    showToastMessage(`Action Plan status updated to '${newStatus}' successfully!`);
  } catch (error) {
    console.error(`Failed to update status for assignment ${assignment.id}:`, error);
    const message = error.response?.data?.message || `Failed to update status to '${newStatus}'.`;
    showToastMessage(message, 'error');
  } finally {
    assignment.isUpdatingStatus = false; // Reset loading state
  }
};

const formatDateTime = (dateTimeString) => {
  if (!dateTimeString) return 'N/A';
  const date = new Date(dateTimeString);
  return date.toLocaleString(); // Format date and time based on user's locale
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
.container {
  margin-block: 0;
  margin-inline: auto;
  max-inline-size: 1200px;
}

.table th,
.table td {
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

.badge {
  border-radius: 0.375rem;
  font-size: 0.875em;
  font-weight: 600;
  padding-block: 0.5em;
  padding-inline: 0.75em;
}

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
