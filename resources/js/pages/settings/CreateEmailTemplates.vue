<template>
  <div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Email Templates</h2>
      <div>
      <!-- Button to open Create Folder Modal -->
      <button @click="openCreateFolderModal" class="btn btn-success"><i class="fas fa-plus me-1"></i> Create Folder</button>
        <button @click="showTemplateModal = true" class="btn btn-success">
          <i class="fas fa-file-alt me-1"></i> Add Email Template
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
              <!-- Edit Button -->
              <button @click="openEditFolderModal(folder)" class="btn btn-warning">Edit</button>
              <!-- Delete Button -->
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
              <th class="p-2">Subject</th>
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
              <td class="p-2">{{ template.subject }}</td>
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
              <button
              @click="deleteTemplate(template.id)"
              class="bg-red-600 text-white px-2 py-1 rounded"
              >
              <i class="fas fa-trash"></i>
              </button>
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

    <!-- Add Email Template Modal -->
    <div class="modal fade show d-block" tabindex="-1" v-if="showTemplateModal">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Email Template</h5>
            <button type="button" class="btn-close" @click="showTemplateModal = false"></button>
          </div>

          <div class="modal-body">
            <div class="row g-3">

              <!-- Template Name -->
              <div class="col-md-6">
                <label for="title" class="form-label">* Template Name</label>
                <input
                  id="title"
                  v-model="newTemplate.title"
                  type="text"
                  class="form-control"
                  placeholder="Template Name"
                  :class="{ 'is-invalid': errors.title }"
                />
                <div v-if="errors.title" class="text-danger small mt-1">
                  {{ errors.title }}
                </div>
              </div>

              <!-- Folder Select -->
              <div class="col-md-6">
                <label for="folder_id" class="form-label">* Folder</label>
                <div class="input-group">
                  <select
                    id="folder_id"
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
                <div v-if="errors.folder_id" class="text-danger small mt-1">
                  {{ errors.folder_id }}
                </div>
              </div>

              <!-- Subject -->
              <div class="col-12">
                <label for="subject" class="form-label">* Subject</label>
                <div class="input-group">
                  <input
                    id="subject"
                    ref="subjectInput"
                    v-model="newTemplate.subject"
                    type="text"
                    class="form-control"
                    placeholder="Subject"
                    :class="{ 'is-invalid': errors.subject }"
                  />
                  <select
                    class="form-select w-auto"
                    @change="insertMergeFieldToSubject($event.target.value, $refs.subjectInput)"
                  >
                    <option disabled selected>Merge Field</option>
                    <option v-for="field in mergeFields" :key="field" :value="field">
                      {{ field }}
                    </option>
                  </select>
                </div>
                <div v-if="errors.subject" class="text-danger small mt-1">
                  {{ errors.subject }}
                </div>
              </div>


              <!-- Body -->
              <div class="col-12">
                <label for="content" class="form-label">* Body</label>
                <Editor
                  id="content"
                  :init="editorInit"
                  :modelValue="newTemplate.content"
                  @update:modelValue="newTemplate.content = $event"
                />
                <div v-if="errors.content" class="text-danger small mt-1">
                  {{ errors.content }}
                </div>
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
            <button class="btn btn-danger" @click="saveTemplate">Save Email Template</button>
          </div>
        </div>
      </div>
    </div>

  <!-- Edit Email Template Modal -->
  <div class="modal fade show d-block" tabindex="-1" v-if="isEditing">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Email Template</h5>
          <button type="button" class="btn-close" @click="cancelEdit"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">

            <!-- Template Name -->
            <div class="col-md-6">
              <label for="edit_title" class="form-label">* Template Name</label>
              <input
                id="edit_title"
                v-model="editForm.title"
                type="text"
                class="form-control"
                placeholder="Template Name"
                :class="{ 'is-invalid': editErrors.title }"
              />
              <div v-if="editErrors.title" class="text-danger small mt-1">
                {{ editErrors.title }}
              </div>
            </div>

            <!-- Folder Select -->
            <div class="col-md-6">
              <label for="edit_folder_id" class="form-label">* Folder</label>
              <div class="input-group">
                <select
                  id="edit_folder_id"
                  v-model="editForm.folder_id"
                  class="form-select"
                  :class="{ 'is-invalid': editErrors.folder_id }"
                >
                  <option value="">Choose Folder</option>
                  <option v-for="folder in folders" :key="folder.id" :value="folder.id">{{ folder.name }}</option>
                </select>
                <button class="btn btn-outline-success" type="button" @click="showFolderModal = true">+</button>
              </div>
              <div v-if="editErrors.folder_id" class="text-danger small mt-1">
                {{ editErrors.folder_id }}
              </div>
            </div>

            <!-- Subject -->
              <div class="col-12">
                <label for="subject" class="form-label">* Subject</label>
                <div class="input-group">
                  <input
                    id="subject"
                    ref="subjectInput"
                    v-model="newTemplate.subject"
                    type="text"
                    class="form-control"
                    placeholder="Subject"
                    :class="{ 'is-invalid': errors.subject }"
                  />
                  <select
                    class="form-select w-auto"
                    @change="insertMergeFieldToSubject($event.target.value, $refs.subjectInput)"
                  >
                    <option disabled selected>Merge Field</option>
                    <option v-for="field in mergeFields" :key="field" :value="field">
                      {{ field }}
                    </option>
                  </select>
                </div>
                <div v-if="errors.subject" class="text-danger small mt-1">
                  {{ errors.subject }}
                </div>
              </div>

            <!-- Body -->
            <div class="col-12">
              <label for="edit_content" class="form-label">* Body</label>
                <Editor
                  id="edit_content"
                  :init="editorInit"
                  :modelValue="editForm.content"
                  @update:modelValue="editForm.content = $event"
                />
                <div v-if="editErrors.content" class="text-danger small mt-1">
                  {{ editErrors.content }}
                </div>
            </div>

            <!-- Attachment -->
            <div class="col-12">
              <label for="edit_attachment" class="form-label">Attachment</label>
              <input
                id="edit_attachment"
                type="file"
                class="form-control"
                @change="handleFileUpload"
                accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.xls"
                :class="{ 'is-invalid': editErrors.attachment }"
              />
              <small class="text-muted">
                Allowed types: PDF, DOC, DOCX, TXT, JPG, JPEG, PNG, XLS (Max: 300KB)
              </small>
              <div v-if="editErrors.attachment" class="text-danger small mt-1">
                {{ editErrors.attachment }}
              </div>
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

    const folders = ref([])
    const folderTemplates = ref([])
    const openedFolderId = ref(null)
    const search = ref('')
    const emailSignatures = ref([]);
    const showCreateFolderModal = ref(false);
    const showEditFolderModal = ref(false);
    // Form Models
    const newFolder = ref({ name: '' })
    const newTemplate = ref({
      title: '',
      folder_id: '',
      subject: '',
      content: '',
      attachment: null,
      signature_template_id: '' 
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
        'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent | image link code | mergefields signatures',
      branding: false,
      resize: true,
      automatic_uploads: true,
      images_upload_handler: handleImageUpload,

      setup(editor) {
        // Merge Fields
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

        // Signature - only one allowed at a time
        editor.ui.registry.addMenuButton('signatures', {
          text: 'Signatures',
          fetch(callback) {
            const items = emailSignatures.value.map(sig => ({
              type: 'menuitem',
              text: sig.title,
              onAction: () => {
                const contentHtml = sig.content || '';
                const attachmentHtml = sig.attachment
                  ? `<p><a href="${sig.attachment}" target="_blank">📎 Attachment</a></p>`
                  : '';

                const signatureBlock = `<div class="email-signature-block">${contentHtml}${attachmentHtml}</div>`;

                const currentContent = editor.getContent();
                const cleanedContent = currentContent.replace(/<div class="email-signature-block">[\s\S]*?<\/div>/, '');

                editor.setContent(cleanedContent + signatureBlock);

                editor.selection.select(editor.getBody(), true);
                editor.selection.collapse(false);
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
    axios.defaults.headers.common['Accept'] = 'application/json'; 

    const fetchEmailSignatures = async () => {
      try {
        const response = await axios.get('/api/signature-templates-email/email');
        emailSignatures.value = response.data;
      } catch (error) {
      }
    };

    // Computed Search Filter
    const filteredFolders = computed(() =>
      folders.value.filter(folder =>
        folder.name.toLowerCase().includes(search.value.toLowerCase())
      )
    )

    // Fetch folders
    const fetchFolders = async () => {
        try {
          const res = await axios.get('/api/folders?type=email') 
          folders.value = res.data
        } catch (err) {
          Swal.fire('Error', 'Failed to load folders', 'error')
        }
      }


    // Open Folder and Load Templates
    const openFolder = async (folderId) => {
      try {
        const res = await axios.get(`/api/folders/${folderId}/templates`)
        folderTemplates.value = res.data
        const folder = folders.value.find(f => f.id === folderId)
        activeFolder.value = folder
        openedFolderId.value = folderId
        currentView.value = 'folder-view'
      } catch (err) {
        Swal.fire('Error', 'Could not load templates for this folder', 'error')
      }
    }

    // Method to create folder
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
          type: 'email',
        });

        folders.value.push(res.data.folder);
        showCreateFolderModal.value = false;
        newFolder.value.name = '';
        Swal.fire('Success', 'Folder created successfully.', 'success');
      } catch (err) {
        Swal.fire('Error', 'Could not create folder', 'error');
      }
    };

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

  // Save Email Template
  const saveTemplate = async () => {
    if (!validateTemplateForm(newTemplate.value)) return;

    try {
      const formData = new FormData();
      formData.append('title', newTemplate.value.title);
      formData.append('folder_id', newTemplate.value.folder_id);
      formData.append('subject', newTemplate.value.subject);
      formData.append('content', newTemplate.value.content);
      formData.append('type', 'email');
      formData.append('signature_template_id', newTemplate.value.signature_template_id);


      if (newTemplate.value.attachment) {
        formData.append('attachment', newTemplate.value.attachment);
      }

      await axios.post('/api/templates', formData);

      // reset form
      newTemplate.value = {
        title: '',
        folder_id: '',
        subject: '',
        content: '',
        attachment: null
      };

      showTemplateModal.value = false;
      await (currentView.value === 'folder-view' ? openFolder(openedFolderId.value) : fetchFolders());

      Swal.fire('Success', 'Email template saved.', 'success');
    } catch (err) {
      Swal.fire('Error', 'Could not save template', 'error');
    }
  };


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
      type: 'email',
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

    const editErrors = ref({})

    const updateTemplate = async () => {
      editErrors.value = {};

      if (!validateTemplateForm(editForm.value, editErrors)) return;

      try {
        const formData = new FormData();
        formData.append('title', editForm.value.title);
        formData.append('subject', editForm.value.subject);
        formData.append('content', editForm.value.content);
        formData.append('type', 'email');
        formData.append('folder_id', editForm.value.folder_id);

        if (editForm.value.attachment) {
          formData.append('attachment', editForm.value.attachment);
        }

        await axios.post(`/api/email-templates/${editingTemplateId.value}?_method=PUT`, formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });

        isEditing.value = false;
        editingTemplateId.value = null;
        await openFolder(openedFolderId.value);
        Swal.fire('Updated!', 'Template updated successfully.', 'success');
      } catch (err) {
        Swal.fire('Error!', 'Could not update template.', 'error');
      }
    };


  const resetNewTemplateForm = () => {
    newTemplate.value = {
      title: '',
      folder_id: '',
      subject: '',
      content: '',
      attachment: null
    };

    // Optional: reset file input if needed
    const fileInput = document.getElementById('new-template-attachment');
    if (fileInput) fileInput.value = '';
  };

  const cancelEdit = () => {
    isEditing.value = false
    editingTemplateId.value = null
    resetNewTemplateForm();
  }
    const resetFolderModal = () => {
      newFolder.value.name = ''
      showFolderModal.value = false
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

    const errors = ref({})

    const validateTemplateForm = (template, errorRef = errors) => {
      errorRef.value = {}; // reset errors
      let firstInvalidField = null;

      if (!template.folder_id) {
        errorRef.value.folder_id = 'Please select a folder.';
        firstInvalidField = firstInvalidField || 'folder_id';
      }

      if (!template.title.trim()) {
        errorRef.value.title = 'Title is required.';
        firstInvalidField = firstInvalidField || 'title';
      }

      if (!template.subject.trim()) {
        errorRef.value.subject = 'Subject is required.';
        firstInvalidField = firstInvalidField || 'subject';
      }

      if (!template.content.trim()) {
        errorRef.value.content = 'Content is required.';
        firstInvalidField = firstInvalidField || 'content';
      }

      if (template.attachment) {
        const file = template.attachment;
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
          errorRef.value.attachment = 'Invalid file type.';
          firstInvalidField = firstInvalidField || 'attachment';
        }

        if (file.size > 300 * 1024) {
          errorRef.value.attachment = 'File size must be less than 300KB.';
          firstInvalidField = firstInvalidField || 'attachment';
        }
      }

      if (firstInvalidField) {
        setTimeout(() => {
          const el = document.getElementById(firstInvalidField);
          if (el) el.focus();
        }, 0);
        return false;
      }

      return true;
    };


    const insertMergeFieldToSubject = (field, inputElement) => {
      if (!field || !inputElement) return;

      const mergeText = `{{${field}}}`;

      const start = inputElement.selectionStart;
      const end = inputElement.selectionEnd;

      const currentValue = newTemplate.value.subject;

      // Insert at cursor
      newTemplate.value.subject =
        currentValue.slice(0, start) + mergeText + currentValue.slice(end);

      // Restore focus and cursor position
      nextTick(() => {
        inputElement.focus();
        const newPos = start + mergeText.length;
        inputElement.setSelectionRange(newPos, newPos);
      });
    };

    // Method to delete folder with confirmation
    const deleteFolder = (folderId) => {
      // Show confirmation dialog
      Swal.fire({
        title: 'Are you sure?',
        text: 'This folder and all its templates will be deleted!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel',
      }).then(async (result) => {
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

      // Method to update folder
      const updateFolder = async () => {
        if (!activeFolder.value.name.trim()) {
          Swal.fire('Error', 'Folder name is required', 'error');
          return;
        }

        try {
          const res = await axios.put(`/api/folders/${activeFolder.value.id}`, {
            name: activeFolder.value.name,
          });

          const index = folders.value.findIndex(folder => folder.id === activeFolder.value.id);
          if (index !== -1) {
            folders.value[index] = res.data.folder;
          }

          showEditFolderModal.value = false;
          activeFolder.value = null;
          Swal.fire('Success', 'Folder updated successfully.', 'success');
        } catch (err) {
          Swal.fire('Error', 'Could not update folder', 'error');
        }
      };
      // Method to open Create Folder Modal
      const openCreateFolderModal = () => {
        newFolder.value.name = ''; 
        showCreateFolderModal.value = true;
      };
      // Method to open Edit Folder Modal
      const openEditFolderModal = (folder) => {
        activeFolder.value = { ...folder }; 
        showEditFolderModal.value = true;
      };

      onMounted(() => {
        fetchFolders();
        fetchEmailSignatures();
      });

</script>

<style scoped>
.modal {
  z-index: 1000;
}
</style>
