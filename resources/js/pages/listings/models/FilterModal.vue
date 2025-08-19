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
          <div class="filter-section mb-4 price-range-container">
            <label class="form-label fw-bold d-block">Price Range</label>
            <div class="d-flex justify-content-between gap-3 mb-2">
              <div class="form-group flex-grow-1">
                <input
                  type="text"
                  class="form-control"
                  v-model="formattedMinPrice"
                  @blur="updateSliderFromInput"
                />
              </div>
              <div class="form-group flex-grow-1">
                <input
                  type="text"
                  class="form-control"
                  v-model="formattedMaxPrice"
                  @blur="updateSliderFromInput"
                />
              </div>
            </div>

            <div class="dual-range-slider-wrap">
              <input type="range" min="0" max="100" step="1" v-model.number="priceSlider.min" @input="updateMinSlider" />
              <input type="range" min="0" max="100" step="1" v-model.number="priceSlider.max" @input="updateMaxSlider" />
              <div class="slider-track"></div>
              <div class="range-fill" :style="rangeFillStyle"></div>
            </div>

            <div class="d-flex justify-content-between small text-muted mt-2">
              <span>Min</span>
              <span>200K</span>
              <span>550K</span>
              <span>950K</span>
              <span>2M</span>
              <span>4M</span>
              <span>Max</span>
            </div>
          </div>
          
          <div v-for="filter in propertyStore.availableFilters" :key="filter.name" class="filter-section mb-4">
            <div v-if="['property_type', 'building_type', 'building_style'].includes(filter.name)">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="form-label fw-bold mb-0">{{ filter.label }}</label>
              </div>

              <div class="selected-tags d-flex flex-wrap gap-1 mb-2">
                <span 
                  v-for="(value, index) in selectedFilters[filter.name]" 
                  :key="index" 
                  class="badge bg-primary d-flex align-items-center"
                >
                  {{ getOptionLabel(filter.name, value) }}
                  <button 
                    type="button" 
                    class="btn-close btn-close-white ms-1" 
                    @click="removeTag(filter.name, value)"
                  ></button>
                </span>
              </div>

              <div class="dropdown border rounded p-2">
                <div 
                  class="dropdown-toggle w-100 text-muted small cursor-pointer" 
                  @click="toggleDropdown(filter.name)"
                >
                  Select {{ filter.label }}
                </div>
                <div 
                  v-if="dropdownOpen[filter.name]" 
                  class="dropdown-menu show w-100 mt-2 p-2"
                  style="max-block-size: 200px; overflow-y: auto;"
                >
                  <div 
                    v-for="option in getAvailableOptions(filter)" 
                    :key="option.value"
                    class="dropdown-item"
                    @click="selectOption(filter.name, option.value)"
                  >
                    {{ option.label }}
                  </div>
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
import { computed, ref, watch } from 'vue';
import { usePropertyStore } from '../store/propertyStore';

const propertyStore = usePropertyStore();

const priceSlider = ref({ min: 0, max: 100 });
const priceInput = ref({ min: null, max: 5000000 });

const pricePoints = [0, 200000, 550000, 950000, 2000000, 4000000, 5000000];

const getPriceFromSlider = (percent) => {
  const pointsCount = pricePoints.length - 1;
  const segment = 100 / pointsCount;
  const index = Math.floor(percent / segment);
  const startPrice = pricePoints[index];
  const endPrice = pricePoints[Math.min(index + 1, pointsCount)];
  const segmentPercent = (percent % segment) / segment;
  
  return Math.round(startPrice + (endPrice - startPrice) * segmentPercent);
};

const getSliderFromPrice = (price) => {
  const pointsCount = pricePoints.length - 1;
  const segment = 100 / pointsCount;

  for (let i = 0; i < pointsCount; i++) {
    const startPrice = pricePoints[i];
    const endPrice = pricePoints[i + 1];

    if (price >= startPrice && price <= endPrice) {
      if (startPrice === endPrice) return (i * segment);
      const relativePrice = price - startPrice;
      const range = endPrice - startPrice;
      const percentageInSegment = relativePrice / range;
      return (i * segment) + (percentageInSegment * segment);
    }
  }
  return price <= pricePoints[0] ? 0 : 100;
};

const formattedMinPrice = computed({
  get: () => {
    return priceInput.value.min ? `$${priceInput.value.min.toLocaleString()}` : '';
  },
  set: (val) => {
    const cleanedValue = val.replace(/[$,]/g, '');
    priceInput.value.min = cleanedValue ? parseInt(cleanedValue) : null;
  }
});

const formattedMaxPrice = computed({
  get: () => {
    return priceInput.value.max ? `$${priceInput.value.max.toLocaleString()}` : '';
  },
  set: (val) => {
    const cleanedValue = val.replace(/[$,]/g, '');
    priceInput.value.max = cleanedValue ? parseInt(cleanedValue) : null;
  }
});

watch(
  priceSlider,
  (newVal) => {
    priceInput.value.min = getPriceFromSlider(newVal.min);
    priceInput.value.max = getPriceFromSlider(newVal.max);
  },
  { deep: true }
);

const updateSliderFromInput = () => {
  if (priceInput.value.min > priceInput.value.max) {
    const temp = priceInput.value.min;
    priceInput.value.min = priceInput.value.max;
    priceInput.value.max = temp;
  }
  
  priceSlider.value.min = getSliderFromPrice(priceInput.value.min);
  priceSlider.value.max = getSliderFromPrice(priceInput.value.max);
};

const updateMinSlider = () => {
  if (priceSlider.value.min > priceSlider.value.max) {
    priceSlider.value.min = priceSlider.value.max;
  }
};

