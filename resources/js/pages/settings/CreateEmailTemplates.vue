<template>
  <div class="main">
    <div class="main-2">
      
      <div class="heading-section">
        <div class="email-templates">
          <h3>Email Template</h3>
        </div>
        <div class="email-icon">
          <i class="fa-regular fa-plus" @click="openModal"></i>
           <i class="fa-regular fa-minus" 
            @click="deleteSelectedTemplates" 
            :class="{'disabled': selectedTemplates.length === 0}" 
            :style="{cursor: selectedTemplates.length === 0 ? 'not-allowed' : 'pointer'}">
            Delete
          </i>
        </div>
      </div>

      <!-- Search Section -->
      <div class="search">
        <input
          type="text"
          placeholder="Enter email template name"
          v-model="searchQuery"
          @input="fetchTemplates"
        />
      </div>

      <!-- Data Section -->
      <div class="data-section">
        <table>
  <thead>
    <tr>
      <th><input type="checkbox" @change="toggleSelectAll" /></th>
      <th>Attachment</th> <!-- Add a column for the attachment -->
      <th>Name</th>
      <th>Created By</th>
      <th>Subject</th>
      <th>Body</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <tr v-for="template in templates.data" :key="template.id">
      <td><input type="checkbox" v-model="selectedTemplates" :value="template.id" /></td>
      <td><img 
          v-if="template.attachment_path" 
          :src="`/storage/${template.attachment_path}`" 
          alt="Template Image" 
          width="50" 
          height="50" 
          class="image-thumbnail" />
        <i 
          v-else 
          class="fa fa-user-circle" 
          style=" color: #ccc;font-size: 50px;" 
          aria-hidden="true"></i>
      </td>      
      <td>{{ template.title }}</td>
      <td>{{ template.created_by }}</td>
      <td>{{ template.subject }}</td>
      <td>{{ template.content }}</td>
      <td>
        <button @click="openModal(template)">Edit</button>
        <button @click="confirmDeleteTemplate(template.id)">Delete</button>
      </td>
    </tr>
  </tbody>
</table>


        <!-- Pagination -->
        <div class="pagination">
          <button @click="previousPage" :disabled="templates.current_page === 1">Previous</button>
          <span>Page {{ templates.current_page }} of {{ templates.last_page }}</span>
          <button @click="nextPage" :disabled="templates.current_page === templates.last_page">Next</button>
        </div>
      </div>

      <!-- Modal -->
      <div v-if="isModalOpen" class="modal-overlay">
        <div class="modal">
          <button class="close-btn" @click="closeModal">&times;</button>
          <h3>{{ formData.id ? 'Edit' : 'Add' }} Email Template</h3>
          <form @submit.prevent="submitForm">
            <div class="form-group">
              <label for="title">Title</label>
              <input type="text" id="title" v-model="formData.title" required />
            </div>

            <div class="form-group">
              <label for="subject">Subject</label>
              <input type="text" id="subject" v-model="formData.subject" required />
            </div>

            <div class="form-group">
              <label for="content">Content</label>
              <textarea id="content" v-model="formData.content" required></textarea>
            </div>

            <div class="form-group">
              <label for="attachment">Attachment</label>
              <input type="file" id="attachment" @change="handleFileUpload" />
            </div>

            <button type="submit" class="submit-btn">Submit</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

import axios from 'axios';
import Swal from 'sweetalert2';
import { onMounted, ref, watch } from 'vue';


