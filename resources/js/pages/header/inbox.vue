<template>
  <div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <!-- Tab Buttons -->
      <div class="btn-group me-3">
        <button
          @click="setActiveTab('email')"
          :class="['btn', activeTab === 'email' ? 'btn-primary text-white' : 'btn-outline-primary']"
        >
          Email
        </button>
        <button
          @click="setActiveTab('sms')"
          :class="['btn', activeTab === 'sms' ? 'btn-primary text-white' : 'btn-outline-primary']"
        >
          SMS
        </button>
        <button
          @click="setActiveTab('whatsapp')"
          :class="['btn', activeTab === 'whatsapp' ? 'btn-success text-white' : 'btn-outline-success']"
        >
          WhatsApp
        </button>
      </div>

      <!-- Inbox/Sent Dropdown -->
      <div class="me-auto">
        <select class="form-select" style="inline-size: 150px;">
          <option>Inbox</option>
          <option>Sent</option>
        </select>
      </div>

      <!-- Send Buttons -->
      <div>
        <button class="btn btn-primary me-2 btn big-dark-button" @click="openEmailModal">
          + Send New Email
        </button>
        <button class="btn big-dark-button" @click="openSmsModal">
          + Send New SMS
        </button>
      </div>
    </div>

    <!-- Tab Component Renderer -->
    <component :is="activeTabComponent" />

    <!-- Modals -->
    <SmsModal v-if="showSmsModal" :show="showSmsModal" @close="showSmsModal = false" />
    <EmailModal v-if="showEmailModal" :show="showEmailModal" @close="showEmailModal = false" />
  </div>
</template>

<script>
import 'bootstrap'
import 'bootstrap/dist/css/bootstrap.min.css'

// Components
import Email from './components/EmailComposer.vue'
import SMS from './components/SmsComposer.vue'
import WhatsApp from './components/WhatsAppComposer.vue'
import EmailModal from './components/modals/EmailModal.vue'
import SmsModal from './components/modals/SmsModal.vue'

export default {
  name: 'Inbox',
  components: {
    SMS,
    Email,
    WhatsApp,
    SmsModal,
    EmailModal,
  },
  data() {
    return {
      activeTab: localStorage.getItem('activeTab') || 'sms',
      showSmsModal: false,
      showEmailModal: false,
    }
  },
  computed: {
    activeTabComponent() {
      if (this.activeTab === 'email') return 'Email'
      if (this.activeTab === 'sms') return 'SMS'
      if (this.activeTab === 'whatsapp') return 'WhatsApp'
      return 'SMS'
    },
  },
  methods: {
    setActiveTab(tab) {
      this.activeTab = tab
      localStorage.setItem('activeTab', tab)
    },
    openSmsModal() {
      console.log('[OPEN SMS MODAL] Triggered')
      this.showSmsModal = true
    },
    openEmailModal() {
      console.log('[OPEN EMAIL MODAL] Triggered')
      this.showEmailModal = true
    },
    closeSmsModal() {
      this.showSmsModal = false
    },
    closeEmailModal() {
      this.showEmailModal = false
    },
  },
}
</script>
