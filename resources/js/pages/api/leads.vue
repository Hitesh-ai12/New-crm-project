<template>
  <div class="leads-container">

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

        <!-- To Field -->
        <div class="form-group">
          <label for="to">To:</label>
          <div class="email-input-container">
            <!-- Selected Leads Will Show Here -->
            <div class="email-tags">
              <span v-for="(lead, index) in selectedLeadsList" :key="lead.id" class="email-tag">
                {{ lead.first_name }} ({{ lead.email}})
                <button @click="removeSelectedLead(index)" class="remove-email">×</button>
              </span>
            </div>

            <!-- Open Lead Modal -->
            <button @click="openSmsLeadModal">+</button>
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
                <select
        id="signature"
        v-model="selectedSignature"
        @change="addSignatureToBody"
        @focus="fetchSignatures"
      >
        <option value="">Select Signature</option>
        <option 
          v-for="sig in signatures" 
          :key="sig.id" 
          :value="sig.content"
        >
          {{ sig.name }}
        </option>
      </select>

        </div>

        <!-- Attachments -->
        <div class="form-group">
          <label for="attachments">Attachments:</label>
          <input type="file" id="attachments" @change="handleAttachments" />
          
          <!-- Show attachment below with remove button -->
          <div v-if="emailData.attachments.length" class="attachment-list mt-2">
            <div v-for="(file, index) in emailData.attachments" :key="index" class="attachment-item">
              <span>{{ file.split('/').pop() }}</span>
              <button type="button" class="btn btn-sm btn-danger ml-2" @click="removeAttachment(index)">
                Remove
              </button>
            </div>
          </div>
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
    
    <!--  Email to field  Lead modal-->   
    <teleport to="body">
      <div v-if="showSmsLeadModal" class="integrte_LeadModal">
        <div class="modal_LeadContent">
          <div class="modal-header">
            <h2>Select Leads</h2>
            <button @click="showSmsLeadModal = false">×</button>
          </div>
          <!-- Modal Body: Leads List -->
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

          <!-- Modal Footer -->
          <div class="modal-footer">
            <button @click="addSelectedPhones">Add Selected</button>
          </div>
        </div>
      </div>
    </teleport>
    
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

    <!-- To Field -->
    <div class="form-group">
      <label for="sms-to">To:</label>
      <div class="input-group">
        <div class="selected-leads">
          <span v-for="(lead, index) in selectedLeadsList" :key="lead.id" class="selected-tag">
            {{ lead.first_name }}
            <button @click="removeSelectedLead(index)">×</button>
          </span>
          <button @click="openSmsLeadModal">+</button>
        </div>
      </div>
    </div>

    <!-- Template List -->
    <div class="form-group">
      <label for="sms-template">Template:</label>
        <select v-model="smsData.template" @change="loadSmsTemplate">
          <option value="" disabled>Select a Template</option>
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

        <li
          v-if="activeColomType === 'my'"
          class="total-leads_colom"
          :class="{ disabled: selectedcoloms.length === 0 }"
          :disabled="selectedcoloms.length === 0"
          @click="opencolomsModal"
        >
          <span>Lead</span>
          <i class="fa-solid fa-table-cells"></i>
        </li>
        
        <li
        v-if="activeLeadType === 'my'"
        class="total-leads"
        @click="openColumnsModal"
        >
        <span>Settings</span>
        <i class="fa-solid fa-gear"></i>
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
          <div 
            v-for="tag in tags" 
            :key="tag.id" 
            class="modal_tag" 
            :class="{ selected: isTagSelected(tag.id) }"
            @click="toggleTagSelection(tag.id)"
          >
            <input
              type="checkbox"
              :value="tag.id"
              :checked="isTagSelected(tag.id)"
              readonly
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

        <div class="modal_stages">
          <div 
            v-for="stage in stages" 
            :key="stage.id" 
            class="modal_stage"
            :class="{ selected: newLead.stage === stage.id }"
            @click="selectStage(stage.id)"
          >
            <input
              type="radio"
              :value="stage.id"
              v-model="newLead.stage"
              readonly
            />
            {{ stage.name }}
          </div>
        </div>

        <button @click="showAddStageInput = !showAddStageInput" class="add-button">Add Stage</button>
        <div v-if="showAddStageInput" class="new-stage-input">
          <input v-model="newStage" placeholder="Enter new stage" id="m_add_stage" class="m_add_stage"/>
          <button @click="addStage" class="submit-button">Add</button>
        </div>

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

    <!-- Leads Table -->
    <div class="table-container">
  <div class="table-wrapper">
    <table class="lead_Table">
      <thead>
        <tr>
          <!-- Fixed Checkbox Column -->
          <th class="checkbox-col">
            <label class="custom-checkbox-label">
              <input type="checkbox" @change="toggleSelectAll" v-model="allSelected" class="custom-checkbox" />
              <span class="custom-checkbox-span"></span>
            </label>
          </th>

          <!-- Draggable Columns (Only First 6 Visible) -->
          <draggable 
            v-model="selectedColumns" 
            tag="tr" 
            item-key="col" 
            handle=".drag-handle"
            @end="onColumnReorder"
          >
            <template #item="{ element, index }">
              <th 
                class="lead_TableHead" 
                :class="{ 'scroll-column': index >= 6 }"
              >
                <span class="drag-handle">☰</span>
                {{ availableColumns[element]?.label || element }}
              </th>
            </template>
          </draggable>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(lead, index) in leads" :key="index">
          <!-- Checkbox Column -->
          <td class="checkbox-col">
            <input type="checkbox" v-model="selectedLeads" :value="lead.id" />
          </td>

          <!-- Dynamic Columns (First 6 Visible, 7th Scrolls) -->
          <td v-for="(col, idx) in selectedColumns" :key="col" :class="{ 'scroll-column': idx >= 6 }">
            <template v-if="col === 'first_name'">
              <router-link :to="'/update-profile/' + lead.id" class="lead-name-link">
                {{ lead[col] || 'N/A' }}
              </router-link>
            </template>
            <template v-else>
              {{ lead[col] || 'N/A' }}
            </template>
          </td>

        </tr>
      </tbody>
    </table>
  </div>
