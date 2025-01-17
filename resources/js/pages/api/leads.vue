<template>
  <div class="leads-container">
    <!--div v-if="loading" class="loading">Loading leads...</div-->
    <!--div v-if="error" class="error">{{ error }}</div-->
    <div class="leads-header">
    <div class="leads-section">
      <div class="lead-buttons">
        <button
          class="btn"
          :class="{ active: activeLeadType === 'all' }"
          @click="fetchLeads('all')"
        >
          All Leads
        </button>
        <button
          class="btn"
          :class="{ active: activeLeadType === 'my' }"
          @click="fetchLeads('my')"
        >
          My Leads
        </button>
      </div>
    </div>

    <div class="icon-section">
      <ul>
        <!-- Common list items for both "All Leads" and "My Leads" -->
        <li class="selected-count total-leads">
          Selected Leads: {{ selectedLeads.length }}
        </li>
        <li class="total-leads">
          <i class="fa-solid fa-plus"></i>
        </li>

        <li
          class="total-leads"
          @click="deleteLeads"
          :class="{ disabled: selectedLeads.length === 0 }"
          :disabled="selectedLeads.length === 0"
        >
          <span>Delete</span>
          <i class="fa-solid fa-trash-can-arrow-up"></i>
        </li>
        
        
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
          <select id="template" v-model="emailData.template" @change="loadTemplate">
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
          <input type="file" id="attachments" multiple @change="handleAttachments" />
        </div>

        <!-- Preview and Schedule -->
        <div class="form-actions">
          <button @click="previewEmail">Preview</button>
          <button @click="scheduleEmail">Schedule</button>
          <button @click="sendEmail">Send Email</button>
        </div>
      </div>
    </div>

    <!-- "My Leads" Button -->
    <li
      v-if="activeLeadType === 'my'"
      class="total-leads"
      @click="openEmailModal"
      :class="{ disabled: selectedLeads.length === 0 }"
      :disabled="selectedLeads.length === 0"
    >
      <span>Email</span>
      <i class="fa-solid fa-envelope"></i>
    </li>
        
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

    <!-- "My Leads" Button -->
    <li
      v-if="activeLeadType === 'my'"
      class="total-leads"
      @click="openSmsModal"
      :class="{ disabled: selectedLeads.length === 0 }"
      :disabled="selectedLeads.length === 0"
    >
      <span>Smart SMS</span>
      <i class="fa-solid fa-comment-dots"></i>
    </li>

        <li
          v-if="activeLeadType === 'my'"
          class="total-leads"
          :class="{ disabled: selectedLeads.length === 0 }"
          :disabled="selectedLeads.length === 0"
          @click="openTagModal"
        >
          <span>Tags</span>
          <i class="fa-solid fa-tags"></i>
        </li>

        <li
          v-if="activeLeadType === 'my'"
          class="total-leads"
          :class="{ disabled: selectedLeads.length === 0 }"
          :disabled="selectedLeads.length === 0"
          @click="openStageModal"
        >
          <span>Stage</span>
          <i class="fa-solid fa-table-cells"></i>
        </li>

        <!-- Hidden on "All Leads" -->
        <li
          v-if="activeLeadType === 'my'"
          class="total-leads"
          :class="{ disabled: selectedLeads.length === 0 }"
          :disabled="selectedLeads.length === 0"
          @click="openModal('listingAlert')"
        >
          <span>New Listing Alert</span>
          <i class="fa-solid fa-bell"></i>
        </li>

        <li
          v-if="activeLeadType === 'my'"
          class="total-leads"
          :class="{ disabled: selectedLeads.length === 0 }"
          :disabled="selectedLeads.length === 0"
          @click="openModal('neighbourhoodAlert')"
        >
          <span>Neighbourhood Alert</span>
          <i class="fa-solid fa-map-location-dot"></i>
        </li>

        <li
          v-if="activeLeadType === 'my'"
          class="total-leads"
          :class="{ disabled: selectedLeads.length === 0 }"
          :disabled="selectedLeads.length === 0"
          @click="openModal('openHouseAlert')"
        >
          <span>Open House Alert</span>
          <i class="fa-solid fa-circle-exclamation"></i>
        </li>

        <li
          v-if="activeLeadType === 'my'"
          class="total-leads"
          :class="{ disabled: selectedLeads.length === 0 }"
          :disabled="selectedLeads.length === 0"
          @click="openModal('actionPlan')"
        >
          <span>Action Plan</span>
          <i class="fa-solid fa-plus"></i>
        </li>

        <li
          v-if="activeLeadType === 'my'"
          class="total-leads"
          :class="{ disabled: selectedLeads.length === 0 }"
          :disabled="selectedLeads.length === 0"
          @click="openModal('marketUpdates')"
        >
          <span>Market Updates</span>
          <i class="fa fa-bar-chart" aria-hidden="true"></i>
        </li>

        <li
          v-if="activeLeadType === 'my'"
          class="total-leads"
          :class="{ disabled: selectedLeads.length === 0 }"
          :disabled="selectedLeads.length === 0"
          @click="openModal('newsletter')"
        >
          <span>Real Estate Newsletter</span>
          <i class="fa-solid fa-envelope-open-text"></i>
        </li>

        <!-- Always visible buttons -->
        <li
          class="total-leads"
          :class="{ disabled: selectedLeads.length === 0 }"
          :disabled="selectedLeads.length === 0"
        >
          <span>Lead Transfer</span>
          <i class="fa-solid fa-right-left"></i>
        </li>

        <li
          class="total-leads"
          @click="exportLeads"
          :class="{ disabled: selectedLeads.length === 0 }"
          :disabled="selectedLeads.length === 0"
        >
          <span>Export Lead</span>
          <i class="fa-solid fa-file-export"></i>
        </li>

        <li class="total-leads" @click="openImportModal">
          <span>Import</span>
          <i class="fa-solid fa-file-import"></i>
        </li>
      </ul>
    </div>
  </div>

    <!-- Import Modal -->
    <div v-if="showImportModal" class="modal-overlay">
      <div class="modal-container">
        <button type="button" @click="closeImportModal" class="close-button">X</button>
        <h2>Select Import Type</h2>

        <div class="import-options" v-if="!showFileInput">
          <!-- Direct File Import -->
          <button @click="importDirectFile" class="import-button">Direct File Import</button>

          <!-- Indirect File Import -->
          <button @click="showFileUploadField" class="import-button">Indirect File Import</button>
        </div>

        <!-- Show File Input for Indirect File Import -->
        <div v-if="showFileInput" class="file-upload-container">
          <input type="file" @change="handleFileUpload" accept=".csv" />
          <button @click="submitCsvFile" class="submit-button">Submit CSV</button>
          <p v-if="fileError" class="error-message">{{ fileError }}</p>
        </div>
      </div>
    </div>