export default {
  setup() {
    const templates = ref({ data: [], current_page: 1, last_page: 1 });
    const selectedTemplates = ref([]);
    const searchQuery = ref('');
    const isModalOpen = ref(false);
    const formData = ref({
      type: 'email',
      title: '',
      subject: '',
      content: '',
      attachment: null,
    });

    const handleFileUpload = (event) => {
    const file = event.target.files[0];
    formData.value.attachment = file; 
};
    // Fetch templates from the server
    const fetchTemplates = async () => {
      const authToken = localStorage.getItem('auth_token');

      try {
        const response = await axios.get('/api/templates', {
          params: {
            page: templates.value.current_page,
            search: searchQuery.value,
            type: 'email',
          },
          headers: {
            'Authorization': `Bearer ${authToken}`,
          },
        });
        templates.value = response.data;
      } catch (error) {
        if (error.response && error.response.status === 401) {
          Swal.fire('Error', 'You are not authenticated. Please log in again.', 'error');
     
        } else {
          console.error('Error fetching templates:', error);
        }
      }
    };


    const submitForm = async () => {
    const authToken = localStorage.getItem('auth_token');

    try {
        let response;
        const form = new FormData();
        form.append('title', formData.value.title);
        form.append('subject', formData.value.subject);
        form.append('content', formData.value.content);
        form.append('type', 'email');

        // Check if file is selected
        console.log(formData.value.attachment);

        if (formData.value.attachment) {
            form.append('attachment', formData.value.attachment);
        }

        response = await axios.post('/api/templates', form, {
            headers: {
                'Authorization': `Bearer ${authToken}`,
            },
        });

        // Handle successful response
        if (response.data.success) {
            fetchTemplates();
            closeModal();
            Swal.fire('Success!', `Template has been ${formData.value.id ? 'updated' : 'created'}.`, 'success');
        } else {
            Swal.fire('Error', 'Failed to submit the template. Please try again.', 'error');
        }
    } catch (error) {
    
        if (error.response && error.response.data.errors) {
            const messages = Object.values(error.response.data.errors).flat().join('\n');
            Swal.fire('Validation Error', messages, 'error');
        } else {
            Swal.fire('Error', 'An error occurred while submitting the template. Please try again.', 'error');
        }
    }
};


    // Open the modal to create a new template or edit an existing one
    const openModal = (template = null) => {
      isModalOpen.value = true;

      if (template) {
        // Populate form with existing template data for editing
        formData.value.id = template.id;
        formData.value.title = template.title;
        formData.value.subject = template.subject;
        formData.value.content = template.content;
        formData.value.attachment = template.attachment || null;
      } else {
        // If no template is passed, reset the form for a new template
        formData.value.id = null;
        formData.value.title = '';
        formData.value.subject = '';
        formData.value.content = '';
        formData.value.attachment = null;
      }
    };

    const closeModal = () => {
      isModalOpen.value = false;
      resetForm();
    };

    const resetForm = () => {
      formData.value.title = '';
      formData.value.subject = '';
      formData.value.content = '';
      formData.value.attachment = null;
    };

    // Delete selected templates with confirmation
    const deleteSelectedTemplates = async () => {
      const authToken = localStorage.getItem('auth_token'); 
      if (!authToken) {
        Swal.fire('Error', 'Please log in to continue', 'error');
        return;
      }

      const result = await Swal.fire({
        title: 'Are you sure?',
        text: 'You will not be able to recover these templates!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete them!',
      });

      if (result.isConfirmed) {
        try {
          // Send POST request with authorization token for bulk deletion
          await axios.post('/api/templates/bulk-delete', {
            ids: selectedTemplates.value,
          }, {
            headers: {
              'Authorization': `Bearer ${authToken}`,
            },
          });
          fetchTemplates();
          Swal.fire('Deleted!', 'Your selected templates have been deleted.', 'success');
        } catch (error) {
          console.error('Error deleting selected templates:', error);
          Swal.fire('Error', 'An error occurred while deleting the templates. Please try again.', 'error');
        }
      }
    };


    // Delete a single template with confirmation
    const confirmDeleteTemplate = async (id) => {
      const authToken = localStorage.getItem('auth_token'); 
      if (!authToken) {
        Swal.fire('Error', 'Please log in to continue', 'error');
        return;
      }

      const result = await Swal.fire({
        title: 'Are you sure?',
        text: 'You will not be able to recover this template!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
      });

      if (result.isConfirmed) {
        try {
          // Send DELETE request with authorization token for single deletion
          await axios.delete(`/api/templates/${id}`, {
            headers: {
              'Authorization': `Bearer ${authToken}`,
            },
          });
          fetchTemplates();
          Swal.fire('Deleted!', 'Template has been deleted.', 'success');
        } catch (error) {
          console.error('Error deleting template:', error);
          Swal.fire('Error', 'An error occurred while deleting the template. Please try again.', 'error');
        }
      }
    };

    // Pagination control functions
    const nextPage = () => {
      if (templates.value.current_page < templates.value.last_page) {
        templates.value.current_page++;
        fetchTemplates();
      }
    };

    const previousPage = () => {
      if (templates.value.current_page > 1) {
        templates.value.current_page--;
        fetchTemplates();
      }
    };

    // Toggle select all templates
    const toggleSelectAll = () => {
      if (selectedTemplates.value.length === templates.value.data.length) {
        selectedTemplates.value = [];
      } else {
        selectedTemplates.value = templates.value.data.map((template) => template.id);
      }
    };

    // Watch for changes in search query
    watch(searchQuery, fetchTemplates);

    onMounted(fetchTemplates);

    return {
      handleFileUpload,
      templates,
      selectedTemplates,
      searchQuery,
      isModalOpen,
      formData,
      openModal,
      closeModal,
      submitForm,
      deleteSelectedTemplates,
      confirmDeleteTemplate,
      nextPage,
      previousPage,
      toggleSelectAll,
    };
  },
};
</script>

<style>
.main {
  block-size: auto;
  inline-size: 100%;
}

.heading-section {
  display: flex;
  justify-content: space-between;
  border-block-end: 1px solid black;
}

.heading-section h3 {
  margin: 0;
}

.search {
  overflow: hidden;
  box-sizing: border-box;
  padding: 15px;
  inline-size: 100%;
}

.search input {
  inline-size: 100%;
}

.data-section {
  inline-size: 100%;
}

.data-section table th {
  inline-size: 20%;
  text-align: justify;
}

table {
  inline-size: 100%;
}

.modal-overlay {
  position: fixed;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: rgba(0, 0, 0, 50%);
  block-size: 100%;
  inline-size: 100%;
  inset-block-start: 0;
  inset-inline-start: 0;
}

.modal {
  padding: 20px;
  border-radius: 8px;
  background: white;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 10%);
  inline-size: 400px;
}

.close-btn {
  border: none;
  background: none;
  cursor: pointer;
  float: inline-end;
  font-size: 24px;
}

.form-group {
  margin-block-end: 15px;
}

label {
  display: block;
  margin-block-end: 5px;
}

textarea {
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  inline-size: 100%;
}

.submit-btn {
  border: none;
  border-radius: 4px;
  background-color: #007bff;
  color: white;
  cursor: pointer;
  padding-block: 10px;
  padding-inline: 15px;
}

.submit-btn:hover {
  background-color: #0056b3;
}

.merge-buttons {
  margin-block-start: 10px;
}

.merge-buttons button {
  border: 1px solid #ccc;
  border-radius: 4px;
  background-color: #f0f0f0;
  cursor: pointer;
  margin-inline-end: 5px;
  padding-block: 5px;
  padding-inline: 10px;
}

.merge-buttons button:hover {
  background-color: #e0e0e0;
}

</style>
