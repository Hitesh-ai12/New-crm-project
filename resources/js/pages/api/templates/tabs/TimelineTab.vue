<template>
  <div class="container d-flex">
    <div class="sidebar me-4">
      <div
        class="menu-item"
        :class="{ active: activeMenuItem === 'all' }"
        @click="activeMenuItem = 'all'; messageFilter = 'all'"
      >
        <div class="icon"><i class="fas fa-headset"></i></div>
        <div class="label">ALL</div>
      </div>
      <div
        class="menu-item"
        :class="{ active: activeMenuItem === 'sms' }"
        @click="activeMenuItem = 'sms'; messageFilter = 'all'"
      >
        <div class="icon"><i class="fas fa-comment"></i></div>
        <div class="label">SMS</div>
      </div>
      <div
        class="menu-item"
        :class="{ active: activeMenuItem === 'email' }"
        @click="activeMenuItem = 'email'"
      >
        <div class="icon"><i class="fas fa-envelope"></i></div>
        <div class="label">Email</div>
      </div>
      <div
        class="menu-item"
        :class="{ active: activeMenuItem === 'notes' }"
        @click="activeMenuItem = 'notes'; messageFilter = 'all'"
      >
        <div class="icon"><i class="fas fa-sticky-note"></i></div>
        <div class="label">Notes</div>
      </div>
      <div
        class="menu-item"
        :class="{ active: activeMenuItem === 'callLog' }"
        @click="activeMenuItem = 'callLog'; messageFilter = 'all'"
      >
        <div class="icon"><i class="fas fa-phone"></i></div>
        <div class="label">Call Log</div>
      </div>
      <div
        class="menu-item"
        :class="{ active: activeMenuItem === 'webActivity' }"
        @click="activeMenuItem = 'webActivity'; messageFilter = 'all'"
      >
        <div class="icon"><i class="fas fa-globe"></i></div>
        <div class="label">WEB Activity</div>
      </div>
    </div>

    <div class="main-content flex-grow-1">
      <h4 class="mb-3">Timeline</h4>

    <div v-if="activeMenuItem === 'email' || activeMenuItem === 'sms'" class="mb-3">
        <label class="me-2 fw-bold">Filter:</label>
        <select v-model="messageFilter" class="form-select w-auto d-inline-block">
          <option value="all">All</option>
          <option value="sent">Sent</option>
          <option value="received">Received</option>
        </select>
      </div>

      <div
        v-for="activity in filteredActivities"
        :key="activity.id" class="card mb-2"
      >
        <div class="card-body">
          <h5 class="card-title">{{ activity.title }}</h5>
          <p class="card-text">
            <!-- Email info -->
            <span v-if="activity.type === 'email' && activity.direction === 'received'">
              From: {{ activity.from }}
            </span>
            <span v-else-if="activity.type === 'email' && activity.direction === 'sent'">
              To: {{ activity.to }}
            </span>

            <!-- SMS info -->
            <span v-else-if="activity.type === 'sms' && activity.direction === 'received'">
              From: {{ activity.phone }}
            </span>
            <span v-else-if="activity.type === 'sms' && activity.direction === 'sent'">
              To: {{ activity.phone }}
            </span>

            <br>
            <span v-html="activity.description"></span>
          </p>

          <p class="text-muted small">{{ activity.date }} {{ activity.time }}</p>

          <button
            v-if="activity.type === 'email' && activity.direction === 'received'"
            class="btn btn-sm btn-primary"
            @click="replyToEmail(activity)"
          >
            Reply
          </button>
          <button
            v-if="activity.type === 'email'"
            class="btn btn-sm btn-info ms-2"
            @click="viewEmailContent(activity)"
          >
            View
          </button>
        <button
        v-if="activity.type === 'sms'"
        class="btn btn-sm btn-info ms-2"
        @click="viewSmsContent(activity)"
        >
        View
        </button>

        </div>
      </div>
      <div v-if="showEmailModal" class="modal fade show d-block" tabindex="-1" role="dialog" style="background-color: rgba(0, 0, 0, 50%);">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ selectedEmail.title }}</h5>
              <button type="button" class="btn-close" @click="showEmailModal = false"></button>
            </div>
            <div class="modal-body">
              <p><strong>From:</strong> {{ selectedEmail.from }}</p>
              <p v-if="selectedEmail.to"><strong>To:</strong> {{ selectedEmail.to }}</p>
              <p><strong>Subject:</strong> {{ selectedEmail.description }}</p>
              <hr>
              <div v-html="selectedEmail.body_html || selectedEmail.body_plain"></div>
              <div v-if="selectedEmail.attachments && selectedEmail.attachments.length">
                <h6>Attachments:</h6>
                <ul>
                  <li v-for="(attachment, i) in selectedEmail.attachments" :key="i">
                    <a :href="attachment" target="_blank">{{ getFileNameFromPath(attachment) }}</a>
                  </li>
                </ul>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="showEmailModal = false">Close</button>
              <button
                v-if="selectedEmail.type === 'email' && selectedEmail.direction === 'received'"
                class="btn btn-primary"
                @click="replyToEmail(selectedEmail); showEmailModal = false;"
              >
                Reply
              </button>
            </div>
          </div>
        </div>
      </div>


      <div
        v-if="showSmsModal"
        class="modal fade show d-block"
        tabindex="-1"
        role="dialog"
        style="background-color: rgba(0, 0, 0, 50%);"
      >
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ selectedSms.title }}</h5>
              <button type="button" class="btn-close" @click="showSmsModal = false"></button>
            </div>
            <div class="modal-body">
              <p><strong>From:</strong> {{ selectedSms.direction === 'received' ? selectedSms.phone : 'You' }}</p>
              <p><strong>To:</strong> {{ selectedSms.direction === 'sent' ? selectedSms.phone : 'You' }}</p>
              <p><strong>Message:</strong></p>
              <div class="border p-2 rounded bg-light">{{ selectedSms.description }}</div>
              <p class="text-muted mt-2 small">{{ selectedSms.date }} {{ selectedSms.time }}</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="showSmsModal = false">Close</button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';