<!-----------------------------06-09-2024-------------------------------------->

    <!-- Tags Modal -->
    <div v-if="showTagModal" class="tag-modal-overlay">
      <div class="tag-modal-container">
        <button type="button" @click="closeTagModal" class="close-button">X</button>
        <h3>Select Tags</h3>
        <div class="modal_tags">
          <div v-for="tag in tags" :key="tag.id" class="modal_tag">
            <input
              type="checkbox"
              :value="tag.id"
              :checked="isTagSelected(tag.id)"
              @change="toggleTagSelection(tag.id)"
            />
            {{ tag.name }}
          </div>
        </div>
        <button @click="showAddTagInput = !showAddTagInput" class="add-button">Add Tag</button>
        <div v-if="showAddTagInput">
          <input v-model="newTag" placeholder="Enter new tag" />
          <button @click="addTag" class="submit-button">Add</button>
        </div>
        <button @click="closeTagModal" class="submit-button">Close</button>
      </div>
    </div>

    <!-- Stage Modal -->
    <div v-if="showStageModal" class="stage-modal-overlay">
      <div class="stage-modal-container">
        <button type="button" @click="closeStageModal" class="close-button">X</button>
        <h3>Select Stage</h3>


        <div v-if="showAddStageInput">
          <input v-model="newStage" placeholder="Enter new stage" id="m_add_stage" class="m_add_stage"/>
          <button @click="addStage" class="submit-button">Add</button>
        </div>
        <button @click="showAddStageInput = !showAddStageInput" class="add-button">Add Stage</button>
        <select v-model="newLead.stage" id="stage_id" class="stage_class">
          <option v-for="stage in stages" :key="stage.id" :value="stage.id" class="stage_values">
            {{ stage.name }}
          </option>
        </select>
        <button @click="closeStageModal" class="submit-button">Close</button>
      </div>
    </div>

   <!-- Generic Modal -->
   <div v-if="showModal" class="modal-overlay">
      <div class="modal-container">
        <button type="button" @click="closeModal" class="close-button">X</button>
        <h2>{{ modalTitle }}</h2>
        
        <!-- Dropdown for dynamic options -->
        <select v-model="selectedOption" class="open_modal_drop-box">
          <option v-for="option in modalOptions" :key="option.value" :value="option.value">
            {{ option.text }}
          </option>
        </select>
        
        <!-- Submit and Cancel Buttons -->
        <div class="modal-buttons">
          <button @click="submitAction" class="submit-button">Submit</button>
          <button @click="closeModal" class="cancel-button">Cancel</button>
        </div>
      </div>
    </div>
