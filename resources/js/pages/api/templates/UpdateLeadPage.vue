<template>
  <div class="lead-management">
    <!-- Header -->
    <div class="header">
      <div class="contact-info">
        <div class="avatar">{{ firstLetter }}</div>
        <div class="details">
          <h2>{{ contact.first_name }}</h2>
          <div class="tags">
            <span class="tag stage">
              <i class="fas fa-chart-line"></i> {{ contact.stage }}
            </span>
            <span class="tag source">
              <i class="fas fa-globe"></i> {{ contact.source }}
            </span>
            <span class="tag custom">
              <i class="fas fa-tag"></i> {{ contact.customTag }}
            </span>
          </div>
        </div>
      </div>
      <div class="actions">
        <button class="btn danger"><i class="fas fa-microphone"></i></button>
        <button class="btn danger"><i class="fas fa-times"></i></button>
      </div>
    </div>

    <!-- Navigation -->
    <div class="navigation">
      <button :class="['nav-btn', activeTab === 'timeline' ? 'active' : '']" @click="activeTab = 'timeline'">Timeline</button>
      <button :class="['nav-btn', activeTab === 'profile' ? 'active' : '']" @click="activeTab = 'profile'">Profile</button>
      <button :class="['nav-btn', activeTab === 'analytics' ? 'active' : '']" @click="activeTab = 'analytics'">Analytics</button>
    </div>

    <!-- Sidebar (Visible only on 'timeline' tab) -->
    <div v-if="activeTab === 'timeline'" class="container">
      <div class="sidebar">
        <div class="menu-item" :class="{ active: activeMenuItem === 'all' }" @click="activeMenuItem = 'all'">
          <div class="icon"><i class="fas fa-headset"></i></div>
          <div class="label">ALL</div>
        </div>
        <div class="menu-item" :class="{ active: activeMenuItem === 'sms' }" @click="activeMenuItem = 'sms'">
          <div class="icon"><i class="fas fa-comment"></i></div>
          <div class="label">SMS</div>
        </div>
        <div class="menu-item" :class="{ active: activeMenuItem === 'email' }" @click="activeMenuItem = 'email'">
          <div class="icon"><i class="fas fa-envelope"></i></div>
          <div class="label">Email</div>
        </div>
        <div class="menu-item" :class="{ active: activeMenuItem === 'notes' }" @click="activeMenuItem = 'notes'">
          <div class="icon"><i class="fas fa-sticky-note"></i></div>
          <div class="label">Notes</div>
        </div>
        <div class="menu-item" :class="{ active: activeMenuItem === 'callLog' }" @click="activeMenuItem = 'callLog'">
          <div class="icon"><i class="fas fa-phone"></i></div>
          <div class="label">Call Log</div>
        </div>
        <div class="menu-item" :class="{ active: activeMenuItem === 'webActivity' }" @click="activeMenuItem = 'webActivity'">
          <div class="icon"><i class="fas fa-globe"></i></div>
          <div class="label">WEB Activity</div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="main-content">
        <div class="content-header">
          <h3>Timeline</h3>
        </div>

        <!-- Timeline Content (shown when 'timeline' tab is active) -->
        <div class="activities">
          <div v-for="(activity, index) in activities" :key="index" class="activity-item">
            <div class="activity-icon">
              <i class="fas fa-globe"></i>
            </div>
            <div class="activity-details">
              <h4>{{ activity.title }}</h4>
              <p>{{ activity.description }}</p>
              <div class="activity-time">{{ activity.date }} {{ activity.time }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Profile Content (shown when 'profile' tab is active) -->
    <div v-if="activeTab === 'profile'" class="container">
      <div class="main-content">
        <h3>Profile</h3>
        <form @submit.prevent="updateUser">
      <div class="form-grid">
        <input type="hidden" v-model="leadId" />

        <!-- Left Column -->
        <div class="form-group">
          <label>Name:</label>
          <input type="text" v-model="user.name" required />
        </div>
        <div class="form-group">
          <label>Email:</label>
          <input type="email" v-model="user.email" required />
        </div>
        <div class="form-group">
          <label>Phone:</label>
          <input type="text" v-model="user.phone" required />
        </div>
        <div class="form-group">
          <label>Latest Activity:</label>
          <input type="text" v-model="user.latestActivity" />
        </div>
        <div class="form-group">
          <label>Activity At:</label>
          <input type="date" v-model="user.activityAt" />
        </div>
        <div class="form-group">
          <label>Created On:</label>
          <input type="date" v-model="user.createdOn" />
        </div>
        <div class="form-group">
          <label>Tags:</label>
          <input type="text" v-model="user.tags" />
        </div>
        <div class="form-group">
          <label>Stage:</label>
          <input type="text" v-model="user.stage" />
        </div>
        <div class="form-group">
          <label>Latest Source:</label>
          <input type="text" v-model="user.latestSource" />
        </div>

        <!-- Right Column -->
        <div class="form-group">
          <label>Latest SMS:</label>
          <input type="text" v-model="user.latestSMS" />
        </div>
        <div class="form-group">
          <label>Latest Email:</label>
          <input type="text" v-model="user.latestEmail" />
        </div>
        <div class="form-group">
          <label>Next Task:</label>
          <input type="text" v-model="user.nextTask" />
        </div>
        <div class="form-group">
          <label>Next Appointment:</label>
          <input type="date" v-model="user.nextAppointment" />
        </div>
        <div class="form-group">
          <label>New Listing Alerts:</label>
          <input type="checkbox" v-model="user.newListingAlerts" />
        </div>
        <div class="form-group">
          <label>Neighbourhood Alerts:</label>
          <input type="checkbox" v-model="user.neighbourhoodAlerts" />
        </div>
        <div class="form-group">
          <label>Open House Alerts:</label>
          <input type="checkbox" v-model="user.openHouseAlerts" />
        </div>
        <div class="form-group">
          <label>Price Drop Alerts:</label>
          <input type="checkbox" v-model="user.priceDropAlerts" />
        </div>
        <div class="form-group">
          <label>Active Action Plans:</label>
          <input type="text" v-model="user.activeActionPlans" />
        </div>
        <div class="form-group">
          <label>Assigned Market Updates:</label>
          <input type="text" v-model="user.assignedMarketUpdates" />
        </div>
        <div class="form-group">
          <label>Assigned Newsletters:</label>
          <input type="text" v-model="user.assignedNewsletters" />
        </div>
        <div class="form-group">
          <label>Notes:</label>
          <textarea v-model="user.notes"></textarea>
        </div>
        <div class="form-group">
          <label>Date of Birth:</label>
          <input type="date" v-model="user.dob" />
        </div>
        <div class="form-group">
          <label>Background:</label>
          <textarea v-model="user.background"></textarea>
        </div>
        <div class="form-group">
          <label>City:</label>
          <input type="text" v-model="user.city" />
        </div>

        <!-- Social Media Links -->
        <div class="form-group">
          <label>Facebook:</label>
          <input type="url" v-model="user.facebook" />
        </div>
        <div class="form-group">
          <label>Instagram:</label>
          <input type="url" v-model="user.instagram" />
        </div>
        <div class="form-group">
          <label>LinkedIn:</label>
          <input type="url" v-model="user.linkedin" />
        </div>
        <div class="form-group">
          <label>WhatsApp:</label>
          <input type="text" v-model="user.whatsapp" />
        </div>
        <div class="form-group">
          <label>Twitter:</label>
          <input type="url" v-model="user.twitter" />
        </div>
        <div class="form-group">
          <label>House Number:</label>
          <input type="text" v-model="user.house_number" required />
        </div>

        <div class="form-group">
          <label>Street:</label>
          <input type="text" v-model="user.street" required />
        </div>

        <div class="form-group">
          <label>City:</label>
          <input type="text" v-model="user.city" required />
        </div>

        <div class="form-group">
          <label>Province:</label>
          <input type="text" v-model="user.province" required />
        </div>

        <div class="form-group">
          <label>Zip Code:</label>
          <input type="text" v-model="user.zip_code" required />
        </div>

      </div>

      <button type="submit" class="btn-submit" @click="updateLead">Update</button>

    </form>
      </div>
    </div>

    <!-- Analytics Content (shown when 'analytics' tab is active) -->
    <div v-if="activeTab === 'analytics'" class="analytics-container">
      <div class="sidebar">
        <ul>
          <li v-bind:class="{ 'active': selectedTab === 'for-sale' }" @click="selectedTab = 'for-sale'">For Sale</li>
          <li v-bind:class="{ 'active': selectedTab === 'sold' }" @click="selectedTab = 'sold'">Sold</li>
          <li v-bind:class="{ 'active': selectedTab === 'rent' }" @click="selectedTab = 'rent'">Rent</li>
          <li v-bind:class="{ 'active': selectedTab === 'leased' }" @click="selectedTab = 'leased'">Leased</li>
          <li v-bind:class="{ 'active': selectedTab === 'pre-construction' }" @click="selectedTab = 'pre-construction'">Pre-Construction</li>
        </ul>
      </div>

      <div class="main-content">
        <div class="analytics-content">
          <div v-if="selectedTab === 'for-sale'">
            <h4>For Sale Analytics Content</h4>
          </div>
          <div v-if="selectedTab === 'sold'">
            <h4>Sold Analytics Content</h4>
          </div>
          <div v-if="selectedTab === 'rent'">
            <h4>Rent Analytics Content</h4>
          </div>
          <div v-if="selectedTab === 'leased'">
            <h4>Leased Analytics Content</h4>
          </div>
          <div v-if="selectedTab === 'pre-construction'">
            <h4>Pre-Construction Analytics Content</h4>
          </div>
        </div>
      </div>
    </div>


    <!-- Footer -->
    <div class="footer">
      <div class="copyright">
        Copyright © 2025 <a href="#">Agentroof</a>. All rights reserved.
      </div>
    </div>

    <!-- Floating Action Button -->
    <div class="fab">
      <button class="fab-btn"><i class="fas fa-plus"></i></button>
    </div>

    <!-- Floating Action Button -->
<div class="fab">
  <button class="fab-btn" @click="toggleActions">
    <i class="fas fa-times" v-if="showActions"></i>
    <i class="fas fa-plus" v-else></i>
  </button>

  <transition-group name="fade" tag="div">
    <div v-if="showActions" class="fab-actions" key="actions">
      <button class="fab-action" title="Call Log"><i class="fas fa-phone"></i></button>
      <button class="fab-action" title="SMS From Smart Number"><i class="fas fa-comment-dots"></i></button>
      <button class="fab-action" title="Send Email"><i class="fas fa-envelope"></i></button>
      <button class="fab-action" title="Add Note"><i class="fas fa-clipboard"></i></button>
      <button class="fab-action" title="Add Action Plan"><i class="fas fa-project-diagram"></i></button>
      <button class="fab-action" title="Add Task"><i class="fas fa-tasks"></i></button>
      <button class="fab-action" title="Lead Transfer"><i class="fas fa-random"></i></button>
    </div>
  </transition-group>
</div>

  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      activeTab: 'timeline',
      activeMenuItem: 'all', 
      leadId: null, 
      contact: {
        first_name: '',
        last_name: '',
        email: '',
        phone: '',
        stage: '',
        source: '',
        customTag: '',
      },
      user: { 
        name: '',
        email: '',
        phone: '',
        stage: '',
        tags: '',
        city: '',
        background: '',
        house_number: '',
        street: '',
        province: '',
        zip_code: ''
      }
    };
  },

  methods: {
    setActiveTab(tabName) {
      this.activeTab = tabName;
    },

    async fetchLead(leadId) {
      console.log("Fetching lead for ID:", leadId);

      try {
        const authToken = localStorage.getItem('auth_token');
        if (!authToken) throw new Error('No authentication token found.');

        const response = await axios.get(`http://127.0.0.1:8000/api/leads/${leadId}`, {
          headers: { Authorization: `Bearer ${authToken}`, Accept: 'application/json' }
        });

        const lead = response.data;
        if (!lead || !lead.id) throw new Error("Invalid lead data received");

        this.contact = {
          first_name: lead.first_name || '',
          last_name: lead.last_name || '',
          email: lead.email || '',
          phone: lead.phone || '',
          stage: lead.stage || '',
          source: lead.source || '',
          customTag: lead.tag || ''
        };

        this.user = {
          name: `${lead.first_name || ''} ${lead.last_name || ''}`.trim(),
          email: lead.email || '',
          phone: lead.phone || '',
          stage: lead.stage || '',
          tags: lead.tag || '',
          city: lead.city || '',
          background: lead.background || '',
          house_number: lead.house_number || '',
          street: lead.street || '',
          province: lead.province || '',
          zip_code: lead.zip_code || ''
        };

        this.leadId = lead.id;

      } catch (error) {
        console.error("Error fetching lead:", error.response?.data || error.message);
      }
    },

    async updateLead() {
      if (!this.leadId) {
        alert("Lead ID is missing!");
        return;
      }

      try {
        const authToken = localStorage.getItem('auth_token');
        if (!authToken) throw new Error('No authentication token found.');

        const nameParts = this.user.name.trim().split(" ");
        const firstName = nameParts[0] || '';
        const lastName = nameParts.slice(1).join(" ") || '';

        const response = await axios.put(`http://127.0.0.1:8000/api/leads/${this.leadId}`, {
          first_name: firstName,
          last_name: lastName,
          email: this.user.email,
          phone: this.user.phone,
          stage: this.user.stage,
          tag: this.user.tags,
          city: this.user.city,
          background: this.user.background,
          house_number: this.user.house_number,
          street: this.user.street,
          province: this.user.province,
          zip_code: this.user.zip_code
        }, {
          headers: { Authorization: `Bearer ${authToken}`, Accept: 'application/json' }
        });

        console.log("Lead updated successfully:", response.data);
        alert("Lead updated successfully!");

      } catch (error) {
        console.error("Update failed:", error.response?.data || error.message);
        alert("Failed to update lead.");
      }
    }
  },

  mounted() {
    const leadIdFromRoute = this.$route.params.id;
    if (leadIdFromRoute) {
      this.fetchLead(leadIdFromRoute);
    } else {
      console.error("Lead ID is missing in route!");
    }
  }
};
</script>





