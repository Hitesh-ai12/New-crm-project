<script setup>
import Editor from '@tinymce/tinymce-vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { nextTick, ref, watch } from 'vue';

// Props & Emits
const props = defineProps({
  show: {
    type: Boolean,
    required: true,
  },
});

const emit = defineEmits(['close']);

const isFullscreen = ref(false);
const showEmailLeadModal = ref(false);
const leads = ref([]); // Holds all fetched leads {id, first_name, last_name, email, ...}
const selectedLeads = ref([]); // Holds IDs of selected leads: [1, 5, 8]
const selectedEmails = ref([]); // Holds email strings of selected leads: ['a@example.com', 'b@example.com']
const tinymceEditor = ref(null); // This will hold the TinyMCE editor instance

const emailData = ref({
  from: 'hiteshpandey732195@gmail.com',
  to: '', // This will be a comma-separated string of emails
  subject: '',
  message: '', // v-model for TinyMCE content
  template: '',
  attachments: [],
  schedule: '',
});

const mergeFields = ref([
  '{{first_name}}',
  '{{last_name}}',
  '{{email}}',
  '{{city}}',
  '{{phone}}',
]);

const signatures = ref([]);
const selectedSignature = ref('');

const emailTemplates = ref([]);
const loadingTemplates = ref(false);

const authToken = localStorage.getItem('auth_token');

// --- General Modal Control ---
const close = () => {
  emit('close');
  // When closing, reset everything for the next open
  emailData.value = {
    from: 'hiteshpandey732195@gmail.com',
    to: '',
    subject: '',
    message: '',
    template: '',
    attachments: [],
    schedule: '',
  };
  selectedLeads.value = [];
  selectedEmails.value = [];
  selectedSignature.value = '';

  // Optional: Clear TinyMCE editor content on close for a fresh start next time
  if (tinymceEditor.value) {
    tinymceEditor.value.setContent('');
  }
};

const expandEmailModal = () => {
  isFullscreen.value = !isFullscreen.value;
  if (tinymceEditor.value) {
    tinymceEditor.value.execCommand('mceRepaint');
  }
};

// --- Lead Selection Logic ---
const fetchLeads = async () => {
  try {
    const res = await axios.get('/api/leads', {
      headers: {
        Authorization: `Bearer ${authToken}`,
      },
    });
    // Ensure leads.value gets the array of lead objects
    // Assuming your API returns data directly or inside a 'data' key
    leads.value = res.data.data || res.data;
  } catch (error) {
    console.error('Error fetching leads', error);
    Swal.fire('Error', 'Could not load leads.', 'error');
  }
};

const openEmailLeadSelectionModal = async () => {
  await fetchLeads(); // Fetch leads every time modal opens to ensure fresh data
  showEmailLeadModal.value = true;
};

const isEmailLeadSelected = (lead) => {
  return selectedLeads.value.includes(lead.id);
};

const toggleEmailLeadSelection = (lead) => {
  const index = selectedLeads.value.indexOf(lead.id);
  if (index === -1) {
    selectedLeads.value.push(lead.id);
  } else {
    selectedLeads.value.splice(index, 1);
  }
  // The watchers for selectedLeads and selectedEmails will handle 'emailData.to' update.
};

const addSelectedEmailsAndCloseModal = () => {
  showEmailLeadModal.value = false;
};

const removeEmail = (index) => {
  const emailToRemove = selectedEmails.value[index];
  selectedEmails.value.splice(index, 1);

  const leadRemoved = leads.value.find(lead => lead.email === emailToRemove);
  if (leadRemoved) {
    selectedLeads.value = selectedLeads.value.filter(id => id !== leadRemoved.id);
  }
};

// --- Watchers for 'To' field updates ---
watch(selectedLeads, (newSelectedLeadIds) => {
  selectedEmails.value = leads.value
    .filter((lead) => newSelectedLeadIds.includes(lead.id))
    .map((lead) => lead.email);
}, { deep: true });

watch(selectedEmails, (newSelectedEmails) => {
  emailData.value.to = newSelectedEmails.join(', ');
}, { deep: true });

// --- TinyMCE Editor Logic ---
// TinyMCE Editor Logic
const handleEditorInit = (event, editor) => {
  tinymceEditor.value = editor;

  // If there's pre-filled message content, load it into editor
  if (emailData.value.message && editor.getContent() === '') {
    editor.setContent(emailData.value.message);
  }
};


