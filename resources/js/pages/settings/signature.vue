<template>
  <div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Signature Templates</h2>
      <div>
        <button
          v-if="currentView === 'folders' && availableFolderOptions.length > 0"
          @click="showFolderModal = true"
          class="btn btn-success me-2"
        >
          <i class="fas fa-plus me-1"></i> Create Folder
        </button>
        <button @click="showTemplateModal = true" class="btn btn-success">
          <i class="fas fa-file-signature me-1"></i> Add Signature Template
        </button>
      </div>
    </div>

    <!-- Search Input -->
    <input v-model="search" type="text" class="form-control mb-3" placeholder="Search folders..." />

    <!-- Folder View -->
    <div v-if="currentView === 'folders'" class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="table-light">
          <tr>
            <th>Name</th>
            <th>Templates</th>
            <th>Created By</th>
            <!-- <th>Action</th> -->
          </tr>
        </thead>
        <tbody>
          <tr v-for="folder in paginatedFolders" :key="folder.id">
            <td @click="openFolder(folder.id)" class="cursor-pointer">
              <i class="fas fa-folder text-warning me-2"></i>{{ folder.name }}
            </td>
            <td>{{ folder.template_count }}</td>
            <td>{{ folder.created_by_name }}</td>
            <!-- <td>
              <button class="btn btn-link text-success p-0" @click.stop="openAddTemplateInFolder(folder.id)">
                Add Signature Template
              </button>
            </td> -->
          </tr>
        </tbody>
      </table>

      <!-- Pagination Controls -->
      <div v-if="filteredFolders.length > perPage" class="d-flex justify-content-between align-items-center mt-2">
        <button class="btn btn-sm btn-outline-secondary" @click="prevPage" :disabled="currentPage === 1">
          Previous
        </button>
        <span>Page {{ currentPage }} of {{ totalPages }}</span>
        <button class="btn btn-sm btn-outline-secondary" @click="nextPage" :disabled="currentPage === totalPages">
          Next
        </button>
      </div>
    </div>

    <!-- Folder Detail View -->
    <div v-else-if="currentView === 'folder-view'">
      <button @click="currentView = 'folders'" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left me-1"></i> Back to Folders
      </button>

      <h3 class="d-flex align-items-center mb-3">
        <i class="fas fa-folder text-warning me-2"></i> {{ activeFolder.name }}
      </h3>

      <ul class="list-group">
        <li v-if="folderTemplates.length === 0" class="list-group-item text-muted fst-italic">
          No signature templates in this folder.
        </li>

        <table v-if="folderTemplates.length > 0" class="table-auto w-full border text-left">
          <thead class="bg-light">
            <tr>
              <th class="p-2"><input type="checkbox" /></th>
              <th class="p-2">Title</th>
              <th class="p-2">Created By</th>
              <th class="p-2">Content</th>
              <th class="p-2">Type</th>
              <th class="p-2">Created At</th>
              <th class="p-2">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="template in folderTemplates" :key="template.id" class="border-b hover:bg-gray-50">
              <td class="p-2"><input type="checkbox" /></td>
              <td class="p-2">{{ template.title }}</td>
              <td class="p-2">{{ template.created_by }}</td>
              <td class="p-2" v-html="template.content"></td>
              <td class="p-2">{{ template.type }}</td>

              <td class="p-2">{{ new Date(template.created_at).toLocaleString() }}</td>
              <td class="p-2 d-flex gap-2">
                <button class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></button>
                <button class="btn btn-sm btn-warning" @click="startEdit(template)">Edit</button>
                <button @click="deleteTemplate(template.id)" class="btn btn-sm btn-danger">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </ul>
    </div>

    <!-- Create Folder Modal -->
    <div class="modal fade show d-block" tabindex="-1" v-if="showFolderModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Create Folder</h5>
            <button type="button" class="btn-close" @click="showFolderModal = false"></button>
          </div>
          <div class="modal-body">
            <label class="form-label">* Folder Type</label>
            <select v-model="newFolder.name" class="form-select">
              <option value="">Select Folder</option>
              <option v-for="type in availableFolderOptions" :key="type" :value="type">{{ type }}</option>
            </select>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="showFolderModal = false">Cancel</button>
            <button class="btn btn-danger" :disabled="!newFolder.name" @click="createFolder">Save</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Signature Template Modal -->
    <div class="modal fade show d-block" tabindex="-1" v-if="showTemplateModal">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Signature Template</h5>
            <button type="button" class="btn-close" @click="showTemplateModal = false"></button>
          </div>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">* Template Name</label>
                  <input
                    v-model="newTemplate.title"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': errors.title }"
                    placeholder="Template Name"
                  />
                  <div class="invalid-feedback" v-if="errors.title">{{ errors.title }}</div>
              </div>
              
              <div class="col-md-6">
                <label class="form-label">* Folder</label>
                <div class="input-group">
                  <select
                    v-model="newTemplate.folder_id"
                    class="form-select"
                    :class="{ 'is-invalid': errors.folder_id }"
                  >
                    <option value="">Choose Folder</option>
                    <option v-for="folder in folders" :key="folder.id" :value="folder.id">
                      {{ folder.name }}
                    </option>
                  </select>
                  <div class="invalid-feedback" v-if="errors.folder_id">{{ errors.folder_id }}</div>

                  <button class="btn btn-outline-success" type="button" @click="showFolderModal = true">+</button>
                </div>
              </div>

            <div class="col-12">
              <label class="form-label">* Body</label>
              <Editor
                :init="editorInit"
                :modelValue="newTemplate.content"
                @update:modelValue="newTemplate.content = $event"
              />
              <div class="text-danger mt-1" v-if="errors.content">{{ errors.content }}</div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="showTemplateModal = false">Cancel</button>
            <button class="btn btn-danger" @click="saveTemplate">Save Signature Template</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Signature Template Modal -->
