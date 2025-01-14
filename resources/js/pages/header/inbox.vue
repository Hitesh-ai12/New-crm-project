<template>
  <div class="inbox-page">
    <!-- Header Section -->
    <div class="header">
      <button
        :class="{ active: activeTab === 'email' }"
        @click="setActiveTab('email')"
      >
        Email
      </button>
      <button
        :class="{ active: activeTab === 'sms' }"
        @click="setActiveTab('sms')"
      >
        SMS
      </button>

      <!-- "Send New" Button -->
      <button v-if="activeTab === 'email'" class="send-new-btn" @click="openEmailModal">
        Send New Mail
      </button>
      <button v-if="activeTab === 'sms'" class="send-new-btn" @click="openSmsModal">
        Send New SMS
      </button>
    </div>

    <!-- Content Section -->
    <div :class="['content', { blurred: isModalOpen }]">
      <div v-if="activeTab === 'email'" class="email-tab">
        <h2>Email Tab</h2>
        <p>Here you can manage your emails.</p>
      </div>

      <div v-if="activeTab === 'sms'" class="sms-tab">
        <h2>SMS Tab</h2>
        <p>Here you can manage your SMS messages.</p>
      </div>
    </div>

    <!-- Modal Section -->
    <div v-if="isModalOpen" class="modal">
      <div class="modal-content">
        <button class="close-btn" @click="closeModal">X</button>
        <textarea id="tiny-editor"></textarea>
        <button class="send-btn" @click="sendContent">Send</button>
      </div>
    </div>
  </div>
</template>

<script>
import { nextTick, ref } from "vue";

export default {
  setup() {
    const activeTab = ref("email");
    const isModalOpen = ref(false);
    const editorInstance = ref(null);

    // Methods to handle tab switching
    const setActiveTab = (tab) => {
      activeTab.value = tab;
    };

    // Open modal and initialize TinyMCE
    const openEmailModal = () => {
      isModalOpen.value = true;
      initializeTinyMCE();
    };

    const openSmsModal = () => {
      isModalOpen.value = true;
      initializeTinyMCE();
    };

    // Close modal and destroy TinyMCE
    const closeModal = () => {
      isModalOpen.value = false;
      destroyTinyMCE();
    };

    // Initialize TinyMCE editor
    const initializeTinyMCE = () => {
      nextTick(() => {
        tinymce.init({
          selector: "#tiny-editor",
          plugins: ["lists", "link"],
          menubar: false,
          toolbar:
            "undo redo | bold italic | alignleft aligncenter alignright | bullist numlist | link",
          setup: (editor) => {
            editorInstance.value = editor;
          },
        });
      });
    };

    // Destroy TinyMCE editor
    const destroyTinyMCE = () => {
      if (editorInstance.value) {
        editorInstance.value.remove();
        editorInstance.value = null;
      }
    };

    // Send content
    const sendContent = () => {
      const content = editorInstance.value.getContent();
      if (!content.trim()) {
        alert("Content cannot be empty.");
        return;
      }
      alert(`Content sent: ${content}`);
      closeModal();
    };

    return {
      activeTab,
      isModalOpen,
      setActiveTab,
      openEmailModal,
      openSmsModal,
      closeModal,
      sendContent,
    };
  },
};
</script>

<style scoped>
.inbox-page {
  padding: 20px;
  font-family: Arial, sans-serif;
}

.header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-block-end: 20px;
}

.header button {
  border: 1px solid #ccc;
  background-color: #8c57ff;
  cursor: pointer;
  padding-block: 10px;
  padding-inline: 20px;
  transition: background-color 0.3s;
}

.header button.active {
  background-color: #8c57ff;
  color: white;
}

.send-new-btn {
  border: none;
  background-color: #8c57ff;
  color: white;
  cursor: pointer;
  margin-inline-start: auto;
  padding-block: 10px;
  padding-inline: 20px;
}

.send-new-btn:hover {
  background-color: #8c57ff;
}

.content {
  transition: filter 0.3s;
}

.content.blurred {
  filter: blur(5px);
  pointer-events: none;
}

.modal {
  position: fixed;
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: rgba(0, 0, 0, 50%);
  inset: 0;
}

.modal-content {
  padding: 20px;
  border-radius: 8px;
  background: white;
  inline-size: 500px;
  max-inline-size: 90%;
  text-align: center;
}

.close-btn {
  position: absolute;
  border: none;
  background: none;
  cursor: pointer;
  font-size: 18px;
  inset-block-start: 10px;
  inset-inline-end: 10px;
}

textarea {
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  block-size: 150px;
  inline-size: 100%;
  margin-block-end: 20px;
}

.send-btn {
  border: none;
  background-color: #8c57ff;
  color: white;
  cursor: pointer;
  padding-block: 10px;
  padding-inline: 20px;
}

.send-btn:hover {
  background-color: #8c57ff;
}
</style>
