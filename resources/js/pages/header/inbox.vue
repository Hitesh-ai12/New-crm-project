<template>
  <div class="container py-3">
    <!-- Top Bar -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <!-- Tab Buttons -->
      <div class="btn-group me-3">
        <button
          @click="activeTab = 'email'"
          :class="['btn', activeTab === 'email' ? 'btn-primary text-white' : 'btn-outline-primary']"
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

      <!-- Dropdown -->
      <div class="me-auto">
        <select class="form-select" style="inline-size: 150px;">
          <option>Inbox</option>
          <option>Sent</option>
        </select>
      </div>

      <!-- Action Buttons -->
      <div>
        <button class="btn btn-primary me-2">+ Send New Email</button>
        <button class="btn btn-success" @click="openSmsModal">+ Send New SMS</button>
      </div>
    </div>

    <!-- Dynamic Component -->
    <component :is="activeTabComponent" />

<!-- OLD -->
<!-- <transition name="fade">
  <SmsModal v-if="showSmsModal" :show="showSmsModal" @close="showSmsModal = false" />
</transition> -->

<!-- NEW -->
<SmsModal v-if="showSmsModal" :show="showSmsModal" @close="showSmsModal = false" />
  </div>
</template>

<script>
import Email from "./components/EmailComposer.vue";
import SMS from "./components/SmsComposer.vue";
import EmailModal from "./components/modals/EmailModal.vue";
import SmsModal from "./components/modals/SmsModal.vue";

export default {
  name: "Inbox",
  components: {
    SMS,
    Email,
    SmsModal,
    EmailModal,
  },
  data() {
    return {
      activeTab: "sms",
      showSmsModal: false,
    };
  },
  computed: {
    activeTabComponent() {
      return this.activeTab === "sms" ? "SMS" : "Email";
    },
  },
  methods: {
    openSmsModal() {
      console.log('[OPEN MODAL] Triggered');
      this.showSmsModal = true;
    },
    closeSmsModal() {
      this.showSmsModal = false;
    },
  },
};
</script>
