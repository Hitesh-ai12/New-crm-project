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
        <!-- Search Bar -->
        <div class="mb-3">
          <input
            type="text"
            class="form-control"
            placeholder="Search by Lead Name, Action Plan, or Status..."
            v-model="searchQuery"
            @input="debouncedSearch"
          />
        </div>

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
              <tr v-else-if="paginatedAssignments.length === 0">
                <td colspan="7" class="text-center py-4">No assignments found matching your criteria.</td>
              </tr>
              <tr v-else v-for="assignment in paginatedAssignments" :key="assignment.id">
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
                  <button
                    v-else-if="assignment.status === 'completed'"
                    class="btn btn-sm btn-primary"
                    @click="reassignCompletedPlan(assignment)"
                    :disabled="assignment.isUpdatingStatus"
                  >
                    <span v-if="assignment.isUpdatingStatus" class="spinner-border spinner-border-sm me-1"></span>
                    Re-assign
                  </button>
                  <span v-else>N/A</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination Controls -->
        <nav v-if="totalPages > 1" aria-label="Page navigation">
          <ul class="pagination justify-content-center">
            <li class="page-item" :class="{ disabled: currentPage === 1 }">
              <a class="page-link" href="#" @click.prevent="currentPage--" :disabled="currentPage === 1">Previous</a>
            </li>
            <li class="page-item" v-for="page in totalPages" :key="page" :class="{ active: currentPage === page }">
              <a class="page-link" href="#" @click.prevent="currentPage = page">{{ page }}</a>
            </li>
            <li class="page-item" :class="{ disabled: currentPage === totalPages }">
              <a class="page-link" href="#" @click.prevent="currentPage++" :disabled="currentPage === totalPages">Next</a>
            </li>
          </ul>
        </nav>
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
import Swal from 'sweetalert2';
import { computed, onMounted, ref } from 'vue';

const emit = defineEmits(['close']);

const assignments = ref([]); // Holds all fetched assignments
const loading = ref(true);
const token = localStorage.getItem('auth_token');

// Pagination state
const currentPage = ref(1);
const itemsPerPage = ref(5); // Adjust as needed
const searchQuery = ref(''); // Search query state
let searchTimeout = null; // For debouncing search input

// Computed properties for pagination and search
const filteredAssignments = computed(() => {
  if (!searchQuery.value) {
    return assignments.value;
  }
  const query = searchQuery.value.toLowerCase();
  return assignments.value.filter(assignment => {
    const leadName = `${assignment.lead?.first_name || ''} ${assignment.lead?.last_name || ''}`.toLowerCase();
    const actionPlanName = assignment.action_plan?.name?.toLowerCase() || '';
    const status = assignment.status?.toLowerCase() || '';
    const currentActionType = assignment.current_action?.type?.toLowerCase() || '';
    const currentActionTaskName = assignment.current_action?.task_name?.toLowerCase() || '';
    const currentActionNoteContent = assignment.current_action?.note_content?.toLowerCase() || '';

    return leadName.includes(query) ||
           actionPlanName.includes(query) ||
           status.includes(query) ||
           currentActionType.includes(query) ||
           currentActionTaskName.includes(query) ||
           currentActionNoteContent.includes(query);
  });
});

const totalPages = computed(() => {
  return Math.ceil(filteredAssignments.value.length / itemsPerPage.value);
});

const paginatedAssignments = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredAssignments.value.slice(start, end);
});

// Debounce function for search input
const debouncedSearch = () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }
  searchTimeout = setTimeout(() => {
    currentPage.value = 1; // Reset to first page on new search
  }, 300); // 300ms delay
};

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
      // Update the specific assignment object directly
      assignments.value[index] = { ...assignments.value[index], ...updatedAssignment };
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

const reassignCompletedPlan = async (assignment) => {
  assignment.isUpdatingStatus = true;
  try {
    await axios.post(
      '/api/assign-action-plans',
      {
        action_plan_ids: [assignment.action_plan_id],
        lead_ids: [assignment.lead_id],
      },
      {
        headers: { Authorization: `Bearer ${token}` },
      }
    );
    showToastMessage('Action Plan has been successfully re-assigned!');
    await fetchAssignments(); // Refresh the list after re-assignment
  } catch (err) {
    console.error('Re-assignment error:', err);
    const message = err.response?.data?.message || 'Failed to re-assign action plan.';
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
/* Modal Overlay & Content Styling (unchanged) */
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

.modal-content-custom-wide {
  position: relative;
  display: flex;
  flex-direction: column;
  padding: 20px;
  border-radius: 8px;
  background: white;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 15%);
  font-family: Inter, sans-serif;
  inline-size: 95%;
  max-block-size: 90vh;
  max-inline-size: 1000px;
}

.modal-header-custom {
  display: flex;
  flex-shrink: 0;
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
  flex-grow: 1;
  margin-block-end: 20px;
  overflow-y: auto;
  padding-inline-end: 15px;
}

.modal-footer-custom {
  display: flex;
  flex-shrink: 0;
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

/* Table Specific Styles (unchanged) */
.table th,
.table td {
  padding-block: 12px;
  padding-inline: 15px;
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

/* Badge Styles (unchanged) */
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

/* Button Styles (unchanged) */
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

.btn-primary {
  border: none;
  background-color: #007bff;
  color: white;
}

.spinner-border-sm {
  border-width: 0.15em;
  block-size: 1rem;
  inline-size: 1rem;
}
</style>