const showSmsModal = ref(false);
const activeMenuItem = ref('all')
const messageFilter = ref('all')
const route = useRoute()
const leadId = ref(route.params.id)
const selectedSms = ref({});
// State for modal
const showEmailModal = ref(false);
const selectedEmail = ref({});

const formatDateTime = (dateTimeStr) => {
  const dt = new Date(dateTimeStr)
  return {
    date: dt.toLocaleDateString(),
    time: dt.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
  }
}


const leadSms = ref([])

onMounted(async () => {
  const token = localStorage.getItem('auth_token')
  const headers = { Authorization: `Bearer ${token}` }

  try {
    const response = await axios.get(`/api/lead/${leadId.value}/sms`, { headers })
    leadSms.value = response.data.map(sms => {
      const { date, time } = formatDateTime(sms.sent_at || sms.received_at)
      return {
        id: sms.id,
        direction: sms.direction,
        phone: sms.direction === 'sent' ? sms.to : sms.from,
        message: sms.message,
        date,
        time
      }
    })
  } catch (e) {
    console.error('Failed to load SMS for lead', e)
  }
})

const viewSmsContent = (sms) => {
  selectedSms.value = sms
  showSmsModal.value = true
}

const activities = ref([

  {
    id: 'web-1',
    type: 'webActivity',
    title: 'Visited Website',
    description: 'User visited homepage',
    date: '2025-06-09',
    time: '10:30 AM',
  },
  {
    id: 'email-received-1',
    type: 'email',
    direction: 'received',
    title: 'Replied Email',
    description: 'User replied to your email.',
    date: '2025-06-08',
    time: '6:10 PM',
  },
  {
    id: 'sms-1',
    type: 'sms',
    title: 'Received SMS',
    description: 'User received an SMS about a new listing',
    date: '2025-06-07',
    time: '1:15 PM',
  },
])