const editorInit = {
  height: 300,
  menubar: false,
  plugins: 'link lists image code preview fullscreen',
  toolbar:
    'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent | image link code | mergefields signatures | preview fullscreen',
  branding: false,
  resize: true,
  automatic_uploads: true,

  setup(editor) {
    editor.ui.registry.addMenuButton('mergefields', {
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
      },
    });

    editor.ui.registry.addMenuButton('signatures', {
      text: 'Signatures',
      fetch(callback) {
        const items = signatures.value.map(sig => ({
          type: 'menuitem',
          text: sig.name || `Signature ${sig.id}`,
          onAction: () => {
            editor.insertContent(sig.content);
          },
        }));
        callback(items);
      },
    });
  },
};


// --- Template Logic ---
const fetchEmailTemplates = async () => {
  loadingTemplates.value = true;
  try {
    const response = await axios.get('/api/templates', {
      headers: { Authorization: `Bearer ${authToken}` },
    });
    emailTemplates.value = response.data.data ?? response.data;
  } catch (error) {
    console.error('Error fetching email templates:', error);
    Swal.fire('Error', 'Could not load email templates.', 'error');
  } finally {
    loadingTemplates.value = false;
  }
};

const loadEmailTemplate = async () => { // Made async to await nextTick
  if (!Array.isArray(emailTemplates.value)) return;
  const selectedTemplate = emailTemplates.value.find(
    (template) => template.id === emailData.value.template
  );

  if (selectedTemplate) {
    emailData.value.subject = selectedTemplate.subject || '';

    // Wait for the next tick to ensure TinyMCE has rendered and handleEditorInit fired
    await nextTick();

    // Set message content in TinyMCE ONLY if tinymceEditor.value is set
    if (tinymceEditor.value) {
      tinymceEditor.value.setContent(selectedTemplate.content || '');
    } else {
      // Fallback: if editor not ready yet, update the v-model directly
      emailData.value.message = selectedTemplate.content || '';
    }

    if (selectedTemplate.attachment_path) {
      emailData.value.attachments = [{ name: selectedTemplate.attachment_path.split('/').pop(), path: selectedTemplate.attachment_path }];
    } else {
      emailData.value.attachments = [];
    }

    // Add signature after template content is loaded, if one is selected
    if (selectedSignature.value && tinymceEditor.value) {
      const currentContent = tinymceEditor.value.getContent();
      if (!currentContent.includes(selectedSignature.value)) {
        tinymceEditor.value.setContent(currentContent + '<br><br>' + selectedSignature.value);
      }
    }
  }
};

// --- Attachments Logic ---
const handleAttachments = (event) => {
  emailData.value.attachments = Array.from(event.target.files);
};

const removeAttachment = (index) => {
  emailData.value.attachments.splice(index, 1);
};

// --- Signatures Logic ---
const fetchSignatures = async () => {
  try {
    const response = await axios.get('/api/signatures', {
      headers: { Authorization: `Bearer ${authToken}` },
    });
    signatures.value = response.data.data;
  } catch (error) {
    console.error('Error fetching signatures:', error);
    Swal.fire('Error', 'Could not load signatures.', 'error');
  }
};

const addSignatureToBody = () => {
  if (selectedSignature.value && tinymceEditor.value) {
    const currentContent = tinymceEditor.value.getContent();
    if (!currentContent.includes(selectedSignature.value)) {
      tinymceEditor.value.setContent(currentContent + '<br><br>' + selectedSignature.value);
    }
  }
};

// --- Email Sending & Preview ---
const insertMergeTagIntoSubject = (event) => {
  const tag = event.target.value;
  if (tag) {
    emailData.value.subject += ` ${tag}`;
    event.target.value = '';
  }
};

const insertMergeTagIntoBody = (event) => {
  const tag = event.target.value;
  if (tag && tinymceEditor.value) {
    tinymceEditor.value.insertContent(` ${tag}`);
    event.target.value = '';
  }
};

const previewEmail = () => {
  // Ensure tinymceEditor.value is available before calling getContent
  const messageContent = tinymceEditor.value ? tinymceEditor.value.getContent() : emailData.value.message;
  Swal.fire({
    title: 'Email Preview',
    html: `<strong>From:</strong> ${emailData.value.from}<br>
           <strong>To:</strong> ${emailData.value.to}<br>
           <strong>Subject:</strong> ${emailData.value.subject}<br>
           <strong>Message:</strong><br> ${messageContent}`,
    icon: 'info',
    customClass: { container: 'swal-text-left' },
    width: 'auto',
  });
};

