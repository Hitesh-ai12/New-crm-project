<template>
  <div class="modal fade show d-block" tabindex="-1" role="dialog" v-if="show" @click.self="close">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" :class="{ 'modal-fullscreen': isFullscreen }">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Compose SMS</h5>
          <div class="d-flex align-items-center gap-2">
            <i class="fas fa-expand-alt text-primary cursor-pointer" @click="expandSmsModal" title="Expand"></i>
            <button type="button" class="btn-close" @click="close" aria-label="Close"></button>
          </div>
        </div>

        <div class="modal-body">
          <!-- From -->
          <div class="mb-3">
            <label class="form-label" for="sms-from">From:</label>
            <input type="text" id="sms-from" class="form-control" v-model="smsData.from" placeholder="Sender name or phone" />
          </div>

          <!-- To -->
          <div class="mb-3">
            <label class="form-label">To:</label>
            <div class="d-flex flex-wrap align-items-center gap-2">
              <span v-for="(lead, index) in selectedLeadsList" :key="lead.id" class="badge bg-success">
                {{ lead.first_name }}
                <button type="button" class="btn-close btn-close-white btn-sm ms-2" @click="removeSelectedLead(index)"></button>
              </span>
              <button type="button" class="btn btn-sm btn-primary" @click="openSmsLeadModal">+</button>
            </div>
          </div>

          <!-- Template -->
          <div class="mb-3">
            <label class="form-label">Template:</label>
            <select class="form-select" v-model="smsData.template" @change="loadSmsTemplate">
              <option value="" disabled>Select a Template</option>
              <option v-for="template in smsTemplates" :key="template.id" :value="template.id">
                {{ template.name }}
              </option>
            </select>
          </div>

          <!-- Merge Fields -->
          <div class="mb-3">
            <label class="form-label">Merge Field:</label>
            <select class="form-select" @change="insertMergeTag($event)">
              <option value="">Select Tag</option>
              <option v-for="tag in mergeFields" :key="tag" :value="tag">{{ tag }}</option>
            </select>
          </div>

          <!-- TinyMCE -->
          <div class="mb-3">
            <label class="form-label">Message:</label>
            <textarea id="sms-editor"></textarea>
          </div>

          <!-- Schedule -->
          <div class="mb-4">
            <label class="form-label">Schedule SMS:</label>
            <input type="datetime-local" class="form-control" v-model="smsData.schedule" />
          </div>

          <!-- Actions -->
          <div class="d-flex justify-content-end gap-2">
            <button class="btn btn-info" @click="previewSms">Preview</button>
            <button class="btn btn-success" @click="sendSms">Send SMS</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<script setup>
import axios from 'axios';
import Swal from 'sweetalert2';
import tinymce from 'tinymce';
import { nextTick, ref, watch } from 'vue';

// TinyMCE Plugins
import 'tinymce/icons/default';
import 'tinymce/plugins/code';
import 'tinymce/plugins/image';
import 'tinymce/plugins/link';
import 'tinymce/plugins/lists';
import 'tinymce/themes/silver';

// Props & Emits
const props = defineProps({
  show: {
    type: Boolean,
    required: true
  }
});
const emit = defineEmits(['close']);

// Reactive State
const isFullscreen = ref(false);
const showSmsLeadModal = ref(false);
const selectedLeadsList = ref([]);
const mergeFields = ref(['{{first_name}}', '{{last_name}}']);

const smsData = ref({
  from: '',
  to: '',
  toName: '',
  subject: '',
  message: '',
  template: '',
  schedule: ''
});

const smsTemplates = ref([]);
const loadingTemplates = ref(false);

// Watch for modal show prop changes
watch(() => props.show, async (newVal) => {
  if (newVal) {
    await nextTick();
    initializeTinyMCE();
    fetchSmsTemplates();
  } else {
    destroyTinyMCE();
  }
});

// Update 'to' and 'toName' fields based on selected leads
const updateSmsToField = () => {
  smsData.value.to = selectedLeadsList.value.map(l => l.phone).join(', ');
  smsData.value.toName = selectedLeadsList.value.map(l => l.first_name).join(', ');
};

