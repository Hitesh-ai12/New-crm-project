<template>
  <div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>SMS Templates</h2>
      <div>
        <button
          v-if="currentView === 'folders'"
          @click="showFolderModal = true"
          class="btn btn-success me-2"
        >
          <i class="fas fa-plus me-1"></i> Create Folder
        </button>
        <button @click="showTemplateModal = true" class="btn btn-success">
          <i class="fas fa-sms me-1"></i> Add SMS Template
        </button>
      </div>
    </div>

    <!-- Search -->
    <input
      v-model="search"
      type="text"
      class="form-control mb-3"
      placeholder="Search by folder name"
    />

    <!-- Folder List View -->
    <div v-if="currentView === 'folders'" class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="table-light">
          <tr>
            <th>Name</th>
            <th>Templates</th>
            <th>Created By</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="folder in paginatedFolders" :key="folder.id">
            <td @click="openFolder(folder.id)" class="cursor-pointer">
              <i class="fas fa-folder text-warning me-2"></i>{{ folder.name }}
            </td>
            <td>{{ folder.template_count }}</td>
            <td>{{ folder.created_by_name }}</td>
            <td>
              <button class="btn btn-link text-success p-0" @click.stop="openAddTemplateInFolder(folder.id)">
                Add SMS Template
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination Controls -->
      <div v-if="filteredFolders.length > perPage" class="d-flex justify-content-between align-items-center mt-2">
        <button class="btn btn-sm btn-outline-secondary" @click="prevPage" :disabled="currentPage === 1">Previous</button>
        <span>Page {{ currentPage }} of {{ totalPages }}</span>
        <button class="btn btn-sm btn-outline-secondary" @click="nextPage" :disabled="currentPage === totalPages">Next</button>
      </div>
    </div>


    <!-- Single Folder View -->
    <div v-else-if="currentView === 'folder-view'">
      <!-- Show only when inside a folder -->
      <button
        v-if="currentView === 'folder-view'"
        @click="currentView = 'folders'"
        class="btn btn-secondary mb-3"
      >
        <i class="fas fa-arrow-left me-1"></i> Back to Folders
      </button>
      
      <h3 class="d-flex align-items-center mb-3">
        <i class="fas fa-folder text-warning me-2"></i> {{ activeFolder.name }}
      </h3>
      <ul class="list-group">
        <li v-if="folderTemplates.length === 0" class="list-group-item text-muted fst-italic">
          No templates in this folder.
        </li>

        <table class="table-auto w-full border text-left" v-if="folderTemplates.length > 0">
          <thead class="bg-gray-100">
            <tr>
              <th class="p-2"><input type="checkbox" /></th>
              <th class="p-2">Title</th>
              <th class="p-2">Created By</th>
              <!-- <th class="p-2">Subject</th> -->
              <th class="p-2">Content</th>
              <th class="p-2">Type</th>
              <th class="p-2">Attachment</th>
              <th class="p-2">Created At</th>
              <th class="p-2">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="template in folderTemplates" :key="template.id" class="border-b hover:bg-gray-50">
              <td class="p-2"><input type="checkbox" /></td>
              <td class="p-2">{{ template.title }}</td>
              <td class="p-2">{{ template.created_by }}</td>
              <!-- <td class="p-2">{{ template.subject }}</td> -->
              <td class="p-2" v-html="template.content"></td>
              <td class="p-2">{{ template.type }}</td>
              <td class="p-2">
                <a
                  v-if="template.attachment_path"
                  :href="`/storage/${template.attachment_path}`"
                  target="_blank"
                  class="text-blue-600 underline"
                >
                  View Attachment
                </a>
                <span v-else>No Attachment</span>
              </td>
              <td class="p-2">{{ new Date(template.created_at).toLocaleString() }}</td>
              <td class="p-2 flex gap-2">
                <button class="bg-red-600 text-white px-2 py-1 rounded"><i class="fas fa-eye"></i></button>
                <button class="btn btn-sm btn-warning" @click="startEdit(template)">Edit</button>
                <button @click="deleteTemplate(template.id)" class="bg-red-600 text-white px-2 py-1 rounded"><i class="fas fa-trash"></i></button>

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
            <label class="form-label">* Folder Name</label>
            <input v-model="newFolder.name" type="text" class="form-control" placeholder="Folder Name" />
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="showFolderModal = false">Cancel</button>
            <button class="btn btn-danger" @click="createFolder">Save</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add SMS Template Modal -->
    <div class="modal fade show d-block" tabindex="-1" v-if="showTemplateModal">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add SMS Template</h5>
            <button type="button" class="btn-close" @click="showTemplateModal = false"></button>
          </div>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">* Template Name</label>
                <input v-model="newTemplate.title" type="text" class="form-control" placeholder="Template Name" />
              </div>
              <div class="col-md-6">
                <label class="form-label">* Folder</label>
                <div class="input-group">
                  <select v-model="newTemplate.folder_id" class="form-select">
                    <option value="">Choose Folder</option>
                    <option v-for="folder in folders" :key="folder.id" :value="folder.id">
                      {{ folder.name }}
                    </option>
                  </select>
                  <button class="btn btn-outline-success" type="button" @click="showFolderModal = true">+</button>
                </div>
              </div>

              <div class="col-12">
                <label class="form-label">Body</label>
                <Editor
                  :init="editorInit"
                  :modelValue="newTemplate.content"
                  @update:modelValue="newTemplate.content = $event"
                />
              </div>
              <div class="col-12">
                <label class="form-label">Attachment</label>
                <input type="file" class="form-control" @change="handleFileUpload" />
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="showTemplateModal = false">Cancel</button>
            <button class="btn btn-danger" @click="saveTemplate">Save SMS Template</button>
          </div>
        </div>
      </div>
    </div>

  <!-- Edit SMS Template Modal -->
  <div class="modal fade show d-block" tabindex="-1" v-if="isEditing">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit SMS Template</h5>
          <button type="button" class="btn-close" @click="cancelEdit"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">* Template Name</label>
              <input v-model="editForm.title" type="text" class="form-control" placeholder="Template Name" />
            </div>
            <div class="col-md-6">
              <label class="form-label">* Folder</label>
              <div class="input-group">
                <select v-model="editForm.folder_id" class="form-select">
                  <option value="">Choose Folder</option>
                  <option v-for="folder in folders" :key="folder.id" :value="folder.id">
                    {{ folder.name }}
                  </option>
                </select>
                <button class="btn btn-outline-success" type="button" @click="showFolderModal = true">+</button>
              </div>
            </div>
            <div class="col-12">
              <label class="form-label">Body</label>
              <Editor
                :init="editorInit"
                :modelValue="editForm.content"
                @update:modelValue="editForm.content = $event"
              />
            </div>
            <div class="col-12">
              <label class="form-label">Attachment</label>
              <input type="file" class="form-control" @change="handleEditFileUpload" />
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" @click="cancelEdit">Cancel</button>
          <button class="btn btn-danger" @click="updateTemplate">Update Template</button>
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

    // View State
    const currentView = ref('folders') 
    const activeFolder = ref(null)

    // Modals
    const showFolderModal = ref(false)
    const showTemplateModal = ref(false)

    // Data
    const folders = ref([])
    const folderTemplates = ref([])
    const openedFolderId = ref(null)
    const search = ref('')

    // Form Models
    const newFolder = ref({ name: '' })
    const newTemplate = ref({
      title: '',
      folder_id: '',
      subject: '',
      content: '',
      attachment: null
    })

    const handleImageUpload = (blobInfo) => {
      return new Promise((resolve, reject) => {
        const file = blobInfo.blob();

        if (file.size > 51200) {
          reject('Image size must be less than 50KB');
          return;
        }

        const formData = new FormData();
        formData.append('file', file, blobInfo.filename());

        axios.post('/api/upload-image', formData)
          .then(response => {
            if (response.data && response.data.url) {
              resolve(response.data.url); 
            } else {
              reject('Upload failed: Invalid response');
            }
          })
          .catch(() => {
            reject('Image upload failed');
          });
      });
    };

    // TinyMCE Config
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
              onAction: () => {
                editor.insertContent(`{{${field}}}`);
              }
            }));
            callback(items);
          }
        });
      }
    };

    // Auth Token
    const authToken = localStorage.getItem('auth_token')
    if (authToken) {
      axios.defaults.headers.common['Authorization'] = `Bearer ${authToken}`
    }

    // Computed Search Filter
    const filteredFolders = computed(() =>
      folders.value.filter(folder =>
        folder.name.toLowerCase().includes(search.value.toLowerCase())
      )
    )

    // Fetch folders
    const fetchFolders = async () => {
        try {
          const res = await axios.get('/api/folders?type=sms') 
          folders.value = res.data
        } catch (err) {
          Swal.fire('Error', 'Failed to load folders', 'error')
        }
      }


    // Open Folder and Load Templates
    const openFolder = async (folderId) => {
      try {
        const res = await axios.get(`/api/folders/${folderId}/templates`, {
          params: {
            type: 'sms' // 👈 This sends ?type=sms in the request
          }
        });

        folderTemplates.value = res.data;
        const folder = folders.value.find(f => f.id === folderId);
        activeFolder.value = folder;
        openedFolderId.value = folderId;
        currentView.value = 'folder-view';
      } catch (err) {
        Swal.fire('Error', 'Could not load templates for this folder', 'error');
      }
    };


    // Create Folder
    const createFolder = async () => {
    if (currentView.value !== 'folders') {
      Swal.fire('Warning', 'You can only create folders from the main view.', 'warning')
      return
    }

    const duplicate = folders.value.some(folder =>
      folder.name.trim().toLowerCase() === newFolder.value.name.trim().toLowerCase()
    )

    if (duplicate) {
      Swal.fire('Warning', 'A folder with this name already exists.', 'warning')
      return
    }

    try {
        const res = await axios.post('/api/folders', {
          name: newFolder.value.name,
          type: 'sms' // 👈 Include type
        })
        folders.value.push(res.data)
        newFolder.value.name = ''
        showFolderModal.value = false
        Swal.fire('Success', 'Folder created successfully.', 'success')
      } catch (err) {
        Swal.fire('Error', 'Could not create folder', 'error')
      }
    }


  // Save sms Template
  const saveTemplate = async () => {
  try {
    const formData = new FormData()
    formData.append('title', newTemplate.value.title)
    formData.append('folder_id', newTemplate.value.folder_id)
    formData.append('subject', newTemplate.value.subject)
    formData.append('content', newTemplate.value.content)
    formData.append('type', 'sms')

    if (newTemplate.value.attachment) {
      formData.append('attachment', newTemplate.value.attachment)
    }

    await axios.post('/api/templates', formData)

    newTemplate.value = {
      title: '',
      folder_id: '',
      subject: '',
      content: '',
      attachment: null
    }

    showTemplateModal.value = false

    // 🔄 Refresh the folder templates if the template was created inside an open folder
    if (currentView.value === 'folder-view' && openedFolderId.value) {
      await openFolder(openedFolderId.value)
    } else {
      await fetchFolders()
    }

    Swal.fire('Success', 'SMS template saved.', 'success')
  } catch (err) {
    Swal.fire('Error', 'Could not save template', 'error')
  }
}
    const currentPage = ref(1)
    const perPage = 10

    const totalPages = computed(() => {
      return Math.ceil(filteredFolders.value.length / perPage)
    })

    const paginatedFolders = computed(() => {
      const start = (currentPage.value - 1) * perPage
      const end = start + perPage
      return filteredFolders.value.slice(start, end)
    })

    const nextPage = () => {
      if (currentPage.value < totalPages.value) {
        currentPage.value++
      }
    }

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const isEditing = ref(false)
const editingTemplateId = ref(null)
const editForm = ref({
  title: '',
  subject: '',
  content: '',
  type: 'sms',
  attachment: null
})