</div>

    <div v-if="filteredLeads.length === 0" class="no-leads">No leads available.</div>

    <div v-if="toast.message" :class="`toast ${toast.type}`">
      {{ toast.message }}
      <button @click="toast.message = ''" class="toast-close-button">X</button>
    </div>  

    <div class="pagination">
      <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1">Previous</button>
      <span>Page {{ currentPage }} of {{ totalPages }}</span>
      <button @click="changePage(currentPage + 1)" :disabled="currentPage === totalPages">Next</button>
    </div>
  </div>

  </div>

  <div v-if="showColumnsModal" class="columns-modal-overlay">
    <div class="columns-modal-container">
      <span class="columns-modal-close" @click="closeColumnsModal">&times;</span>
      <h3>Select and Arrange Columns</h3>

      <ul class="columns-modal-list">
        <li v-for="(col, key) in availableColumns" :key="key" class="columns-modal-item">
          <input
            type="checkbox"
            :checked="selectedColumns.includes(key)"
            @change="toggleColumnSelection(key)"
            :disabled="col.disabled"
          />
          {{ col.label }}
        </li>
      </ul>

      <p v-if="selectedColumns.length === 0" class="columns-modal-warning">
        ⚠️ Please select at least one column!
      </p>

      <button class="columns-modal-update-btn" @click="updateColumns">Save</button>
      <button class="columns-modal-update-btn cancel-btn" @click="closeColumnsModal">Cancel</button>
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
import draggable from "vuedraggable";

