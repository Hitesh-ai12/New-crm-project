<template>
  <div>
    <h1 class="tss-class">Tags | Stage | Source</h1>

    <div class="cards-container">
      <!-- Tag Card -->
      <div class="card">
        <h3>Tags</h3>
        <div class="tags">
          <span v-for="(tag, index) in tags" :key="tag.id" class="tag">
            <div class="tag-icons">
              <span class="tag-icon" @click="openModal('tag', tag)">✏️</span>
              <span class="delete-icon" @click="confirmDelete(tag)">🗑️</span>
            </div>
            {{ tag.name }}
          </span>
          <button @click="openModal('tag')">➕ Add Tag</button>
        </div>
      </div>

      <!-- Stage Card -->
      <div class="card">
        <h3>Stage</h3>
        <div class="stages">
          <span v-for="(stage, index) in stages" :key="index" class="stage">
            <div class="tag-icons">
              <span class="tag-icon" @click="openModal('stage', stage)">✏️</span>
              <span class="delete-icon" @click="confirmDelete(stage)">🗑️</span>
            </div>
            {{ stage.name }}
          </span>
          <button @click="openModal('stage')">➕ Add Stage</button>
        </div>
      </div>

      <!-- Source Card -->
      <div class="card">
        <h3>Source</h3>
        <div class="sources">
          <span v-for="(source, index) in sources" :key="index" class="source">
            <div class="tag-icons">
              <span class="tag-icon" @click="openModal('source', source)">✏️</span>
              <span class="delete-icon" @click="confirmDelete(source)">🗑️</span>
            </div>
            {{ source.name }}
          </span>
          <button @click="openModal('source')">➕ Add Source</button>
        </div>
      </div>
    </div>

    <!-- Input Modal -->
    <div v-if="showModal" class="modal">
      <div class="modal-content">
        <h3>{{ modalEditMode ? 'Edit' : 'Add' }} {{ modalType }}</h3>
        <input type="text" v-model="inputValue" :placeholder="'Enter ' + modalType" />

        <div class="modal-actions">
          <button @click="saveItem">{{ modalEditMode ? 'Update' : 'Add' }} {{ modalType }}</button>
          <button @click="closeModal">Close</button>
        </div>
      </div>
    </div>

    <!-- Toast Notification -->
    <div v-if="showToast" class="toast">
      {{ toastMessage }}
    </div>
  </div>
</template>

<script lang="js">
import '@/assets/css/settings/tags-stages-sources.css';
import useTagsStagesSources from '@/assets/js/settings/tagsStagesSources.js';

export default {
  setup() {
    const {
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
    } = useTagsStagesSources();

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
  },
};
</script>