<!------------------------------------------------------------------->

    <div>
      <div class="header">
        <input v-model="searchQuery" type="text" placeholder="Search leads by name..." class="search-bar" />
        <div class="total-leads">Total Leads: {{ filteredLeads.length }}</div>
        <div class="total-leads">Last 7 Days</div>
        <div class="total-leads">Hot</div>
        <div class="total-leads">Buyer</div>
        <div class="total-leads">Seller</div>
        <div class="total-leads">Closed</div>
        <button @click="showForm = true" class="create-new-button">+ Create New</button>
      </div>
    </div>

    <div v-if="showForm" class="form-overlay">
      <div class="form-container">
        <button type="button" @click="showForm = false" id="cancel_button">X</button>
        <h2>Create New Lead</h2>
        <form @submit.prevent="submitForm">
        <label>
          First Name:
          <input v-model="newLead.first_name" type="text" required />
          <span v-if="errors.first_name" class="error">{{ errors.first_name }}</span>
        </label>
        <label>
          Last Name:
          <input v-model="newLead.last_name" type="text" required />
          <span v-if="errors.last_name" class="error">{{ errors.last_name }}</span>
        </label>
        <label>
          Email:
          <input v-model="newLead.email" type="email" required />
          <span v-if="errors.email" class="error">{{ errors.email }}</span>
        </label>
        <label>
          Phone:
          <input v-model="newLead.phone" type="tel" required />
          <span v-if="errors.phone" class="error">{{ errors.phone }}</span>
        </label>
        <label>
        Tag:
        <select v-model="newLead.tag" required>
          <option v-for="tag in tags" :key="tag.id" :value="tag.id">
            {{ tag.name }}
          </option>
        </select>
        <span v-if="errors.tag" class="error">{{ errors.tag }}</span>
      </label>

      <label>
        Stage:
        <select v-model="newLead.stage" required>
          <option v-for="stage in stages" :key="stage.id" :value="stage.id">
            {{ stage.name }}
          </option>
        </select>
        <span v-if="errors.stage" class="error">{{ errors.stage }}</span>
      </label>
        <button type="submit" id="submit_button">Submit</button>
      </form>

      </div>
    </div>
    <!-- Leads Table -->
    <div>

    <div>
      <label for="pageSize">Rows per page:</label>
      <select v-model="pageSize" @change="setPageSize(Number($event.target.value))">
        <option value="5">5</option>
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
      </select>
    </div>
    <div v-if="!loading && !error">
      <table id="leadsTable" class="table table-striped table-bordered leads-table" v-if="filteredLeads.length">
      <thead>
        <tr>
          <th>
            <label class="custom-checkbox-label">
              <input type="checkbox" @change="toggleSelectAll" class="custom-checkbox" />
              <span class="custom-checkbox-span"></span>
            </label>
          </th>
          <!-- Other headers -->
          <th>First Name</th>
          <th>Last Name</th>
          <th>Phone</th>
          <th>Email</th>
          <th>Activity</th>
          <th>Created</th>
          <th>Tag</th>
          <th>Stage</th>
          <th>Source</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="lead in paginatedLeads" :key="lead.id">
          <td>
            <label class="custom-checkbox-label">
              <input type="checkbox" :value="lead.id" v-model="selectedLeads" class="custom-checkbox" />
              <span class="custom-checkbox-span"></span>
            </label>
          </td>
          <!-- Other columns -->
          <td>
            <router-link :to="{ name: 'update-profile', params: { id: lead.id } }" style="color: blue; text-decoration: underline;">
              {{ lead.first_name }}
            </router-link>
          </td>
          <td>{{ lead.last_name }}</td>
          <td>{{ lead.phone }}</td>
          <td>{{ lead.email }}</td>
          <td>{{ lead.activity || 'N/A' }}</td>
          <td>{{ new Date(lead.created_at).toLocaleDateString() }}</td>
          <td>{{ lead.tag || 'N/A' }}</td>
          <td>{{ lead.stage || 'N/A' }}</td>
          <td>{{ lead.source || 'N/A' }}</td>
        </tr>
      </tbody>
    </table>
  </div>
      <!-- No leads available message -->
      <div v-if="filteredLeads.length === 0" class="no-leads">No leads available.</div>
    </div>
    <div v-if="toast.message" :class="`toast ${toast.type}`">
      {{ toast.message }}
      <button @click="toast.message = ''" class="toast-close-button">X</button>
    </div>  
      <div>
      <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1">Previous</button>
      <span>Page {{ currentPage }} of {{ totalPages }}</span>
      <button @click="changePage(currentPage + 1)" :disabled="currentPage === totalPages">Next</button>
    </div>
  </div>