onMounted(async () => {
  try {
    const token = localStorage.getItem('auth_token')
    const headers = { Authorization: `Bearer ${token}` }

    const sentSmsRes = await axios.get('/api/sent-sms', { headers })
    const sentSms = sentSmsRes.data.map(s => {
      const { date, time } = formatDateTime(s.sent_at)
      return {
        id: `sms-sent-${s.id}`, 
        type: 'sms',
        direction: 'sent',
        phone: s.to,
        title: 'Sent SMS',
        description: s.message,
        leadId: s.lead_id,
        date, time
      }
    })


    const recSmsRes = await axios.get('/api/incoming-sms', { headers })
    const recSms = recSmsRes.data.map(s => {
      const { date, time } = formatDateTime(s.received_at)
      return {
        id: `sms-rec-${s.id}`, 
        type: 'sms',
        direction: 'received',
        phone: s.from,
        title: 'Received SMS',
        description: s.message,
        leadId: s.lead_id,
        date, time
      }
    })


    // ✅ Push into the main activities timeline
    activities.value.push(...recSms, ...sentSms)


    // Fetch Sent Emails
    const sentResponse = await axios.get('/api/sent-emails', {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })

    console.log('Raw Sent Emails API Response:', sentResponse.data)

    const sentEmails = sentResponse.data.map(email => {
      const createdAt = new Date(email.created_at)
      return {
        id: `sent-${email.id}`, 
        type: 'email',
        direction: 'sent',
        title: 'Sent Email',
        description: email.subject,
        date: createdAt.toLocaleDateString(),
        time: createdAt.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
        to: email.to, 
        body_plain: email.message, 
        body_html: null,
        attachments: email.attachments 
      }
    })
    console.log('Formatted Sent Emails:', sentEmails)
    activities.value.push(...sentEmails)

    // Fetch Received Emails (Replies)
    const receivedResponse = await axios.get('/api/received-emails', {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })

    console.log('--- RECEIVED REPLIES (from DB) ---')
    console.log('Raw Received Emails API Response:', receivedResponse.data)

    const receivedEmails = receivedResponse.data.map(reply => {
        const receivedAt = new Date(reply.received_at)
        return {
            id: `received-${reply.id}`,
            type: 'email',
            direction: 'received',
            title: reply.title, // 'Reply Received'
            description: reply.description, // Subject
            from: reply.from,
            to: reply.to, 
            body_plain: reply.body_plain,
            body_html: reply.body_html,
            attachments: reply.attachments,
            date: receivedAt.toLocaleDateString(),
            time: receivedAt.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
        }
    })
      console.log('Formatted Received Emails:', receivedEmails)

      // --- IMPORTANT: Push received emails to the activities array ---
      activities.value.push(...receivedEmails);

      activities.value.sort((a, b) => {
          const dateA = new Date(`${a.date} ${a.time}`);
          const dateB = new Date(`${b.date} ${b.time}`);
          return dateB.getTime() - dateA.getTime(); 
      });

    } catch (error) {
      console.error('Failed to load emails:', error)
      if (error.response && error.response.status === 401) {
          console.error('Authentication failed. Please log in again.');
      }
    }
})


  // 💡 Computed: Filtered list based on menu
const filteredActivities = computed(() => {
  if (activeMenuItem.value === 'all') return activities.value

  if (activeMenuItem.value === 'email' || activeMenuItem.value === 'sms') {
    return activities.value.filter((a) => {
      if (a.type !== activeMenuItem.value) return false
      if (messageFilter.value === 'all') return true
      return a.direction === messageFilter.value && a.type === activeMenuItem.value
    })
  }

  return activities.value.filter((a) => a.type === activeMenuItem.value)
})


// Action when user clicks reply
const replyToEmail = (activity) => {
  alert(`Replying to: ${activity.title} from ${activity.from}. Full subject: ${activity.description}`);
}

// Function to view full email content in a modal
const viewEmailContent = (activity) => {
  selectedEmail.value = activity;
  showEmailModal.value = true;
};

// Helper to extract file name from path
const getFileNameFromPath = (path) => {
  return path.substring(path.lastIndexOf('/') + 1);
};

</script>

<style scoped>
.container {
  display: flex;
}

.sidebar {
  padding: 10px;
  border-radius: 8px;
  background: #f8f9fa;
  inline-size: 180px;
}

.menu-item {
  display: flex;
  align-items: center;
  padding: 8px;
  border-radius: 6px;
  cursor: pointer;
  margin-block-end: 6px;
}

.menu-item .icon {
  margin-inline-end: 8px;
}

.menu-item.active {
  background-color: #007bff;
  color: white;
}

.main-content {
  flex-grow: 1;
}
</style>


<style scoped>
.container {
  display: flex;
}

.sidebar {
  padding: 10px;
  border-radius: 8px;
  background: #f8f9fa;
  inline-size: 180px;
}

.menu-item {
  display: flex;
  align-items: center;
  padding: 8px;
  border-radius: 6px;
  cursor: pointer;
  margin-block-end: 6px;
}

.menu-item .icon {
  margin-inline-end: 8px;
}

.menu-item.active {
  background-color: #007bff;
  color: white;
}

.main-content {
  flex-grow: 1;
}
</style>