<style scoped>
.fab {
  position: fixed;
  inset-block-end: 20px;
  inset-inline-end: 20px;
}

.fab-btn {
  border: none;
  border-radius: 50%;
  background-color: red;
  block-size: 60px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 30%);
  color: white;
  cursor: pointer;
  font-size: 24px;
  inline-size: 60px;
}

.fab-actions {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 10px;
  margin-block-end: 10px;
  margin-inline-end: 10px;
}

.fab-action {
  display: flex;
  align-items: center;
  border: none;
  border-radius: 50px;
  background-color: white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 20%);
  color: #333;
  cursor: pointer;
  font-size: 16px;
  gap: 8px;
  padding-block: 8px;
  padding-inline: 12px;
}

.fab-action i {
  font-size: 18px;
}

.fade-enter-active,
 .fade-leave-active {
  transition: opacity 0.3s;
}

.fade-enter-from,
 .fade-leave-to {
  opacity: 0;
}

.lead-management {
  display: flex;
  flex-direction: column;
  background-color: #f5f5f5;
  block-size: 100vh;
  font-family: Arial, sans-serif;
}

.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 35px;
  background-color: white;
  border-block-end: 1px solid #e0e0e0;
}

.contact-info {
  display: flex;
  align-items: center;
}

.avatar {
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
  background-color: #8e56f3;
  block-size: 54px;
  color: white;
  font-size: 32px;
  font-weight: bold;
  inline-size: 50px;
  margin-inline-end: 15px;
}

