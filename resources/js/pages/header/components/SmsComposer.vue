<template>
  <div class="chat-container d-flex" style="block-size: 90vh;">
    <div class="sidebar border-end d-flex flex-column" style="inline-size: 350px; min-inline-size: 250px;">
      <div class="p-3 border-bottom">
        <input type="text" class="form-control rounded-pill" placeholder="Search or start new chat" v-model="searchQuery" />
      </div>

      <div class="flex-grow-1 overflow-auto chat-list">
        <div
          v-for="chat in filteredChats"
          :key="chat.id"
          @click="selectChat(chat)"
          :class="['d-flex align-items-center p-3 border-bottom chat-item', {'bg-primary text-white': selectedChat && selectedChat.id === chat.id}]"
        >
          <img src="https://cdn-icons-png.flaticon.com/512/1384/1384031.png" width="50" height="50" class="rounded-circle me-3" alt="User Avatar" />
          <div class="flex-grow-1">
            <div class="fw-bold">{{ chat.name }}</div>
            <small class="text-muted text-truncate d-block" :class="{'text-white-50': selectedChat && selectedChat.id === chat.id}">{{ chat.lastMessagePreview || 'No messages yet' }}</small>
          </div>
          <div class="text-end">
            <small :class="{'text-white-50': selectedChat && selectedChat.id === chat.id}">{{ chat.time }}</small>
            <span v-if="chat.status === 'New'" class="badge bg-danger ms-2">New</span>
          </div>
        </div>
      </div>
    </div>

    <div class="chat-window d-flex flex-column flex-grow-1">
      <div v-if="selectedChat" class="chat-header d-flex align-items-center p-3 border-bottom bg-light">
        <img src="https://cdn-icons-png.flaticon.com/512/1384/1384031.png" width="40" height="40" class="rounded-circle me-3" alt="Chat Avatar" />
        <h5 class="mb-0 flex-grow-1">{{ selectedChat.name }}</h5>
        <div>
          <button class="btn btn-outline-secondary btn-sm">Profile</button>
        </div>
      </div>
      <div v-else class="chat-header d-flex align-items-center justify-content-center flex-grow-1 bg-light text-muted">
        Select a chat to start messaging
      </div>

      <div class="messages-area flex-grow-1 p-4 overflow-auto" ref="messagesArea">
        <template v-if="selectedChat && selectedChat.fullMessages">
          <div v-for="(msg, i) in selectedChat.fullMessages" :key="i" class="mb-3">
            <div v-if="msg.date" class="text-center text-muted small mb-2 date-separator">
              <strong>{{ msg.date }}</strong>
            </div>
            <div :class="['message-bubble p-2 rounded shadow-sm', msg.isUser ? 'bg-success text-white ms-auto' : 'bg-white me-auto']" style="max-inline-size: 75%;">
              <div>{{ msg.text }}</div>
              <small class="d-block text-end mt-1 text-black-50">{{ msg.time }}</small>
            </div>
          </div>
        </template>
      </div>

      <div v-if="selectedChat" class="message-input-area border-top p-3 bg-light">
        <div class="input-group">
          <input
            type="text"
            class="form-control rounded-pill pe-5"
            placeholder="Type a message"
            v-model="newMessage"
            @keyup.enter="sendMessage"
          />
          <button class="btn btn-primary rounded-circle send-button" @click="sendMessage">
            <i class="bi bi-send"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "SMS",
  data() {
    return {
      searchQuery: "",
      newMessage: "",
      selectedChat: null, // Initialize as null to show "Select a chat" message
      chats: [
        {
          id: 1,
          name: "Khushal Singh",
          time: "06:26 PM", // Last message time
          status: "Opened",
          lastMessagePreview: "Can you please send me the location details",
          fullMessages: [
            { date: "Mon, 09 Jun", text: "Waheguru Ji Ka Khalsa...", time: "09:03 PM", isUser: false },
            { date: "Sat, 14 Jun", text: "Tomorrow Diwan at Darbar Sahib", time: "04:39 AM", isUser: false },
            { text: "Can you please send me the location details", time: "06:26 PM", isUser: true }
          ]
        },
        {
          id: 2,
          name: "Simran Singh Ghuman",
          time: "05:50 PM",
          status: "Opened",
          lastMessagePreview: "Sure, I'll send it shortly.",
          fullMessages: [
            { date: "Sun, 15 Jun", text: "Hello there!", time: "05:45 PM", isUser: false },
            { text: "Sure, I'll send it shortly.", time: "05:50 PM", isUser: true }
          ]
        },
        {
          id: 3,
          name: "Kulraj Singh",
          time: "06:33 AM",
          status: "New",
          lastMessagePreview: "New lead arrived!",
          fullMessages: [
            { date: "Tue, 17 Jun", text: "New lead arrived!", time: "06:33 AM", isUser: false }
          ]
        },
        {
          id: 4,
          name: "Sonpoot Kaur Biryah",
          time: "06:29 AM",
          status: "Opened",
          lastMessagePreview: "Meeting confirmed for Friday.",
          fullMessages: [
            { date: "Mon, 16 Jun", text: "Hi Sonpoot, are you available for a quick call?", time: "06:20 AM", isUser: true },
            { text: "Yes, I am. What's it about?", time: "06:25 AM", isUser: false },
            { text: "Meeting confirmed for Friday.", time: "06:29 AM", isUser: true }
          ]
        }
      ]
    };
  },
  computed: {
    filteredChats() {
      return this.chats.filter(chat =>
        chat.name.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    }
  },
  methods: {
    selectChat(chat) {
      this.selectedChat = chat;
      // Ensure scroll to bottom after chat selection
      this.$nextTick(() => {
        this.scrollToBottom();
      });
    },
    sendMessage() {
      if (this.newMessage.trim() === "") {
        return; // Don't send empty messages
      }

      const now = new Date();
      const hours = now.getHours();
      const minutes = now.getMinutes();
      const ampm = hours >= 12 ? 'PM' : 'AM';
      const formattedHours = hours % 12 === 0 ? 12 : hours % 12;
      const time = `<span class="math-inline">\{formattedHours\}\:</span>{minutes < 10 ? '0' + minutes : minutes} ${ampm}`;

      const today = new Date();
      const messageDate = new Date(this.selectedChat.fullMessages[this.selectedChat.fullMessages.length - 1]?.timestamp || 0); // Get last message date or 0
      const showDateSeparator = !this.selectedChat.fullMessages.length ||
                                today.toDateString() !== messageDate.toDateString();

      const newMsg = {
        text: this.newMessage,
        time: time,
        isUser: true,
        timestamp: now.getTime(), // Add timestamp for sorting/date checks
      };

      if (showDateSeparator) {
        const options = { weekday: 'short', month: 'short', day: 'numeric' };
        newMsg.date = today.toLocaleDateString('en-US', options);
      }


      this.selectedChat.fullMessages.push(newMsg);
      this.newMessage = ""; // Clear input

      // Scroll to bottom after sending message
      this.$nextTick(() => {
        this.scrollToBottom();
      });
    },
    scrollToBottom() {
      const messagesArea = this.$refs.messagesArea;
      if (messagesArea) {
        messagesArea.scrollTop = messagesArea.scrollHeight;
      }
    }
  },
  mounted() {
    // Select the first chat by default on mount
    if (this.chats.length > 0) {
      this.selectChat(this.chats[0]);
    }
  }
};
</script>

