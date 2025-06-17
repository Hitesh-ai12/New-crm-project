<template>
  <div class="container py-3">
    <div class="btn-group mb-3">
      <button @click="activeTab = 'sms'" :class="['btn', activeTab === 'sms' ? 'btn-danger' : 'btn-outline-danger']">
        SMS
      </button>
      <button @click="activeTab = 'email'" :class="['btn', activeTab === 'email' ? 'btn-danger' : 'btn-outline-danger']">
        Email
      </button>
    </div>

    <component :is="activeTabComponent" />
  </div>
</template>

<script>
import Email from "./components/EmailComposer.vue";
import SMS from "./components/SmsComposer.vue";

export default {
  name: "Inbox",
  components: { SMS, Email },
  data() {
    return {
      activeTab: "sms",
    };
  },
  computed: {
    activeTabComponent() {
      return this.activeTab === "sms" ? "SMS" : "Email";
    }
  },
  methods: {
    tabClass(tab) {
      return `px-4 py-2 border rounded ${this.activeTab === tab ? 'bg-red-500 text-white' : 'bg-white text-black'}`;
    }
  }
};
</script>
