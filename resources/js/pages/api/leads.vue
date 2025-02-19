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

 <!--  Email to field  Lead modal-->   
  <div v-if="showLeadModal" class="integrte_LeadModal">
    <div class="modal_LeadContent">
      <div class="modal-header">
        <h2>Select Leads</h2>
        <button @click="closeLeadModal">×</button>
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
                  :value="lead.email" 
                  v-model="selectedEmails"
                  :checked="selectedLeads.includes(lead.id)"
                />

                <!--input type="checkbox" :value="lead.email" v-model="selectedEmails" /-->
              </td>
              <td>{{ lead.first_name }} {{ lead.last_name }}</td>
              <td>{{ lead.email }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button @click="addSelectedEmails">Add Selected</button>
      </div>
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

        <!-- To Field -->
        <div class="form-group">
          <label for="to">To:</label>
          <div class="email-input-container">
            <div class="email-tags">
              <span v-for="(email, index) in selectedEmails" :key="index" class="email-tag">
                {{ email }}
                <button @click="removeEmail(index)" class="remove-email">×</button>
              </span>
            </div>
            <button @click="openLeadModal">+</button> <!-- Open Lead Modal -->
          </div>
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

        <!-- Merge Field List for Subject -->
        <div class="form-group">
          <label for="mergeFields">Merge Fields:</label>
          <select id="mergeFields" v-model="selectedMergeField" @change="addMergeFieldToSubject">
            <option value="">Select Merge Field</option>
            <option v-for="field in mergeFields" :key="field" :value="field">
              {{ field }}
            </option>
          </select>
        </div>

        <!-- Template List -->
        <div class="form-group">
          <label for="template">Template:</label>
          <select id="template" v-model="emailData.template" @change="loadTemplate">
            <option value="">Select Template</option>
            <option v-for="template in templates" :key="template.id" :value="template.id">
              {{ template.title }} <!-- Show template title instead of ID -->
            </option>
          </select>
        </div>

        <!-- TinyMCE Editor -->
        <textarea id="email-editor"></textarea>
        <div class="form-group">
          <label for="signature">Select Signature:</label>
          <select id="signature" v-model="selectedSignature" @change="addSignatureToBody">
            <option value="">Select Signature</option>
            <option v-for="(sig, index) in signatures" :key="index" :value="sig">
              Signature {{ index + 1 }}
            </option>
          </select>
        </div>

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
    
    





<!-- Lead Selection Modal For SMS -->
<div v-if="showSmsLeadModal" class="modal">
  <div class="modal-header">
    <h2>Select Lead for SMS</h2>
    <button @click="closeSmsLeadModal">×</button>
  </div>

  <div class="modal-body">
    <ul>
      <li v-for="lead in leads" :key="lead.id" @click="selectSmsLead(lead)">
        {{ lead.first_name }} {{ lead.last_name }} - {{ lead.phone }}
      </li>
    </ul>
  </div>
</div>




  <!-- SMS Modal -->
  <div v-if="showSmsModal" class="modal" :class="{ 'modal-fullscreen': isFullscreen }">
    <div class="modal-header">
      <h2>Compose SMS</h2>
      <i class="fas fa-expand-alt" @click="expandSmsModal" title="Expand"></i>
      <button @click="closeSmsModal">×</button>
    </div>

    <div class="modal-body">
      <!-- From -->
      <div class="form-group">
        <label for="sms-from">From:</label>
        <input type="text" id="sms-from" v-model="smsData.from" placeholder="Enter sender's name or phone number" />
      </div>

      <!-- To -->
      <div class="form-group">
        <label for="sms-to">To:</label>
        <div class="input-group">
          <input
            type="text"
            id="sms-to"
            v-model="smsData.to"
            placeholder="Select a lead"
            readonly
          />
          <button class="plus-button" @click="openSmsLeadModal">+</button>
        </div>
      </div>



      <!-- Subject with TinyMCE -->
    <div class="form-group">
      <label for="sms-subject">Subject:</label>
      <Editor v-model="smsData.subject" :init="tinyMceConfig" />
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

     
      <!-- Message Body (Simple Textarea with Character Counter) -->
<!-- Message Body (Textarea with Character Counter inside) -->
<div class="form-group textarea-container">
  <label for="sms-message">Message:</label>
  <textarea
    id="sms-message"
    v-model="smsData.message"
    placeholder="Type your message here..."
    rows="5"
    maxlength="300"
    @input="countCharacters"
  ></textarea>
  <span class="char-counter">{{ smsData.message.length }}/300</span>
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
        
        <select v-model="selectedOption" class="open_modal_drop-box">
          <option v-for="option in modalOptions" :key="option.value" :value="option.value">
            {{ option.text }}
          </option>
        </select>
        
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
import { computed, nextTick, onMounted, ref, watch } from 'vue';

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
  
    
    const showLeadModal = ref(false);
    const selectedEmails = ref([]);
 

    
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


    // ✅ Sync Selected Emails to "To" Field
      const addSelectedEmails = () => {
        showLeadModal.value = false;
      };


   // Start Send Email And Sms On Selected Leads Functionlity...

    const emailData = ref({
      from: 'hiteshpandey732195@gmail.com',
      to: '',
      subject: '',
      message: '',
      template: '',
      attachments: [],
    });

// ✅ Function to remove an email from the list
const removeEmail = (index) => {
  selectedEmails.value.splice(index, 1);
  emailData.value.to = selectedEmails.value.join(', ');
};

    // ✅ Automatically updates "To" field
    watch(selectedLeads, (newSelectedLeads) => {
     selectedEmails.value = leads.value
       .filter((lead) => newSelectedLeads.includes(lead.id))
       .map((lead) => lead.email);

     emailData.value.to = selectedEmails.value.join(', ');
     console.log('Updated To Field:', emailData.value.to);
    });

    // ✅ Sync selectedEmails to emailData.to (Handles modal selection)
    watch(selectedEmails, (newSelectedEmails) => {
      emailData.value.to = newSelectedEmails.join(', ');
      console.log('Updated To Field from Modal:', emailData.value.to);
    });

    // ✅ Function to open lead modal
    const openLeadModal = async () => {
        console.log('Lead modal opening...')
        showLeadModal.value = true
        await getMyLeads()
      }

      const closeLeadModal = () => {
        showLeadModal.value = false;
      };

    // ✅ Add/remove leads from modal
    const toggleLeadSelection = (email) => {
      if (selectedEmails.value.includes(email)) {
        selectedEmails.value = selectedEmails.value.filter((e) => e !== email);
      } else {
        selectedEmails.value.push(email);
      }
    };     

    const selectedMergeField = ref('');

    const signatures = ref([
      "Best regards,<br>John Doe<br>Company Name",
      "Sincerely,<br>Jane Smith<br>Marketing Head",
      "Thank you,<br>Support Team<br>XYZ Pvt Ltd",
    ]);

    const selectedSignature = ref("");

    const mergeFields = ref([
      '{{firstName}}',
      '{{lastName}}',
      '{{email}}',
      '{{company}}',
      '{{phone}}',
    ]);
     
    const addMergeFieldToSubject = () => {
        if (selectedMergeField.value) {
          emailData.value.subject += ' ' + selectedMergeField.value; 
        }
      };
      
      const addSignatureToBody = () => {
        if (selectedSignature.value) {
          const editor = tinymce.get("email-editor");
          const existingContent = editor.getContent();
          editor.setContent(existingContent + "<br><br>" + selectedSignature.value);
        }
      };

      const templates = ref([]);
      const authToken = localStorage.getItem('auth_token');
      const fetchTemplates = async () => {
          try {
            const response = await axios.get('/api/templates', {
              headers: {
               'Authorization': `Bearer ${authToken}`,
              }
            });

            // Ensure correct response structure
            templates.value = response.data.data ?? response.data;  
          } catch (error) {
            console.error('Error fetching templates:', error);

            if (error.response && error.response.status === 401) {
              alert('Session expired. Please log in again.');
              
            }
          }
        };

    // onMounted(fetchTemplates);
    const initializeTinyMCE = () => {
      nextTick(() => {
        if (!tinymce.get("email-editor")) {
          tinymce.init({
            selector: "#email-editor",
            plugins: [
              "anchor autolink autoresize autosave charmap code codesample directionality emoticons fullscreen help image insertdatetime link lists liststyles media nonbreaking pagebreak preview quickbars save searchreplace table visualblocks visualchars wordcount",
            ],
            toolbar: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media table | preview fullscreen emoticons charmap | searchreplace code",
            menubar: "file edit view insert format tools table help",
            height: 500,
            setup: (editor) => {
              tinymceEditor.value = editor;
            },
          });
        }
      });
    };


    const destroyTinyMCE = () => {
      if (tinymce.get("email-editor")) { 
        tinymce.get("email-editor").remove();
      }
    };

    const openEmailModal = () => {
      if (selectedLeads.value.length === 0) {
        Swal.fire({
          icon: "warning",
          title: "No Leads Selected",
          text: "Please select at least one lead before sending an email.",
        });
        return;
      }

      showEmailModal.value = true;

      nextTick(() => {
        destroyTinyMCE();
        initializeTinyMCE();
      });
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
        let content = selectedTemplate.content;
        tinymce.get("email-editor").setContent(content);

        // If a signature is already selected, append it after template content
        if (selectedSignature.value) {
          addSignatureToBody();
        }
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

        // Handle email sending
        const sendEmail = async () => {
        const message = tinymceEditor.value.getContent();

        if (!emailData.value.to) {
          Swal.fire({
            icon: 'error',
            title: 'No Recipients',
            text: 'Please add at least one recipient.',
          });
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
              await axios.post('/api/send-email', {
                from: emailData.value.from,
                to: emailData.value.to.split(',').map(email => email.trim()),
                subject: emailData.value.subject,
                message: message,
                template_id: emailData.value.template,
              });

              Swal.fire({
                icon: 'success',
                title: 'Email Sent!',
                text: 'Your email has been successfully sent.',
              });

              closeEmailModal();
            } catch (error) {
              Swal.fire({
                icon: 'error',
                title: 'Failed to Send',
                text: 'Something went wrong. Please try again later.',
              });
            }
          }
        });
      };



      const showEmailLeadModal = ref(false); // For Email Lead Selection
      const showSmsLeadModal = ref(false); // For SMS Lead Selection

    // SMS Modal Data
    const smsData = ref({
      from: '',
      to: '',
      subject: '',
      message: '',
      template: '',
      schedule: '',
    });
    const openSmsLeadModal = () => {
  showSmsLeadModal.value = true;
};

