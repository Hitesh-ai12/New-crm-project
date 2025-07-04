<template>
  <div class="email-chat d-flex shadow rounded" style="block-size: 90vh;">
    <!-- Lead Sidebar -->
    <div class="border-end p-3 col-lg-4 col-md-5 col-sm-5 col-xs-12 col-12" style="inline-size: 300px;">
      <div class="header-search-area">
        <input class="form-control mb-2" v-model="searchQuery" placeholder="Enter Lead Name" />
      </div>
      <div v-for="lead in filteredLeads" :key="lead.leadId" class="lead-item p-2 border-bottom" @click="selectLead(lead)" 
           :class="{ 'bg-primary text-white': selectedLead?.leadId === lead.leadId }">
        <div class="d-flex flex-column">
          <div class="fw-bold">{{ lead.firstName }} {{ lead.lastName }}</div>
          <div class="d-flex justify-content-between align-items-center">
            <span class="badge bg-success">{{ lead.status || 'Open' }}</span>
            <small class="text-muted">{{ lead.date || '16jun, 2025 | 11:00 am' }}</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Chat Window -->
    <div class="flex-grow-1 d-flex flex-column bg-light">
      <div class="border-bottom p-3 bg-white">
        <h5 v-if="selectedLead">{{ selectedLead.firstName }} {{ selectedLead.lastName }}</h5>
        <p v-else class="text-muted">Select a lead to view emails</p>
      </div>

      <div class="flex-grow-1 overflow-auto p-3" ref="emailArea">
        <div v-for="(msg, index) in messages" :key="index" class="mb-3">
          <div class="text-center text-muted small" v-if="msg.date">{{ msg.date }}</div>
          <div :class="['p-3 rounded shadow-sm', msg.direction === 'sent' ? 'bg-info text-white ms-auto' : 'bg-white me-auto']" style="max-inline-size: 75%;">
            <strong>{{ msg.subject }}</strong>
            <div class="mt-1" v-html="msg.description"></div>
            <small class="d-block text-end mt-1">{{ msg.time }}</small>
          </div>
        </div>
      </div>
    </div>
    
  </div>
</template>

<script>
import '@/pages/header/inbox.css';
import axios from 'axios';

export default {
  data() {
    return {
      leads: [],
      selectedLead: null,
      messages: [],
      searchQuery: '',
    };
  },
  computed: {
    filteredLeads() {
      return this.leads.filter(lead => {
        const fullName = `${lead.firstName || ''} ${lead.lastName || ''}`.trim().toLowerCase();
        return fullName.includes(this.searchQuery.toLowerCase());
      });
    },
  },
  methods: {
    async fetchLeads() {
      const token = localStorage.getItem('auth_token');
      const headers = { Authorization: `Bearer ${token}` };
      const res = await axios.get('/api/email/chats', { headers });
      this.leads = res.data;
    },

    async selectLead(lead) { // 🟢 use this name to match template
      const token = localStorage.getItem('auth_token');
      const headers = { Authorization: `Bearer ${token}` };

      const res = await axios.get(`/api/email/lead/${lead.leadId}`, { headers });

      this.selectedLead = lead;
      this.messages = res.data.reverse();

      this.$nextTick(() => this.scrollToBottom());
    },


    scrollToBottom() {
    const el = this.$refs.emailArea;
    if (el) el.scrollTop = el.scrollHeight;
    },

  },
  mounted() {
    this.fetchLeads();
  }
};
</script>
<style>
.lead-item {
  padding: 10px;
  cursor: pointer;
  transition: background-color 0.2s;
}

.lead-item:hover {
  background-color: #f8f9fa;
}

.lead-item .badge {
  font-size: 0.75rem;
  padding-block: 0.25em;
  padding-inline: 0.4em;
}
</style>
