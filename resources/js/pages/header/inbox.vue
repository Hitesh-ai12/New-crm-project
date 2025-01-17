<template>
  <div class="inbox-page">
    <!-- Header Section -->
    <div class="header">
      <button
        :class="{ active: activeTab === 'email' }"
        @click="setActiveTab('email')"
      >
        Email
      </button>
      <button
        :class="{ active: activeTab === 'sms' }"
        @click="setActiveTab('sms')"
      >
        SMS
      </button>

      <button v-if="activeTab === 'email'" class="send-new-btn" @click="openEmailModal">
        Send New Email
      </button>
      <button v-if="activeTab === 'sms'" class="send-new-btn" @click="openSmsModal">
        Send New SMS
      </button>
    </div>

    <!-- Content Section -->
    <div :class="['content', { blurred: isModalOpen }]">
      <div v-if="activeTab === 'email'" class="email-tab">
        <h2>Email Tab</h2>
        <p>Manage your emails here.</p>
      </div>
      <div v-if="activeTab === 'sms'" class="sms-tab">
        <h2>SMS Tab</h2>
        <p>Manage your SMS messages here.</p>
      </div>
    </div>

    <!-- Email Modal -->
    <div v-if="showEmailModal" class="modal">
      <div class="modal-header">
        <h2>Compose Email</h2>
        <button @click="closeEmailModal">×</button>
      </div>
      <div class="modal-body">
        <!-- From -->
        <div class="form-group">
          <label for="from">From:</label>
          <input
            type="email"
            id="from"
            v-model="emailData.from"
            placeholder="Enter sender email"
          />
        </div>

        <!-- To -->
        <div class="form-group">
          <label for="to">To:</label>
          <input
            type="email"
            id="to"
            v-model="emailData.to"
            placeholder="Enter recipient email"
          />
        </div>

        <!-- Subject -->
        <div class="form-group">
          <label for="subject">Subject:</label>
          <input
            type="text"
            id="subject"
            v-model="emailData.subject"
            placeholder="Enter subject"
          />
        </div>

        <!-- Template List -->
        <div class="form-group">
          <label for="template">Template:</label>
          <select id="template" v-model="emailData.template" @change="loadEmailTemplate">
            <option value="">Select Template</option>
            <option v-for="template in templates" :key="template.id" :value="template.id">
              {{ template.name }}
            </option>
          </select>
        </div>

        <!-- TinyMCE Editor -->
        <textarea id="email-editor"></textarea>

        <!-- Attachments -->
        <div class="form-group">
          <label for="attachments">Attachments:</label>
          <input type="file" id="attachments" multiple @change="handleEmailAttachments" />
        </div>

        <!-- Preview and Schedule -->
        <div class="form-actions">
          <button @click="previewEmail">Preview</button>
          <button @click="scheduleEmail">Schedule</button>
          <button @click="sendEmail">Send Email</button>
        </div>
      </div>
    </div>

    <!-- SMS Modal -->
    <div v-if="showSmsModal" class="modal" :class="{ 'modal-fullscreen': isFullscreen }">
      <div class="modal-header">
        <h2>Compose SMS</h2>
        <!-- Expand Icon -->
        <i class="fas fa-expand-alt" @click="expandSmsModal" title="Expand"></i>
        <button @click="closeSmsModal">×</button>
      </div>
      <div class="modal-body">
        <!-- From -->
        <div class="form-group">
          <label for="sms-from">From:</label>
          <input
            type="text"
            id="sms-from"
            v-model="smsData.from"
            placeholder="Enter sender's name or phone number"
          />
        </div>

        <!-- To -->
        <div class="form-group">
          <label for="sms-to">To:</label>
          <input
            type="text"
            id="sms-to"
            v-model="smsData.to"
            placeholder="Enter recipient's phone number"
          />
        </div>

        <!-- Template List -->
        <div class="form-group">
          <label for="sms-template">Template:</label>
          <select id="sms-template" v-model="smsData.template" @change="loadSmsTemplate">
            <option value="">Select Template</option>
            <option v-for="template in smsTemplates" :key="template.id" :value="template.id">
              {{ template.name }}
            </option>
          </select>
        </div>

        <!-- Merge Tags -->
        <div class="form-group">
          <label for="merge-tags">Insert Merge Tag:</label>
          <select id="merge-tags" @change="insertMergeTag($event)">
            <option value="">Select Tag</option>
            <option v-for="tag in mergeTags" :key="tag" :value="tag">{{ tag }}</option>
          </select>
        </div>

        <!-- Message Body -->
        <div class="form-group">
          <label for="sms-message">Message:</label>
          <textarea
            id="sms-message"
            v-model="smsData.message"
            placeholder="Type your message here..."
            rows="5"
          ></textarea>
        </div>

        <!-- Schedule SMS -->
        <div class="form-group">
          <label for="sms-schedule">Schedule SMS:</label>
          <input type="datetime-local" id="sms-schedule" v-model="smsData.schedule" />
        </div>

        <!-- Actions -->
        <div class="form-actions">
          <button @click="previewSms">Preview</button>
          <button @click="sendSms">Send SMS</button>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import { nextTick, ref } from "vue";

