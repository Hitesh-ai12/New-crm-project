<template>
  <div class="signature-wrapper">
    <!-- Left Panel - Signature List -->
    <div class="signature-list">
      <h3>Signatures</h3>
      <div class="signature-items">
        <div
          v-for="sig in filteredSignatures"
          :key="sig.id"
          :class="['signature-item', sig.id === selectedSignature?.id ? 'active' : '']"
          @click="selectSignature(sig)"
        >
          <div class="name">{{ sig.name }}</div>
          <div class="default-tag" v-if="sig.is_default">Default</div>
          <button class="delete-btn" @click.stop="deleteSignature(sig.id)">🗑️</button>
        </div>
      </div>
      <button class="add-btn" @click="createNewSignature">+ Add New</button>
    </div>

    <!-- Right Panel - Signature Editor -->
    <div class="signature-editor">
      <h3>{{ isEditing ? 'Update Signature' : 'Create Signature' }}</h3>
      <form @submit.prevent="submitForm">
        <label>Signature Name</label>
        <input v-model="formData.name" type="text" required />

        <label>Content</label>
        <textarea id="signature-editor"></textarea>

        <label class="checkbox">
          <input type="checkbox" v-model="formData.is_default" />
          Set as default
        </label>

        <div class="btn-group">
          <button type="button" @click="resetForm">Cancel</button>
          <button type="submit">{{ isEditing ? 'Update Signature' : 'Create Signature' }}</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios';
import Swal from 'sweetalert2';
import tinymce from 'tinymce';
import 'tinymce/icons/default';
import 'tinymce/plugins/link';
import 'tinymce/plugins/lists';
import 'tinymce/themes/silver';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

const signatures = ref([]);
const searchQuery = ref('');
const isEditing = ref(false);
const selectedSignature = ref(null);

const formData = ref({
  id: null,
  name: '',
  content: '',
  is_default: false,
});

const getAuthToken = () => localStorage.getItem('auth_token');

const fetchSignatures = async () => {
  try {
    const response = await axios.get('/api/signatures', {
      headers: {
        Authorization: `Bearer ${getAuthToken()}`,
      },
    });
    signatures.value = response.data;
  } catch (err) {
    console.error('Fetch error:', err);
  }
};

const filteredSignatures = computed(() =>
  signatures.value.filter(sig =>
    sig.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
);

const selectSignature = (sig) => {
  selectedSignature.value = sig;
  editSignature(sig);
};

const createNewSignature = () => {
  resetForm();
  isEditing.value = false;
};

const editSignature = (sig) => {
  formData.value = { ...sig };
  isEditing.value = true;
  nextTick(() => initTinyMCE()); // Delay to ensure DOM is ready
};

const resetForm = () => {
  formData.value = {
    id: null,
    name: '',
    content: '',
    is_default: false,
  };
  isEditing.value = false;
  selectedSignature.value = null;
  destroyTinyMCE();
};

const submitForm = async () => {
  formData.value.content = tinymce.get('signature-editor')?.getContent() || '';
  try {
    if (isEditing.value) {
      await axios.put(`/api/signatures/${formData.value.id}`, formData.value, {
        headers: { Authorization: `Bearer ${getAuthToken()}` },
      });
      Swal.fire('Updated!', 'Signature updated successfully.', 'success');
    } else {
      await axios.post('/api/signatures', formData.value, {
        headers: { Authorization: `Bearer ${getAuthToken()}` },
      });
      Swal.fire('Created!', 'Signature added successfully.', 'success');
    }
    resetForm();
    fetchSignatures();
  } catch (err) {
    console.error('Submit error:', err);
    Swal.fire('Error', 'Failed to save signature.', 'error');
  }
};

const deleteSignature = async (id) => {
  const result = await Swal.fire({
    title: 'Are you sure?',
    text: 'This will delete the signature.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!',
  });

  if (result.isConfirmed) {
    try {
      await axios.delete(`/api/signatures/${id}`, {
        headers: { Authorization: `Bearer ${getAuthToken()}` },
      });
      Swal.fire('Deleted!', 'Signature deleted.', 'success');
      fetchSignatures();
    } catch (err) {
      console.error('Delete error:', err);
      Swal.fire('Error', 'Could not delete signature.', 'error');
    }
  }
};

const initTinyMCE = () => {
  destroyTinyMCE();
  tinymce.init({
  selector: '#signature-editor',
  menubar: false,
  plugins: 'link lists image media',
  toolbar: 'undo redo | bold italic | bullist numlist | link image media',
  file_picker_types: 'file image media',
  file_picker_callback: (callback, value, meta) => {
    const input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('accept', meta.filetype === 'image' ? 'image/*' : '*/*');

    input.onchange = async function () {
      const file = this.files[0];
      const formData = new FormData();
      formData.append('file', file);

      try {
        const response = await axios.post('/api/upload', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
            Authorization: `Bearer ${getAuthToken()}`,
          },
        });

        const fileUrl = response.data.url;

        callback(fileUrl, { title: file.name });
      } catch (error) {
        console.error('Upload error:', error);
        Swal.fire('Upload Failed', 'Could not upload the file.', 'error');
      }
    };

    input.click();
  }
});

};

const destroyTinyMCE = () => {
  const existingEditor = tinymce.get('signature-editor');
  if (existingEditor) {
    existingEditor.destroy();
  }
};

onMounted(() => {
  fetchSignatures();
  initTinyMCE();
});

onBeforeUnmount(() => {
  destroyTinyMCE();
});
</script>


<style scoped>
/* Use same styles as your SMS template component */
.main {
  padding: 1rem;
  inline-size: 100%;
}

.heading-section {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.search {
  margin-block: 1rem;
  margin-inline: 0;
}

.table-signature {
  border-collapse: collapse;
  inline-size: 100%;
}

.table-signature th,
.table-signature td {
  padding: 0.75rem;
  border-block-end: 1px solid #ddd;
}

.modal-overlay {
  position: fixed;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: rgba(0, 0, 0, 50%);
  inset: 0;
}

.modal {
  padding: 2rem;
  border-radius: 8px;
  background: #fff;
  inline-size: 400px;
}

.close-btn {
  border: none;
  background: none;
  float: inline-end;
  font-size: 1.5rem;
}

.form-group {
  margin-block-end: 1rem;
}

.submit-btn {
  border: none;
  background: #007bff;
  color: white;
  cursor: pointer;
  padding-block: 0.5rem;
  padding-inline: 1rem;
}
</style>
