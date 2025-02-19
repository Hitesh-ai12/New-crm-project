
import axios from 'axios';
import Swal from 'sweetalert2';
import { computed, onMounted, ref } from 'vue';

export default {
  name: 'LeadsPage',
  setup() {
    // Reactive properties
    const allLeads = ref([]); // All available leads
    const myLeads = ref([]); // Logged-in user's leads
    const selectedView = ref('all'); // To track the selected view ('all' or 'my')
    const leads = ref([]);
    const loading = ref(true);
    const error = ref('');
    const searchQuery = ref('');
    const showForm = ref(false);
    const selectedLeads = ref([]);
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
    const pageSize = ref(10); // Default page size
    const totalPages = computed(() => Math.ceil(leads.value.length / pageSize.value));

    const showEmailModal = ref(false);
    const showSmsModal = ref(false);
    const emailMessage = ref('');
    const smsMessage = ref('');
    const newTag = ref('');
    const newStage = ref('');
    const showTagModal = ref(false);
    const showStageModal = ref(false);
    const showAddTagInput = ref(false);
    const showAddStageInput = ref(false);

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

    //--------Tag
    const isTagSelected = (tagId) => {
        return selectedTags.value.has(tagId);
      };

      const toggleTagSelection = (tagId) => {
        if (selectedTags.value.has(tagId)) {
          selectedTags.value.delete(tagId);
        } else {
          selectedTags.value.add(tagId);
        }
        newLead.value.tag = Array.from(selectedTags.value); // Update newLead.tag with selected tag IDs
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

    const openEmailModal = () => {
      if (selectedLeads.value.length === 0) {
        alert('No leads selected.');
        return;
      }
      showEmailModal.value = true;
    };

    const closeEmailModal = () => {
      showEmailModal.value = false;
      emailMessage.value = '';
    };

    const openSmsModal = () => {
      if (selectedLeads.value.length === 0) {
        alert('No leads selected.');
        return;
      }
      showSmsModal.value = true;
    };

    const closeSmsModal = () => {
      showSmsModal.value = false;
      smsMessage.value = '';
    };

    const sendEmail = async () => {
      if (emailMessage.value.trim() === '') {
        alert('Message cannot be empty.');
        return;
      }

      try {
        await axios.post('/leads/send-email', {
          lead_ids: selectedLeads.value,
          message: emailMessage.value,
        });
        alert('Emails sent successfully.');
        closeEmailModal();
      } catch (err) {
        alert('Failed to send emails.');
      }
    };

    const sendSms = async () => {
      if (smsMessage.value.trim() === '') {
        alert('Message cannot be empty.');
        return;
      }

      try {
        await axios.post('/leads/send-sms', {
          lead_ids: selectedLeads.value,
          message: smsMessage.value,
        });
        alert('SMS sent successfully.');
        closeSmsModal();
      } catch (err) {
        alert('Failed to send SMS.');
      }
    };

    const fetchLeads = async () => {
      try {
        const response = await axios.get('/leads');
        leads.value = Array.isArray(response.data) ? response.data : [];
      } catch (err) {
        error.value = 'Failed to fetch leads.';
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
      newTag.value = ''; // Reset input
    };

    const openStageModal = () => {
      showStageModal.value = true;
    };

    const closeStageModal = () => {
      showStageModal.value = false;
      newStage.value = ''; // Reset input
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
        lead.first_name.toLowerCase().includes(searchQuery.value.toLowerCase())
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
      // Add logic to handle indirect file import
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
      alert('Direct File Import option selected');
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
      await fetchLeads();
      await fetchItems();
    });

    return {
      leads,
      loading,
      error,
      searchQuery,
      filteredLeads,
      showForm,
      newLead,
      errors,
      selectedLeads,
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
      showEmailModal,
      showSmsModal,
      emailMessage,
      smsMessage,
      openEmailModal,
      closeEmailModal,
      openSmsModal,
      closeSmsModal,
      sendEmail,
      sendSms,
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