<!-- Edit Signature Template Modal -->
<div class="modal fade show d-block" tabindex="-1" v-if="isEditing">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Signature Template</h5>
        <button type="button" class="btn-close" @click="cancelEdit"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <!-- Template Name -->
          <div class="col-md-6">
            <label class="form-label">* Template Name</label>
            <input
              v-model="editForm.title"
              type="text"
              class="form-control"
              :class="{ 'is-invalid': errors.title }"
              placeholder="Template Name"
            />
            <div class="invalid-feedback" v-if="errors.title">{{ errors.title }}</div>
          </div>

          <!-- Folder -->
          <div class="col-md-6">
            <label class="form-label">* Folder</label>
            <div class="form-control" :class="{ 'is-invalid': errors.folder_id }" readonly>
              {{ folders.find(f => f.id === editForm.folder_id)?.name || 'No Folder' }}
            </div>
            <div class="invalid-feedback" v-if="errors.folder_id">{{ errors.folder_id }}</div>
          </div>

          <!-- Body -->
          <div class="col-12">
            <label class="form-label">* Body</label>
            <Editor
              :init="editorInit"
              :modelValue="editForm.content"
              @update:modelValue="editForm.content = $event"
            />
            <div class="text-danger mt-1" v-if="errors.content">{{ errors.content }}</div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" @click="cancelEdit">Cancel</button>
        <button class="btn btn-danger" @click="updateTemplate">Update Signature Template</button>
      </div>

    </div>
  </div>
</div>


  </div>
</template>

<script setup>
import Editor from '@tinymce/tinymce-vue'
import axios from 'axios'
import 'bootstrap'
import 'bootstrap/dist/css/bootstrap.min.css'
import Swal from 'sweetalert2'
import { computed, onMounted, ref } from 'vue'

// State & Reactive Variables
const currentView = ref('folders')
const showFolderModal = ref(false)
const showTemplateModal = ref(false)
const folders = ref([])
const folderTemplates = ref([])
const activeFolder = ref(null)
const openedFolderId = ref(null)
const search = ref('')
const currentPage = ref(1)
const perPage = 10



const errors = ref({});

