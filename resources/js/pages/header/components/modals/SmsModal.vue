<template>
  <teleport to="body">
    <div v-if="showSmsLeadModal" class="integrte_LeadModal">
      <div class="modal_LeadContent">
        <div class="modal-header">
          <h2>Select Leads</h2>
          <button @click="showSmsLeadModal = false">×</button>
        </div>
        <div class="modal_LeadBody">
          <table>
            <thead>
              <tr>
                <th>Select</th>
                <th>Name</th>
                <th>Phone</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="lead in leads" :key="lead.id">
                <td>
                  <input
                    type="checkbox"
                    :value="lead.phone"
                    :checked="isLeadSelected(lead)"
                    @change="toggleSmsLeadSelection(lead)"
                  />
                </td>
                <td>{{ lead.first_name }} {{ lead.last_name }}</td>
                <td>{{ lead.phone }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="modal-footer">
          <button @click="addSelectedPhones">Add Selected</button>
        </div>
      </div>
    </div>
  </teleport>

  <div class="modal-wrapper" v-if="show">
    <div class="modal-backdrop-blur" @click="close"></div>

    <div
      class="modal fade show d-block"
      tabindex="-1"
      role="dialog"
      v-if="show"
      @click.self="close"
    >
      <div
        class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"
        :class="{ 'modal-fullscreen': isFullscreen }"
      >
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Compose SMS</h5>
            <div class="d-flex align-items-center gap-2">
              <i
                class="fas fa-expand-alt text-primary cursor-pointer"
                @click="expandSmsModal"
                title="Expand"
              ></i>
              <button
                type="button"
                class="btn-close"
                @click="$emit('close')"
                aria-label="Close"
              ></button>
            </div>
          </div>

          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label" for="sms-from">From:</label>
              <input
                type="text"
                id="sms-from"
                class="form-control"
                v-model="smsData.from"
                placeholder="Sender name or phone"
              />
            </div>

            <div class="mb-3">
              <label class="form-label">To:</label>
              <div class="d-flex flex-wrap align-items-center gap-2">
                <span
                  v-for="(lead, index) in selectedLeadsList"
                  :key="lead.id"
                  class="badge bg-success"
                >
                  {{ lead.first_name }}
                  <button
                    type="button"
                    class="btn-close btn-close-white btn-sm ms-2"
                    @click="removeSelectedLead(index)"
                  ></button>
                </span>
                <button
                  type="button"
                  class="btn btn-sm btn-primary"
                  @click="openSmsLeadModal"
                >
                  +
                </button>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Template:</label>
              <select
                class="form-select"
                v-model="smsData.template"
                @change="loadSmsTemplate"
              >
                <option value="" disabled>Select a Template</option>
                <option
                  v-for="template in smsTemplates"
                  :key="template.id"
                  :value="template.id"
                >
                  {{ template.name }}
                </option>
              </select>
            </div>


            <div class="mb-3">
              <label class="form-label">Message:</label>
              <Editor
                id="sms-editor"
                :init="smsEditorInit"
                :modelValue="smsData.message"
                @update:modelValue="smsData.message = $event"
              />
            </div>

            <div class="mb-4">
              <label class="form-label">Schedule SMS:</label>
              <input
                type="datetime-local"
                class="form-control"
                v-model="smsData.schedule"
              />
            </div>

            <div class="d-flex justify-content-end gap-2">
              <button class="btn btn-info" @click="previewSms">
                Preview
              </button>
              <button class="btn btn-success" @click="sendSms">
                Send SMS
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Editor from '@tinymce/tinymce-vue';
import axios from 'axios';
import Swal from 'sweetalert2';

import { nextTick, onMounted, ref, watch } from 'vue';

// Props & Emits
const props = defineProps({
  show: {
    type: Boolean,
    required: true,
  },
});
const emit = defineEmits(['close']);

const isFullscreen = ref(false);
const showSmsLeadModal = ref(false);
const selectedLeadsList = ref([]);
const leads = ref([]); 
const selectedLeads = ref([]);
const smsTinyEditor = ref(null); 

const mergeFields = ref([
  '{{first_name}}',
  '{{last_name}}',
  '{{email}}',
  '{{city}}',
  '{{phone}}',
]);

// SMS Modal Data
const smsData = ref({
  from: '',
  to: '',
  toName: '',
  subject: '',
  message: '',
  template: '',
  schedule: '',
});

watch(
  () => props.show,
  (newVal) => {
    if (newVal) {
      nextTick(() => {
        fetchSmsTemplates(); 
      });
    } else {
      smsData.value = {
        from: '',
        to: '',
        toName: '',
        subject: '',
        message: '',
        template: '',
        schedule: '',
      };
      selectedLeadsList.value = [];
      selectedLeads.value = [];
    }
  }
);

// Close modal function
const close = () => {
  emit('close');
};

// Update "To" field dynamically
const updateSmsToField = () => {
  smsData.value.to = selectedLeadsList.value.map((lead) => lead.phone).join(', ');
  smsData.value.toName = selectedLeadsList.value.map((lead) => lead.first_name).join(', ');
};

// Toggle a lead selection
const toggleSmsLeadSelection = (lead) => {
  const index = selectedLeads.value.indexOf(lead.id);
  if (index === -1) {
    selectedLeads.value.push(lead.id);
    selectedLeadsList.value.push({
      id: lead.id,
      first_name: lead.first_name,
      phone: lead.phone,
    });
  } else {
    selectedLeads.value.splice(index, 1);
    selectedLeadsList.value = selectedLeadsList.value.filter((l) => l.id !== lead.id);
  }
  updateSmsToField();
};

const smsEditorInit = {
  height: 300,
  menubar: false,
  plugins: 'link lists code',
  toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist | mergeFieldButton',
  branding: false,
  setup(editor) {
    editor.ui.registry.addMenuButton('mergeFieldButton', {
      text: 'Merge Fields',
      fetch(callback) {
        const items = mergeFields.value.map(field => ({
          type: 'menuitem',
          text: field,
          onAction: () => {
            editor.insertContent(field);
          },
        }));
        callback(items);
      }
    });
  },
};




// Check if a lead is selected
const isLeadSelected = (lead) => {
  return selectedLeads.value.includes(lead.id);
};
const authToken = localStorage.getItem('auth_token');

const fetchLeads = async () => {
  try {
    const res = await axios.get('/api/leads', {
      headers: {
        Authorization: `Bearer ${authToken}`,
      },
    });
    leads.value = res.data;
  } catch (error) {
    console.error('Error fetching leads', error);
  }
};

const openSmsLeadModal = async () => {
  await fetchLeads();
  showSmsLeadModal.value = true;
};

// Remove a selected lead
const removeSelectedLead = (index) => {
  const removedLead = selectedLeadsList.value[index];
  selectedLeadsList.value.splice(index, 1);
  selectedLeads.value = selectedLeads.value.filter((id) => id !== removedLead.id);
  updateSmsToField();
};

// Update "To" field when leads change
watch(selectedLeads, () => {
  smsData.value.to = selectedLeadsList.value.map((lead) => lead.phone).join(', ');
  smsData.value.toName = selectedLeadsList.value.map((lead) => lead.first_name).join(', ');
});

const addSelectedPhones = () => {
  showSmsLeadModal.value = false;
};

const closeSmsLeadModal = () => {
  showSmsLeadModal.value = false;
};


const selectSmsLead = (lead) => {
  selectedLeads.value = [lead.id];
  selectedLeadsList.value = [{ id: lead.id, first_name: lead.first_name, phone: lead.phone }];
  updateSmsToField();
  showSmsLeadModal.value = false;
};

const smsTemplates = ref([]);
const loadingTemplates = ref(false);

const fetchSmsTemplates = async () => {
  loadingTemplates.value = true;
  try {
    const response = await axios.get('/api/sms-templates');
    smsTemplates.value = response.data.templates;
  } catch (error) {
    Swal.fire('Error', 'Could not load SMS templates.', 'error');
  } finally {
    loadingTemplates.value = false;
  }
};

const loadSmsTemplate = () => {
  if (!Array.isArray(smsTemplates.value)) return;
  const selectedTemplate = smsTemplates.value.find(
    (template) => template.id === smsData.value.template
  );
  if (selectedTemplate) {
    smsData.value.message = selectedTemplate.content;


  }
};



const previewSms = () => {
  Swal.fire({
    title: 'SMS Preview',
    html: `<strong>From:</strong> ${smsData.value.from}<br>
              <strong>To:</strong> ${smsData.value.to}<br>
              <strong>Message:</strong><br> ${smsData.value.message}`,
    icon: 'info',
  });
};

const sendSms = async () => {
  const userId = parseInt(localStorage.getItem('user_id'));

  if (!userId || isNaN(userId)) {
    Swal.fire('Missing User ID', 'Please log in again.', 'error');
    return;
  }

  if (selectedLeadsList.value.length === 0 || !smsData.value.message.trim()) {
    Swal.fire('Missing Information', 'Recipient and message cannot be empty.', 'error');
    return;
  }

  try {
    const selectedPhones = selectedLeadsList.value.map((lead) => lead.phone);

    const selectedLeadIds = selectedLeadsList.value.map((lead) => lead.id);

    await axios.post('/api/send-sms', {
      lead_ids: selectedLeadIds,
      user_id: userId,
      from: smsData.value.from,
      to: selectedPhones.join(', '), 
      message: smsData.value.message,
      schedule: smsData.value.schedule || null,
    });

    Swal.fire('Success', 'SMS sent successfully.', 'success');
    close();
  } catch (err) {
    console.error('SMS Send Error:', err);
    Swal.fire('Error', 'Failed to send SMS.', 'error');
  }
};

const expandSmsModal = () => {
  isFullscreen.value = !isFullscreen.value;
  nextTick(() => {
  });
};

onMounted(() => {
  fetchSmsTemplates();
  
});
</script>

<style scoped>
.modal-backdrop {
  background-color: rgba(0, 0, 0, 50%);
}

.modal-fullscreen {
  margin: 0;
  block-size: 100%;
  inline-size: 100%;
  max-inline-size: 100%;
}

.cursor-pointer {
  cursor: pointer;
}

.btn-close-white {
  filter: invert(1);
}

.modal-backdrop-blur {
  position: fixed;
  z-index: 1040;
  backdrop-filter: blur(4px);
  background-color: rgba(0, 0, 0, 40%);
  inset: 0;
}

.modal-wrapper {
  position: fixed;
  z-index: 1050;
  inset: 0;
}

/* Custom styles for selected leads display */
.selected-leads {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  padding: 5px;
  border: 1px solid #ced4da;
  border-radius: 0.25rem;
  gap: 5px;
  min-block-size: 38px; /* Match input height */
}

.selected-tag {
  display: inline-flex;
  align-items: center;
  border-radius: 0.25rem;
  background-color: #28a745; /* Bootstrap success color */
  color: white;
  gap: 5px;
  padding-block: 0.25em;
  padding-inline: 0.6em;
}

.selected-tag button {
  padding: 0;
  border: none;
  background: none;
  color: white;
  cursor: pointer;
  font-size: 0.8em;
  line-height: 1;
}

.selected-tag button:focus {
  outline: none;
}

/* Add this to your <style scoped> section */
.integrte_LeadModal {
  position: fixed; /* Use fixed positioning relative to the viewport */
  z-index: 1060; /* Ensure it's above the main modal (modal-wrapper z-index is 1050) */
  display: flex; /* Use flex to easily center content if needed */
  flex-direction: column;
  background-color: white; /* Give it a background */
  block-size: 100%; /* Take full height of the viewport */
  box-shadow: -2px 0 5px rgba(0, 0, 0, 20%); /* Add a subtle shadow */
  inline-size: 350px; /* Adjust width as needed */
  inset-block-start: 0;
  inset-inline-end: 0; /* Position it on the right side */
}

.integrte_LeadModal .modal_LeadContent {
  display: flex;
  flex-direction: column;
  block-size: 100%;
  inline-size: 100%;
}

.integrte_LeadModal .modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 15px;
  border-block-end: 1px solid #eee;
}

.integrte_LeadModal .modal_LeadBody {
  flex-grow: 1; /* Allow content to take available space */
  padding: 15px;
  overflow-y: auto; /* Enable scrolling for the lead list */
}

.integrte_LeadModal .modal_LeadBody table {
  border-collapse: collapse;
  inline-size: 100%;
}

.integrte_LeadModal .modal_LeadBody th,
.integrte_LeadModal .modal_LeadBody td {
  padding: 8px;
  border-block-end: 1px solid #eee;
  text-align: start;
}

.integrte_LeadModal .modal-footer {
  padding: 15px;
  border-block-start: 1px solid #eee;
  text-align: end; /* Align button to the right */
}
</style>
