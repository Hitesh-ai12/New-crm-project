<template>
  <div class="modal fade modal-right" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="btn btn-link p-0" data-bs-dismiss="modal" aria-label="Close">
            <i class="fa-solid fa-arrow-left fs-4"></i>
          </button>
          <h2 class="modal-title ms-2" id="filterModalLabel">Filter</h2>
        </div>

        <div class="modal-body">
          <div class="filter-section mb-4">
            <label class="form-label fw-bold d-block">Price Range</label>
            <div class="d-flex justify-content-between small text-muted mb-2">
              <span>MIN</span>
              <span>MAX</span>
            </div>
            <input type="range" class="form-range" min="2000" max="8000" step="100">
            <div class="d-flex justify-content-between small">
              <span>$200K</span>
              <span>$800K</span>
            </div>
          </div>
          
          <div v-for="filter in propertyStore.availableFilters" :key="filter.name" class="filter-section mb-4">
            
            <div v-if="['property_type', 'building_type', 'building_style'].includes(filter.name)">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="form-label fw-bold mb-0">{{ filter.label }}</label>
                <div class="form-check d-flex align-items-center">
                  <input class="form-check-input" type="checkbox" :id="filter.name + 'All'" @change="toggleAll(filter.name, $event.target.checked)">
                  <label class="form-check-label ms-1" :for="filter.name + 'All'">All</label>
                </div>
              </div>
              
              <div class="multi-select-box border rounded p-2">
                <div class="selected-tags d-flex flex-wrap gap-1 mb-2">
                  <span v-for="(value, index) in selectedFilters[filter.name]" :key="index" class="badge bg-secondary d-flex align-items-center">
                    {{ getOptionLabel(filter.name, value) }}
                    <button type="button" class="btn-close btn-close-white ms-1" @click="removeTag(filter.name, value)"></button>
                  </span>
                </div>
                
                <div v-for="option in filter.options" :key="option.value" class="form-check">
                  <input 
                    class="form-check-input" 
                    type="checkbox" 
                    :value="option.value" 
                    :id="filter.name + option.value"
                    v-model="selectedFilters[filter.name]"
                  >
                  <label class="form-check-label" :for="filter.name + option.value">
                    {{ option.label }}
                  </label>
                </div>
              </div>
            </div>

            <div v-else-if="filter.type === 'single-select'">
              <label class="form-label fw-bold d-block">{{ filter.label }}</label>
              <select 
                class="form-select" 
                v-model="selectedFilters[filter.name]"
                :id="filter.name"
              >
                <option value="" disabled>Select {{ filter.label }}</option>
                <option v-for="option in filter.options" :key="option.value" :value="option.value">
                  {{ option.label }}
                </option>
              </select>
            </div>
          </div>

          <div class="filter-section mb-4">
            <label class="form-label fw-bold d-block">Bedrooms</label>
            <div class="d-flex flex-wrap gap-2">
              <button 
                v-for="bed in bedOptions" 
                :key="bed.value" 
                type="button" 
                class="btn btn-outline-secondary"
                :class="{ 'active-filter': selectedBeds === bed.value }"
                @click="selectedBeds = bed.value"
              >
                {{ bed.label }}
              </button>
            </div>
          </div>
          
          <div class="filter-section mb-4">
            <label class="form-label fw-bold d-block">Bathrooms</label>
            <div class="d-flex flex-wrap gap-2">
              <button 
                v-for="bath in bathOptions" 
                :key="bath.value" 
                type="button" 
                class="btn btn-outline-secondary"
                :class="{ 'active-filter': selectedBaths === bath.value }"
                @click="selectedBaths = bath.value"
              >
                {{ bath.label }}
              </button>
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="resetFilters">Reset</button>
          <button type="button" class="btn btn-primary" @click="applyFiltersAndClose">Apply</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import '@fortawesome/fontawesome-free/css/all.css';
import 'bootstrap';
import { Modal } from 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import { ref, watch } from 'vue';
import { usePropertyStore } from '../store/propertyStore';

const propertyStore = usePropertyStore();
const selectedFilters = ref({});
const selectedBeds = ref(null);
const selectedBaths = ref(null);