export default {
  setup() {
    const activeTab = ref("email");
    const isModalOpen = ref(false);
    const showEmailModal = ref(false);
    const showSmsModal = ref(false);
    const editorInstance = ref(null);

    const emailData = ref({ template: "", attachments: [] });
    const smsData = ref({ template: "", schedule: "" });
    const isFullscreen = ref(false);

    const templates = ref([
      { id: 1, name: "Welcome", content: "Welcome, {name}!" },
      { id: 2, name: "Promotion", content: "Hello {name}, check our offers!" },
    ]);

    const smsTemplates = ref([
      { id: 1, name: "SMS Template 1", content: "Hello {name}, check this offer!" },
      { id: 2, name: "SMS Template 2", content: "Hey {name}, special deal for you!" },
    ]);

    const mergeTags = ref(["{name}", "{phone}"]);

    const setActiveTab = (tab) => (activeTab.value = tab);

    const initializeEditor = (selector) => {
      nextTick(() => {
        tinymce.init({
          selector,
          plugins: ["lists", "link"],
          menubar: false,
          toolbar: "undo redo | bold italic | bullist numlist | link",
          setup: (editor) => (editorInstance.value = editor),
        });
      });
    };

    const openEmailModal = () => {
      showEmailModal.value = true;
      initializeEditor("#email-editor");
    };

    const closeEmailModal = () => {
      showEmailModal.value = false;
      tinymce.remove("#email-editor");
    };

    const openSmsModal = () => {
      showSmsModal.value = true;
      initializeEditor("#sms-editor");
    };

    const closeSmsModal = () => {
      showSmsModal.value = false;
      tinymce.remove("#sms-editor");
    };

    const loadEmailTemplate = () => {
      const template = templates.value.find((t) => t.id === emailData.value.template);
      if (template && editorInstance.value) {
        editorInstance.value.setContent(template.content);
      }
    };

    const loadSmsTemplate = () => {
      const template = smsTemplates.value.find((t) => t.id === smsData.value.template);
      if (template && editorInstance.value) {
        editorInstance.value.setContent(template.content);
      }
    };

    const handleEmailAttachments = (event) => {
      emailData.value.attachments = Array.from(event.target.files);
    };

    const previewEmail = () => alert(editorInstance.value.getContent());
    const sendEmail = () => closeEmailModal();

    const previewSms = () => alert(editorInstance.value.getContent());
    const sendSms = () => closeSmsModal();

    return {
      activeTab,
      isModalOpen,
      showEmailModal,
      showSmsModal,
      emailData,
      smsData,
      templates,
      smsTemplates,
      mergeTags,
      setActiveTab,
      openEmailModal,
      closeEmailModal,
      openSmsModal,
      closeSmsModal,
      loadEmailTemplate,
      loadSmsTemplate,
      handleEmailAttachments,
      previewEmail,
      sendEmail,
      previewSms,
      sendSms,
    };
  },
};
</script>

<style scoped>
.inbox-page {
  padding: 20px;
  font-family: Arial, sans-serif;
}

.header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-block-end: 20px;
}

.header button {
  border: 1px solid #ccc;
  background-color: #8c57ff;
  cursor: pointer;
  padding-block: 10px;
  padding-inline: 20px;
  transition: background-color 0.3s;
}

.header button.active {
  background-color: #8c57ff;
  color: white;
}

.send-new-btn {
  border: none;
  background-color: #8c57ff;
  color: white;
  cursor: pointer;
  margin-inline-start: auto;
  padding-block: 10px;
  padding-inline: 20px;
}

.send-new-btn:hover {
  background-color: #7648c9;
}

.content {
  transition: filter 0.3s;
}

.content.blurred {
  filter: blur(5px);
  pointer-events: none;
}

.modal {
  position: fixed;
  z-index: 1000;
  padding: 20px;
  border-radius: 8px;
  background-color: white;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 10%);
  inline-size: 600px;
  inset-block-start: 50%;
  inset-inline-start: 50%;
  transform: translate(-50%, -50%);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-block-end: 10px;
}

.modal-body .form-group {
  margin-block-end: 10px;
}

.modal-body .form-group label {
  display: block;
  margin-block-end: 5px;
}

.modal-body .form-group input,
.modal-body .form-group select,
.modal-body .form-group textarea {
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  inline-size: 100%;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

button:hover {
  background-color: #0056b3;
}

.modal-fullscreen {
  padding: 40px;
  border-radius: 0;
  block-size: 100%;
  inline-size: 100%;

  /* Fullscreen modal styling */
  inset: 0;
}
</style>