</template>


<script lang="js">
import '@/assets/leads.css';
import axios from 'axios';
import Swal from 'sweetalert2';
import tinymce from 'tinymce';
import 'tinymce/icons/default';
import 'tinymce/plugins/link';
import 'tinymce/plugins/lists';
import 'tinymce/themes/silver';
import { computed, nextTick, onMounted, ref } from 'vue';

export default {
  name: 'LeadsPage',
  setup() {
    const activeLeadType = ref('all'); // 'all' or 'my'
    const leads = ref([]);
    const loading = ref(true);
    const error = ref('');
    const searchQuery = ref('');
    const showForm = ref(false);
 
    const newLead = ref({
      id: 0,
      first_name: '',
      last_name: '',
      email: '',
      phone: '',
      tag: '',
      stage: '',
    });
    const errors = ref({});
    const toast = ref({ message: '', type: '' });
    const tags = ref([]);
    const stages = ref([]);
    const sources = ref([]);
    
    // Modal states
    const showModal = ref(false);
    const modalTitle = ref('');
    const modalOptions = ref([]);
    const selectedOption = ref(null);
    const modalType = ref('');
    const selectedTags = ref(new Set());
    
    const showImportModal = ref(false);
    const showFileInput = ref(false);
    const selectedFile = ref(null);
    const fileError = ref('');

    const currentPage = ref(1);
    const pageSize = ref(10); 
    const totalPages = computed(() => Math.ceil(leads.value.length / pageSize.value));

  
    const smsMessage = ref('');
    const newTag = ref('');
    const newStage = ref('');
    const showTagModal = ref(false);
    const showStageModal = ref(false);
    const showAddTagInput = ref(false);
    const showAddStageInput = ref(false);

    const showEmailModal = ref(false); 
    const tinymceEditor = ref(null);
    const emailMessage = ref('');

    const showSmsModal = ref(false);
    const selectedLeads = ref([]);
    const isFullscreen = ref(false); 
    // Modal methods
    const openModal = (type) => {
      modalType.value = type;
      modalTitle.value = getModalTitle(type);
      modalOptions.value = getModalOptions(type);
      showModal.value = true;
    };

    const closeModal = () => {
      showModal.value = false;
    };


    //-  ---- - -- - Tag
    const isTagSelected = (tagId) => {
        return selectedTags.value.has(tagId);
      };

      const toggleTagSelection = (tagId) => {
        if (selectedTags.value.has(tagId)) {
          selectedTags.value.delete(tagId);
        } else {
          selectedTags.value.add(tagId);
        }
        newLead.value.tag = Array.from(selectedTags.value); 
      };

    const submitAction = () => {
      // Handle the action for the selected option here
      console.log('Selected Option:', selectedOption.value);
      closeModal();
    };

    const getModalTitle = (type) => {
      const titles = {
        listingAlert: 'New Listing Alert',
        neighbourhoodAlert: 'Neighbourhood Alert',
        openHouseAlert: 'Open House Alert',
        actionPlan: 'Action Plan',
        marketUpdates: 'Market Updates',
        newsletter: 'Real Estate Newsletter'
      };
      return titles[type] || 'Unknown Modal';
    };

    const getModalOptions = (type) => {
      const options = {
        listingAlert: [{ value: 'alert1', text: 'Alert 1' }, { value: 'alert2', text: 'Alert 2' }],
        neighbourhoodAlert: [{ value: 'neigh1', text: 'Neighbourhood 1' }, { value: 'neigh2', text: 'Neighbourhood 2' }],
        openHouseAlert: [{ value: 'house1', text: 'House 1' }, { value: 'house2', text: 'House 2' }],
        actionPlan: [{ value: 'plan1', text: 'Plan 1' }, { value: 'plan2', text: 'Plan 2' }],
        marketUpdates: [{ value: 'update1', text: 'Update 1' }, { value: 'update2', text: 'Update 2' }],
        newsletter: [{ value: 'news1', text: 'Newsletter 1' }, { value: 'news2', text: 'Newsletter 2' }]
      };
      return options[type] || [];
    };

    //----------------------*-*****-*-**-*-*---*
    const emailData = ref({
      from: 'you@example.com',
      to: '',
      subject: '',
      message: '',
      template: '',
      attachments: [],
    });

    const templates = ref([
      { id: 1, name: 'Welcome Email', content: '<p>Welcome to our service!</p>' },
      { id: 2, name: 'Follow-Up', content: '<p>Just checking in with you!</p>' },
    ]);

    const initializeTinyMCE = () => {
      nextTick(() => {
        tinymce.init({
            selector: '#email-editor', // Replace with your editor's ID
            plugins: [
              'anchor autolink autoresize autosave charmap code codesample directionality emoticons fullscreen help image insertdatetime link lists liststyles media nonbreaking pagebreak preview quickbars save searchreplace table visualblocks visualchars wordcount',
            ],
            toolbar: [
              'undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media table | preview fullscreen emoticons charmap | searchreplace code',
            ].join(' | '),
            menubar: 'file edit view insert format tools table help',
            height: 500,
            setup: (editor) => {
            tinymceEditor.value = editor;
          },
        });
      });
    };

    const destroyTinyMCE = () => {
      if (tinymceEditor.value) {
        tinymceEditor.value.remove();
        tinymceEditor.value = null;
      }
    };

    const openEmailModal = () => {
      if (selectedLeads.value.length === 0) {
        alert('No leads selected.');
        return;
      }
      showEmailModal.value = true;
      initializeTinyMCE();
    };

    const closeEmailModal = () => {
      showEmailModal.value = false;
      destroyTinyMCE();
    };

    const loadTemplate = () => {
      const selectedTemplate = templates.value.find(
        (template) => template.id === emailData.value.template
      );
      if (selectedTemplate) {
        tinymceEditor.value.setContent(selectedTemplate.content);
      }
    };

    const handleAttachments = (event) => {
      emailData.value.attachments = Array.from(event.target.files);
    };

    const previewEmail = () => {
      const message = tinymceEditor.value.getContent();
      alert(`Preview:\nFrom: ${emailData.value.from}\nTo: ${emailData.value.to}\nSubject: ${emailData.value.subject}\nMessage:\n${message}`);
    };

    const scheduleEmail = () => {
      alert('Feature not implemented yet. Add scheduling logic here!');
    };

    const sendEmail = async () => {
      const message = tinymceEditor.value.getContent();

      if (!emailData.value.to || !message.trim()) {
        alert('Recipient email and message cannot be empty.');
        return;
      }

      try {
        const formData = new FormData();
        formData.append('from', emailData.value.from);
        formData.append('to', emailData.value.to);
        formData.append('subject', emailData.value.subject);
        formData.append('message', message);

        emailData.value.attachments.forEach((file, index) => {
          formData.append(`attachments[${index}]`, file);
        });

        await axios.post('/leads/send-email', formData, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });
        alert('Emails sent successfully.');
        closeEmailModal();
      } catch (err) {
        alert('Failed to send emails.');
      }
    };

//*-*--*-*--*-*-*-*-*-*--*-*-*-*-*-*-*-*-*-*-*-*-*-*-

const smsData = ref({
      from: '',
      to: '',
      message: '',
      template: '',
      schedule: '',
    });

    const smsTemplates = ref([
      { id: 1, name: 'Appointment Reminder', content: 'Hi {name}, this is a reminder for your appointment.' },
      { id: 2, name: 'Promotional Offer', content: 'Hello {name}, check out our latest promotional offers!' },
    ]);

    const mergeTags = ref(['{name}', '{email}', '{phone}']);

    const openSmsModal = () => {
      if (selectedLeads.value.length === 0) {
        alert('No leads selected.');
        return;
      }
      showSmsModal.value = true;
    };

    const expandSmsModal = () => {
      isFullscreen.value = !isFullscreen.value; 
    };

    const closeSmsModal = () => {
      showSmsModal.value = false;
      smsData.value = { from: '', to: '', message: '', template: '', schedule: '' };
    };

    const loadSmsTemplate = () => {
      const selectedTemplate = smsTemplates.value.find(
        (template) => template.id === smsData.value.template
      );
      if (selectedTemplate) {
        smsData.value.message = selectedTemplate.content;
      }
    };

    const insertMergeTag = (event) => {
      const tag = event.target.value;
      if (tag) {
        smsData.value.message += ` ${tag}`;
        event.target.value = '';
      }
    };

    const previewSms = () => {
      alert(
        `Preview:\nFrom: ${smsData.value.from}\nTo: ${smsData.value.to}\nMessage:\n${smsData.value.message}`
      );
    };

    const sendSms = async () => {
      if (!smsData.value.from || !smsData.value.to || !smsData.value.message.trim()) {
        alert('Sender, recipient, and message cannot be empty.');
        return;
      }

      try {
        await axios.post('/leads/send-sms', {
          lead_ids: selectedLeads.value,
          from: smsData.value.from,
          to: smsData.value.to,
          message: smsData.value.message,
          schedule: smsData.value.schedule || null,
        });
        alert('SMS sent successfully.');
        closeSmsModal();
      } catch (err) {
        alert('Failed to send SMS.');
      }
    };
 
    const fetchLeads = async (type) => {
        activeLeadType.value = type;
        loading.value = true; 

        if (type === 'all') {
          await getAllLeads();
        } else if (type === 'my') {
          await getMyLeads();
        }
      };

    const getAllLeads = async () => {
      try {
        const response = await axios.get('/leads');
        leads.value = Array.isArray(response.data) ? response.data : [];
      } catch (err) {
        error.value = 'Failed to fetch leads.';
      } finally {
        loading.value = false;
      }
    };

    const getMyLeads = async () => {
      try {
        const response = await axios.get('/leads'); 
        leads.value = Array.isArray(response.data) ? response.data : [];
      } catch (err) {
        error.value = 'Failed to fetch my leads.';
      } finally {
        loading.value = false;
      }
    };

    const fetchItems = async () => {
      try {
        const response = await axios.get('/items');

        tags.value = response.data.tags;
        stages.value = response.data.stages;
        sources.value = response.data.sources;
      } catch (error) {
        console.error('Failed to fetch items:', error);
      }
    };

    const openTagModal = () => {
      showTagModal.value = true;
    };

    const closeTagModal = () => {
      showTagModal.value = false;
      newTag.value = '';
    };

    const openStageModal = () => {
      showStageModal.value = true;
    };

    const closeStageModal = () => {
      showStageModal.value = false;
      newStage.value = ''; 
    };

    const addTag = async () => {
      if (newTag.value.trim()) {
        try {
          const response = await axios.post('/api/items', {
            name: newTag.value,
            type: 'tag',
          });
          tags.value.push(response.data.item);
          newTag.value = '';
          showAddTagInput.value = false;
          Swal.fire('Success', 'Tag added successfully', 'success');
        } catch (error) {
          console.error('Failed to add tag:', error);
          Swal.fire('Error', 'Failed to add tag', 'error');
        }
      }
    };

    const addStage = async () => {
      if (newStage.value.trim()) {
        try {
          const response = await axios.post('/api/items', {
            name: newStage.value,
            type: 'stage',
          });
          stages.value.push(response.data.item);
          newStage.value = '';
          showAddStageInput.value = false;
          Swal.fire('Success', 'Stage added successfully', 'success');
        } catch (error) {
          console.error('Failed to add stage:', error);
          Swal.fire('Error', 'Failed to add stage', 'error');
        }
      }
    };

    const filteredLeads = computed(() => {
      return leads.value.filter((lead) =>
        lead.first_name && lead.first_name.toLowerCase().includes(searchQuery.value.toLowerCase())
      );
    });


    const getTagValue = (id) => {
      const tag = tags.value.find(t => t.id === id);
      return tag ? tag.name : 'N/A';
    };

    const getStageValue = (id) => {
      const stage = stages.value.find(s => s.id === id);
      return stage ? stage.name : 'N/A';
    };

    const validateForm = () => {
      errors.value = {
        first_name: newLead.value.first_name ? '' : 'First name is required.',
        last_name: newLead.value.last_name ? '' : 'Last name is required.',
        email: newLead.value.email && /^\S+@\S+\.\S+$/.test(newLead.value.email) ? '' : 'Valid email is required.',
        phone: newLead.value.phone && /^\d{10}$/.test(newLead.value.phone) ? '' : 'Valid phone number is required.',
        tag: newLead.value.tag ? '' : 'Tag is required.',
        stage: newLead.value.stage ? '' : 'Stage is required.',
      };
      return !Object.values(errors.value).some((errorMsg) => errorMsg);
    };

    const deleteLeads = async () => {
      if (selectedLeads.value.length === 0) {
        showToast('No leads selected.', 'error');
        return;
      }

      const result = await Swal.fire({
        title: 'Are you sure?',
        text: 'You won\'t be able to revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
      });

      if (result.isConfirmed) {
        try {
          const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
          const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

          await axios.post('/leads/delete', { lead_ids: selectedLeads.value }, {
            headers: {
              'X-CSRF-TOKEN': csrfToken,
            },
          });

          leads.value = leads.value.filter(lead => !selectedLeads.value.includes(lead.id));
          selectedLeads.value = [];

          Swal.fire(
            'Deleted!',
            'Selected leads have been deleted.',
            'success'
          );
        } catch (err) {
          Swal.fire(
            'Error!',
            'Failed to delete leads.',
            'error'
          );
        }
      }
    };

    const changePage = (page) => {
      if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
      }
    };

    const setPageSize = (size) => {
      pageSize.value = size;
      currentPage.value = 1; 
    };

    const paginatedLeads = computed(() => {
      const start = (currentPage.value - 1) * pageSize.value;
      const end = start + pageSize.value;
      return leads.value.slice(start, end);
    });

    const exportLeads = async () => {
      if (selectedLeads.value.length === 0) {
        showToast('No leads selected.', 'error');
        return;
      }

      try {
        const response = await axios.post('/leads/export', { lead_ids: selectedLeads.value }, {
          responseType: 'blob',
        });

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'leads.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        showToast('Leads exported successfully!', 'success');
      } catch (err) {
        showToast('Failed to export leads.', 'error');
      }
    };

    const importLeads = async (event) => {
      const file = event.target.files[0];

      if (!file) {
        showToast('No file selected.', 'error');
        return;
      }

      const formData = new FormData();
      formData.append('file', file);

      try {
        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

        await axios.post('/leads/import', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
            'X-CSRF-TOKEN': csrfToken,
          },
        });

        await fetchLeads();
        showToast('Leads imported successfully!', 'success');
      } catch (err) {
        showToast('Failed to import leads.', 'error');
      }
    };

    const importIndirectFile = () => {
      closeImportModal();
      alert('Indirect File Import option selected');
      // Add logic to handle indirect file impor
    };

    const openImportModal = () => {
      showImportModal.value = true;
    };

    const closeImportModal = () => {
      showImportModal.value = false;
      showFileInput.value = false;
      selectedFile.value = null;
      fileError.value = '';
    };

    const showFileUploadField = () => {
      showFileInput.value = true;
    };

    const handleFileUpload = (event) => {
      const file = event.target.files[0];
      if (file && file.type === 'text/csv') {
        selectedFile.value = file;
        fileError.value = '';
      } else {
        selectedFile.value = null;
        fileError.value = 'Please select a valid CSV file.';
      }
    };

    const submitCsvFile = () => {
      if (!selectedFile.value) {
        fileError.value = 'No file selected or invalid file type.';
        return;
      }
      // Proceed with the file upload logic
      console.log('CSV file selected:', selectedFile.value);
      closeImportModal();
    };

    const importDirectFile = () => {
      closeImportModal();

    };

    const showToast = (message, type) => {
      toast.value = { message, type };
      setTimeout(() => {
        toast.value.message = '';
      }, 5000);
    };

    const submitForm = async () => {
        if (!validateForm()) {
          return;
        }

        try {
          const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
          const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';
          const authToken = localStorage.getItem('auth_token');  // Get the token from localStorage

          const response = await axios.post('/leads', {
            first_name: newLead.value.first_name,
            last_name: newLead.value.last_name,
            email: newLead.value.email,
            phone: newLead.value.phone,
            tag: String(newLead.value.tag), 
            stage: String(newLead.value.stage),
          }, {
            headers: {
              'X-CSRF-TOKEN': csrfToken,
              'Authorization': `Bearer ${authToken}`,  // Include the auth token here
            },
          });

          newLead.value = { id: 0, first_name: '', last_name: '', email: '', phone: '', tag: '', stage: '' };
          showForm.value = false;

          leads.value.push(response.data);

          showToast('Lead created successfully!', 'success');
        } catch (err) {
          showToast('Failed to create lead.', 'error');
        }
      };

    const toggleSelectAll = (event) => {
      if (event.target.checked) {
        selectedLeads.value = filteredLeads.value.map((lead) => lead.id);
      } else {
        selectedLeads.value = [];
      }
    };

    onMounted(async () => {
      await  fetchLeads(activeLeadType.value);
      await  fetchItems();
      await  destroyTinyMCE(); // Clean up TinyMCE on component unmount
    });

    return {
      expandSmsModal,
      showSmsModal,
      selectedLeads,
      smsData,
      smsTemplates,
      mergeTags,
      openSmsModal,
      closeSmsModal,
      loadSmsTemplate,
      insertMergeTag,
      previewSms,
      sendSms,
      emailData,
      templates,
      loadTemplate,
      handleAttachments,
      previewEmail,
      scheduleEmail,
      emailMessage,
      showEmailModal,
      openEmailModal,
      closeEmailModal,
      sendEmail,
      getAllLeads,
      getMyLeads,
      fetchLeads,
      activeLeadType,
      leads,
      loading,
      error,
      searchQuery,
      filteredLeads,
      showForm,
      newLead,
      errors,
      submitForm,
      toggleSelectAll,
      toast,
      getTagValue,
      getStageValue,
      tags,
      stages,
      sources,
      fetchItems,
      showImportModal,
      openImportModal,
      closeImportModal,
      importDirectFile,
      importIndirectFile,
      showFileInput,
      showFileUploadField,
      handleFileUpload,
      submitCsvFile,
      fileError,
      deleteLeads,
      exportLeads,
      importLeads,
      currentPage,
      pageSize,
      totalPages,
      changePage,
      setPageSize,
      paginatedLeads,
      showToast,
      newTag,
      newStage,
      showTagModal,
      showStageModal,
      showAddTagInput,
      showAddStageInput,
      openTagModal,
      closeTagModal,
      openStageModal,
      closeStageModal,
      addTag,
      addStage,
      openModal,
      submitAction,
      showModal,
      modalTitle,
      modalOptions,
      selectedOption,
      isTagSelected,
      toggleTagSelection,
      closeModal,
    };
  },
};
</script>

<style>
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

button {
  border: none;
  border-radius: 4px;
  background-color: #007bff;
  color: white;
  cursor: pointer;
  padding-block: 8px;
  padding-inline: 12px;
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
