<template>
  <div class="container mt-4">

    <!-- Rows per page -->
    <div class="mb-3 d-flex align-items-center gap-2">
      <label for="pageSize" class="form-label mb-0">Rows per page:</label>
      <select
        id="pageSize"
        class="form-select form-select-sm w-auto"
        v-model="pageSize"
        @change="setPageSize(Number($event.target.value))"
      >
        <option value="5">5</option>
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
      </select>
    </div>

    <!-- Leads Table -->
    <div class="table-container table-responsive">
      <div class="table-wrapper">

        <table class="table table-bordered table-striped align-middle lead_Table">
          <thead class="table-light">
            <tr>
              <th class="checkbox-col">
                <label class="custom-checkbox-label d-flex align-items-center gap-1">
                  <li class="total-leads list-unstyled mb-0 d-flex align-items-center gap-1" @click="openColumnsModal">
                    <span>Settings</span>
                    <i class="fa-solid fa-gear"></i>
                  </li>
                  <span class="custom-checkbox-span"></span>
                </label>
              </th>

              <!-- Draggable Columns -->
              <draggable
                v-model="selectedColumns"
                tag="tr"
                item-key="col"
                handle=".drag-handle"
                @end="onColumnReorder"
              >
                <template #item="{ element, index }">
                  <th :class="['text-nowrap', { 'scroll-column': index >= 6 }]">
                    <span class="drag-handle me-1 cursor-move">☰</span>
                    {{ availableColumns[element]?.label || element }}
                  </th>
                </template>
              </draggable>
            </tr>
          </thead>

          <tbody>
            <tr v-for="lead in leads" :key="lead.id">
              <td
                v-for="(col, idx) in selectedColumns"
                :key="col"
                :class="['text-nowrap', { 'scroll-column': idx >= 6 }]"
              >
                <template v-if="col === 'first_name'">
                  <router-link :to="'/update-profile/' + lead.id" class="text-decoration-none fw-bold text-primary">
                    {{ lead[col] || 'N/A' }}
                  </router-link>
                </template>
                <template v-else>
                  {{ lead[col] || 'N/A' }}
                </template>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-if="leads.length === 0" class="alert alert-warning mt-3">No leads available.</div>

    <!-- Toast Message -->
    <div
      v-if="toast.message"
      :class="`alert alert-${toast.type === 'success' ? 'success' : 'danger'} d-flex justify-content-between align-items-center mt-3`"
      role="alert"
    >
      {{ toast.message }}
      <button @click="toast.message = ''" type="button" class="btn-close" aria-label="Close"></button>
    </div>

    <!-- Pagination -->
    <div class="pagination d-flex justify-content-center align-items-center gap-3 mt-4">
      <button class="btn btn-outline-primary btn-sm" @click="changePage(currentPage - 1)" :disabled="currentPage === 1">
        Previous
      </button>
      <span>Page {{ currentPage }} of {{ totalPages }}</span>
      <button class="btn btn-outline-primary btn-sm" @click="changePage(currentPage + 1)" :disabled="currentPage === totalPages">
        Next
      </button>
    </div>

    <!-- Column Settings Modal -->
    <div v-if="showColumnsModal" class="columns-modal-overlay">
      <div class="columns-modal-container">
        <span class="columns-modal-close" @click="closeColumnsModal">&times;</span>
        <h5 class="mb-3">Select and Arrange Columns</h5>

        <ul class="columns-modal-list list-unstyled mb-3">
          <li v-for="(col, key) in availableColumns" :key="key" class="columns-modal-item d-flex align-items-center gap-2">
            <input
              type="checkbox"
              class="form-check-input"
              :checked="selectedColumns.includes(key)"
              @change="toggleColumnSelection(key)"
              :disabled="col.disabled"
            />
            <label class="form-check-label mb-0">{{ col.label }}</label>
          </li>
        </ul>

        <p v-if="selectedColumns.length === 0" class="text-danger fw-semibold">
          ⚠️ Please select at least one column!
        </p>

        <div class="d-flex justify-content-end gap-2">
          <button class="btn btn-primary btn-sm" @click="updateColumns" :disabled="selectedColumns.length === 0">
            Save
          </button>
          <button class="btn btn-secondary btn-sm" @click="closeColumnsModal">
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>


<script setup>

import axios from 'axios'
import 'bootstrap'
import 'bootstrap/dist/css/bootstrap.min.css'
import { onMounted, ref } from 'vue'
import draggable from 'vuedraggable'

const leads = ref([])
const selectedColumns = ref([])
const showColumnsModal = ref(false)
const currentPage = ref(1)
const totalPages = ref(1)
const pageSize = ref(10)
const toast = ref({ message: '', type: '' })

const authToken = localStorage.getItem('auth_token')

