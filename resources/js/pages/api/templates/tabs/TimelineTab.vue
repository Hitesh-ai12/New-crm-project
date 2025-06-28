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

const route = useRoute();
const leadId = ref(route.params.id);

const showSmsModal = ref(false);
const selectedSms = ref({});
const showEmailModal = ref(false);
const selectedEmail = ref({});
const activeMenuItem = ref('all');
const messageFilter = ref('all');
const activities = ref([]);

// Format a datetime string safely
const formatDateTime = (dateTimeStr) => {
  const dt = new Date(dateTimeStr);
  if (isNaN(dt)) return { date: '', time: '' };
  return {
    date: dt.toLocaleDateString(),
    time: dt.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
  };
};

// Fetch all SMS and Emails
onMounted(async () => {
  const token = localStorage.getItem('auth_token');
  const headers = { Authorization: `Bearer ${token}` };

  try {
    // ✅ Get SMS messages
    const smsResponse = await axios.get(`/api/lead/${leadId.value}/sms`, { headers });

    const smsMessages = smsResponse.data.map(s => {
      const dt = new Date(`${s.date} ${s.time}`);
      return {
        id: s.id,
        type: 'sms',
        direction: s.direction,
        phone: s.phone,
        title: s.title,
        description: s.description,
        leadId: s.leadId,
        date: dt.toLocaleDateString(),
        time: dt.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
      };
    });

    activities.value.push(...smsMessages);

    // ✅ Get sent emails
    const res = await axios.get(`/api/email/lead/${leadId.value}`);
    const allEmails = res.data;

    // Format emails
    const formattedEmails = allEmails.map(email => {
      const dt = new Date(`${email.date} ${email.time}`);

      return {
        id: `${email.direction}-${Math.random().toString(36).substr(2, 9)}`, // or use email.id if exists
        type: 'email',
        direction: email.direction,
        title: email.direction === 'sent' ? 'Sent Email' : 'Reply Received',
        description: email.subject,
        date: dt.toLocaleDateString(),
        time: dt.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
        from: email.from,
        to: email.to,
        body_plain: email.description,
        body_html: email.body_html || null,
        attachments: email.attachments || []
      };
    });

    // Add to activity list
    activities.value.push(...formattedEmails);


    // ✅ Sort all activities by date-time (descending)
    activities.value.sort((a, b) => {
      const dateA = new Date(`${a.date} ${a.time}`);
      const dateB = new Date(`${b.date} ${b.time}`);
      return dateB.getTime() - dateA.getTime();
    });

  } catch (err) {
    console.error('Failed to load timeline data:', err);
  }
});

// 🧠 Computed filtered activities by menu and direction
const filteredActivities = computed(() => {
  if (activeMenuItem.value === 'all') return activities.value;

  return activities.value.filter(a => {
    if (a.type !== activeMenuItem.value) return false;
    if (messageFilter.value === 'all') return true;
    return a.direction === messageFilter.value;
  });
});

// Open SMS Modal
const viewSmsContent = (sms) => {
  selectedSms.value = sms;
  showSmsModal.value = true;
};

// Open Email Modal
const viewEmailContent = (email) => {
  selectedEmail.value = email;
  showEmailModal.value = true;
};

// Reply to Email Action
const replyToEmail = (activity) => {
  alert(`Replying to: ${activity.title} from ${activity.from}. Full subject: ${activity.description}`);
};

// Extract file name from path
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
