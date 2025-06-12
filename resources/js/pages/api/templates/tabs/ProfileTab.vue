<template>
  <form @submit.prevent="updateLead">
    <input type="hidden" v-model="leadId" />
    <h4 class="mb-3">Profile</h4>

    <div class="row g-3">
      <!-- Left Column -->
      <div class="col-md-6">
        <label class="form-label">Name</label>
        <input type="text" v-model="user.name" class="form-control" required />
      </div>
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" v-model="user.email" class="form-control" required />
      </div>
      <div class="col-md-6">
        <label class="form-label">Phone</label>
        <input type="text" v-model="user.phone" class="form-control" required />
      </div>
      <div class="col-md-6">
        <label class="form-label">Latest Activity</label>
        <input type="text" v-model="user.latestActivity" class="form-control" />
      </div>
      <div class="col-md-6">
        <label class="form-label">Activity At</label>
        <input type="date" v-model="user.activityAt" class="form-control" />
      </div>
      <div class="col-md-6">
        <label class="form-label">Created On</label>
        <input type="date" v-model="user.createdOn" class="form-control" />
      </div>
      <div class="col-md-6">
        <label class="form-label">Tags</label>
        <input type="text" v-model="user.tags" class="form-control" />
      </div>
      <div class="col-md-6">
        <label class="form-label">Stage</label>
        <input type="text" v-model="user.stage" class="form-control" />
      </div>
      <div class="col-md-6">
        <label class="form-label">Latest Source</label>
        <input type="text" v-model="user.latestSource" class="form-control" />
      </div>
      <div class="col-md-6">
        <label class="form-label">Latest SMS</label>
        <input type="text" v-model="user.latestSMS" class="form-control" />
      </div>
      <div class="col-md-6">
        <label class="form-label">Latest Email</label>
        <input type="text" v-model="user.latestEmail" class="form-control" />
      </div>
      <div class="col-md-6">
        <label class="form-label">Next Task</label>
        <input type="text" v-model="user.nextTask" class="form-control" />
      </div>
      <div class="col-md-6">
        <label class="form-label">Next Appointment</label>
        <input type="date" v-model="user.nextAppointment" class="form-control" />
      </div>

      <!-- Checkboxes -->
      <div class="col-md-6 form-check">
        <input type="checkbox" v-model="user.newListingAlerts" class="form-check-input" />
        <label class="form-check-label">New Listing Alerts</label>
      </div>
      <div class="col-md-6 form-check">
        <input type="checkbox" v-model="user.neighbourhoodAlerts" class="form-check-input" />
        <label class="form-check-label">Neighbourhood Alerts</label>
      </div>
      <div class="col-md-6 form-check">
        <input type="checkbox" v-model="user.openHouseAlerts" class="form-check-input" />
        <label class="form-check-label">Open House Alerts</label>
      </div>
      <div class="col-md-6 form-check">
        <input type="checkbox" v-model="user.priceDropAlerts" class="form-check-input" />
        <label class="form-check-label">Price Drop Alerts</label>
      </div>

      <!-- More Text Inputs -->
      <div class="col-md-6">
        <label class="form-label">Active Action Plans</label>
        <input type="text" v-model="user.activeActionPlans" class="form-control" />
      </div>
      <div class="col-md-6">
        <label class="form-label">Assigned Market Updates</label>
        <input type="text" v-model="user.assignedMarketUpdates" class="form-control" />
      </div>
      <div class="col-md-6">
        <label class="form-label">Assigned Newsletters</label>
        <input type="text" v-model="user.assignedNewsletters" class="form-control" />
      </div>

      <!-- Textarea Fields -->
      <div class="col-md-6">
        <label class="form-label">Notes</label>
        <textarea v-model="user.notes" class="form-control"></textarea>
      </div>
      <div class="col-md-6">
        <label class="form-label">Background</label>
        <textarea v-model="user.background" class="form-control"></textarea>
      </div>

      <!-- Date of Birth -->
      <div class="col-md-6">
        <label class="form-label">Date of Birth</label>
        <input type="date" v-model="user.dob" class="form-control" />
      </div>

      <!-- Address -->
      <div class="col-md-6">
        <label class="form-label">House Number</label>
        <input type="text" v-model="user.house_number" class="form-control" required />
      </div>
      <div class="col-md-6">
        <label class="form-label">Street</label>
        <input type="text" v-model="user.street" class="form-control" required />
      </div>
      <div class="col-md-6">
        <label class="form-label">City</label>
        <input type="text" v-model="user.city" class="form-control" required />
      </div>
      <div class="col-md-6">
        <label class="form-label">Province</label>
        <input type="text" v-model="user.province" class="form-control" required />
      </div>
      <div class="col-md-6">
        <label class="form-label">Zip Code</label>
        <input type="text" v-model="user.zip_code" class="form-control" required />
      </div>

      <!-- Social Media -->
      <div class="col-md-6">
        <label class="form-label">Facebook</label>
        <input type="url" v-model="user.facebook" class="form-control" />
      </div>
      <div class="col-md-6">
        <label class="form-label">Instagram</label>
        <input type="url" v-model="user.instagram" class="form-control" />
      </div>
      <div class="col-md-6">
        <label class="form-label">LinkedIn</label>
        <input type="url" v-model="user.linkedin" class="form-control" />
      </div>
      <div class="col-md-6">
        <label class="form-label">WhatsApp</label>
        <input type="text" v-model="user.whatsapp" class="form-control" />
      </div>
      <div class="col-md-6">
        <label class="form-label">Twitter</label>
        <input type="url" v-model="user.twitter" class="form-control" />
      </div>

      <!-- Submit -->
      <div class="col-12">
        <button type="submit" class="btn btn-primary mt-3">Update</button>
      </div>
    </div>
  </form>