const newFolder = ref({ name: '' })
const newTemplate = ref({
  title: '',
  folder_id: '',
  subject: '',
  content: '',
  attachment: null
})

const editForm = ref({
  title: '',
  folder_id: '',
  subject: '',
  content: '',
  attachment: null,
  type: 'signature'
})

const isEditing = ref(false)
const editingTemplateId = ref(null)

const mergeFields = [
  'first_name', 'last_name', 'email', 'phone',
  'job_title', 'department', 'company', 'website'
]



// Token Setup for Axios
const authToken = localStorage.getItem('auth_token')
if (authToken) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${authToken}`
}
function focusFirstError() {
  const firstErrorKey = Object.keys(errors.value)[0];
  const el = document.querySelector(`[name="${firstErrorKey}"]`);
  if (el) el.focus();
}


// Image Upload Handler (must be before editorInit)
const handleImageUpload = (blobInfo) => {
  return new Promise((resolve, reject) => {
    const formData = new FormData()
    formData.append('file', blobInfo.blob(), blobInfo.filename())
    axios.post('/api/upload-image', formData)
      .then(res => res.data.url ? resolve(res.data.url) : reject('No URL returned'))
      .catch(() => reject('Upload failed'))
  })
}

// Editor Configuration
const editorInit = {
  height: 300,
  menubar: false,
  plugins: 'link lists image code',
  toolbar:
    'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent | image link code | mergefields',
  branding: false,
  resize: true,
  automatic_uploads: true,
  images_upload_handler: handleImageUpload,
  setup(editor) {
    editor.ui.registry.addMenuButton('mergefields', {
      text: 'Merge Fields',
      fetch(callback) {
        const items = mergeFields.map(field => ({
          type: 'menuitem',
          text: field,
          onAction: () => editor.insertContent(`{{${field}}}`)
        }))
        callback(items)
      }
    })
  }
}

// Fetch folders from API
const fetchFolders = async () => {
  try {
    const res = await axios.get('/api/signature-folders?type=signature')
    folders.value = res.data
  } catch (err) {
    Swal.fire('Error', 'Failed to load signature folders', 'error')
  }
}

// Create Folder
const createFolder = async () => {
  const exists = folders.value.some(f => f.name.toLowerCase() === newFolder.value.name.toLowerCase())
  if (exists) return Swal.fire('Warning', 'Folder already exists.', 'warning')

  try {
    const res = await axios.post('/api/signature-folders', {
      name: newFolder.value.name,
      type: 'signature'
    })
    folders.value.push(res.data)
    newFolder.value.name = ''
    showFolderModal.value = false
    Swal.fire('Success', 'Folder created.', 'success')
  } catch {
    Swal.fire('Error', 'Failed to create folder.', 'error')
  }
}

// Open Folder
const openFolder = async (id) => {
  try {
    const res = await axios.get(`/api/signature-folders/${id}/templates`)
    folderTemplates.value = res.data
    activeFolder.value = folders.value.find(f => f.id === id)
    openedFolderId.value = id
    currentView.value = 'folder-view'
  } catch {
    Swal.fire('Error', 'Cannot load folder templates.', 'error')
  }
}

function validateTemplateForm(form) {
  errors.value = {};
  let isValid = true;

  if (!form.title || form.title.trim() === '') {
    errors.value.title = 'Template name is required.';
    isValid = false;
  }

  if (!form.folder_id) {
    errors.value.folder_id = 'Please choose a folder.';
    isValid = false;
  }

  if (!form.content || form.content.trim() === '') {
    errors.value.content = 'Body content is required.';
    isValid = false;
  }

  return isValid;
}

// Save Template
const saveTemplate = async () => {
  if (!validateTemplateForm(newTemplate.value)) return;

  try {
    const selectedFolder = folders.value.find(f => f.id === parseInt(newTemplate.value.folder_id));
    const folderType = selectedFolder ? selectedFolder.name.toLowerCase() : null;

    if (!folderType) {
      Swal.fire('Error', 'Invalid folder selected.', 'error');
      return;
    }

    const formData = new FormData();
    formData.append('title', newTemplate.value.title);
    formData.append('subject', newTemplate.value.subject || '');
    formData.append('content', newTemplate.value.content);
    formData.append('type', folderType);
    formData.append('folder_id', newTemplate.value.folder_id);
    if (newTemplate.value.attachment) {
      formData.append('attachment', newTemplate.value.attachment);
    }

    await axios.post('/api/signature-templates', formData);

    newTemplate.value = {
      title: '',
      folder_id: '',
      subject: '',
      content: '',
      attachment: null
    };

    showTemplateModal.value = false;

    currentView.value === 'folder-view' 
      ? await openFolder(openedFolderId.value)
      : await fetchFolders();

    Swal.fire('Success', 'Template saved.', 'success');
  } catch (error) {
    console.error(error);
    Swal.fire('Error', 'Failed to save template.', 'error');
  }
};


// Start editing template: populate editForm and open modal
const startEdit = (template) => {
  editingTemplateId.value = template.id;
  editForm.value = {
    title: template.title,
    folder_id: template.folder_id,
    subject: template.subject,
    content: template.content,
    attachment: null,
    type: template.type
  };
  isEditing.value = true;
}

// Cancel editing
const cancelEdit = () => {
  isEditing.value = false;
  errors.value = {};
  editingTemplateId.value = null;
  editForm.value = {
    title: '',
    folder_id: '',
    subject: '',
    content: '',
    attachment: null,
    type: 'email'
  };
}

// Update template API call
const updateTemplate = async () => {
  if (!validateTemplateForm(editForm.value)) return;

  try {
    const formData = new FormData();
    formData.append('title', editForm.value.title);
    formData.append('subject', editForm.value.subject || '');
    formData.append('content', editForm.value.content);
    formData.append('folder_id', editForm.value.folder_id);
    formData.append('type', editForm.value.type);
    if (editForm.value.attachment) {
      formData.append('attachment', editForm.value.attachment);
    }

    await axios.post(`/api/signature-templates/${editingTemplateId.value}`, formData, {
      headers: { 'X-HTTP-Method-Override': 'PUT' }
    });

    Swal.fire('Success', 'Template updated.', 'success');
    isEditing.value = false;

    currentView.value === 'folder-view'
      ? await openFolder(openedFolderId.value)
      : await fetchFolders();

  } catch (error) {
    console.error(error);
    Swal.fire('Error', 'Failed to update template.', 'error');
  }
};


// Delete template API call
const deleteTemplate = async (id) => {
  const result = await Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!'
  });

  if (result.isConfirmed) {
    try {
      await axios.delete(`/api/signature-templates/${id}`);

      Swal.fire('Deleted!', 'Template has been deleted.', 'success');

      // Refresh the templates list
      if (currentView.value === 'folder-view') {
        await openFolder(openedFolderId.value);
      } else {
        await fetchFolders();
      }

    } catch (error) {
      console.error(error);
      Swal.fire('Error', 'Failed to delete template.', 'error');
    }
  }
}

// Subject Merge Field Insertion
const insertMergeFieldToSubject = (field) => {
  if (field) newTemplate.value.subject += ` {{${field}}} `
}

// Pagination Logic
const filteredFolders = computed(() =>
  folders.value.filter(f => f.name.toLowerCase().includes(search.value.toLowerCase()))
)

const paginatedFolders = computed(() => {
  const start = (currentPage.value - 1) * perPage
  return filteredFolders.value.slice(start, start + perPage)
})


const folderTypes = ['SMS', 'Email', 'WhatsApp']

const availableFolderOptions = computed(() => {
  const createdNames = folders.value.map(f => f.name.toLowerCase())
  return folderTypes.filter(type => !createdNames.includes(type.toLowerCase()))
})

const totalPages = computed(() => Math.ceil(filteredFolders.value.length / perPage))
const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++ }
const prevPage = () => { if (currentPage.value > 1) currentPage.value-- }

// Load data on mount
onMounted(fetchFolders)
</script>


<style scoped>
.modal {
  z-index: 1000;
}
</style>