const sendEmail = async () => {
  const userId = parseInt(localStorage.getItem('user_id'));
  if (!userId || isNaN(userId)) {
    Swal.fire({ icon: 'error', title: 'Missing User ID', text: 'Please log in again. User ID is missing or invalid.' });
    return;
  }

  // CRITICAL FIX: Ensure tinymceEditor.value is available before trying to get content
  // If the editor somehow isn't ready, use emailData.value.message directly (from v-model)
  const messageToSend = tinymceEditor.value ? tinymceEditor.value.getContent() : emailData.value.message;

  if (selectedLeads.value.length === 0 || !emailData.value.subject.trim() || !messageToSend.trim()) {
    Swal.fire({ icon: 'error', title: 'Missing Information', text: 'Recipient, Subject, and Message cannot be empty.', });
    return;
  }

  Swal.fire({
    title: 'Are you sure?',
    text: 'Do you want to send this email?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, send it!',
    cancelButtonText: 'Cancel',
  }).then(async (result) => {
    if (result.isConfirmed) {
      try {
        const formData = new FormData();
        formData.append('from', emailData.value.from);
        formData.append('subject', emailData.value.subject);
        formData.append('message', messageToSend);
        formData.append('template_id', emailData.value.template || '');
        formData.append('user_id', userId);
        formData.append('schedule', emailData.value.schedule || '');

        selectedLeads.value.forEach((id) => { formData.append('lead_ids[]', id); });
        selectedEmails.value.forEach((email) => { formData.append('to[]', email); });

        emailData.value.attachments.forEach((file) => {
          if (file instanceof File) {
            formData.append('attachments[]', file);
          } else if (file.path) {
            formData.append('attachment_paths[]', file.path);
          }
        });

        await axios.post('/api/send-email', formData, {
          headers: { 'Content-Type': 'multipart/form-data', Authorization: `Bearer ${authToken}` },
        });

        Swal.fire({ icon: 'success', title: 'Email Sent!', text: 'Your email has been successfully sent.' });
        close(); // Close the modal and reset data
      } catch (error) {
        console.error('Email Send Error:', error);
        Swal.fire({ icon: 'error', title: 'Failed to Send', text: 'Something went wrong. Please try again later.', });
      }
    }
  });
};

// --- Watch for 'show' prop to initialize/destroy components/fetch data ---
watch(
  () => props.show,
  async (newVal) => {
    if (newVal) {
      // Fetch dynamic data when modal opens
      await fetchEmailTemplates();
      await fetchSignatures();
      // Wait for nextTick to ensure the TinyMCE component has rendered
      await nextTick();
      // If a template was previously selected and the editor is now ready, load it.
      // Or simply ensure message is cleared for new compose
      if (tinymceEditor.value) {
        tinymceEditor.value.setContent(''); // Clear editor content for a new email
      }
    } else {
      // Reset is now handled in the `close()` function for consistency
    }
  },
  { immediate: true } // Run immediately when component mounts/is created
);
</script>

