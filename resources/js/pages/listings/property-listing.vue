<template>
  <div class="page-flex">
    <div class="main-wrapper">
      <main class="main-content-area">
        <div class="container-fluid">
          <div class="header-section">
            <div class="header-top d-flex justify-content-between align-items-center mb-3">
              <div class="d-flex align-items-center gap-2">
                <a href="#" class="property-settings text-secondary fs-5">
                  <i class="fa-solid fa-gear"></i>
                </a>
                <h2 class="mb-0">Property Listings</h2>
              </div>
              <div class="header-actions d-flex align-items-center gap-2">
                <button class="btn clear-btn text-danger fw-bold">
                  <i class="fa-solid fa-xmark"></i> Clear All
                </button>
                <button class="btn btn-action">
                  <i class="fa-solid fa-envelope"></i> Email
                </button>
                <button class="btn btn-action d-none d-md-inline-block">
                  <i class="fa-solid fa-tags"></i> Email Tags
                </button>
                <button class="btn btn-action">
                  <i class="fa-solid fa-comment-sms"></i> Send SMS
                </button>
                <button class="btn btn-action d-none d-md-inline-block">
                  <i class="fa-solid fa-tags"></i> SMS Tags
                </button>
              </div>
            </div>

            <div class="header-filters p-3 rounded d-flex align-items-center flex-wrap gap-3">
              <div class="filter-group d-flex align-items-center flex-grow-1 gap-2">
                <div class="dropdown-select-container">
                  <select class="form-select" v-model="propertyStore.filters.listingType" @change="propertyStore.handleListingTypeChange">
                    <option value="sale">For Sale</option>
                    <option value="rent">For Rent</option>
                    <option value="sold">Recently Sold</option>
                    <option value="open-house">Open House</option>
                    <option value="leased">Leased</option>
                  </select>
                </div>
                <div class="search-input-container flex-grow-1 position-relative">
                  <input type="text" class="form-control" placeholder="Address | MLS Num | City | Neighbourhood" v-model="propertyStore.filters.search" @keyup.enter="propertyStore.applyFilters(propertyStore.filters.search)">
                  <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                </div>
              </div>
              <div class="filter-sort-group d-flex align-items-center gap-2">
                <button class="btn btn-light-blue" @click="showFilterModal">
                  <i class="fa-solid fa-sliders me-1"></i> Filters
                </button>
                <div class="dropdown">
                  <button class="btn btn-light-blue dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-arrow-down-short-wide me-1"></i> Short
                  </button>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Price: High to Low</a></li>
                    <li><a class="dropdown-item" href="#">Price: Low to High</a></li>
                    <li><a class="dropdown-item" href="#">Date: Newest</a></li>
                  </ul>
                </div>
              </div>
            </div>
          </div>

          <div v-if="propertyStore.loading" class="text-center my-4">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
          <div v-else-if="propertyStore.error" class="alert alert-danger text-center my-4">
            {{ propertyStore.error }}
          </div>
          <div v-else class="row property-cards-area g-3 mt-3">
            <div v-for="(property, index) in propertyStore.properties" :key="property.ar_uid || index" class="col-xl-4 col-lg-6 col-md-6 col-sm-6">
              <div class="property-list-card" @click="showPropertyDetails(property)">
                <div class="list-cart-label">
                  <span :style="{ background: property.labelColor }">{{ property.label }}</span>
                </div>
                <div class="list-card-body-cnt">
                  <div class="property-img">
                    <img :src="property.mainImage" alt="Property Image" />
                  </div>
                  <div class="property-details">
                    <h4>{{ property.title }}</h4>
                    <h6 class="text-muted"><i class="fa-solid fa-location-dot"></i> {{ property.address }}</h6>
                    <h5>From <em>{{ property.price }}</em></h5>
                  </div>
                </div>
                <ul class="property-features-list list-unstyled d-flex gap-3">
                  <li><i class="fa-solid fa-bed text-primary"></i> {{ property.beds }}</li>
                  <li><i class="fa-solid fa-bath text-info"></i> {{ property.baths }}</li>
                  <li><i class="fa-solid fa-ruler-combined text-success"></i> {{ property.size }}</li>
                  <li><i class="fa-solid fa-building text-secondary"></i> {{ property.type }}</li>
                </ul>
              </div>
            </div>
            <div v-if="propertyStore.properties.length === 0 && !propertyStore.loading" class="col-12 text-center text-muted my-5">
              No properties found matching your criteria.
            </div>
          </div>

          <footer class="footer-area text-center py-4 text-muted">
            2025 © Agentroof - <a href="http://www.agentroof.com" class="text-decoration-none">agentroof.com</a>
          </footer>
        </div>
      </main>
    </div>
    
    <PropertyDetailsModal :property="selectedProperty" ref="propertyDetailsModal" />
    <FilterModal ref="filterModal" /> 
  </div>
</template>

<script setup>
import '@fortawesome/fontawesome-free/css/all.css';
import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import { onMounted, onUnmounted, ref } from 'vue';
import FilterModal from './models/FilterModal.vue';
import PropertyDetailsModal from './models/PropertyDetailsModal.vue';
import { usePropertyStore } from './store/propertyStore';

const propertyStore = usePropertyStore();

