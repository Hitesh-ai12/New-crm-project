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
    <div v-if="showModal" class="tss-modal-overlay">
      <div class="tss-modal">
        <h3>{{ modalEditMode ? 'Edit' : 'Add' }} {{ modalType }}</h3>
        <input type="text" v-model="inputValue" :placeholder="'Enter ' + modalType" />

        <div class="tss-modal-actions">
          <button @click="saveItem">{{ modalEditMode ? 'Update' : 'Add' }} {{ modalType }}</button>
          <button class="tss-cancel" @click="closeModal">Close</button>
        </div>
      </div>
    </div>


<!-- Toast Notification -->
<div v-if="showToast" class="tss-toast">
  {{ toastMessage }}
</div>

  </div>
</template>

<script lang="js">
// import '@/assets/css/settings/tags-stages-sources.css';
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

<style scoped>
.cards-container {
  display: flex;
  gap: 20px;
  margin-block-start: 20px;
}

.card {
  position: relative;
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 8px;
  background: #fff;
  box-shadow: 0 0 13px 2px #0000002e;
  inline-size: 33.33%;
  text-align: center;
}

.card h3 {
  border-block-end: 1px solid #d9d9d9;
  margin-block-end: 60px;
  padding-block-end: 20px;
  text-align: start;
}

.card .tags button {
  position: absolute;
  inset-block-start: 20px;
  inset-inline-end: 20px;
}

.card .stages button {
  position: absolute;
  inset-block-start: 20px;
  inset-inline-end: 20px;
}

.card .sources button {
  position: absolute;
  inset-block-start: 20px;
  inset-inline-end: 20px;
}

.card .tags {
  display: flex;
  flex-wrap: wrap;
  justify-content: flex-start;
  gap: 12px;
}

.card .stages {
  display: flex;
  flex-wrap: wrap;
  justify-content: flex-start;
  gap: 12px;
}

.card .sources {
  display: flex;
  flex-wrap: wrap;
  justify-content: flex-start;
  gap: 12px;
}

.card .tags .tag {
  position: relative;
  display: inline-block;
  border: 1px solid #fecf88;
  border-radius: 5px;
  background: #fecf884f;
  cursor: pointer;
  padding-block: 5px;
  padding-inline: 10px;
  transition: 0.4s ease;
}

.card .stages .stage {
  position: relative;
  display: inline-block;
  border: 1px solid #88cdfe;
  border-radius: 5px;
  background: #88cdfe4f;
  cursor: pointer;
  padding-block: 5px;
  padding-inline: 10px;
  transition: 0.4s ease;
}

.card .sources .source {
  position: relative;
  display: inline-block;
  border: 1px solid #fe9388;
  border-radius: 5px;
  background: #fe93884f;
  cursor: pointer;
  padding-block: 5px;
  padding-inline: 10px;
  transition: 0.4s ease;
}

.card .stages .stage .tag-icons {
  opacity: 0;
}

.card .stages .stage:hover .tag-icons {
  opacity: 1;
}

.card .sources .source .tag-icons {
  opacity: 0;
}

.card .sources .source:hover .tag-icons {
  opacity: 1;
}

.card .tags .tag .tag-icons {
  opacity: 0;
}

.card .tags .tag:hover .tag-icons {
  opacity: 1;
}

.card .tag-icons {
  position: absolute;
  display: flex;
  justify-content: space-between;
  background: #fff;
  box-shadow: 0 2px 4px 0 #8b8b8b66;
  inline-size: 80px;
  inset-block-start: -50px;
  inset-inline: 0;
  margin-block: 0;
  margin-inline: auto;
  padding-block: 5px;
  padding-inline: 10px;
  transition: 0.4s ease;
}

.tags,
.stages,
.sources {
  margin-block: 10px;
  margin-inline: 0;
}

.tag,
.stage,
.source {
  display: flex;
  align-items: center;
  margin-block-end: 5px;
}

.tag-icon {
  color: #007bff;
  cursor: pointer;
  margin-inline-start: 5px;
}

.modal {
  position: fixed;
  z-index: 1000;
  padding: 20px;
  background-color: white;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 10%);
  inset-block-start: 50%;
  inset-inline-start: 50%;
  transform: translate(-50%, -50%);

  /* Ensure it's above other elements */
}

.modal-actions button {
  padding: 10px;
  border: none;
  border-radius: 5px;
  background: #8c57ff;
  color: #fff;
  cursor: pointer;
  font-size: 16px;
  font-weight: 700;
  inline-size: 150px;
  padding-block: 10px;
  padding-inline: 20px;
}

.modal-content {
  padding: 20px;
  border-radius: 8px;
  background: #fff;
  text-align: center;
}

.modal-content h3 {
  margin-block-end: 20px;
}

.modal-content input {
  box-sizing: border-box;
  padding: 10px;
  inline-size: 100%;
  margin-block-end: 20px;
}

.modal-actions {
  display: flex;
  justify-content: space-around;
}

/* Optionally, add an overlay */
.modal-overlay {
  position: fixed;
  z-index: 999;
  background-color: rgba(0, 0, 0, 50%);
  block-size: 100%;
  inline-size: 100%;
  inset-block-start: 0;
  inset-inline-start: 0;
}

