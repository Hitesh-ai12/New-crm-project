<template>
  <div class="modal fade" id="propertyDetailsModal" tabindex="-1" aria-labelledby="propertyDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <div class="d-flex w-100 justify-content-between align-items-center">
            <h5 class="modal-title" id="propertyDetailsModalLabel">Property Details</h5>
            <div class="header-actions">
              <button class="btn btn-sm btn-primary"><i class="fa-solid fa-plus"></i> Add to Selection</button>
              <button class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-envelope"></i> Email to</button>
              <button class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-comment-sms"></i> SMS to</button>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
        </div>
        <div class="modal-body">
          <div v-if="propertyStore.detailsLoading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
          <div v-else-if="!propertyStore.selectedPropertyDetails" class="text-center py-5">
            <p class="text-muted">Property details not available.</p>
          </div>
          <div v-else>
            <div id="propertySlider" class="carousel slide mb-4" data-bs-ride="carousel">
              <div class="carousel-indicators">
                <button 
                  v-for="(imgNum, index) in propertyStore.selectedPropertyDetails.images.images" 
                  :key="index"
                  type="button" 
                  :data-bs-target="'#propertySlider'" 
                  :data-bs-slide-to="index"
                  :class="{ active: index === 0 }" 
                  :aria-current="index === 0 ? 'true' : null"
                  :aria-label="'Slide ' + (index + 1)">
                </button>
              </div>

              <div class="carousel-inner rounded-3">
                <div v-for="(imgNum, index) in propertyStore.selectedPropertyDetails.images.images" :key="index" class="carousel-item" :class="{ active: index === 0 }">
                  <img :src="getImageUrl(propertyStore.selectedPropertyDetails.images, imgNum)" class="d-block w-100" alt="Property Image">
                </div>
              </div>
              <button class="carousel-control-prev" type="button" data-bs-target="#propertySlider" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#propertySlider" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
              </button>
            </div>

            <div class="property-overview mb-4">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                  <span class="badge bg-success">{{ propertyStore.selectedPropertyDetails.common.sale_lease }}</span>
                  <span class="text-muted small">Added {{ propertyStore.selectedPropertyDetails.common.added_ts }}</span>
                  <span class="text-muted small">{{ propertyStore.selectedPropertyDetails.common.property_type }}</span>
                </div>
                <div class="text-end">
                  <h4 class="mb-0 text-primary fw-bold">{{ formatPrice(propertyStore.selectedPropertyDetails.common.listed_price) }}</h4>
                </div>
              </div>
              <h3 class="fw-bold">{{ propertyStore.selectedPropertyDetails.common.address.addr }}</h3>
              <div class="d-flex align-items-center gap-3 text-muted mb-3">
                <span><i class="fa-solid fa-bed me-1"></i> {{ propertyStore.selectedPropertyDetails.common.bed }}+{{ propertyStore.selectedPropertyDetails.common.bed_plus }} Beds</span>
                <span><i class="fa-solid fa-bath me-1"></i> {{ propertyStore.selectedPropertyDetails.common.bath }} Baths</span>
                <span><i class="fa-solid fa-car me-1"></i> {{ propertyStore.selectedPropertyDetails.common.garage_space || 'N/A' }} Garages</span>
              </div>
              <hr>
            </div>
             <div class="row details-section mb-4">
              <div class="col-md-6 mb-3">
                <div class="card p-3 h-100">
                  <h5 class="fw-bold mb-3">Key Facts</h5>
                  <div class="row">
                    <div v-for="(fact, index) in propertyStore.selectedPropertyDetails.facts" :key="index" class="col-sm-6 mb-2">
                      <p class="mb-0 small text-muted">{{ fact.key }}</p>
                      <p class="mb-0 fw-bold">{{ fact.value }}</p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <div class="card p-3 h-100">
                  <h5 class="fw-bold mb-3">Description</h5>
                  <p>{{ propertyStore.selectedPropertyDetails.description[0].value }}</p>
                </div>
              </div>
            </div>

            <div class="row details-section mb-4">
              <div class="col-md-4 mb-3" v-for="(section, index) in propertyStore.selectedPropertyDetails.details" :key="index">
                <div class="card p-3 h-100">
                  <h5 class="fw-bold mb-3">{{ section.key }}</h5>
                  <div v-for="(detail, i) in section.value" :key="i" class="d-flex justify-content-between mb-1">
                    <span class="text-muted small">{{ detail.key }}:</span>
                    <span class="fw-bold">{{ detail.value }}</span>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="propertyStore.selectedPropertyDetails.rooms && propertyStore.selectedPropertyDetails.rooms.length > 0" class="card p-3 mb-4">
              <h5 class="fw-bold mb-3">Rooms</h5>
              <div v-for="(room, index) in limitedRooms" :key="index" class="room-item p-2 mb-2 border rounded">
                <p class="mb-1 fw-bold">{{ room.type }} <span class="text-muted small">({{ room.dimension }})</span></p>
                <p class="mb-0 small">{{ room.description }}</p>
                <span class="badge bg-light text-dark">{{ room.level }}</span>
              </div>
              <div v-if="propertyStore.selectedPropertyDetails.rooms.length > 2" class="text-center mt-3">
                <button @click="toggleAllRooms" class="btn btn-link">
                  <span v-if="!showAllRooms">View more</span>
                  <span v-else>View less</span>
                </button>
              </div>
            </div>

            <div v-if="propertyStore.selectedPropertyDetails.histories && propertyStore.selectedPropertyDetails.histories.length > 0" class="card p-3 mb-4">
              <h5 class="fw-bold mb-3">Listing History</h5>
              <div v-for="(history, index) in propertyStore.selectedPropertyDetails.histories" :key="index" class="history-item p-2 mb-2 border rounded">
                <p class="mb-0 fw-bold">{{ formatPrice(history.price) }} <span class="badge bg-secondary">{{ history.status }}</span></p>
                <p class="mb-0 small text-muted">List: {{ formatDate(history.date) }}</p>
                <p class="mb-0 small text-muted">Listed Brokerage: {{ history.brokerage }}</p>
              </div>
            </div>

            <div v-if="propertyStore.selectedPropertyDetails.similar_properties && propertyStore.selectedPropertyDetails.similar_properties.length > 0" class="similar-properties-section">
              <h5 class="fw-bold mb-3">Similar Properties</h5>
              <div class="similar-properties-slider">
                <div class="d-flex overflow-auto gap-3 pb-3">
                  <div v-for="(similarProp, index) in propertyStore.selectedPropertyDetails.similar_properties" :key="index" class="similar-card card flex-shrink-0" style="inline-size: 250px;">
                    <img :src="getImageUrl(similarProp.images, similarProp.images.images)" class="card-img-top" alt="Similar Property">
                    <div class="card-body">
                      <h6 class="card-title">{{ similarProp.addr }}</h6>
                      <p class="card-text small text-muted">{{ similarProp.city }}</p>
                      <span class="fw-bold text-primary">{{ formatPrice(similarProp.listed_price) }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { usePropertyStore } from '@/pages/listings/store/propertyStore';
import '@fortawesome/fontawesome-free/css/all.css';
import 'bootstrap';
import { Modal } from 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import { computed, ref } from 'vue';

const propertyStore = usePropertyStore();
const showAllRooms = ref(false);

const show = () => {
  const modalElement = document.getElementById('propertyDetailsModal');
  if (modalElement) {
    const modal = new Modal(modalElement);
    modal.show();
  }
};
defineExpose({ show });

const getImageUrl = (images, imageNumber) => {
  if (images && images.directory && images.ml_num) {
    // Sahi URL format: directory + Photo + ml_num + - + imageNumber + .jpeg
    // Ex: https://cdn.agentroof.com/trreb/condo/PhotoW12343397-1.jpeg
    return `${images.directory}Photo${images.ml_num}-${imageNumber}.jpeg`;
  }
  return 'https://placehold.co/100x100';
};
const formatPrice = (price) => {
  return price ? `$${price.toLocaleString()}` : 'N/A';
};
const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return date.toLocaleDateString();
};

const toggleAllRooms = () => {
  showAllRooms.value = !showAllRooms.value;
};

const limitedRooms = computed(() => {
  if (!propertyStore.selectedPropertyDetails || !propertyStore.selectedPropertyDetails.rooms) {
    return [];
  }
  if (showAllRooms.value) {
    return propertyStore.selectedPropertyDetails.rooms;
  }
  return propertyStore.selectedPropertyDetails.rooms.slice(0, 2);
});
</script>

<style scoped>
.modal-dialog {
  inline-size: 90vw;
  max-inline-size: 90vw;
}

.header-actions .btn {
  font-size: 0.8rem;
  padding-block: 0.3rem;
  padding-inline: 0.6rem;
}

.similar-properties-slider {
  overflow-x: auto;
  white-space: nowrap;
}

.similar-properties-slider::-webkit-scrollbar {
  block-size: 8px;
}

.similar-properties-slider::-webkit-scrollbar-thumb {
  border-radius: 4px;
  background: #ccc;
}

.room-item,
.history-item {
  background-color: #f8f9fa;
}

.badge.bg-light.text-dark {
  background-color: #e9ecef !important;
  color: #343a40 !important;
}
</style>