.details h2 {
  margin: 0;
  font-size: 18px;
}

.tags {
  display: flex;
  margin-block-start: 5px;
}

.tag {
  display: flex;
  align-items: center;
  border-radius: 3px;
  font-size: 12px;
  margin-inline-end: 10px;
  padding-block: 2px;
  padding-inline: 8px;
}

.tag i {
  margin-inline-end: 5px;
}

.stage {
  border: 1px solid #e0e0e0;
  background-color: #f0f0f0;
}

.source {
  border: 1px solid #f8bbd0;
  background-color: #fce4ec;
  color: #8e56f3;
}

.custom {
  border: 1px solid #bbdefb;
  background-color: #e3f2fd;
  color: #2196f3;
}

.actions {
  display: flex;
}

.btn {
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  border-radius: 3px;
  block-size: 36px;
  cursor: pointer;
  inline-size: 36px;
  margin-inline-start: 10px;
}

.danger {
  background-color: #8e56f3;
  color: white;
}

.navigation {
  display: flex;
  margin: 2px;
  background-color: white;
  border-block-end: 1px solid #e0e0e0;
}

.nav-btn {
  position: relative;
  border: none;
  background: none;
  color: #666;
  cursor: pointer;
  font-weight: bold;
  padding-block: 10px;
  padding-inline: 15px;
}

