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

        <button
          class="btn btn-danger"
          :disabled="isDisabled"
          @click="confirmDeleteSelected"
          title="Delete Selected Action Plans"
        >
          <i class="fas fa-trash-alt"></i> Delete
        </button>
        
        <button
          class="btn btn-info"
          @click="showAssignmentsModal = true"
          title="View Assigned Action Plans"
        >
          <i class="fas fa-list-alt"></i> View Assignments
        </button>
      </div>
    </div>

    <!-- Search Bar -->
    <div class="mb-3">
      <input
        type="text"
        class="form-control"
        placeholder="Search action plans by name..."
        v-model="searchQuery"
        @input="debouncedSearch"
      />
    </div>

    <!-- Loader added to the main content area -->
    <Loader v-if="loading" />

    <!-- Data Table -->
    <div class="table-responsive" v-if="!loading">
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
            <th>Tag(s)</th>
            <th>Stage(s)</th>
            <th>Source(s)</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="actionPlans.length === 0">
            <td colspan="12" class="text-center py-4">No action plans found matching your criteria.</td>
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
            <td>{{ item.active_leads_count }}</td>
            <td>{{ item.paused_leads_count }}</td>
            <td>{{ item.stopped_leads_count }}</td>
            <td>{{ item.completed_leads_count }}</td>
            <td>{{ item.creator?.name || 'N/A' }}</td>
            <td><div class="scrollable-cell">{{ getTagsForDisplay(item) }}</div></td>
            <td><div class="scrollable-cell">{{ getStagesForDisplay(item) }}</div></td>
            <td><div class="scrollable-cell">{{ getSourcesForDisplay(item) }}</div></td>
            <td>
              <button class="btn btn-sm btn-info me-2" @click="openEditModal(item)" title="Edit Action Plan">
                <i class="fas fa-edit"></i>
              </button>
              <button class="btn btn-sm btn-danger" @click="confirmDelete(item.id)" title="Delete Action Plan">
                <i class="fas fa-trash-alt"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination Controls -->
    <nav v-if="totalPages > 1 && !loading" aria-label="Page navigation">
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
    <AssignTagModal 
      v-if="showAssignTagModal" 
      @close="showAssignTagModal = false" 
      @tags-assigned="handleTagsAssigned"
      :selectedActionPlanIds="selectedActionPlanIds"
    />
    <AssignStageModal 
      v-if="showAssignStageModal" 
      @close="showAssignStageModal = false" 
      @stages-assigned="handleStagesAssigned"
      :selectedActionPlanIds="selectedActionPlanIds"
    />
    <AssignSourceModal 
      v-if="showAssignSourceModal" 
      @close="showAssignSourceModal = false" 
      @sources-assigned="handleSourcesAssigned"
      :selectedActionPlanIds="selectedActionPlanIds"
    />
    
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
import Swal from 'sweetalert2';
import { computed, onMounted, ref, watch } from 'vue';

const token = localStorage.getItem('auth_token');
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

const actionPlans = ref([]);
const loading = ref(true);

const currentPage = ref(1);
const itemsPerPage = ref(10);
const totalPages = ref(1);
const searchQuery = ref('');
let searchTimeout = null;

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
const showAssignmentsModal = ref(false);

const showConfirmModal = ref(false);
const idsToDelete = ref([]);

const isDisabled = computed(() => selectedActionPlanIds.value.length === 0);

import Loader from '@/pages/loader/loader.vue';
import ActionPlanFormModal from './models/ActionPlanFormModal.vue';
import AssignSourceModal from './models/AssignSourceModal.vue';
import AssignStageModal from './models/AssignStageModal.vue';
import AssignTagModal from './models/AssignTagModal.vue';
import AssignUserModal from './models/AssignUserModal.vue';
import LeadActionPlanAssignmentsModal from './models/LeadActionPlanAssignmentsModal.vue';

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

const allTagsLookup = ref({});
const allStagesLookup = ref({});
const allSourcesLookup = ref({});