<style scoped>
/* Main Container */
.chat-container {
  overflow: hidden;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 10%);
  margin-block: 20px;
  margin-inline: auto;
  max-inline-size: 1200px;
}

/* Left Sidebar */
.sidebar {
  background-color: #f0f2f5; /* Light gray, similar to WhatsApp */
}

.chat-list .chat-item {
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.chat-list .chat-item:hover {
  background-color: #e9ecef; /* Slightly darker on hover */
}

.chat-list .chat-item.bg-primary {
  background-color: #007bff !important; /* Blue for selected */
}

/* Right Chat Window */
.chat-window {
  background-color: #e5ddd5; /* WhatsApp background color */
}

.chat-header {
  background-color: #f0f2f5; /* Similar to sidebar header */
  min-block-size: 60px;
}

.message-bubble {
  position: relative;
  border-radius: 7.5px;
  max-inline-size: 75%; /* Limit bubble width */
  padding-block: 8px;
  padding-inline: 12px;
  word-wrap: break-word; /* Ensure long words wrap */
}

.message-bubble.bg-white { /* Incoming messages */
  background-color: #fff;
  margin-inline-end: auto;
}

.message-bubble.bg-success { /* User's messages */
  background-color: #dcf8c6; /* Light green, WhatsApp send bubble */
  margin-inline-start: auto;
}

.message-bubble small {
  color: rgba(0, 0, 0, 45%); /* Darker gray for time in light bubble */
  font-size: 0.75em;
}

.message-bubble.bg-success small {
  color: rgba(0, 0, 0, 45%); /* Darker gray for time in green bubble */
}

.date-separator {
  position: sticky;
  z-index: 10;
  display: inline-block;
  border-radius: 5px;
  backdrop-filter: blur(3px); /* Optional: blur effect for date separator */
  background-color: rgba(229, 221, 213, 80%); /* Background for date to stand out */
  inset-block-start: 10px;
  padding-block: 4px;
  padding-inline: 8px;
}

.message-input-area {
  background-color: #f0f2f5; /* Light gray for input area */
  min-block-size: 70px;
}

.send-button {
  position: absolute;
  z-index: 2;
  display: flex;
  align-items: center;
  justify-content: center;
  border-color: #00a884;
  background-color: #00a884; /* WhatsApp green */
  block-size: 45px;
  inline-size: 45px;
  inset-block-start: 50%;
  inset-inline-end: 5px;
  transform: translateY(-50%);
}

.send-button:hover {
  border-color: #008a6c;
  background-color: #008a6c;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
  block-size: 6px;
  inline-size: 6px;
}

::-webkit-scrollbar-thumb {
  border-radius: 10px;
  background-color: rgba(0, 0, 0, 20%);
}

::-webkit-scrollbar-track {
  background-color: rgba(0, 0, 0, 5%);
}
</style>