const propertyDetailsModal = ref(null);
const filterModal = ref(null);
const selectedProperty = ref(null);

// Dashboard.vue <script setup> block mein
const showPropertyDetails = async (property) => {
  // Yahan hum property object se 'slug' nikal rahe hain
  const slug = property.slug;
  
  await propertyStore.fetchPropertyDetails(slug);
  
  if (propertyStore.selectedPropertyDetails) {
    if (propertyDetailsModal.value) {
      propertyDetailsModal.value.show();
    }
  } else {
    alert('Property details not available.');
  }
};

const showFilterModal = () => {
  if (filterModal.value) {
    filterModal.value.show();
  }
};

const handleScroll = () => {
  const scrollPosition = window.innerHeight + window.scrollY;
  const bottomOfPage = document.documentElement.offsetHeight;
  if (scrollPosition >= bottomOfPage - 200) {
    propertyStore.loadMoreProperties();
  }
};

onMounted(() => {
  propertyStore.handleListingTypeChange();
  window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>

<style scoped>
/* Base Styles */
body {
  background-color: #f8f9fa;
  font-family: Arial, sans-serif;
}

.main-wrapper {
  padding: 2rem;
}

/* Header Section */
.header-section {
  border-block-end: 1px solid #e9ecef;
  margin-block-end: 1.5rem;
  padding-block-end: 1rem;
}

.header-top {
  margin-block-end: 1rem;
}

.table-title h2 {
  color: #343a40;
  font-size: 1.5rem;
  font-weight: 600;
}

.header-actions .btn {
  border-radius: 0.25rem;
  font-size: 0.875rem;
  font-weight: 600;
  padding-block: 0.5rem;
  padding-inline: 1rem;
}

.btn-action {
  border: 1px solid #dcdfe6;
  background-color: #f1f4f9;
  color: #495057;
}

.clear-btn {
  border: none;
  background-color: transparent;
}

/* Filters Section */
.header-filters {
  border: 1px solid #dcdfe6;
  border-radius: 0.5rem;
  background-color: #f1f4f9;
}

.form-select,
.form-control {
  border-color: #dcdfe6;
  block-size: 40px;
}

.search-input-container {
  max-inline-size: 400px;
}

.search-icon {
  position: absolute;
  color: #6c757d;
  inset-block-start: 50%;
  inset-inline-end: 15px;
  transform: translateY(-50%);
}

.btn-light-blue {
  border: none;
  background-color: #d8e2f8;
  block-size: 40px;
  color: #2b56ad;
  font-weight: 600;
}

.btn-light-blue.dropdown-toggle::after {
  display: none;
}

.filter-sort-group .btn {
  white-space: nowrap;
}

/* Property Cards */
.property-cards-area {
  margin-block-start: 1rem;
}

.property-list-card .list-cart-label span {
  position: relative;
  color: white;
  float: inline-end;
  font-size: 12px;
  font-weight: bold;
  inset-inline-end: -15px;
  padding-block: 7px;
  padding-inline: 30px 13px;
}

.property-list-card .list-cart-label span::before {
  position: absolute;
  block-size: 0;
  border-block-end: 15px solid transparent;
  border-block-start: 15px solid transparent;
  border-inline-start: 24px solid white;
  content: "";
  inline-size: 0;
  inset-block-start: 0;
  inset-inline-start: 0;
}

.property-list-card {
  position: relative;
  overflow: hidden;
  border: 1px solid #e9ecef;
  border-radius: 0.5rem;
  background-color: #fff;
  cursor: pointer;
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.property-list-card:hover {
  box-shadow: 0 4px 8px rgba(0, 0, 0, 5%);
  transform: translateY(-3px);
}

.list-cart-label {
  position: absolute;
  inset-block-start: 10px;
  inset-inline-end: 10px;
}

.list-cart-label span {
  border-radius: 0.25rem;
  color: #fff;
  font-size: 0.8rem;
  padding-block: 0.25rem;
  padding-inline: 0.75rem;
}

.list-card-body-cnt {
  display: flex;
  align-items: center;
  padding: 1rem;
  gap: 15px;
}

.property-img img {
  border: 2px solid #e9ecef;
  border-radius: 50%;
  block-size: 70px;
  inline-size: 70px;
  object-fit: cover;
}

.property-details {
  flex-grow: 1;
}

.property-details h4 {
  font-size: 1rem;
  font-weight: 600;
  margin-block-end: 0.25rem;
}

.property-details h6 {
  font-size: 0.875rem;
  margin-block-end: 0.5rem;
}

.property-details h5 {
  color: #dc3545; /* Price color from image */
  font-size: 1.1rem;
  font-weight: 700;
  margin-block-end: 0;
}

.property-details h5 em {
  color: #212529;
  font-style: normal;
}

.property-features-list {
  justify-content: start;
  padding: 1rem;
  margin: 0;
  background-color: #f8f9fa;
  border-block-start: 1px solid #e9ecef;
}

.property-features-list li {
  display: flex;
  align-items: center;
  color: #495057;
  font-size: 0.8rem;
  font-weight: 500;
}

.property-features-list li i {
  font-size: 0.9rem;
  margin-inline-end: 5px;
}

/* Footer Section */
.footer-area {
  border-block-start: 1px solid #e9ecef;
  margin-block-start: 2rem;
}
</style>