<template>
  <teleport to="body">
    <div v-if="showEmailLeadModal" class="integrte_LeadModal">
      <div class="modal_LeadContent">
        <div class="modal-header">
          <h2>Select Leads for Email</h2>
          <button @click="showEmailLeadModal = false">×</button>
        </div>
        <div class="modal_LeadBody">
          <table>
            <thead>
              <tr>
                <th>Select</th>
                <th>Name</th>
                <th>Email</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="lead in leads" :key="lead.id">
                <td>
                  <input
                    type="checkbox"
                    :value="lead.id"
                    :checked="isEmailLeadSelected(lead)"
                    @change="toggleEmailLeadSelection(lead)"
                  />
                </td>
                <td>{{ lead.first_name }} {{ lead.last_name }}</td>
                <td>{{ lead.email }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="modal-footer">
          <button @click="addSelectedEmailsAndCloseModal">Add Selected</button>
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
            <h5 class="modal-title">Compose Email</h5>
            <div class="d-flex align-items-center gap-2">
              <i
                class="fas fa-expand-alt text-primary cursor-pointer"
                @click="expandEmailModal"
                title="Expand"
              ></i>
              <button
                type="button"
                class="btn-close"
                @click="close" aria-label="Close"
              ></button>
            </div>
          </div>

          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label" for="email-from">From:</label>
              <input
                type="email"
                id="email-from"
                class="form-control"
                v-model="emailData.from"
                placeholder="Sender email address"
              />
            </div>

            <div class="mb-3">
              <label class="form-label">To:</label>
              <div class="d-flex flex-wrap align-items-center gap-2">
                <span
                  v-for="(email, index) in selectedEmails"
                  :key="index"
                  class="badge bg-success"
                >
                  {{ email }}
                  <button
                    type="button"
                    class="btn-close btn-close-white btn-sm ms-2"
                    @click="removeEmail(index)"
                  ></button>
                </span>
                <button
                  type="button"
                  class="btn btn-sm btn-primary"
                  @click="openEmailLeadSelectionModal"
                >
                  +
                </button>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label" for="email-subject">Subject:</label>
              <div class="d-flex gap-2 align-items-center">
                <input
                  type="text"
                  id="email-subject"
                  class="form-control"
                  v-model="emailData.subject"
                  placeholder="Email Subject"
                />

                <select class="form-select w-auto" @change="insertMergeTagIntoSubject($event)">
                  <option value="">+ Merge Field</option>
                  <option v-for="tag in mergeFields" :key="tag" :value="tag">
                    {{ tag }}
                  </option>
                </select>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Template:</label>
              <select
                class="form-select"
                v-model="emailData.template"
                @change="loadEmailTemplate"
              >
                <option value="" disabled>Select a Template</option>
                <option
                  v-for="template in emailTemplates"
                  :key="template.id"
                  :value="template.id"
                >
                  {{ template.name }}
                </option>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label">* Body</label>
              <Editor
                :init="editorInit"
                v-model="emailData.message"
                @init="handleEditorInit"
              />

            </div>

            <div class="mb-3">
              <label class="form-label">Attachments:</label>
              <input type="file" class="form-control" multiple @change="handleAttachments" />
              <div class="mt-2">
                <span
                  v-for="(attachment, index) in emailData.attachments"
                  :key="index"
                  class="badge bg-secondary me-2"
                >
                  {{ attachment.name || attachment.path || attachment }}
                  <button
                    type="button"
                    class="btn-close btn-close-white btn-sm ms-2"
                    @click="removeAttachment(index)"
                  ></button>
                </span>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Signature:</label>
              <select
                class="form-select"
                v-model="selectedSignature"
                @change="addSignatureToBody"
              >
                <option value="">No Signature</option>
                <option
                  v-for="signature in signatures"
                  :key="signature.id"
                  :value="signature.content"
                >
                  {{ signature.name || 'Signature ' + signature.id }}
                </option>
              </select>
            </div>

            <div class="mb-4">
              <label class="form-label">Schedule Email:</label>
              <input
                type="datetime-local"
                class="form-control"
                v-model="emailData.schedule"
              />
            </div>

            <div class="d-flex justify-content-end gap-2">
              <button class="btn btn-info" @click="previewEmail">Preview</button>
              <button class="btn btn-success" @click="sendEmail">Send Email</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
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

.integrte_LeadModal {
  position: fixed;
  z-index: 1060;
  display: flex;
  flex-direction: column;
  background-color: white;
  block-size: 100%;
  box-shadow: -4px 0 10px rgba(0, 0, 0, 20%);
  inline-size: 380px;
  inset-block-start: 0;
  inset-inline-end: 0;
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
  flex-grow: 1;
  padding: 15px;
  overflow-y: auto;
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
  text-align: end;
}

.selected-leads {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  padding: 5px;
  border: 1px solid #ced4da;
  border-radius: 0.25rem;
  gap: 5px;
  min-block-size: 38px;
}

.badge {
  display: inline-flex;
  align-items: center;
  border-radius: 0.25rem;
  font-size: 0.75em;
  font-weight: 700;
  gap: 5px;
  line-height: 1;
  padding-block: 0.35em;
  padding-inline: 0.65em;
  text-align: center;
  vertical-align: baseline;
  white-space: nowrap;
}

.badge .btn-close-white {
  font-size: 0.6em;
  margin-inline-start: 0.5em;
}

/* SweetAlert2 text alignment */
.swal-text-left .swal2-html-container {
  text-align: start !important;
}
</style>