</template>


<script setup>
import { reactive, ref } from 'vue'

const leadId = ref('123') 
const user = reactive({
  name: '',
  email: '',
  phone: '',
  latestActivity: '',
  activityAt: '',
  createdOn: '',
  tags: '',
  stage: '',
  latestSource: '',
  latestSMS: '',
  latestEmail: '',
  nextTask: '',
  nextAppointment: '',
  newListingAlerts: false,
  neighbourhoodAlerts: false,
  openHouseAlerts: false,
  priceDropAlerts: false,
  activeActionPlans: '',
  assignedMarketUpdates: '',
  assignedNewsletters: '',
  notes: '',
  dob: '',
  background: '',
  city: '',
  facebook: '',
  instagram: '',
  linkedin: '',
  whatsapp: '',
  twitter: '',
  house_number: '',
  street: '',
  province: '',
  zip_code: ''
})

const leftFields = {
  name: { label: 'Name', type: 'input', attrs: { required: true } },
  email: { label: 'Email', type: 'input', attrs: { type: 'email', required: true } },
  phone: { label: 'Phone', type: 'input', attrs: { required: true } },
  latestActivity: { label: 'Latest Activity', type: 'input' },
  activityAt: { label: 'Activity At', type: 'input', attrs: { type: 'date' } },
  createdOn: { label: 'Created On', type: 'input', attrs: { type: 'date' } },
  tags: { label: 'Tags', type: 'input' },
  stage: { label: 'Stage', type: 'input' },
  latestSource: { label: 'Latest Source', type: 'input' },
  latestSMS: { label: 'Latest SMS', type: 'input' },
  latestEmail: { label: 'Latest Email', type: 'input' },
  nextTask: { label: 'Next Task', type: 'input' },
  nextAppointment: { label: 'Next Appointment', type: 'input', attrs: { type: 'date' } },
  house_number: { label: 'House Number', type: 'input', attrs: { required: true } },
  street: { label: 'Street', type: 'input', attrs: { required: true } },
  city: { label: 'City', type: 'input', attrs: { required: true } },
  province: { label: 'Province', type: 'input', attrs: { required: true } },
  zip_code: { label: 'Zip Code', type: 'input', attrs: { required: true } },
}

const rightFields = {
  newListingAlerts: { label: 'New Listing Alerts', type: 'checkbox' },
  neighbourhoodAlerts: { label: 'Neighbourhood Alerts', type: 'checkbox' },
  openHouseAlerts: { label: 'Open House Alerts', type: 'checkbox' },
  priceDropAlerts: { label: 'Price Drop Alerts', type: 'checkbox' },
  activeActionPlans: { label: 'Active Action Plans', type: 'input' },
  assignedMarketUpdates: { label: 'Assigned Market Updates', type: 'input' },
  assignedNewsletters: { label: 'Assigned Newsletters', type: 'input' },
  notes: { label: 'Notes', type: 'textarea' },
  dob: { label: 'Date of Birth', type: 'input', attrs: { type: 'date' } },
  background: { label: 'Background', type: 'textarea' },
  facebook: { label: 'Facebook', type: 'input', attrs: { type: 'url' } },
  instagram: { label: 'Instagram', type: 'input', attrs: { type: 'url' } },
  linkedin: { label: 'LinkedIn', type: 'input', attrs: { type: 'url' } },
  whatsapp: { label: 'WhatsApp', type: 'input' },
  twitter: { label: 'Twitter', type: 'input', attrs: { type: 'url' } },
}
  
function updateLead() {
  console.log('Lead data:', user)
  alert('Lead Updated Successfully!')
}
</script>

<style scoped>
textarea.form-control {
  resize: vertical;
}
</style>
