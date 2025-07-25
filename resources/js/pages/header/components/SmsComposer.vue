<template>
  <div class="chat-container d-flex shadow rounded" style="block-size: 90vh; margin-block: 20px; margin-inline: auto; max-inline-size: 1200px;">
    
    <!-- Sidebar (Lead List) -->
    <div class="sidebar border-end d-flex flex-column bg-white" style="inline-size: 320px; min-inline-size: 250px;">
      <div class="p-3 border-bottom">
        <input
          type="text"
          class="form-control rounded-pill"
          placeholder="Search lead"
          v-model="searchQuery"
        />
      </div>

      <div class="flex-grow-1 overflow-auto chat-list">
        <div
          v-for="lead in filteredLeads"
          :key="lead.leadId"
          @click="selectChat(lead)"
          :class="[
            'd-flex align-items-center p-3 border-bottom chat-item',
            { 'bg-primary text-white': selectedChat && selectedChat.leadId === lead.leadId }
          ]"
          style="cursor: pointer;"
        >
          <img
            src="https://cdn-icons-png.flaticon.com/512/1384/1384031.png"
            width="45"
            height="45"
            class="rounded-circle me-3"
            alt="Avatar"
          />
          <div class="flex-grow-1">
            <div class="fw-bold">{{ lead.name }}</div>
            <small class="text-muted" :class="{ 'text-white-50': selectedChat && selectedChat.leadId === lead.leadId }">
              Lead ID: {{ lead.leadId }}
            </small>
          </div>
        </div>
      </div>
    </div>

    <!-- Chat Window -->
    <div class="chat-window d-flex flex-column flex-grow-1 bg-light">
      <!-- Header -->
      <div v-if="selectedChat" class="chat-header d-flex align-items-center p-3 border-bottom bg-white">
        <img
          src="https://cdn-icons-png.flaticon.com/512/1384/1384031.png"
          width="40"
          height="40"
          class="rounded-circle me-3"
          alt="Avatar"
        />
        <h5 class="mb-0 flex-grow-1">{{ selectedChat.name }}</h5>
      </div>
      <div v-else class="chat-header d-flex align-items-center justify-content-center p-3 text-muted">
        Select a lead to view messages
      </div>

      <!-- Messages -->
      <div class="messages-area flex-grow-1 p-4 overflow-auto" ref="messagesArea">
        <template v-if="messages.length && selectedChat">
          <div
            v-for="(msg, index) in messages"
            :key="index"
            class="mb-3"
          >
            <div v-if="msg.date" class="text-center small text-muted mb-2">
              <strong>{{ msg.date }}</strong>
            </div>
            <div
              :class="[
                'message-bubble p-3 rounded shadow-sm',
                msg.direction === 'sent' ? 'bg-success text-white ms-auto' : 'bg-white me-auto'
              ]"
              style="max-inline-size: 75%;"
            >
              <div>{{ msg.description }}</div>
              <small class="d-block text-end mt-1 text-white-50">
                {{ msg.time }}
              </small>
            </div>
          </div>
        </template>
      </div>

      <!-- Input -->
      <div v-if="selectedChat" class="p-3 border-top bg-white">
        <div class="input-group position-relative">
          <input
            type="text"
            class="form-control rounded-pill pe-5"
            placeholder="Type a message"
            v-model="newMessage"
            @keyup.enter="sendMessage"
          />
            <button
              class="btn btn-primary rounded-circle position-absolute"
              style="
                z-index: 1;
                block-size: 40px;
                inline-size: 40px;
                inset-block-start: 50%;
                inset-inline-end: 10px;
                transform: translateY(-50%);"
              @click="sendMessage"
            >
              <i class="bi bi-send"></i>
            </button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import axios from 'axios';
import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import Swal from 'sweetalert2';

export default {
  data() {
    return {
      searchQuery: "",
      newMessage: "",
      selectedChat: null,
      leads: [],
      messages: [],
    };
  },
  computed: {
    filteredLeads() {
      return this.leads.filter(lead =>
        lead.name.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    },
  },
  methods: {
    async fetchLeads() {
      try {
        const token = localStorage.getItem('auth_token');
        const headers = { Authorization: `Bearer ${token}` };
        const res = await axios.get("/api/sms/leads", { headers });
        this.leads = res.data;
      } catch (err) {
        console.error("Error loading leads:", err);
      }
    },

    async selectChat(lead) {
      try {
        const token = localStorage.getItem('auth_token');
        const headers = { Authorization: `Bearer ${token}` };

        const res = await axios.get(`/api/sms/lead/${lead.leadId}`, { headers });

        const messages = res.data;

        const phone = messages.length > 0 ? messages[0].phone : lead.phone || '';

        this.selectedChat = {
          ...lead,
          phone
        };

        this.messages = messages.reverse();

        this.$nextTick(() => this.scrollToBottom());
      } catch (err) {
        console.error("Error loading messages:", err);
      }
    },

    // Send SMS message via API
    async sendMessage() {
      
      const token = localStorage.getItem('auth_token');
      const userId = parseInt(localStorage.getItem('user_id'));
      const headers = { Authorization: `Bearer ${token}` };

      try {
        const payload = {
          lead_ids: [this.selectedChat.leadId],
          user_id: userId,
          from: '+16187452168',
          to: this.selectedChat.phone,
          subject: 'SMS Chat',
          message: this.newMessage,
        };

        await axios.post('/api/send-sms', payload, { headers });

        // Format time & date
        const now = new Date();
        const time = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const date = now.toISOString().slice(0, 10);

        // Push to UI
        this.messages.push({
          id: `sms-sent-${Date.now()}`,
          type: 'sms',
          direction: 'sent',
          phone: this.selectedChat.phone,
          title: 'Sent SMS',
          description: this.newMessage,
          leadId: this.selectedChat.leadId,
          date,
          time,
        });

        this.newMessage = '';
        this.$nextTick(() => this.scrollToBottom());
      } catch (err) {
        console.error("Send SMS Error:", err);
        Swal.fire("Error", "Failed to send SMS.", "error");
      }
    },

    // Scroll to bottom of message window
    scrollToBottom() {
      const el = this.$refs.messagesArea;
      if (el) el.scrollTop = el.scrollHeight;
    },
  },

  mounted() {
    this.fetchLeads();
  },
};
</script>