// Insert merge tag at cursor position in TinyMCE editor
const insertMergeTag = (event) => {
  const tag = event.target.value;
  if (tag && tinymce.get('sms-editor')) {
    tinymce.get('sms-editor').execCommand('mceInsertContent', false, ` ${tag} `);
    event.target.value = '';
  }
};

// Load template content into TinyMCE editor
const loadSmsTemplate = () => {
  const selected = smsTemplates.value.find(t => t.id === smsData.value.template);
  if (selected) {
    smsData.value.message = selected.content;
    const editor = tinymce.get('sms-editor');
    if (editor) editor.setContent(selected.content);
  }
};

// Remove a lead from the selected leads list and update fields
const removeSelectedLead = (index) => {
  selectedLeadsList.value.splice(index, 1);
  updateSmsToField();
};

// Toggle fullscreen state
const expandSmsModal = () => {
  isFullscreen.value = !isFullscreen.value;
};

// Initialize TinyMCE editor
const initializeTinyMCE = () => {
  if (tinymce.get('sms-editor')) return; // Prevent multiple initializations

  tinymce.init({
    selector: '#sms-editor',
    height: 300,
    license_key: 'gpl',
    menubar: "file edit view insert format tools table help",
    plugins: 'link lists image code',
    toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist | link code | mergeFieldButton',
    setup: (editor) => {
      editor.on('Change', () => {
        smsData.value.message = editor.getContent();
      });
      editor.ui.registry.addMenuButton('mergeFieldButton', {
        text: 'Merge Fields',
        fetch: (callback) => {
          const items = mergeFields.value.map(field => ({
            type: 'menuitem',
            text: field,
            onAction: () => editor.insertContent(field),
          }));
          callback(items);
        }
      });
    }
  });
};

// Destroy TinyMCE editor instance
const destroyTinyMCE = () => {
  const editor = tinymce.get('sms-editor');
  if (editor) {
    editor.remove();
  }
};

// Fetch SMS templates from API
const fetchSmsTemplates = async () => {
  loadingTemplates.value = true;
  try {
    const res = await axios.get('/api/sms-templates');
    smsTemplates.value = res.data.templates || [];
  } catch (err) {
    Swal.fire('Error', 'Could not load SMS templates.', 'error');
  } finally {
    loadingTemplates.value = false;
  }
};

// Send SMS via API
const sendSms = async () => {
  const userId = parseInt(localStorage.getItem('user_id'));
  const editor = tinymce.get('sms-editor');
  const messageContent = editor?.getContent({ format: 'text' }) || smsData.value.message;

  if (!userId || !smsData.value.from || selectedLeadsList.value.length === 0 || !messageContent.trim()) {
    Swal.fire('Missing Info', 'Sender, recipient, and message are required.', 'error');
    return;
  }

  try {
    const selectedPhones = selectedLeadsList.value.map(l => l.phone);
    const selectedLeadIds = selectedLeadsList.value.map(l => l.id);

    await axios.post('/api/send-sms', {
      user_id: userId,
      from: smsData.value.from,
      to: selectedPhones.join(', '),
      lead_ids: selectedLeadIds,
      subject: smsData.value.subject,
      message: messageContent,
      schedule: smsData.value.schedule || null,
    });

    Swal.fire('Success', 'SMS sent successfully.', 'success');
    emit('close');
  } catch (err) {
    Swal.fire('Error', 'Failed to send SMS.', 'error');
  }
};

// Show SMS preview modal
const previewSms = () => {
  const content = tinymce.get('sms-editor')?.getContent({ format: 'text' }) || smsData.value.message;

  Swal.fire({
    title: 'SMS Preview',
    html: `
      <strong>From:</strong> ${smsData.value.from}<br>
      <strong>To:</strong> ${smsData.value.to}<br>
      <strong>Message:</strong><br>${content}
    `,
    icon: 'info'
  });
};
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
</style>
