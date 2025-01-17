<template>
  <div class="main">
    <div class="main-2">
      <div class="heading-section">
        <div class="sms-templates">
          <h3>SMS Template</h3>
        </div>
        <div class="sms-icon">
          <i class="fa-regular fa-plus" @click="openModal"></i>
          <i class="fa-solid fa-trash-can-arrow-up" @click="deleteSelectedTemplates"></i>
        </div>
      </div>

      <!-- Search Section -->
      <div class="search">
        <input type="text" v-model="searchQuery" placeholder="     ENTER SMS template NAME" />
      </div>

      <div class="data-section">
        <table class="table-sms-body">
          <thead>
            <tr>
              <th class="sms_checkbox_field"><input type="checkbox" class="sms_checkbox" @change="toggleSelectAll" /></th>
              <th>Name</th>
              <th>Created By</th>
              <th>Body</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="template in filteredTemplates" :key="template.id">
              <td class="sms_checkbox_field">
                <input type="checkbox" v-model="selectedTemplates" :value="template.id" />
              </td>
              <td>{{ template.title }}</td>
              <td>{{ template.created_by }}</td>
              <td>{{ template.content }}</td>
              <td>
                <button @click="editTemplate(template)">Edit</button>
                <button @click="deleteTemplate(template.id)">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Modal -->
      <div v-if="isModalOpen" class="modal-overlay">
        <div class="modal">
          <button class="close-btn" @click="closeModal">&times;</button>
          <h3>{{ isEditing ? 'Edit SMS Template' : 'Add SMS Template' }}</h3>
          <form @submit.prevent="submitForm">
            <div class="form-group">
              <label for="title">Title</label>
              <input type="text" id="title" v-model="formData.title" required />
            </div>
            <div class="form-group">
              <label for="content">Content</label>
              <textarea id="content" v-model="formData.content" rows="4" required></textarea>
            </div>
            <button type="submit" class="submit-btn">{{ isEditing ? 'Update' : 'Submit' }}</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2'; // Import SweetAlert2
import { computed, ref } from 'vue';

export default {
  setup() {
    const isModalOpen = ref(false);
    const isEditing = ref(false);
    const formData = ref({
      type: "sms",
      title: "",
      content: "",
      id: null,
    });

    const smsTemplates = ref([]);
    const selectedTemplates = ref([]);
    const searchQuery = ref("");

    const getAuthToken = () => localStorage.getItem('auth_token');

    const fetchTemplates = async () => {
      const authToken = getAuthToken();
      try {
        const response = await axios.get('/api/templates', {
          params: { type: 'sms' },
          headers: {
            'Authorization': `Bearer ${authToken}`,
          },
        });
        smsTemplates.value = response.data.data;
      } catch (error) {
        console.error("Error fetching templates:", error);
      }
    };

    const filteredTemplates = computed(() => {
      return smsTemplates.value.filter((template) => 
        template.title.toLowerCase().includes(searchQuery.value.toLowerCase())
      );
    });

    const openModal = () => {
      isModalOpen.value = true;
      isEditing.value = false;
      resetForm();
    };

    const editTemplate = (template) => {
      formData.value = { ...template };
      isModalOpen.value = true;
      isEditing.value = true;
    };

    const closeModal = () => {
      isModalOpen.value = false;
      resetForm();
    };

    const resetForm = () => {
      formData.value.title = "";
      formData.value.content = "";
      formData.value.id = null;
    };

    const submitForm = async () => {
      try {
        if (isEditing.value) {
          await updateTemplate();
          Swal.fire('Updated!', 'The template has been updated successfully.', 'success');
        } else {
          await createTemplate();
          Swal.fire('Created!', 'The template has been created successfully.', 'success');
        }
        closeModal();
        fetchTemplates();
      } catch (error) {
        console.error("Error submitting form:", error);
        Swal.fire('Error!', 'There was an issue saving the template.', 'error');
      }
    };

    const createTemplate = async () => {
      const authToken = getAuthToken();
      if (!authToken) {
        console.error("Auth token is missing");
        return;
      }

      try {
        await axios.post('/api/templates', formData.value, {
          headers: {
            'Authorization': `Bearer ${authToken}`,
          },
        });
      } catch (error) {
        console.error("Error creating template:", error);
      }
    };

    const updateTemplate = async () => {
      const authToken = getAuthToken();
      if (!authToken) {
        console.error("Auth token is missing");
        return;
      }

      try {
        await axios.put(`/api/templates/${formData.value.id}`, formData.value, {
          headers: {
            'Authorization': `Bearer ${authToken}`,
          },
        });
      } catch (error) {
        console.error("Error updating template:", error);
      }
    };

    const deleteTemplate = async (id) => {
      const authToken = getAuthToken();
      if (!authToken) {
        console.error("Auth token is missing");
        return;
      }

      const result = await Swal.fire({
        title: 'Are you sure?',
        text: 'You won\'t be able to revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
      });

      if (result.isConfirmed) {
        try {
          await axios.delete(`/api/templates/${id}`, {
            headers: {
              'Authorization': `Bearer ${authToken}`,
            },
          });
          Swal.fire('Deleted!', 'The template has been deleted.', 'success');
          fetchTemplates();
        } catch (error) {
          console.error("Error deleting template:", error);
          Swal.fire('Error!', 'There was an issue deleting the template.', 'error');
        }
      }
    };

    const deleteSelectedTemplates = async () => {
      const authToken = getAuthToken();
      if (!authToken) {
        console.error("Auth token is missing");
        return;
      }

      const result = await Swal.fire({
        title: 'Are you sure?',
        text: 'You won\'t be able to revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete them!',
      });

      if (result.isConfirmed) {
        try {
          await axios.delete('/api/templates', {
            data: { ids: selectedTemplates.value },
            headers: {
              'Authorization': `Bearer ${authToken}`,
            },
          });
          Swal.fire('Deleted!', 'The selected templates have been deleted.', 'success');
          fetchTemplates();
        } catch (error) {
          console.error("Error deleting selected templates:", error);
          Swal.fire('Error!', 'There was an issue deleting the selected templates.', 'error');
        }
      }
    };

    const toggleSelectAll = () => {
      if (selectedTemplates.value.length === filteredTemplates.value.length) {
        selectedTemplates.value = [];
      } else {
        selectedTemplates.value = filteredTemplates.value.map((template) => template.id);
      }
    };

    fetchTemplates();

    return {
      isModalOpen,
      isEditing,
      formData,
      smsTemplates,
      selectedTemplates,
      searchQuery,
      filteredTemplates,
      openModal,
      closeModal,
      submitForm,
      resetForm,
      editTemplate,
      deleteTemplate,
      deleteSelectedTemplates,
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

.table-sms-body th {
  border-block-end: 1px solid black;
  text-align: start;
}

.table-sms-body td {
  border-block-end: 1px solid black;
  text-align: start;
}

.table-sms-body {
  background-color: white;
  border-block-end: 1px solid black;
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
