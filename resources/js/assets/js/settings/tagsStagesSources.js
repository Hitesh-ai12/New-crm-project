import axios from 'axios';
import Swal from 'sweetalert2';
import { onMounted, ref } from 'vue';

// ✅ Add token to all axios requests
axios.interceptors.request.use(config => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default function useTagsStagesSources() {
  const tags = ref([]);
  const stages = ref([]);
  const sources = ref([]);
  const showModal = ref(false);
  const modalType = ref('');
  const modalEditMode = ref(false);
  const inputValue = ref('');
  const editingItem = ref(null);
  const showToast = ref(false);
  const toastMessage = ref('');

  const fetchItems = async () => {
    try {
      const response = await axios.get('/api/items');
      const data = response.data;
      tags.value = data.tags || [];
      stages.value = data.stages || [];
      sources.value = data.sources || [];
    } catch (error) {
      console.error('Error fetching items:', error);
    }
  };

  const openModal = (type, item = null) => {
    modalType.value = type;
    if (item) {
      modalEditMode.value = true;
      inputValue.value = item.name;
      editingItem.value = item;
    } else {
      modalEditMode.value = false;
      inputValue.value = '';
      editingItem.value = null;
    }
    showModal.value = true;
  };

  const closeModal = () => {
    showModal.value = false;
  };

  const saveItem = async () => {
    if (inputValue.value.trim() === '') {
      alert(`Please enter a valid ${modalType.value}.`);
      return;
    }

    try {
      if (modalEditMode.value && editingItem.value) {
        const response = await axios.put(`/api/items/${editingItem.value.id}`, {
          name: inputValue.value,
        });
        const updatedItem = response.data.item;
        updateList(modalType.value, updatedItem);
        showToastMessage(`${modalType.value} updated successfully!`);
      } else {
        const response = await axios.post('/api/items', {
          name: inputValue.value,
          type: modalType.value,
        });
        const newItem = response.data.item;
        addItemToList(modalType.value, newItem);
        showToastMessage(`${modalType.value} added successfully!`);
      }
      closeModal();
    } catch (error) {
      console.error('Error saving item:', error);
      alert('There was an error saving your data.');
    }
  };

  const updateList = (type, updatedItem) => {
    if (type === 'tag') {
      tags.value = tags.value.map(item => item.id === updatedItem.id ? updatedItem : item);
    } else if (type === 'stage') {
      stages.value = stages.value.map(item => item.id === updatedItem.id ? updatedItem : item);
    } else if (type === 'source') {
      sources.value = sources.value.map(item => item.id === updatedItem.id ? updatedItem : item);
    }
  };

  const addItemToList = (type, newItem) => {
    if (type === 'tag') {
      tags.value.push(newItem);
    } else if (type === 'stage') {
      stages.value.push(newItem);
    } else if (type === 'source') {
      sources.value.push(newItem);
    }
  };

  const confirmDelete = (item) => {
    Swal.fire({
      title: `Are you sure you want to delete this ${item.type}?`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, Delete',
      cancelButtonText: 'No, Cancel',
    }).then(async (result) => {
      if (result.isConfirmed) {
        await deleteItemConfirmed(item);
      }
    });
  };

  const deleteItemConfirmed = async (item) => {
    try {
      await axios.delete(`/api/items/${item.id}`);
      if (item.type === 'tag') {
        tags.value = tags.value.filter(i => i.id !== item.id);
      } else if (item.type === 'stage') {
        stages.value = stages.value.filter(i => i.id !== item.id);
      } else if (item.type === 'source') {
        sources.value = sources.value.filter(i => i.id !== item.id);
      }
      showToastMessage('Item deleted successfully!');
    } catch (error) {
      console.error('Error deleting item:', error);
      alert('There was an error deleting your data.');
    }
  };

  const showToastMessage = (message) => {
    toastMessage.value = message;
    showToast.value = true;
    setTimeout(() => (showToast.value = false), 3000);
  };

  onMounted(() => {
    fetchItems();
  });

  return {
    tags,
    stages,
    sources,
    showModal,
    modalType,
    modalEditMode,
    inputValue,
    openModal,
    closeModal,
    saveItem,
    showToast,
    toastMessage,
    confirmDelete,
  };
}
