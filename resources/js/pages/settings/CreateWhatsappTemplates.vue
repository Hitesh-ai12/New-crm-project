
<template>
  <div class="container py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>WhatsApp Templates</h2>
      <div>
        <button @click="openCreateFolderModal" class="btn btn-success"><i class="fas fa-plus me-1"></i> Create Folder</button>
        <button @click="showTemplateModal = true" class="btn btn-success">
          <i class="fab fa-whatsapp me-1"></i> Add WhatsApp Template
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
              <button @click="openEditFolderModal(folder)" class="btn btn-warning">Edit</button>
              <button @click="deleteFolder(folder.id)" class="bg-red-600 text-white px-2 py-1 rounded">
                <i class="fas fa-trash"></i> Delete
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
      <button v-if="currentView === 'folder-view'" @click="currentView = 'folders'" class="btn btn-secondary mb-3">
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
              <td class="p-2" v-html="template.content"></td>
              <td class="p-2">{{ template.type }}</td>
              <td class="p-2">
                <a v-if="template.attachment_path" :href="`/storage/${template.attachment_path}`" target="_blank" class="text-blue-600 underline">
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
    <div class="modal fade show d-block" v-if="showCreateFolderModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Create Folder</h5>
            <button type="button" class="btn-close" @click="showCreateFolderModal = false"></button>
          </div>
          <div class="modal-body">
            <label class="form-label">* Folder Name</label>
            <input
              v-model="newFolder.name"
              type="text"
              class="form-control"
              placeholder="Enter folder name"
              required
            />
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="showCreateFolderModal = false">Cancel</button>
            <button class="btn btn-primary" @click="createFolder">
              Create Folder
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- Update Folder Modal -->
    <div class="modal fade show d-block" v-if="showEditFolderModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Folder</h5>
            <button type="button" class="btn-close" @click="showEditFolderModal = false"></button>
          </div>
          <div class="modal-body">
            <label class="form-label">* Folder Name</label>
            <input
              v-model="activeFolder.name"
              type="text"
              class="form-control"
              placeholder="Enter new folder name"
              required
            />
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="showEditFolderModal = false">Cancel</button>
            <button class="btn btn-primary" @click="updateFolder">
              Save Changes
            </button>
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
                <input
                  v-model="newTemplate.title"
                  type="text"
                  class="form-control"
                  placeholder="Template Name"
                  :class="{ 'is-invalid': errors.title }"
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
                  <button class="btn btn-outline-success" type="button" @click="showFolderModal = true">+</button>
                </div>
                <div class="invalid-feedback" v-if="errors.folder_id">{{ errors.folder_id }}</div>
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

                  <!-- Attachment -->
                  <div class="col-12">
                    <label for="attachment" class="form-label">Attachment</label>
                    <input
                      id="attachment"
                      type="file"
                      class="form-control"
                      @change="handleFileUpload"
                      accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.xls"
                      :class="{ 'is-invalid': errors.attachment }"
                    />
                    <small class="text-muted d-block">
                      Allowed types: PDF, DOC, DOCX, TXT, JPG, JPEG, PNG, XLS (Max: 300KB)
                    </small>
                    <div v-if="errors.attachment" class="text-danger small mt-1">
                      {{ errors.attachment }}
                    </div>
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
              <input
                v-model="editForm.title"
                type="text"
                class="form-control"
                placeholder="Template Name"
                :class="{ 'is-invalid': errors.title }"
              />
              <div class="invalid-feedback" v-if="errors.title">{{ errors.title }}</div>
            </div>

            <div class="col-md-6">
              <label class="form-label">* Folder</label>
              <div class="input-group">
                <select
                  v-model="editForm.folder_id"
                  class="form-select"
                  :class="{ 'is-invalid': errors.folder_id }"
                >
                  <option value="">Choose Folder</option>
                  <option v-for="folder in folders" :key="folder.id" :value="folder.id">
                    {{ folder.name }}
                  </option>
                </select>
                <button class="btn btn-outline-success" type="button" @click="showFolderModal = true">+</button>
              </div>
              <div class="invalid-feedback" v-if="errors.folder_id">{{ errors.folder_id }}</div>
            </div>

            <div class="col-12">
              <label class="form-label">* Body</label>
              <Editor
                :init="editorInit"
                :modelValue="editForm.content"
                @update:modelValue="editForm.content = $event"
              />
              <div class="text-danger mt-1" v-if="errors.content">{{ errors.content }}</div>
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

    const currentView = ref('folders') 
    const activeFolder = ref(null)
    const showFolderModal = ref(false)
    const showTemplateModal = ref(false)
    const folders = ref([])
    const folderTemplates = ref([])
    const openedFolderId = ref(null)
    const search = ref('')
    const whatsappSignatures = ref([]);
    const showCreateFolderModal = ref(false);
    const showEditFolderModal = ref(false);

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

    const editorInit = {
      height: 300,
      menubar: false,
      plugins: 'link lists image code',
      toolbar:
        'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent | image link code | mergefields signatures',
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

        editor.ui.registry.addMenuButton('signatures', {
          text: 'Signatures',
          fetch(callback) {
            const items = whatsappSignatures.value.map(sig => ({
              type: 'menuitem',
              text: sig.title,
              onAction: () => {
                const titleHtml = `<h4>${sig.title}</h4>`;
                const contentHtml = sig.content || '';
                const attachmentHtml = sig.attachment
                  ? `<p><a href="${sig.attachment}" target="_blank">📎 Attachment</a></p>`
                  : '';
                const fullContent = `${titleHtml}\n${contentHtml}\n${attachmentHtml}`;
                editor.insertContent(fullContent);
              }
            }));
            callback(items);
          }
        });
      }
    };

    const authToken = localStorage.getItem('auth_token')
    if (authToken) {
      axios.defaults.headers.common['Authorization'] = `Bearer ${authToken}`
    }

    const filteredFolders = computed(() =>
      folders.value.filter(folder =>
        folder.name.toLowerCase().includes(search.value.toLowerCase())
      )
    )

    const handleFileUpload = (event) => {
      const file = event.target.files[0];
      if (!file) return;
      const allowedFileTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'text/plain',
        'image/jpeg',
        'image/png',
        'image/jpg'
      ];
      if (!allowedFileTypes.includes(file.type)) {
        Swal.fire('Invalid File', 'Allowed file types: PDF, DOC, DOCX, TXT, JPG, PNG, XLS', 'warning');
        event.target.value = ''; 
        return;
      }
      if (file.size > 300 * 1024) {
        Swal.fire('File Too Large', 'Max allowed size is 300KB.', 'warning');
        event.target.value = ''; 
        return;
      }
      newTemplate.value.attachment = file;
    };

    const fetchFolders = async () => {
      try {
        const res = await axios.get('/api/folders?type=whatsapp') 
        folders.value = res.data
      } catch (err) {
        Swal.fire('Error', 'Failed to load folders', 'error')
      }
    }

    const errors = ref({});
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
        errors.value.content = 'Content is required.';
        isValid = false;
      }
      return isValid;
    }

    const openFolder = async (folderId) => {
      try {
        const res = await axios.get(`/api/folders/${folderId}/templates`, {
          params: { type: 'whatsapp' }
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

    const createFolder = async () => {
      if (!newFolder.value.name.trim()) {
        Swal.fire('Error', 'Folder name is required', 'error');
        return;
      }
      const duplicate = folders.value.some(folder =>
        folder.name.trim().toLowerCase() === newFolder.value.name.trim().toLowerCase()
      );
      if (duplicate) {
        Swal.fire('Warning', 'A folder with this name already exists.', 'warning');
        return;
      }
      try {
        const res = await axios.post('/api/folders', {
          name: newFolder.value.name,
          type: 'whatsapp',
        });
        folders.value.push(res.data.folder);
        showCreateFolderModal.value = false;
        newFolder.value.name = '';
        Swal.fire('Success', 'Folder created successfully.', 'success');
      } catch (err) {
        Swal.fire('Error', 'Could not create folder', 'error');
      }
    };

    const saveTemplate = async () => {
      if (!validateTemplateForm(newTemplate.value)) return;
      try {
        const formData = new FormData()
        formData.append('title', newTemplate.value.title)
        formData.append('folder_id', newTemplate.value.folder_id)
        formData.append('subject', newTemplate.value.subject)
        formData.append('content', newTemplate.value.content)
        formData.append('type', 'whatsapp')
        if (newTemplate.value.attachment) {
          formData.append('attachment', newTemplate.value.attachment)
        }
        await axios.post('/api/templates', formData)
        newTemplate.value = { title: '', folder_id: '', subject: '', content: '', attachment: null }
        showTemplateModal.value = false
        if (currentView.value === 'folder-view' && openedFolderId.value) {
          await openFolder(openedFolderId.value)
        } else {
          await fetchFolders()
        }
        Swal.fire('Success', 'WhatsApp template saved.', 'success')
      } catch (err) {
        Swal.fire('Error', 'Could not save template', 'error')
      }
    }

    const currentPage = ref(1)
    const perPage = 10
    const totalPages = computed(() => Math.ceil(filteredFolders.value.length / perPage))
    const paginatedFolders = computed(() => {
      const start = (currentPage.value - 1) * perPage
      return filteredFolders.value.slice(start, start + perPage)
    })
    const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++ }
    const prevPage = () => { if (currentPage.value > 1) currentPage.value-- }

    const isEditing = ref(false)
    const editingTemplateId = ref(null)
    const editForm = ref({ title: '', subject: '', content: '', type: 'whatsapp', attachment: null })
    const startEdit = (template) => {
      isEditing.value = true
      editingTemplateId.value = template.id
      editForm.value = { title: template.title, subject: template.subject, content: template.content, folder_id: template.folder_id, type: template.type, attachment: null }
    }
    const updateTemplate = async () => {
      if (!validateTemplateForm(newTemplate.value)) return;
      try {
        const formData = new FormData();
        formData.append('title', editForm.value.title);
        formData.append('subject', editForm.value.subject);
        formData.append('content', editForm.value.content);
        formData.append('type', 'whatsapp');
        formData.append('folder_id', editForm.value.folder_id);
        if (editForm.value.attachment) formData.append('attachment', editForm.value.attachment);
        await axios.post(`/api/email-templates/${editingTemplateId.value}?_method=PUT`, formData, { headers: { 'Content-Type': 'multipart/form-data' } });
        isEditing.value = false;
        editingTemplateId.value = null;
        await openFolder(openedFolderId.value);
        Swal.fire('Updated!', 'Template updated successfully.', 'success');
      } catch (err) {
        console.error(err);
        Swal.fire('Error!', 'Could not update template.', 'error');
      }
    }

    const cancelEdit = () => { isEditing.value = false; editingTemplateId.value = null }
    const deleteTemplate = async (id) => {
      const result = await Swal.fire({ title: 'Are you sure?', text: 'This will permanently delete the template.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, delete it!' })
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

    const fetchWhatsappSignatures = async () => {
      try {
        const response = await axios.get('/api/signature-templates-sms/whatsapp');
        whatsappSignatures.value = response.data;
      } catch (error) {}
    };

    const updateFolder = async () => {
      if (!activeFolder.value.name.trim()) {
        Swal.fire('Error', 'Folder name is required', 'error');
        return;
      }
      try {
        const res = await axios.put(`/api/folders/${activeFolder.value.id}`, { name: activeFolder.value.name });
        const index = folders.value.findIndex(folder => folder.id === activeFolder.value.id);
        if (index !== -1) folders.value[index] = res.data.folder;
        showEditFolderModal.value = false;
        activeFolder.value = null;
        Swal.fire('Success', 'Folder updated successfully.', 'success');
        fetchFolders();
      } catch (err) {
        Swal.fire('Error', 'Could not update folder', 'error');
      }
    };

    const mergeFields = ['first_name', 'last_name', 'email', 'phone', 'city', 'zip_code', 'province', 'street', 'house_number', 'tag', 'stage', 'created_at']

    const deleteFolder = (folderId) => {
      Swal.fire({ title: 'Are you sure?', text: 'This folder and all its templates will be deleted!', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Yes, delete it!', cancelButtonText: 'No, cancel' }).then(async (result) => {
        if (result.isConfirmed) {
          try {
            await axios.delete(`/api/folders/${folderId}`);
            folders.value = folders.value.filter(folder => folder.id !== folderId);
            Swal.fire('Deleted!', 'The folder and its templates have been deleted.', 'success');
          } catch (err) {
            Swal.fire('Error', 'Could not delete the folder.', 'error');
          }
        } else {
          Swal.fire('Cancelled', 'The folder is safe :)', 'info');
        }
      });
    };

    const openCreateFolderModal = () => { newFolder.value.name = ''; showCreateFolderModal.value = true; };
    const openEditFolderModal = (folder) => { activeFolder.value = { ...folder }; showEditFolderModal.value = true; };
    const handleEditFileUpload = (event) => { editForm.value.attachment = event.target.files[0] }
    const openAddTemplateInFolder = (folderId) => { newTemplate.value.folder_id = folderId; showTemplateModal.value = true }

    onMounted(() => { fetchFolders(); fetchWhatsappSignatures(); });
</script>
<style scoped>
.modal {
  z-index: 1000;
}
</style>