const updateMaxSlider = () => {
  if (priceSlider.value.max < priceSlider.value.min) {
    priceSlider.value.max = priceSlider.value.min;
  }
};

const rangeFillStyle = computed(() => {
  const minPos = priceSlider.value.min;
  const maxPos = priceSlider.value.max;
  return {
    left: `${minPos}%`,
    width: `${maxPos - minPos}%`,
  };
});

const selectedFilters = ref({});
const selectedBeds = ref(null);
const selectedBaths = ref(null);
const dropdownOpen = ref({});

const bedOptions = ref([
  { value: '1', label: '1+' },
  { value: '2', label: '2+' },
  { value: '3', label: '3+' },
  { value: '4', label: '4+' },
  { value: '5', label: '5+' },
  { value: '6', label: '6+' },
]);
const bathOptions = ref([
  { value: '1', label: '1+' },
  { value: '2', label: '2+' },
  { value: '3', label: '3+' },
  { value: '4', label: '4+' },
  { value: '5', label: '5+' },
  { value: '6', label: '6+' },
]);

watch(
  () => propertyStore.availableFilters,
  (newFilters) => {
    selectedFilters.value = {};
    newFilters.forEach(filter => {
      if (['property_type', 'building_type', 'building_style'].includes(filter.name)) {
        selectedFilters.value[filter.name] = [];
      }
    });
  },
  { deep: true, immediate: true }
);

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

// Updated function to include price filters
const applyFiltersAndClose = async () => {
  const filtersToApply = { ...selectedFilters.value };
  
  // Add min and max prices to the filters
  if (priceInput.value.min !== null) {
    filtersToApply['min_price'] = priceInput.value.min;
  }
  if (priceInput.value.max !== null) {
    filtersToApply['max_price'] = priceInput.value.max;
  }
  
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
  priceInput.value.min = null;
  priceInput.value.max = 5000000;
  priceSlider.value.min = 0;
  priceSlider.value.max = 100;
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

const toggleDropdown = (filterName) => {
  dropdownOpen.value[filterName] = !dropdownOpen.value[filterName];
};

const getAvailableOptions = (filter) => {
  return filter.options.filter(
    (o) => !selectedFilters.value[filter.name]?.includes(o.value)
  );
};

const selectOption = (filterName, value) => {
  if (!selectedFilters.value[filterName]) {
    selectedFilters.value[filterName] = [];
  }
  selectedFilters.value[filterName].push(value);
};

const removeTag = (filterName, value) => {
  selectedFilters.value[filterName] = selectedFilters.value[filterName].filter(
    (v) => v !== value
  );
};

defineExpose({
  show
});
</script>

<style scoped>
/* Custom CSS for the Price Range Slider and Inputs */
.price-range-container {
  position: relative;
  inline-size: 100%;
}

.dual-range-slider-wrap {
  position: relative;
  block-size: 20px;
  inline-size: 100%;
}

/* ... existing slider styles ... */

.dual-range-slider-wrap input[type="range"]::-webkit-slider-thumb {
  /* Match red color from the image */
  border: 1px solid #dc3545;
  border-radius: 50%;
  appearance: none;
  background-color: #dc3545;
  block-size: 16px;
  cursor: pointer;
  inline-size: 16px;
  margin-block-start: -6px; /* Adjust to center the thumb */
  pointer-events: all;
}

.dual-range-slider-wrap input[type="range"]::-moz-range-thumb {
  border: 1px solid #dc3545;
  border-radius: 50%;
  background-color: #dc3545;
  block-size: 16px;
  cursor: pointer;
  inline-size: 16px;
  pointer-events: all;
}

.slider-track {
  position: absolute;
  border-radius: 2px;
  background: #e9ecef;
  block-size: 4px;
  inline-size: 100%;
  inset-block-start: 50%;
  transform: translateY(-50%);
}

.range-fill {
  position: absolute;
  z-index: 1;
  border-radius: 2px;
  background: #dc3545; /* Match red color from the image */
  block-size: 4px;
  inset-block-start: 50%;
  transform: translateY(-50%);
}

.form-control {
  border: 1px solid #ced4da;
  border-radius: 0.25rem;
  padding-block: 0.375rem;
  padding-inline: 0.75rem;
  text-align: center;
}

.form-group input {
  border-color: #dc3545; /* Input border color from image */
}

.dropdown-toggle {
  border-radius: 4px;
  background: #f8f9fa;
  padding-block: 6px;
  padding-inline: 10px;
}

.dropdown-item {
  cursor: pointer;
  padding-block: 6px;
  padding-inline: 10px;
}

.dropdown-item:hover {
  background: #007bff;
  color: #fff;
}

.dual-range-slider-wrap input[type="range"] {
  position: absolute;
  z-index: 2;
  margin: 0;
  appearance: none;
  background: transparent;
  block-size: 20px;
  inline-size: 100%;
  pointer-events: none;
}

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
  margin: 0;
  block-size: 100vh;
  float: inline-end;
  inline-size: 550px;
  inset-block-start: 0;
  max-inline-size: 100%;
  transform: translateX(0);
}

.modal.modal-right .modal-content {
  border-radius: 0;
  block-size: 100%;
}

.modal-header {
  display: flex;
  flex-shrink: 0;
  align-items: center;
  padding: var(--bs-modal-header-padding);
  border-block-end: var(--bs-modal-header-border-width) solid var(--bs-modal-header-border-color);
  border-start-end-radius: var(--bs-modal-inner-border-radius);
  border-start-start-radius: var(--bs-modal-inner-border-radius);
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