// Columns config
const availableColumns = ref({
  first_name: { label: 'Name', disabled: true },
  email: { label: 'Email', disabled: true },
  phone: { label: 'Phone', disabled: true },
  latest_activity: { label: 'Latest Activity' },
  activity_at: { label: 'Activity At' },
  created_on: { label: 'Created On' },
  tags: { label: 'Tags' },
  stage: { label: 'Stage' },
  latest_source: { label: 'Latest Source' },
  latest_sms: { label: 'Latest SMS' },
  latest_email: { label: 'Latest Email' },
  next_task: { label: 'Next Task' },
  next_appointment: { label: 'Next Appointment' },
  new_listing_alert: { label: 'New Listing Alerts' },
  neighbourhood_alert: { label: 'Neighbourhood Alerts' },
  price_drop_alert: { label: 'Price Drop Alerts' },
  action_plans: { label: 'Active Action Plans' },
  market_updates: { label: 'Assigned Market Updates' },
  real_estate_newsletter: { label: 'Assigned Newsletters' },
  notes: { label: 'Notes' },
  dob: { label: 'DOB' },
  background: { label: 'Background' },
  city: { label: 'City' },
  facebook: { label: 'Facebook' },
  instagram: { label: 'Instagram' },
  linkedin: { label: 'LinkedIn' },
  whatsapp: { label: 'WhatsApp' },
  twitter: { label: 'Twitter' }
})

// 🔃 Fetch Leads with Pagination
const fetchLeads = async () => {
  try {
    const response = await axios.get('/api/leads', {
      params: { page: currentPage.value, perPage: pageSize.value },
      headers: { Authorization: `Bearer ${authToken}` }
    })
    leads.value = response.data.data || response.data
    totalPages.value = response.data.last_page || 1
  } catch (error) {
    showToast("Failed to fetch leads", 'error')
  }
}

// 🔁 Pagination
const changePage = (page) => {
  if (page > 0 && page <= totalPages.value) {
    currentPage.value = page
    fetchLeads()
  }
}

const setPageSize = (size) => {
  pageSize.value = size
  currentPage.value = 1
  fetchLeads()
}


// ✅ Toast helper
const showToast = (message, type = 'success') => {
  toast.value = { message, type }
  setTimeout(() => toast.value.message = '', 3000)
}

// 🧠 Column Settings
const openColumnsModal = () => showColumnsModal.value = true
const closeColumnsModal = () => showColumnsModal.value = false

const toggleColumnSelection = (column) => {
  if (!availableColumns.value[column].disabled) {
    const idx = selectedColumns.value.indexOf(column)
    if (idx === -1) {
      selectedColumns.value.push(column)
      showToast(`Column "${availableColumns.value[column].label}" added`)
    } else {
      selectedColumns.value.splice(idx, 1)
      showToast(`Column "${availableColumns.value[column].label}" removed`)
    }
  }
}

const updateColumns = async () => {
  await saveColumnVisibilitySettings()
  closeColumnsModal()
}

const onColumnReorder = async () => {
  await saveColumnOrderSettings()
}

// 🔐 Settings API Calls
const fetchColumnSettings = async () => {
  try {
    const res = await axios.get("/api/columns-settings", {
      headers: { Authorization: `Bearer ${authToken}` }
    })

    selectedColumns.value = Array.isArray(res.data.selectedColumns)
      ? res.data.selectedColumns
      : ['first_name', 'email', 'phone']
  } catch (e) {
    console.error('🔴 Error fetching column settings', e)
    selectedColumns.value = ['first_name', 'email', 'phone']
  }
}

const saveColumnVisibilitySettings = async () => {
  try {
    await axios.post("/api/columns-settings", {
      selectedColumns: selectedColumns.value
    }, {
      headers: { Authorization: `Bearer ${authToken}` }
    })
    showToast("Column visibility saved")
  } catch {
    showToast("Failed to save column visibility", 'error')
  }
}

const saveColumnOrderSettings = async () => {
  try {
    await axios.post("/api/columns-order", {
      selectedColumns: selectedColumns.value
    }, {
      headers: { Authorization: `Bearer ${authToken}` }
    })
    showToast("Column order saved")
  } catch {
    showToast("Failed to save column order", 'error')
  }
}

// ✅ Initial load
onMounted(() => {
  fetchLeads()
  fetchColumnSettings()
})
</script>

<style scoped>
.table-container {
  overflow-x: auto;
}

.table-wrapper {
  min-inline-size: 900px;
}

.scroll-column {
  background-color: #f9f9f9;
}

.toast {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-radius: 6px;
  font-weight: 500;
  margin-block-start: 10px;
  padding-block: 10px;
  padding-inline: 16px;
}

.toast.success {
  background-color: #d1e7dd;
  color: #0f5132;
}

.toast.error {
  background-color: #f8d7da;
  color: #842029;
}

.toast-close-button {
  border: none;
  background: none;
  color: inherit;
  cursor: pointer;
  font-weight: bold;
}

.columns-modal-overlay {
  position: fixed;
  z-index: 1050;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 50%);
  block-size: 100%;
  inline-size: 100%;
  inset-block-start: 0;
  inset-inline-start: 0;
}

.columns-modal-container {
  padding: 20px;
  border-radius: 12px;
  background: #fff;
  box-shadow: 0 0 10px rgba(0, 0, 0, 20%);
  inline-size: 400px;
}

.columns-modal-close {
  cursor: pointer;
  float: inline-end;
  font-size: 1.5rem;
  font-weight: bold;
}

.columns-modal-list {
  padding: 0;
  list-style: none;
  margin-block-start: 10px;
  max-block-size: 300px;
  overflow-y: auto;
}

.columns-modal-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding-block: 8px;
  padding-inline: 0;
}

.columns-modal-warning {
  color: #d9534f;
  margin-block-start: 10px;
}

.total-leads {
  display: flex;
  align-items: center;
  cursor: pointer;
  font-weight: 500;
  gap: 6px;
}

.select-container {
  margin-block-end: 15px;
}
</style>
