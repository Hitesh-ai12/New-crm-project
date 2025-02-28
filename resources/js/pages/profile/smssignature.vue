<template>
  <div class="signature-container">
    <div class="signature-section">
      <div class="signature-list">
        <h3 class="signature_heading">Signatures <button @click="openAddModal">+</button></h3>
        <ul>
          <li v-for="(signature, index) in sortedSignatures" :key="signature.id" @click="selectSignature(signature)">
            <div class="show_defaultSignature">
              <span class="signature-name">{{ signature.name }}</span>
              <span v-if="signature.is_default" class="default-tag">Default</span>
              <button @click.stop="confirmDeleteSignature(signature.id)">🗑</button>
            </div>
          </li>
        </ul>

        <div class="dropdown_signature">
          <label>Set Default Signature</label>
          <select v-model="defaultSignature" @change="updateDefaultSignature">
            <option v-for="signature in signatures" :key="signature.id" :value="signature.id">
              {{ signature.name }}
            </option>
          </select>
        </div>
      </div>

      <div class="signature-editor">
        <h3>Edit Signature</h3>
        <input v-model="newSignature.name" placeholder="Signature Name" />
        <label>
          Set as default
          <input type="checkbox" v-model="newSignature.is_default" />
        </label>
        <textarea id="signature-editor"></textarea>

        <input type="file" @change="handleImageUpload" accept="image/*" />
        <img v-if="imagePreview" :src="imagePreview" alt="Signature Preview" class="preview-image" />

        <button @click="saveSignature">{{ newSignature.id ? 'Update' : 'Create' }} Signature</button>
      </div>
    </div>

    <div v-if="toastMessage" :class="['toast', toastType, { show: toastMessage }]">
      {{ toastMessage }}
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import tinymce from 'tinymce';

export default {
  data() {
    return {
      signatures: [],
      newSignature: {
        name: '',
        content: '',
        is_default: false,
        id: null, 
        image: null,
      },
      defaultSignature: null,
      toastMessage: '',
      toastType: '', 
      imagePreview: null,
    };
  },
  computed: {
    sortedSignatures() {
      return this.signatures.slice().sort((a, b) => b.is_default - a.is_default);
    }
  },
  mounted() {
    this.fetchSignatures();
    this.initTinyMCE();
  },
  activated() {
    this.reinitializeTinyMCE();
  },
  methods: {
    async fetchSignatures() {
      try {
        const response = await axios.get('/api/signatures');
        this.signatures = response.data;

        if (this.signatures.length > 0) {
          this.defaultSignature = this.signatures.find(sig => sig.is_default)?.id || this.signatures[0].id;
        }
      } catch (error) {
        console.error('Error fetching signatures:', error);
        this.showToast('Error fetching signatures.', 'error');
      }
    },

    async saveSignature() {
      const content = tinymce.get("signature-editor").getContent();
      this.newSignature.content = content;
      
      const formData = new FormData();
      formData.append('name', this.newSignature.name);
      formData.append('content', this.newSignature.content);
      formData.append('is_default', this.newSignature.is_default);
      if (this.newSignature.image) {
        formData.append('image', this.newSignature.image);
      }

      try {
        if (this.newSignature.id) {
          await axios.post(`/api/signatures/${this.newSignature.id}`, formData);
          this.showToast('Signature updated successfully!', 'success');
        } else {
          await axios.post('/api/signatures', formData);
          this.showToast('Signature added successfully!', 'success');
        }
        await this.fetchSignatures();
        this.resetForm(); 
      } catch (error) {
        console.error('Error saving signature:', error);
        this.showToast('Error saving signature.', 'error');
      }
    },

    handleImageUpload(event) {
      const file = event.target.files[0];
      if (file) {
        this.newSignature.image = file;
        this.imagePreview = URL.createObjectURL(file);
      }
    },

    selectSignature(signature) {
      this.newSignature.id = signature.id;
      this.newSignature.name = signature.name;
      this.newSignature.is_default = signature.is_default;
      this.imagePreview = signature.image || null;
      tinymce.get("signature-editor").setContent(signature.content);
    },

    confirmDeleteSignature(id) {
      Swal.fire({
        title: 'Are you sure?',
        text: "Do you really want to delete this signature?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
      }).then(async (result) => {
        if (result.isConfirmed) {
          this.deleteSignature(id);
        }
      });
    },

    async deleteSignature(id) {
      try {
        await axios.delete(`/api/signatures/${id}`);
        await this.fetchSignatures(); 
        this.showToast('Signature deleted successfully!', 'success');
      } catch (error) {
        console.error('Error deleting signature:', error);
        this.showToast('Error deleting signature.', 'error');
      }
    },

    showToast(message, type) {
      this.toastMessage = message;
      this.toastType = type;
      
      setTimeout(() => {
        this.toastMessage = ''; 
      }, 3000);
    },

    initTinyMCE() {
      tinymce.init({
        selector: "#signature-editor",
        menubar: false,
        plugins: "lists link image",
        toolbar: "bold italic underline | alignleft aligncenter alignright | bullist numlist | link image",
        setup: (editor) => {
          editor.on('change', () => {
            this.newSignature.content = editor.getContent();
          });
        },
      });
    },

    reinitializeTinyMCE() {
      if (tinymce.get('signature-editor')) {
        tinymce.get('signature-editor').remove();
      }
      this.initTinyMCE();
    },

    resetForm() {
      this.newSignature = { name: '', content: '', is_default: false, id: null, image: null };
      this.imagePreview = null;
      tinymce.get("signature-editor").setContent(''); 
    },
  },
};
</script>


<style scoped>
.signature-container {
  display: flex;
  flex-direction: column;
  padding: 20px;
}

.signature-section {
  display: flex;
  justify-content: space-between;
}

.signature-list {
  padding: 10px;
  border: 1px solid #110f0f;
  border-radius: 5px;
  background-color: #f9f9f9;
  border-inline-end: 1px solid #110f0f;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 10%);
  inline-size: 30%;
}

.signature-editor {
  inline-size: 65%;
}

select {
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 5px;
  font-size: 16px;
  min-inline-size: 200px;
}

.singnature_heading {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border: 2px solid #ccc;
  border-radius: 5px;
  background-color: #f9f9f9;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 10%);
  padding-block: 10px;
  padding-inline: 15px;
}

.singnature_heading button {
  border: none;
  border-radius: 3px;
  color: rgb(14, 12, 12);
  cursor: pointer;
  padding-block: 5px;
  padding-inline: 10px;
}

.singnature_heading button:hover {
  color: #763be6;
}

.signature-list li {
  border: 2px solid #ccc;
  border-radius: 5px;
  background-color: #f9f9f9;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 10%);
  list-style-type: none;
  margin-block: 5px;
  margin-inline: 0;
  padding-block: 10px;
  padding-inline: 15px;
}

.signature-list li:hover {
  background-color: #f0f0f0;
}

.toast {
  position: fixed;
  z-index: 100;
  border-radius: 5px;
  background-color: rgba(0, 0, 0, 70%);
  color: #fff;
  inset-block-start: 10px;
  inset-inline-end: 10px;
  padding-block: 10px;
  padding-inline: 20px;
}

.toast.show {
  opacity: 1;
  visibility: visible;
}

.toast.success {
  background-color: #28a745;
}

.toast.error {
  background-color: #dc3545;
}

.default-tag {
  border-radius: 5px;
  background-color: #ffc107;
  padding-block: 2px;
  padding-inline: 5px;
}
</style>
