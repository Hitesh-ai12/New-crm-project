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
      <!-- <div class="col-md-6">
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
      </div> -->
      <div class="col-md-6">
        <label class="form-label">Tags</label>
        <input type="text" v-model="user.tags" class="form-control" />
      </div>
      <div class="col-md-6">
        <label class="form-label">Stage</label>
        <input type="text" v-model="user.stage" class="form-control" />
      </div>
      <!-- <div class="col-md-6">
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
      </div> -->
      <!-- <div class="col-md-6">
        <label class="form-label">Next Task</label>
        <input type="text" v-model="user.nextTask" class="form-control" />
      </div> -->
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
import axios from 'axios'
import { onMounted, reactive, ref } from 'vue'

// Auth token setup
const token = localStorage.getItem('auth_token')
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
}

const leadId = ref(null)

// Form model
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

// ✅ Payload mapping (frontend → backend)
const mapToBackendPayload = () => ({
  first_name: user.name.split(' ')[0],
  last_name: user.name.split(' ').slice(1).join(' ') || null,
  email: user.email,
  phone: user.phone,
  tag: user.tags,
  stage: user.stage,
  new_listing_alert: user.newListingAlerts ? '1' : '0',
  neighbourhood_alert: user.neighbourhoodAlerts ? '1' : '0',
  open_house_alert: user.openHouseAlerts ? '1' : '0',
  price_drop_alert: user.priceDropAlerts ? '1' : '0',
  action_plans: user.activeActionPlans,
  real_estate_newsletter: user.assignedNewsletters,
  market_updates: user.assignedMarketUpdates,
  background: user.background,
  notes: user.notes,
  dob: user.dob,
  city: user.city,
  house_number: user.house_number,
  street: user.street,
  province: user.province,
  zip_code: user.zip_code,
  facebook: user.facebook,
  instagram: user.instagram,
  linkedin: user.linkedin,
  whatsapp: user.whatsapp,
  twitter: user.twitter
})

// ✅ PUT request to update lead
const updateLead = async () => {
  try {
    const payload = mapToBackendPayload()
    const token = localStorage.getItem('auth_token')

    const response = await axios.put(`/api/leads/${leadId.value}`, payload, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })

    alert('Lead updated successfully!')
    console.log(response.data)
  } catch (error) {
    if (error.response?.data?.errors) {
      alert('Validation failed: ' + JSON.stringify(error.response.data.errors))
    } else {
      alert('Update failed: ' + error.message)
    }
  }
}

// ✅ Pre-fill form from backend
const loadLead = async (id) => {
  try {
    const { data } = await axios.get(`/api/leads/${id}`)
    leadId.value = data.id
    user.name = `${data.first_name || ''} ${data.last_name || ''}`.trim()
    user.email = data.email
    user.phone = data.phone
    user.tags = data.tag
    user.stage = data.stage
    user.newListingAlerts = data.new_listing_alert === '1'
    user.neighbourhoodAlerts = data.neighbourhood_alert === '1'
    user.openHouseAlerts = data.open_house_alert === '1'
    user.priceDropAlerts = data.price_drop_alert === '1'
    user.activeActionPlans = data.action_plans
    user.assignedMarketUpdates = data.market_updates
    user.assignedNewsletters = data.real_estate_newsletter
    user.background = data.background
    user.notes = data.notes
    user.dob = data.dob
    user.city = data.city
    user.house_number = data.house_number
    user.street = data.street
    user.province = data.province
    user.zip_code = data.zip_code
    user.facebook = data.facebook
    user.instagram = data.instagram
    user.linkedin = data.linkedin
    user.whatsapp = data.whatsapp
    user.twitter = data.twitter
  } catch (err) {
    console.error(err)
  }
}

// Load lead on mount
onMounted(() => {
  loadLead(1) // Replace with dynamic ID if needed
})
</script>


<style scoped>
textarea.form-control {
  resize: vertical;
}
</style>