const closeSmsLeadModal = () => {
  showSmsLeadModal.value = false;
};

// Select a lead and update the "To" field
const selectSmsLead = (lead) => {
  smsData.value.to = lead.phone;
  showSmsLeadModal.value = false;
};
  // Prevent exceeding 300 characters
  const countCharacters = () => {
    if (smsData.value.message.length > 300) {
      smsData.value.message = smsData.value.message.slice(0, 300);
    }
  };

  const smsTemplates = ref([
    { id: 1, name: 'Appointment Reminder', content: 'Hi {name}, this is a reminder for your appointment.' },
    { id: 2, name: 'Promotional Offer', content: 'Hello {name}, check out our latest promotional offers!' },
  ]);


  const selectLead = (lead) => {
    smsData.value.to = lead.phone; // Set selected lead's phone number in "To" field
    closeLeadModal(); // Close the modal
  };


  const mergeTags = ref(['{name}', '{email}', '{phone}']);

    const tinyMceConfig = {
      height: 200,
      menubar: false,
      plugins: 'link lists',
      toolbar: 'undo redo | bold italic | bullist numlist | merge_tags_button',
      setup: (editor) => {
        editor.ui.registry.addMenuButton('merge_tags_button', {
          text: 'Merge Tags',
          fetch: (callback) => {
            const items = mergeTags.value.map((tag) => ({
              type: 'menuitem',
              text: tag,
              onAction: () => editor.insertContent(tag),
            }));
            callback(items);
          },
        });
      },
    };

    const openSmsModal = () => {
      if (selectedLeads.value.length === 0) {
        Swal.fire('No leads selected!', 'Please select leads before sending an SMS.', 'warning');
        return;
      }
      showSmsModal.value = true;
    };

    const expandSmsModal = () => {
      isFullscreen.value = !isFullscreen.value;
    };

    const closeSmsModal = () => {
      showSmsModal.value = false;
      smsData.value = { from: '', to: '', subject: '', message: '', template: '', schedule: '' };
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
        smsData.value.subject += ` ${tag}`;
        event.target.value = '';
      }
    };

    const previewSms = () => {
      Swal.fire({
        title: 'SMS Preview',
        html: `<strong>From:</strong> ${smsData.value.from}<br>
              <strong>To:</strong> ${smsData.value.to}<br>
              <strong>Subject:</strong> ${smsData.value.subject}<br>
              <strong>Message:</strong><br> ${smsData.value.message}`,
        icon: 'info',
      });
    };

    const sendSms = async () => {
      if (!smsData.value.from || !smsData.value.to || !smsData.value.message.trim()) {
        Swal.fire('Missing Information', 'Sender, recipient, and message cannot be empty.', 'error');
        return;
      }

      try {
        await axios.post('/leads/send-sms', {
          lead_ids: selectedLeads.value,
          from: smsData.value.from,
          to: smsData.value.to,
          subject: smsData.value.subject,
          message: smsData.value.message,
          schedule: smsData.value.schedule || null,
        });
        Swal.fire('Success', 'SMS sent successfully.', 'success');
        closeSmsModal();
      } catch (err) {
        Swal.fire('Error', 'Failed to send SMS.', 'error');
      }
    };
   // End -- Send Email And Sms On Selected Leads Functionlity...


   
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
        console.log(response);
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
          const authToken = localStorage.getItem('auth_token');  

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
              'Authorization': `Bearer ${authToken}`, 
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
      await fetchLeads(activeLeadType.value);
      await fetchItems();
      await destroyTinyMCE(); 
      await fetchTemplates();
    });

    return {
      removeEmail,
      toggleLeadSelection,
      toggleSelectAll,
      showLeadModal,
      selectedEmails,
      addSelectedEmails,
      closeLeadModal,
      openLeadModal,
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
      selectedMergeField,
      addMergeFieldToSubject,
      mergeFields,
      signatures,
      addSignatureToBody,
      selectedSignature,
      selectLead,
      countCharacters,
      showEmailLeadModal,
      openSmsLeadModal,
      selectSmsLead,
      closeSmsLeadModal,
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
  inset: 0;
}

