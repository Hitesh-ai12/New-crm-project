<template>
  <div class="whatsapp-ui d-flex" style="block-size: 90vh;">
    <!-- Sidebar -->
    <div class="chat-sidebar border-end" style="inline-size: 320px;">
      <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
        <input class="form-control me-2" placeholder="Search or start a new chat" v-model="searchQuery" />
        <button class="btn btn-sm btn-outline-primary" @click="fetchMyLeads">Leads</button>
      </div>

      <div class="chat-list overflow-auto">
        <div
          v-for="chat in filteredChats"
          :key="chat.id"
          class="chat-item d-flex p-3 border-bottom align-items-start"
          :class="{ 'bg-light': selectedChat && selectedChat.id === chat.id }"
          @click="selectChat(chat)"
        >
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between">
              <strong>{{ chat.name }}</strong>
              <small>{{ chat.time }}</small>
            </div>
            <div class="text-muted text-truncate">{{ chat.lastMessage }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Chat Area -->
    <div class="chat-area d-flex flex-column flex-grow-1" v-if="selectedChat">
      <div class="p-3 border-bottom bg-white d-flex align-items-center">
        <div class="flex-grow-1">
          <strong>{{ selectedChat.name }}</strong>
          <div class="text-muted small">online</div>
        </div>
        <div class="d-flex gap-2">
          <i class="bi bi-search"></i>
          <i class="bi bi-telephone"></i>
        </div>
      </div>

      <div
        class="chat-messages flex-grow-1 overflow-auto p-3 bg-light"
        ref="chatContainer"
      >
        <div
          v-for="(msg, index) in selectedChat.messages"
          :key="index"
          class="mb-3"
        >
          <div
            :class="[
              'p-2 rounded',
              msg.from === 'me' ? 'bg-success text-white ms-auto' : 'bg-white me-auto',
            ]"
            style="max-inline-size: 75%;"
          >
            {{ msg.text }}
            <div class="text-end small mt-1">{{ msg.time }}</div>
          </div>
        </div>
      </div>

      <div class="chat-input p-3 bg-white border-top d-flex align-items-center">
        <i class="bi bi-emoji-smile me-2"></i>
        <input
          v-model="newMessage"
          @keyup.enter="sendMessage"
          class="form-control me-2"
          placeholder="Type a message"
        />
        <button class="btn btn-primary" @click="sendMessage">
          <i class="bi bi-send-fill"></i>
        </button>
      </div>

    </div>


    <!-- No chat selected fallback -->
    <div class="chat-area d-flex flex-column flex-grow-1 justify-content-center align-items-center text-muted" v-else>
      <p>Select a chat to begin</p>
    </div>

    <!-- Lead Selection Modal -->
    <div v-if="showLeadModal" class="modal d-block bg-dark bg-opacity-50" @click.self="showLeadModal = false">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Select Leads</h5>
            <button type="button" class="btn-close" @click="showLeadModal = false"></button>
          </div>
          <div class="modal-body">
            <div v-if="leads.length === 0">No leads found.</div>
            <div v-else>
              <div v-for="lead in leads" :key="lead.id" class="form-check mb-2">
                <input
                  class="form-check-input"
                  type="checkbox"
                  :id="'lead-' + lead.id"
                  v-model="selectedLeadIds"
                  :value="lead.id"
                />
                <label class="form-check-label" :for="'lead-' + lead.id">
                  {{ lead.first_name }} {{ lead.last_name }} - {{ lead.phone }}
                </label>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary" @click="useSelectedLeads">Use Selected Leads</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';

export default {
  name: 'WhatsAppComposer',
  data() {
    return {
      searchQuery: '',
      selectedChat: null,
      newMessage: '',
      showLeadModal: false,
      leads: [],
      selectedLeadIds: [],
      chats: [],
    };
  },

  computed: {
    filteredChats() {
      return this.chats.filter(chat =>
        (chat.name || '').toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    },
  },

  methods: {
    scrollToBottom() {
      this.$nextTick(() => {
        const container = this.$refs.chatContainer;
        if (container) {
          container.scrollTop = container.scrollHeight;
        }
      });
    },

    async selectChat(chat) {
      const authToken = localStorage.getItem('auth_token');
      try {
        const response = await axios.get(`/api/whatsapp/messages/${chat.id}`, {
          headers: { Authorization: `Bearer ${authToken}` },
        });

        this.selectedChat = {
          ...chat,
          messages: response.data,
        };

        this.scrollToBottom();
      } catch (err) {
        console.error('❌ Failed to load messages for chat:', err);
      }
    },

async sendMessage() {
  if (!this.newMessage.trim() || !this.selectedChat) return;

  const authToken = localStorage.getItem('auth_token');
  const timeNow = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

  try {
    const response = await axios.post(
      '/api/whatsapp/send',
      {
        message: this.newMessage,
        recipients: [this.selectedChat.phone],
      },
      {
        headers: {
          Authorization: `Bearer ${authToken}`,
        },
      }
    );

if (response.data && response.data.status === 'success') {
  if (!this.selectedChat.messages) {
    this.$set(this.selectedChat, 'messages', []);
  }

  this.selectedChat.messages.push({
    from: 'me',
    text: this.newMessage,
    time: timeNow,
  });

  const chat = this.chats.find(c => c.id === this.selectedChat.id);
  if (chat) {
    chat.lastMessage = this.newMessage;
    chat.time = timeNow;
  }

  this.selectedChat = { ...this.selectedChat }; // force reactivity
  this.newMessage = '';
  this.scrollToBottom();

  this.$nextTick(() => {
    const input = this.$el.querySelector('.chat-input input');
    if (input) input.focus();
  });
} else {
  console.error('🚨 Message sending failed:', response.data);
}

  } catch (error) {
    console.error('Failed to send message:', error);
  }
},

    async fetchMyLeads() {
      try {
        const authToken = localStorage.getItem('auth_token');
        const response = await axios.get('/api/leads', {
          headers: {
            Authorization: `Bearer ${authToken}`,
          },
        });
        this.leads = response.data;
        this.showLeadModal = true;
      } catch (error) {
        console.error('Failed to fetch leads:', error);
      }
    },

    useSelectedLeads() {
      const selectedLeads = this.leads.filter(lead => this.selectedLeadIds.includes(lead.id));

      selectedLeads.forEach(lead => {
        const exists = this.chats.find(c => c.id === lead.id);
        if (!exists) {
          const newChat = {
            id: lead.id,
            name: `${lead.first_name || ''} ${lead.last_name || ''}`.trim(),
            phone: lead.phone,
            time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
            lastMessage: 'New chat started.',
            messages: [],
          };
          this.chats.push(newChat);
        }
      });

      const first = selectedLeads[0];
      if (first) {
        this.selectedChat = this.chats.find(c => c.id === first.id);
      }

      this.showLeadModal = false;
      this.selectedLeadIds = [];
    },

      async fetchChats() {
        try {
          const authToken = localStorage.getItem('auth_token');
          const response = await axios.get('/api/whatsapp/chats', {
            headers: {
              Authorization: `Bearer ${authToken}`,
            },
          });
          this.chats = response.data;
          return Promise.resolve(); 
        } catch (error) {
          console.error('Failed to load chats:', error);
          return Promise.reject(error);
        }
      },
  },
  mounted() {
    this.fetchChats(); 

  },
};
</script>

<style scoped>
.chat-item:hover {
  background-color: #8f2727;
  cursor: pointer;
}

.chat-messages {
  background-repeat: repeat;
  background-size: cover;
}

.modal-dialog .modal-content .modal-header button {
  block-size: 38px;
  margin-inline-end: 20px;
}
</style>
