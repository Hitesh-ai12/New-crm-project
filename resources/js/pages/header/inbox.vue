<template>
  <div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div class="btn-group me-3">
        <button
          @click="activeTab = 'email'"
          :class="['btn', activeTab === 'email' ? 'btn big-border-button' : 'btn big-border-button']"
        >
          Email
        </button>
        <button
          @click="activeTab = 'sms'"
          :class="['btn', activeTab === 'sms' ? 'btn-primary text-white' : 'btn-outline-primary']"
        >
          SMS
        </button>
      </div>

      <div class="me-auto">
        <select class="form-select" style="inline-size: 150px;">
          <option>Inbox</option>
          <option>Sent</option>
        </select>
      </div>

      <div>
        <button class="btn btn-primary me-2 btn big-dark-button" @click="openEmailModal">
          + Send New Email
        </button>
        <button class="btn big-dark-button" @click="openSmsModal">
          + Send New SMS
        </button>
      </div>
    </div>

    <component :is="activeTabComponent" />

    <SmsModal v-if="showSmsModal" :show="showSmsModal" @close="showSmsModal = false" />
    <EmailModal v-if="showEmailModal" :show="showEmailModal" @close="showEmailModal = false" />
  </div>
</template>

<script>
import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import Email from './components/EmailComposer.vue';
import SMS from './components/SmsComposer.vue';
import EmailModal from './components/modals/EmailModal.vue';
import SmsModal from './components/modals/SmsModal.vue';

export default {
  name: 'Inbox',
  components: {
    SMS,
    Email,
    SmsModal,
    EmailModal,
  },
  data() {
    return {
      activeTab: 'sms',
      showSmsModal: false,
      showEmailModal: false, 
    };
  },
  computed: {
    activeTabComponent() {
      return this.activeTab === 'sms' ? 'SMS' : 'Email';
    },
  },
  methods: {
    openSmsModal() {
      console.log('[OPEN SMS MODAL] Triggered');
      this.showSmsModal = true;
    },
    // Added method to open Email Modal
    openEmailModal() {
      console.log('[OPEN EMAIL MODAL] Triggered');
      this.showEmailModal = true;
    },
    closeSmsModal() {
      this.showSmsModal = false;
    },
    closeEmailModal() { 
      this.showEmailModal = false;
    },
  },
};
</script>