.integrte_LeadModal {
  position: fixed;
  z-index: 1000;
  padding: 50px;
  border-radius: 8px;
  background-color: #fff;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 10%);
  inline-size: 550px;
  inset-block-start: 50%;
  inset-inline: 1600px 0;
  inset-inline-start: 50%;
  inset-inline-start: 1582px;
  transform: translate(-50%, -50%);
}

.modal_LeadContent {
  display: grid;
  padding: 0;
  border: 5px solid #87b;
  background-color: #f5eded;
  block-size: 500px;
  inline-size: 100%;
  margin-block: 0%;
}

.modal_LeadBody {
  block-size: 320px;
  overflow-y: scroll;
}

.email-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 5px;
}

.email-tag {
  display: flex;
  align-items: center;
  border-radius: 20px;
  background-color: #e0e0e0;
  padding-block: 5px;
  padding-inline: 10px;
}

.remove-email {
  border: none;
  background: none;
  color: red;
  cursor: pointer;
  font-weight: bold;
  margin-inline-start: 5px;
}

.textarea-container {
  position: relative;
  display: inline-block;
  inline-size: 100%;
}

.textarea-container textarea {
  box-sizing: border-box;
  inline-size: 100%;
  padding-block-end: 25px; /* Space for counter */
}

.char-counter {
  position: absolute;
  background: white;
  color: gray;
  font-size: 12px;
  inset-block-end: 8px;
  inset-inline-end: 10px;
  padding-block: 2px;
  padding-inline: 5px;
}

.input-group {
  display: flex;
  align-items: center;
}

.input-group input {
  flex: 1;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 5px 0 0 5px;
}

.plus-button {
  border: none;
  border-radius: 0 5px 5px 0;
  background-color: #007bff;
  color: white;
  cursor: pointer;
  font-size: 18px;
  padding-block: 8px;
  padding-inline: 12px;
}

.plus-button:hover {
  background-color: #0056b3;
}

</style>
