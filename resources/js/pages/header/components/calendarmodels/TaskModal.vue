<template>
  <div class="task_modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Add Task</h2>
        <button @click="$emit('close')">×</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Select Lead:</label>
          <select v-model="taskData.lead_id">
            <option v-for="lead in leads" :key="lead.id" :value="lead.id">
              {{ lead.first_name }} {{ lead.last_name }}
            </option>
          </select>
        </div>
        <div class="form-group">
          <label>Task Title:</label>
          <input type="text" v-model="taskData.title" placeholder="Enter task title">
        </div>
        <div class="form-group">
          <label>Task Type:</label>
          <select v-model="taskData.type">
            <option value="Meeting">Meeting</option>
            <option value="Call">Call</option>
            <option value="Email">Email</option>
            <option value="Text">Text</option>
            <option value="Follow-up">Follow-up</option>
          </select>
        </div>
        <div class="form-group">
          <label>Due Date:</label>
          <input type="datetime-local" v-model="taskData.due_date" />
        </div>
        <div class="form-actions">
          <button @click="saveTask">Add Task</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';

export default {
  props: {
    leads: Array
  },
  emits: ['close', 'save'],
  setup(props, { emit }) {
    const taskData = ref({
      lead_id: '',
      title: '',
      type: 'Meeting',
      due_date: ''
    });

    const saveTask = () => {
      if (!taskData.value.lead_id || !taskData.value.title || !taskData.value.due_date) {
        alert('Please fill all required fields');
        return;
      }

      emit('save', taskData.value);  // ✅ Pass data to parent
      emit('close');
    };

    return {
      taskData,
      saveTask
    };
  }
};
</script>

<style scoped>
/* Copy the modal styles from your original CSS */
.task_modal {
  position: fixed;
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 50%);
  block-size: 100%;
  inline-size: 100%;
  inset-block-start: 0;
  inset-inline-start: 0;
}

.modal-content {
  padding: 20px;
  border-radius: 8px;
  background: white;
  block-size: auto;
  inline-size: 400px;
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-block-end: 1px solid #ccc;
  margin-block-end: 10px;
}

.modal-body .form-group {
  margin-block-end: 15px;
}

.modal-body .form-group label {
  display: block;
  margin-block-end: 5px;
}

.modal-body .form-group input,
.modal-body .form-group select,
.modal-body .form-group textarea {
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  inline-size: 100%;
}

.modal-body .form-actions {
  text-align: end;
}

.modal-body .form-actions button {
  border: none;
  border-radius: 4px;
  background-color: #8c57ff;
  color: white;
  cursor: pointer;
  padding-block: 10px;
  padding-inline: 20px;
}

.modal-body .form-actions button:hover {
  background-color: #6a43d6;
}
</style>