.nav-btn.active {
  color: #8e56f3;
}

.nav-btn.active::after {
  position: absolute;
  background-color: #8e56f3;
  block-size: 2px;
  content: "";
  inline-size: 100%;
  inset-block-end: 0;
  inset-inline-start: 0;
}

.container {
  display: flex;
  overflow: hidden;
  flex: 1;
}

.sidebar {
  background-color: #f8f8f8;
  border-inline-end: 1px solid #e0e0e0;
  inline-size: 250px;
}

.menu-item {
  display: flex;
  align-items: center;
  padding: 15px;
  border-block-end: 1px solid #f0f0f0;
  cursor: pointer;
}

.menu-item:hover {
  background-color: #f0f0f0;
}

.menu-item.active {
  background-color: #e3f2fd;
  border-inline-start: 3px solid #2196f3;
}

.icon {
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background-color: #e0e0e0;
  block-size: 24px;
  inline-size: 24px;
  margin-inline-end: 10px;
}

.main-content {
  flex: 1;
  padding: 15px;
  overflow-y: auto;
}

.content-header {
  margin-block-end: 15px;
}

.content-header h3 {
  margin: 0;
  font-weight: normal;
}

.activities {
  border-radius: 3px;
  background-color: white;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 10%);
}

.activity-item {
  display: flex;
  padding: 15px;
  border-block-end: 1px solid #f0f0f0;
}