const bedOptions = ref([
  { value: '1+', label: '1+' },
  { value: '2+', label: '2+' },
  { value: '3+', label: '3+' },
  { value: '4+', label: '4+' },
  { value: '5+', label: '5+' },
  { value: '6+', label: '6+' },
]);

const bathOptions = ref([
  { value: '1+', label: '1+' },
  { value: '2+', label: '2+' },
  { value: '3+', label: '3+' },
  { value: '4+', label: '4+' },
  { value: '5+', label: '5+' },
  { value: '6+', label: '6+' },
]);

// watch ka use karke, store ke appliedFilters ko local state mein sync karein
watch(
  () => propertyStore.appliedFilters,
  (newFilters) => {
    selectedFilters.value = {};
    selectedBeds.value = null;
    selectedBaths.value = null;

    for (const key in newFilters) {
      if (['bed', 'bath'].includes(key)) {
        if (key === 'bed') selectedBeds.value = newFilters[key];
        if (key === 'bath') selectedBaths.value = newFilters[key];
      } else {
        selectedFilters.value[key] = newFilters[key];
      }
    }
  },
  { deep: true, immediate: true }
);

const show = () => {
  const modalElement = document.getElementById('filterModal');
  if (modalElement) {
    const modal = new Modal(modalElement);
    modal.show();
  }
};
const close = () => {
  const modalElement = document.getElementById('filterModal');
  if (modalElement) {
    const modal = Modal.getInstance(modalElement);
    if (modal) {
      modal.hide();
    }
  }
};
const applyFiltersAndClose = async () => {
  const filtersToApply = { ...selectedFilters.value };
  if (selectedBeds.value) {
    filtersToApply['bed'] = selectedBeds.value;
  }
  if (selectedBaths.value) {
    filtersToApply['bath'] = selectedBaths.value;
  }
  await propertyStore.applyFilters(filtersToApply);
  close();
};
const resetFilters = () => {
  selectedFilters.value = {};
  selectedBeds.value = null;
  selectedBaths.value = null;
  propertyStore.clearAllFilters();
  close();
};

const toggleAll = (filterName, isChecked) => {
  const options = propertyStore.availableFilters.find(f => f.name === filterName)?.options;
  if (isChecked) {
    selectedFilters.value[filterName] = options.map(o => o.value);
  } else {
    selectedFilters.value[filterName] = [];
  }
};

const getOptionLabel = (filterName, value) => {
  const filter = propertyStore.availableFilters.find(f => f.name === filterName);
  const option = filter?.options.find(o => o.value === value);
  return option?.label || value;
};

const removeTag = (filterName, value) => {
  selectedFilters.value[filterName] = selectedFilters.value[filterName].filter(v => v !== value);
};

defineExpose({
  show
});
</script>

<style scoped>
.active-filter {
  border-color: #007bff;
  background-color: #007bff;
  color: white;
}

.btn-outline-secondary {
  border-color: #ced4da;
  color: #495057;
}

.btn-outline-secondary.active-filter {
  border-color: #007bff;
  background-color: #007bff;
  color: white;
}

/* Right side modal customization */
.modal.modal-right .modal-dialog {
  position: fixed;
  margin: 0;
  block-size: 100%;
  inset-inline-end: 0;
  max-inline-size: 400px; /* adjust width */
  transform: translateX(100%);
  transition: transform 0.3s ease-out;
}

.modal.modal-right.show .modal-dialog {
  transform: translateX(0);
}

.modal.modal-right .modal-content {
  border-radius: 0;
  block-size: 100%;
}

.modal-header {
  border-block-end: none;
}

.modal-footer {
  display: flex;
  justify-content: space-between;
  padding: 1rem;
  border-block-start: none;
}

.modal-footer .btn {
  flex: 1;
  margin-block: 0;
  margin-inline: 5px;
}

.btn-primary {
  border-color: #007bff;
  background-color: #007bff;
}

.btn-secondary {
  border-color: #6c757d;
  background-color: #6c757d;
}

.form-check-label {
  font-size: 0.875rem;
}

.select-options-box p {
  color: #495057;
  font-size: 0.9rem;
}

.modal-body {
  position: relative;
  flex: 1 1 auto;
  padding: var(--bs-modal-padding);
  overflow-y: scroll;
}
</style>