const fetchActionPlans = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/automation/action-plans', {
      params: { 
        page: currentPage.value,
        per_page: itemsPerPage.value,
        search: searchQuery.value,
        include: 'actions,creator'
      },
      headers: { Authorization: `Bearer ${token}` }
    });
    actionPlans.value = response.data.data || [];
    totalPages.value = response.data.last_page || 1;
  } catch (error) {
    console.error('Failed to load action plans:', error);
    showToastMessage('Failed to load action plans.', 'error');
    actionPlans.value = [];
    totalPages.value = 1;
  } finally {
    loading.value = false;
  }
};

const fetchItemsLookup = async () => {
  try {
    const token = localStorage.getItem('auth_token');
    const [tagsRes, stagesRes, sourcesRes] = await Promise.all([
      axios.get('/api/items?type=tag', { headers: { Authorization: `Bearer ${token}` } }),
      axios.get('/api/items?type=stage', { headers: { Authorization: `Bearer ${token}` } }),
      axios.get('/api/items?type=source', { headers: { Authorization: `Bearer ${token}` } })
    ]);

    allTagsLookup.value = (tagsRes.data.tags || []).reduce((acc, item) => {
      acc[item.id] = item.name;
      return acc;
    }, {});
    
    allStagesLookup.value = (stagesRes.data.stages || []).reduce((acc, item) => {
      acc[item.id] = item.name;
      return acc;
    }, {});

    allSourcesLookup.value = (sourcesRes.data.sources || []).reduce((acc, item) => {
      acc[item.id] = item.name;
      return acc;
    }, {});

  } catch (error) {
    console.error('Failed to load tags/stages/sources for lookup:', error);
  }
};

const debouncedSearch = () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }
  searchTimeout = setTimeout(() => {
    currentPage.value = 1;
    fetchActionPlans();
  }, 300);
};

watch(currentPage, () => {
  fetchActionPlans();
});


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

const confirmDelete = (id) => {
  Swal.fire({
    title: 'Are you sure?',
    text: "You are about to delete this action plan. This action cannot be undone.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!'
  }).then((result) => {
    if (result.isConfirmed) {
      executeDelete([id]);
    }
  });
};

const confirmDeleteSelected = () => {
  if (selectedActionPlanIds.value.length === 0) {
    showToastMessage('Please select at least one action plan to delete.', 'warning');
    return;
  }
  Swal.fire({
    title: 'Are you sure?',
    text: `You are about to delete ${selectedActionPlanIds.value.length} selected action plan(s).`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete them!'
  }).then((result) => {
    if (result.isConfirmed) {
      executeDelete(selectedActionPlanIds.value);
    }
  });
};

const executeDelete = async (ids) => {
  try {
    await axios.post('/api/automation/action-plans/batch-delete', { ids });
    await fetchActionPlans();
    selectedActionPlanIds.value = [];
    showToastMessage(response.data.message || 'Action Plan(s) deleted successfully!', 'success');
  } catch (error) {
    console.error('Failed to delete action plan(s):', error);
    showToastMessage('Failed to delete action plan(s).', 'error');
  }
};

const handleTagsAssigned = async (assignedTagIds) => {
    try {
        if (selectedActionPlanIds.value.length === 0) {
            showToastMessage('Please select at least one action plan to assign tags to.', 'warning');
            return;
        }

        const payload = {
            action_plan_ids: selectedActionPlanIds.value,
            tag_ids: assignedTagIds
        };

        const response = await axios.post('/api/automation/action-plans/add-tag-action', payload);
        showToastMessage(response.data.message || 'Tags action added to plan(s) successfully!', 'success');
        showAssignTagModal.value = false;
        await fetchActionPlans();
    } catch (error) {
        console.error('Failed to add tags action to plan(s):', error);
        const message = error.response?.data?.message || 'Something went wrong.';
        showToastMessage(message, 'error');
    }
};