const startEdit = (template) => {
  isEditing.value = true
  editingTemplateId.value = template.id
  editForm.value = {
    title: template.title,
    subject: template.subject,
    content: template.content,
    folder_id: template.folder_id,
    type: template.type,
    attachment: null
  }
}


const updateTemplate = async () => {
  try {
    const formData = new FormData();
    formData.append('title', editForm.value.title);
    formData.append('subject', editForm.value.subject);
    formData.append('content', editForm.value.content);
    formData.append('type', 'sms');
    formData.append('folder_id', editForm.value.folder_id);

    if (editForm.value.attachment) {
      formData.append('attachment', editForm.value.attachment);
    }

    // ✅ method spoofing
    await axios.post(`/api/email-templates/${editingTemplateId.value}?_method=PUT`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    isEditing.value = false;
    editingTemplateId.value = null;
    await openFolder(openedFolderId.value);
    Swal.fire('Updated!', 'Template updated successfully.', 'success');
  } catch (err) {
    console.error(err);
    Swal.fire('Error!', 'Could not update template.', 'error');
  }
};


  const cancelEdit = () => {
    isEditing.value = false
    editingTemplateId.value = null
  }

  const deleteTemplate = async (id) => {
    const result = await Swal.fire({
      title: 'Are you sure?',
      text: 'This will permanently delete the template.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!'
    })

    if (result.isConfirmed) {
      try {
        await axios.delete(`/api/templates/${id}`)
        await openFolder(openedFolderId.value)
        Swal.fire('Deleted!', 'Template has been deleted.', 'success')
      } catch (err) {
        Swal.fire('Error!', 'Failed to delete template.', 'error')
      }
    }
  }


const mergeFields = [
  'first_name', 'last_name', 'email', 'phone',
  'city', 'zip_code', 'province', 'street',
  'house_number', 'tag', 'stage', 'created_at'
]

const insertMergeFieldToSubject = (field) => {
  if (!field) return
  newTemplate.value.subject += ` {{${field}}} `
}

const insertMergeFieldToEditor = (field) => {
  if (!field) return
  tinymce.activeEditor.execCommand('mceInsertContent', false, `{{${field}}}`)
}

const handleEditFileUpload = (event) => {
  editForm.value.attachment = event.target.files[0]
}

// File Input Handler
const handleFileUpload = (event) => {
  newTemplate.value.attachment = event.target.files[0]
}

// Open Modal to Add Template to Folder
const openAddTemplateInFolder = (folderId) => {
  newTemplate.value.folder_id = folderId
  showTemplateModal.value = true
}

onMounted(fetchFolders)
</script>



<style scoped>
.modal {
  z-index: 1000;
}
</style>