.tss-toast {
  position: fixed;
  z-index: 99999; /* बहुत ऊपर रहेगा */
  border-radius: 8px;
  animation: slideIn 0.3s ease-out;
  background-color: red;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 20%);
  color: #fff;
  font-size: 14px;
  inset-block-start: 12px;
  inset-inline-end: 20px;
  line-height: 1.4;
  padding-block: 12px;
  padding-inline: 20px;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 20%);
}

/* Smooth top-right entry */
@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateX(50%);
  }

  to {
    opacity: 1;
    transform: translateX(0);
  }
}

h1.tss-class {
  font-size: 25px;
}

.modal_stage {
  display: flex;
  flex-wrap: wrap;
  justify-content: flex-start;
  gap: 10px;
  margin-block: 0;
}

.modal_stages .modal_stage {
  display: flex;
  align-items: center;
  background: #fff;
  inline-size: auto;
  padding-inline: 0;
}

.modal_stages .modal_stage input {
  inline-size: auto;
  margin-inline-end: 0;
}

.modal-buttons {
  display: flex;
  justify-content: center;
  gap: 10px;
}

.submit-button,
.cancel-button {
  background: #9268dd;
  inline-size: 100px;
}

.import-options {
  display: flex;
  flex-direction: row !important;
  justify-content: space-between;
  margin-block-start: 20px;
}

.columns-modal-container {
  position: relative;
  padding: 20px;
  border-radius: 8px;
  background: #fff;
  block-size: 500px;
  box-shadow: 0 4px 10px #0000004d;
  inline-size: 600px;
  text-align: center;
}

.columns-modal-list {
  display: flex;
  flex-wrap: wrap;
  padding: 0;
  gap: 10px;
  margin-block-start: 15px;
  text-align: start;
}

.columns-modal-item {
  display: flex;
  align-items: center;
  gap: 10px;
  inline-size: calc(33% - 10px);
}

.columns-modal-update-btn {
  padding: 10px;
  border: none;
  border-radius: 5px;
  background: #8c57ff;
  color: #fff;
  cursor: pointer;
  font-size: 16px;
  font-weight: 700;
  inline-size: 120px;
  margin-block-start: 15px;
}

.columns-modal-update-btn:hover {
  background: #9155fd;
}

.cancel-btn {
  background: #e31521;
  margin-inline-start: 20px;
}

.cancel-btn:hover {
  background: #dd3e47;
}

.card h3 {
  border-block-end: 1px solid #d9d9d9;
  padding-block-end: 20px;
  text-align: start;
}

.card .tag-icons {
  position: absolute;
  display: flex;
  background: transparent;
  gap: 5px;
  inline-size: fit-content;
  inset-block-start: -25px;
  inset-inline-start: 10px;
  transition: 0.4s ease;
}

.tag-icon {
  cursor: pointer;
}

.stage-modal-container > div {
  display: flex;
  gap: 10px;
}

.stage-modal-container > div button.submit-button {
  margin: 0;
  padding-inline: 40px;
}

.stage-modal-container button.submit-button {
  margin-block: 10px;
  margin-inline: auto;
  padding-block: 10px;
  padding-inline: 40px;
  text-align: center;
}

.modal-container h2 {
  font-size: 17.5px;
  text-align: center;
}

select.open_modal_drop-box {
  padding: 10px;
  border: 1px solid #8c57ff;
  border-radius: 6px;
  background-color: #fff;
  block-size: 100%;
  inline-size: 100%;
  margin-block-start: 10px;
}

select.open_modal_drop-box:focus-visible {
  border: 1px solid #8c57ff;
}

.modal-buttons .cancel-button {
  border: #fff;
  border-radius: 5px;
  color: #fff;
  cursor: pointer;
  margin-block: 7px;
  margin-inline: 0;
  padding-block: 10px;
  padding-inline: 25px;
}

button.import-button {
  background: #8c57ff !important;
}

/* Overlay with blur */
.tss-modal-overlay {
  position: fixed;
  z-index: 999;
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(5px);
  background: rgba(0, 0, 0, 40%);
  inset: 0;
}

/* Modal box */
.tss-modal {
  padding: 25px;
  border-radius: 12px;
  animation: modalFadeIn 0.3s ease;
  background: white;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 20%);
  inline-size: 400px;
  max-inline-size: 90%;
  text-align: center;
}

/* Heading */
.tss-modal h3 {
  font-size: 20px;
  font-weight: bold;
  margin-block-end: 20px;
}

/* Input */
.tss-modal input {
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 14px;
  inline-size: 100%;
  margin-block-end: 20px;
}

/* Buttons container */
.tss-modal-actions {
  display: flex;
  justify-content: space-between;
  gap: 10px;
}

/* Primary button */
.tss-modal-actions button {
  flex: 1;
  padding: 10px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 600;
  transition: background 0.2s ease;
}

.tss-modal-actions button:not(.tss-cancel) {
  background: #8c57ff;
  color: white;
}

.tss-modal-actions button:not(.tss-cancel):hover {
  background: #7646d8;
}

/* Cancel button */
.tss-cancel {
  background: #eee;
}

.tss-cancel:hover {
  background: #ddd;
}

/* Animation */
@keyframes modalFadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

</style>