const handleStagesAssigned = async (assignedStageIds) => {
    try {
        if (selectedActionPlanIds.value.length === 0) {
            showToastMessage('Please select at least one action plan to assign stages to.', 'warning');
            return;
        }

        const payload = {
            action_plan_ids: selectedActionPlanIds.value,
            stage_ids: assignedStageIds
        };

        const response = await axios.post('/api/automation/action-plans/change-stage-action', payload);
        showToastMessage(response.data.message || 'Change Stage action added to plan(s) successfully!', 'success');
        showAssignStageModal.value = false;
        await fetchActionPlans();
    } catch (error) {
        console.error('Failed to add change stage action to plan(s):', error);
        const message = error.response?.data?.message || 'Something went wrong.';
        showToastMessage(message, 'error');
    }
};

const handleSourcesAssigned = async (assignedSourceIds) => {
    try {
        if (selectedActionPlanIds.value.length === 0) {
            showToastMessage('Please select at least one action plan to assign sources to.', 'warning');
            return;
        }

        const payload = {
            action_plan_ids: selectedActionPlanIds.value,
            source_ids: assignedSourceIds
        };

        const response = await axios.post('/api/automation/action-plans/assign-source-action', payload);
        showToastMessage(response.data.message || 'Assign Source action added to plan(s) successfully!', 'success');
        showAssignSourceModal.value = false;
        await fetchActionPlans();
    } catch (error) {
        console.error('Failed to add assign source action to plan(s):', error);
        const message = error.response?.data?.message || 'Something went wrong.';
        showToastMessage(message, 'error');
    }
};

const getTagsForDisplay = (item) => {
  if (!item.actions || item.actions.length === 0) return 'N/A';
  
  const tagIds = item.actions
    .filter(action => action.type === 'Add Tag(s)' && action.add_tags && action.add_tags.length > 0)
    .flatMap(action => action.add_tags);

  if (tagIds.length === 0) return 'N/A';

  const tagNames = tagIds.map(id => allTagsLookup.value[id]).filter(name => name);
  return tagNames.length > 0 ? tagNames.join(', ') : 'N/A';
};

const getStagesForDisplay = (item) => {
  if (!item.actions || item.actions.length === 0) return 'N/A';
  
  const stageIds = item.actions
    .filter(action => action.type === 'Change Stage' && action.new_stage && action.new_stage.length > 0)
    .flatMap(action => action.new_stage);

  if (stageIds.length === 0) return 'N/A';

  const stageNames = stageIds.map(id => allStagesLookup.value[id]).filter(name => name);
  return stageNames.length > 0 ? stageNames.join(', ') : 'N/A';
};

const getSourcesForDisplay = (item) => {
  if (!item.actions || item.actions.length === 0) return 'N/A';
  
  const sourceIds = item.actions
    .filter(action => action.type === 'Assign Source' && action.assign_action_plan)
    .map(action => parseInt(action.assign_action_plan));

  if (sourceIds.length === 0) return 'N/A';

  const sourceNames = sourceIds.map(id => allSourcesLookup.value[id]).filter(name => name);
  return sourceNames.length > 0 ? sourceNames.join(', ') : 'N/A';
};

onMounted(() => {
  fetchItemsLookup();
  fetchActionPlans();
});
</script>


<style scoped>
.scrollable-cell {
  max-inline-size: 150px;
  overflow-x: auto;
  padding-block-end: 5px;
  white-space: nowrap;
}

.scrollable-cell::-webkit-scrollbar {
  block-size: 5px;
}

.scrollable-cell::-webkit-scrollbar-track {
  border-radius: 10px;
  background: #f1f1f1;
}

.scrollable-cell::-webkit-scrollbar-thumb {
  border-radius: 10px;
  background: #888;
}

.scrollable-cell::-webkit-scrollbar-thumb:hover {
  background: #555;
}

.table th,
.table td {
  text-align: center;
  vertical-align: middle;
}

.btn-info {
  border: none;
  background-color: #17a2b8;
  color: white;
}

.btn-info:hover {
  background-color: #138496;
}
</style>