.activity-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background-color: #f0f0f0;
  block-size: 36px;
  inline-size: 36px;
  margin-inline-end: 15px;
}

.activity-details h4 {
  margin: 0;
  font-size: 16px;
}

.activity-details p {
  color: #666;
  margin-block: 5px;
  margin-inline: 0;
}

.activity-time {
  color: #999;
  font-size: 12px;
  margin-block-start: 5px;
}

.footer {
  background-color: white;
  border-block-start: 1px solid #e0e0e0;
  color: #666;
  font-size: 12px;
  padding-block: 10px;
  padding-inline: 15px;
  text-align: center;
}

.fab {
  position: fixed;
  inset-block-end: 30px;
  inset-inline-end: 30px;
}

.fab-btn {
  border: none;
  border-radius: 50%;
  background-color: #e40000;
  block-size: 56px;
  box-shadow: 0 3px 5px rgba(0, 0, 0, 20%);
  color: white;
  cursor: pointer;
  font-size: 24px;
  inline-size: 56px;
}

/* Form Container */
.user-update-form {
  padding: 20px;
  border-radius: 10px;
  margin: auto;
  background: white;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 10%);
  font-family: Arial, sans-serif;
  max-inline-size: 900px;
}

/* Form Title */
.form-title {
  color: #333;
  font-size: 1.5rem;
  font-weight: bold;
  margin-block-end: 20px;
  text-align: center;
}

/* Two-column Layout */
.form-grid {
  display: grid;
  gap: 15px;
  grid-template-columns: repeat(2, 1fr);
}

/* Form Group */
.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  color: #555;
  font-weight: bold;
  margin-block-end: 5px;
}

/* Input Fields */
input,
textarea {
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 5px;
  font-size: 14px;
  outline: none;
  transition: 0.3s;
}

input:focus,
textarea:focus {
  border-color: #e40000;
  box-shadow: 0 0 5px rgba(228, 0, 0, 20%);
}

/* Checkbox Styling */
.form-group input[type="checkbox"] {
  accent-color: #e40000;
  block-size: 18px;
  inline-size: 18px;
  margin-inline-end: 10px;
}

.form-group label span {
  display: flex;
  align-items: center;
}

/* Social Media Fields */
.social-input {
  display: flex;
  align-items: center;
}

.social-input i {
  color: #666;
  margin-inline-end: 10px;
}

/* Submit Button */
.btn-submit {
  padding: 12px;
  border: none;
  border-radius: 5px;
  background: #8e56f3;
  color: white;
  cursor: pointer;
  font-size: 1rem;
  inline-size: 100%;
  margin-block-start: 20px;
  transition: 0.3s;
}

.btn-submit:hover {
  background: #8e56f3;
}

.analytics-options button.active {
  background-color: #8e56f3;
  color: white;
}

/* Responsive Design */
@media (max-width: 768px) {
  .form-grid {
    grid-template-columns: 1fr;
  }
}

.analytics-container {
  display: flex;
}

.sidebar {
  padding: 10px;
  background: #f1f1f1;
  inline-size: 200px;
}

.sidebar ul {
  padding: 0;
  list-style: none;
}

.sidebar li {
  padding: 10px;
  border-block-end: 1px solid #ddd;
  color: #666;
  cursor: pointer;
  font-weight: bold;
}

.sidebar li.active {
  background: #8e56f3;
  border-inline-start: 5px solid #8e56f3;
  color: white;
}

.main-content {
  flex-grow: 1;
  padding: 20px;
}

.analytics-content {
  padding: 20px;
  border-radius: 8px;
  background: white;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 10%);
}

.analytics-content h4 {
  color: #333;
  font-size: 1.25rem;
  font-weight: bold;
  margin-block-end: 20px;
}

/* Active Tab Indicator */
.sidebar li.active {
  background-color: #8e56f3;
  border-inline-start: 5px solid #8e56f3;
  color: white;
}

/* Responsive Design */
@media (max-width: 768px) {
  .sidebar {
    inline-size: 100%;
  }

  .main-content {
    padding: 15px;
  }
}

</style>
