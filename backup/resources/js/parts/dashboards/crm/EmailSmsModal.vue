<template>
    <div v-if="showModal" class="modal">
      <div class="modal-content">
        <h2>{{ modalType === 'email' ? 'Send Email' : 'Send SMS' }}</h2>
        <textarea v-model="message" placeholder="Enter your message here..."></textarea>
        <button @click="sendMessage">Send</button>
        <button @click="closeModal">Close</button>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      showModal: Boolean,
      modalType: String,
      message: String
    },
    methods: {
      sendMessage() {
        this.$emit('send-message');
      },
      closeModal() {
        this.$emit('close');
      }
    }
  };
  </script>
  