export default {
  name: 'LeadsPage',
   components: { draggable },
  setup() {
  

    const activeLeadType = ref('all');
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
 

    const authToken = localStorage.getItem('auth_token');
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

    const removeEmail = (index) => {
      selectedEmails.value.splice(index, 1);
      emailData.value.to = selectedEmails.value.join(', ');
    };

    watch(selectedLeads, (newSelectedLeads) => {
     selectedEmails.value = leads.value
       .filter((lead) => newSelectedLeads.includes(lead.id))
       .map((lead) => lead.email);

     emailData.value.to = selectedEmails.value.join(', ');
     console.log('Updated To Field:', emailData.value.to);
    });

    watch(selectedEmails, (newSelectedEmails) => {
      emailData.value.to = newSelectedEmails.join(', ');
      console.log('Updated To Field from Modal:', emailData.value.to);
    });

    const openLeadModal = async () => {
        console.log('Lead modal opening...')
        showLeadModal.value = true
        await getMyLeads()
      }

      const closeLeadModal = () => {
        showLeadModal.value = false;
      };

    const toggleLeadSelection = (email) => {
      if (selectedEmails.value.includes(email)) {
        selectedEmails.value = selectedEmails.value.filter((e) => e !== email);
      } else {
        selectedEmails.value.push(email);
      }
    };     

    const selectedMergeField = ref('');

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
      
      const templates = ref([]);

      const fetchTemplates = async () => {
          try {
            const response = await axios.get('/api/templates', {
              headers: {
               'Authorization': `Bearer ${authToken}`,
              }
            });

            templates.value = response.data.data ?? response.data;  
          } catch (error) {
            console.error('Error fetching templates:', error);

            if (error.response && error.response.status === 401) {
              alert('Session expired. Please log in again.');
              
            }
          }
        };

        const initializeTinyMCE = () => {
          nextTick(() => {
            if (!tinymce.get("email-editor")) {
              tinymce.init({
                selector: "#email-editor",
                plugins: 'link lists image code',
                toolbar: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | link image media | mergeFieldButton | preview fullscreen",
                menubar: "file edit view insert format tools table help",
                height: 500,
                setup: (editor) => {
                  tinymceEditor.value = editor;

                  // Custom Merge Field Button
                  editor.ui.registry.addMenuButton("mergeFieldButton", {
                    text: "Merge Fields",
                    fetch: (callback) => {
                      const items = mergeFields.value.map((field) => ({
                        type: "menuitem",
                        text: field,
                        onAction: () => {
                          editor.insertContent(field);
                        },
                      }));
                      callback(items);
                    },
                  });
                },
                image_title: true,
                automatic_uploads: true,
                file_picker_types: "image",
                file_picker_callback: (callback, value, meta) => {
                  const input = document.createElement("input");
                  input.setAttribute("type", "file");
                  input.setAttribute("accept", "image/*");

                  input.onchange = function () {
                    const file = this.files[0];
                    const reader = new FileReader();

                    reader.onload = function () {
                      const base64 = reader.result;
                      callback(base64, { title: file.name });
                    };
                    reader.readAsDataURL(file);
                  };

                  input.click();
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

          emailData.value.subject = selectedTemplate.subject;

          // ✅ Fix: set attachment using correct key
          if (selectedTemplate.attachment_path) {
            emailData.value.attachments = [selectedTemplate.attachment_path];
            console.log('Mapped attachment:', selectedTemplate.attachment_path);
          } else {
            emailData.value.attachments = [];
          }

          if (selectedSignature.value) {
            addSignatureToBody();
          }
        }
      };

      const removeAttachment = (index) => {
          emailData.value.attachments.splice(index, 1);
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
        // Create FormData
        const formData = new FormData();
        formData.append('from', emailData.value.from);
        emailData.value.to.split(',').map(email => email.trim()).forEach(email => {
          formData.append('to[]', email);
        });
        formData.append('subject', emailData.value.subject);
        formData.append('message', message);
        formData.append('template_id', emailData.value.template);

        // Append attachments (assuming attachments are File objects)
        emailData.value.attachments.forEach((file, index) => {
          formData.append('attachments[]', file);
        });

        await axios.post('/api/send-email', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
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



    const showEmailLeadModal = ref(false);
    const showSmsLeadModal = ref(false);
    const selectedPhones = ref([]); 
    const selectedNames = ref([]); 
    const selectedLeadsList = ref([]); 

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

    const mergeTags = ref(['{name}', '{email}', '{phone}']);

    // ✅ Update "To" field dynamically
    const updateSmsToField = () => {
      smsData.value.to = selectedLeadsList.value.map(lead => lead.phone).join(', ');
      smsData.value.toName = selectedLeadsList.value.map(lead => lead.first_name).join(', ');
    };

    // Toggle a lead selection
    const toggleSmsLeadSelection = (lead) => {
      const index = selectedLeads.value.indexOf(lead.id);
      if (index === -1) {
        selectedLeads.value.push(lead.id);
        selectedLeadsList.value.push({ id: lead.id, first_name: lead.first_name, phone: lead.phone });
      } else {
        selectedLeads.value.splice(index, 1);
        selectedLeadsList.value = selectedLeadsList.value.filter(l => l.id !== lead.id);
      }
      updateSmsToField();
    };


    // ✅ Check if a lead is selected
    const isLeadSelected = (lead) => {
      return selectedLeads.value.includes(lead.id);
    };

    // ✅ Remove a selected lead
    const removeSelectedLead = (index) => {
      const removedLead = selectedLeadsList.value[index];
      selectedLeadsList.value.splice(index, 1);
      selectedLeads.value = selectedLeads.value.filter(id => id !== removedLead.id);
      updateSmsToField();
    };

    // ✅ Update "To" field when leads change
    watch(selectedLeads, (newSelectedLeads) => {
      smsData.value.to = selectedLeadsList.value.map(lead => lead.phone).join(', ');
      smsData.value.toName = selectedLeadsList.value.map(lead => lead.first_name).join(', ');
    });    
    const addSelectedPhones = () => {
      showSmsLeadModal.value = false;
    }; 

    // ✅ Open SMS Lead Selection Modal
    const openSmsLeadModal = () => {
      console.log("Opening SMS Lead Modal");
      showSmsLeadModal.value = true;
    };

    const closeSmsLeadModal = () => {
      showSmsLeadModal.value = false;
    };

    // Select a lead and update the "To" field
    const selectSmsLead = (lead) => {
      smsData.value.toName = lead.first_name;
      smsData.value.to = lead.phone;
      showSmsLeadModal.value = false;
    };

    // ✅ Sync "To" field when leads are selected/deselected
    watch(selectedLeads, () => {
      selectedLeadsList.value = leads.value.filter(lead => selectedLeads.value.includes(lead.id));
      smsData.value.to = selectedLeadsList.value.map(lead => lead.phone).join(', ');
      smsData.value.toName = selectedLeadsList.value.map(lead => lead.first_name).join(', ');
    });


    // Prevent exceeding 300 characters
    const countCharacters = () => {
      if (smsData.value.message.length > 300) {
        smsData.value.message = smsData.value.message.slice(0, 300);
      }
        };
        
    const smsTemplates = ref([]);
    const loadingTemplates = ref(false);

    const fetchSmsTemplates = async () => {
      loadingTemplates.value = true;
      try {
        const response = await axios.get('/api/sms-templates');
        console.log('Fetched templates:', response.data);
        smsTemplates.value = response.data.templates; // 👈 make sure you're accessing the correct property
      } catch (error) {
        console.error('Failed to load SMS templates:', error);
        Swal.fire('Error', 'Could not load SMS templates.', 'error');
      } finally {
        loadingTemplates.value = false;
      }
    };


    const selectLead = (lead) => {
      smsData.value.to = lead.phone;
      closeLeadModal(); 
    };

    // ✅ "Select All" Leads
    const toggleSelectAll = (event) => {
      if (event.target.checked) {
        selectedLeads.value = leads.value.map(lead => lead.id);
        selectedLeadsList.value = leads.value.map(lead => ({
          id: lead.id,
          first_name: lead.first_name,
          phone: lead.phone
        }));
      } else {
        selectedLeads.value = [];
        selectedLeadsList.value = [];
      }
      updateSmsToField();
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
      if (!Array.isArray(smsTemplates.value)) return;
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
        if (!smsData.value.from || selectedLeadsList.value.length === 0 || !smsData.value.message.trim()) {
          Swal.fire('Missing Information', 'Sender, recipient, and message cannot be empty.', 'error');
          return;
        }

        try {
        
          const selectedPhones = selectedLeadsList.value.map(lead => lead.phone);

          await axios.post('/api/send-sms', {
            lead_ids: selectedLeads.value,
            from: smsData.value.from,
            to: selectedPhones.join(', '),
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

   // Column selection modal
    const showColumnsModal = ref(false);
    const selectedColumns = ref(['first_name', 'email', 'phone']); 
    const availableColumns = ref({
      first_name: { label: 'Name', disabled: true },
      email: { label: 'Email', disabled: true },
      phone: { label: 'Phone', disabled: true },
      latest_activity: { label: 'Latest Activity' },
      activity_at: { label: 'Activity At' },
      created_on: { label: 'Created On' },
      tags: { label: 'Tags' },
      stage: { label: 'Stage' },
      latest_source: { label: 'Latest Source' },
      latest_sms: { label: 'Latest SMS' },
      latest_email: { label: 'Latest Email' },
      next_task: { label: 'Next Task' },
      next_appointment: { label: 'Next Appointment' },
      new_listing_alert: { label: 'New Listing Alerts' },
      neighbourhood_alert: { label: 'Neighbourhood Alerts' },
      open_house_alert: { label: 'Open House Alerts' },
      price_drop_alert: { label: 'Price Drop Alerts' },
      action_plans: { label: 'Active Action Plans' },
      market_updates: { label: 'Assigned Market Updates' },
      real_estate_newsletter: { label: 'Assigned Newsletters' },
      notes: { label: 'Notes' },
      dob: { label: 'DOB' },
      background: { label: 'Background' },
      city: { label: 'City' },
      facebook: { label: 'Facebook' },
      instagram: { label: 'Instagram' },
      linkedin: { label: 'LinkedIn' },
      whatsapp: { label: 'WhatsApp' },
      twitter: { label: 'Twitter' }
    });
   
    const openColumnsModal = () => { showColumnsModal.value = true; };
    const closeColumnsModal = () => { showColumnsModal.value = false; };

    const updateColumns = async () => {
      await saveColumnVisibilitySettings();
      closeColumnsModal(); 
    };
   
    const fetchColumnSettings = async () => {
      try {
        const response = await axios.get("/api/columns-settings", {
          headers: { Authorization: `Bearer ${authToken}` },
        });
        selectedColumns.value = Array.isArray(response.data.selectedColumns)
          ? response.data.selectedColumns
          : ['first_name', 'email', 'phone'];
      } catch (error) {
        showToast('Failed to fetch column settings!');
      }
    };

    // Save column settings
    const saveColumnSettings = async () => {
      try {
        await axios.post("/api/columns-settings", { selectedColumns: selectedColumns.value }, {
          headers: { Authorization: `Bearer ${authToken}` },
        });
        showToast("Column settings saved successfully!");
      } catch (error) {
        showToast("Failed to save column settings!");
      }
    };

    // column visibility toggle
    const toggleColumnSelection = (column) => {
      if (!availableColumns.value[column].disabled) {
        const index = selectedColumns.value.indexOf(column);
        if (index === -1) {
          selectedColumns.value.push(column);
          showToast(`Column "${availableColumns.value[column].label}" added`);
        } else {
          selectedColumns.value.splice(index, 1);
          showToast(`Column "${availableColumns.value[column].label}" removed`);
        }
      }
    };

    const saveColumnVisibilitySettings = async () => {
      try {
        await axios.post("/api/columns-settings", { selectedColumns: selectedColumns.value }, {
          headers: { Authorization: `Bearer ${authToken}` },
        });
        showToast("Column visibility settings saved successfully!");
      } catch (error) {
        showToast("Failed to save column visibility settings!");
      }
    };

    const saveColumnOrderSettings = async () => {
      try {
      
        await axios.post("/api/columns-order", { selectedColumns: selectedColumns.value }, {
          headers: { Authorization: `Bearer ${authToken}` },
        });
        showToast("Column order saved successfully!");
      } catch (error) {
        showToast("Failed to save column order.");
      }
    };

    const onColumnReorder = async () => {
      try {
        await saveColumnOrderSettings();
      } catch (error) {
        showToast("Failed to save column order.");
      }
    };
    const signatures = ref([]);
    const hasFetchedSignatures = ref(false);

    const fetchSignatures = async () => {
      if (hasFetchedSignatures.value) return;

      try {
        const response = await axios.get('/api/signatures', {
          headers: {
            Authorization: `Bearer ${authToken}`,
          },
        });
        
    console.log('API Response:', response);         // Logs full response
    console.log('Signature Data:', response.data);  // Logs only the data
        signatures.value = response.data;
        hasFetchedSignatures.value = true;
      } catch (error) {
        console.error('Error fetching signatures:', error);
        if (error.response?.status === 401) {
          alert('Session expired. Please log in again.');
        }
      }
    };

    const addSignatureToBody = () => {
        if (selectedSignature.value) {
          const editor = tinymce.get("email-editor");
          const existingContent = editor.getContent();
          editor.setContent(existingContent + "<br><br>" + selectedSignature.value);
        }
      };


    onMounted(async () => {
      await fetchLeads(activeLeadType.value);
      await fetchItems();
      
      try {
        await destroyTinyMCE(); 
      } catch (error) {
        console.warn("TinyMCE destruction failed:", error);
      }
       fetchSmsTemplates();
      await fetchTemplates();
      await fetchColumnSettings();
    });

    return {
      removeAttachment,
      fetchSignatures,
      removeEmail,
      saveColumnSettings,
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
      showSmsLeadModal,
      addSelectedPhones,
      toggleSmsLeadSelection, 
      selectedPhones,
      selectedNames,
      selectedLeadsList,
      isLeadSelected,
      removeSelectedLead,
      addSelectedPhones,
      toggleSmsLeadSelection,
      updateSmsToField,
      showColumnsModal,
      openColumnsModal,
      closeColumnsModal,
      selectedColumns,
      availableColumns,
      updateColumns,
      toggleColumnSelection,
      fetchColumnSettings, 
      onColumnReorder,   
    };
  },
};
</script>

<style>
.lead_Table th,
.lead_Table td {
  padding: 12px;
  border: 1px solid #ddd;
  min-inline-size: 252px;
  text-align: start;
  white-space: nowrap;
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

button {
  border-radius: 4px;
  padding-block: 8px;
  padding-inline: 12px;
}

button:hover {
  background-color: #8c57ff;
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
  border: 5px solid #a17de1;
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

.plus-button {
  border: none;
  border-radius: 0 5px 5px 0;
  background-color: #a17de1;
  color: white;
  cursor: pointer;
  font-size: 18px;
  padding-block: 8px;
  padding-inline: 12px;
}

.plus-button:hover {
  background-color: #a17de1;
}

.modal-header h2 {
  margin: 0;
}

.modal-header button {
  border: none;
  background: none;
  cursor: pointer;
  font-size: 20px;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  border-block-start: 1px solid #ddd;
  padding-block-start: 10px;
}

.modal-footer button {
  border: none;
  border-radius: 4px;
  background-color: #a17de1;
  color: white;
  cursor: pointer;
  padding-block: 8px;
  padding-inline: 12px;
}

.selected-tag {
  display: flex;
  align-items: center;
  border-radius: 5px;
  background: #8c57ff;
  color: white;
  padding-block: 5px;
  padding-inline: 10px;
}

.selected-tag button {
  border: none;
  background: none;
  color: white;
  cursor: pointer;
  font-weight: bold;
  margin-inline-start: 8px;
}

.selected-tag button:hover {
  color: #fdd;
}

.selected-leads {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 5px;
}

.columns-modal-overlay {
  position: fixed;
  z-index: 999;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 50%);
  block-size: 100vh;
  inline-size: 100vw;
  inset-block-start: 0;
  inset-inline-start: 0;
}

.columns-modal-container {
  position: relative;
  padding: 20px;
  border-radius: 8px;
  background: white;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 30%);
  inline-size: 400px;
  text-align: center;
}

.columns-modal-close {
  position: absolute;
  cursor: pointer;
  font-size: 24px;
  inset-block-start: 10px;
  inset-inline-end: 15px;
}

.columns-modal-list {
  display: grid;
  padding: 0;
  gap: 10px;
  grid-template-columns: repeat(2, 1fr);
  margin-block-start: 15px;
  text-align: start;
}

.columns-modal-item {
  display: flex;
  align-items: center;
  gap: 8px;
}

.columns-modal-warning {
  color: red;
  font-size: 12px;
  margin-block-start: 10px;
}

.columns-modal-update-btn {
  padding: 10px;
  border: none;
  border-radius: 5px;
  background: red;
  color: white;
  cursor: pointer;
  font-size: 16px;
  font-weight: bold;
  inline-size: 100%;
  margin-block-start: 15px;
}

.columns-modal-update-btn:hover {
  background: darkred;
}

.cancel-btn {
  background: gray;
}

.cancel-btn:hover {
  background: darkgray;
}

.column-container {
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 5px;
  background: #f9f9f9;
  margin-block: 10px;
  margin-inline: 0;
  max-inline-size: 300px;
}

.column-item {
  display: flex;
  align-items: center;
  padding: 5px;
  border-block-end: 1px solid #ddd;
  cursor: grab;
}

/* Extra tr ko hide karne ke liye */
.draggable-header-wrapper > tr:nth-child(2) {
  display: none;
}

.tag-modal-container,
.stage-modal-container {
  position: relative;
  padding: 20px;
  border-radius: 8px;
  background: white;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 30%);
  inline-size: 400px;
  text-align: center;
}

.modal_tags {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 10px;
  margin-block: 15px;
}

.modal_tag {
  display: flex;
  align-items: center;
  border: 2px solid transparent;
  border-radius: 5px;
  background: #f9f9f9;
  cursor: pointer;
  gap: 8px;
  padding-block: 8px;
  padding-inline: 12px;
  transition: all 0.3s ease;
  user-select: none;
}

.modal_tag.selected {
  border-color: #8c57ff;
  background: #f3e8ff;
}

.add-button,
.submit-button {
  padding: 10px;
  border: none;
  border-radius: 5px;
  background: #8c57ff;
  color: white;
  cursor: pointer;
  font-size: 14px;
  font-weight: bold;
  margin-block-start: 10px;
}

.add-button:hover,
.submit-button:hover {
  background: #7135d8;
}

.close-button {
  position: absolute;
  border: none;
  background: none;
  cursor: pointer;
  font-size: 15px;
  inset-block-start: 10px;
  inset-inline-end: 10px;
}

.modal_stage {
  display: flex;
  align-items: center;
  border: 2px solid transparent;
  border-radius: 5px;
  background: #f9f9f9;
  cursor: pointer;
  gap: 8px;
  padding-block: 8px;
  padding-inline: 12px;
  transition: all 0.3s ease;
  user-select: none;
}

.modal_stage.selected {
  border-color: #8c57ff;
  background: #f3e8ff;
}

/* Table Styling */
.lead_Table {
  border-collapse: separate;
  border-spacing: 0;
  inline-size: 100%;
  table-layout: fixed;
  white-space: nowrap;
}

/* Table Data Cells */
.lead_Table td {
  padding: 12px;
  border: 1px solid #ddd;
  text-align: start;
  white-space: nowrap;
}

/* Fix Checkbox Column */
.checkbox-col {
  position: sticky;
  z-index: 3; /* Highest priority */
  background: white;
  inline-size: 40px !important;
  inset-inline-start: 0;
  text-align: center;
}

/* Drag Handle Styling */
.drag-handle {
  display: inline-block;
  cursor: grab;
  padding-inline-end: 5px;
}

/* Table Header & Body Alignment */
.lead_Table thead {
  display: table-header-group;
}

.lead_Table tbody {
  display: table-row-group;
  max-block-size: 400px;
  overflow-y: auto;
}

.table-container {
  max-inline-size: 100%;
  overflow-x: auto;
}

.lead_Table th:nth-child(n+8),
.lead_Table td:nth-child(n+8) {
  min-inline-size: 150px;
}

.lead_Table th:nth-child(3),
.lead_Table td:nth-child(3) {
  overflow: hidden;
  max-inline-size: 200px;
  text-overflow: ellipsis;
  word-break: break-word;
}

@media (max-width: 768px) {
  .table-container {
    overflow-x: scroll;
  }
}

.lead-name-link {
  color: #8c57ff;
  text-decoration: underline;
}

.lead-name-link:hover {
  color: #8c57ff;
}

.subject-container {
  display: flex;
  align-items: center;
}

.subject-input {
  flex-grow: 1;
  padding: 5px;
  border: none;
  border-block-end: 1px solid #ccc;
}

.merge-field {
  padding: 5px;
  border: 1px solid #ccc;
  border-radius: 5px;
  margin-inline-start: 10px;
}
</style>
