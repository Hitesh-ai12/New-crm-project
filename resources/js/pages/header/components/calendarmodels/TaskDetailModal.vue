<template>
  <div class="task-modal-overlay">
    <div class="task-modal-content">
      <h3>Task Details</h3>
      <p><strong>Lead:</strong> {{ task.lead_name }}</p>
      <p><strong>Title:</strong> {{ task.title }}</p>
      <p><strong>Type:</strong> {{ task.type }}</p>
      <p><strong>Date & Time:</strong> {{ task.due_date }}</p>
      <button @click="$emit('close')">Close</button>
    </div>
  </div>
</template>

<script>
export default {
  props: ['task']
}
</script>

<style scoped>
.task-modal-overlay {
  position: fixed;
  z-index: 999;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 40%);
  inset: 0;
}

.task-modal-content {
  border-radius: 10px;
  background: white;
  inline-size: 400px;
  max-inline-size: 90%;
  padding-block: 1rem;
  padding-inline: 2rem;
}

.task-modal-content h3 {
  margin-block-end: 1rem;
}

.task-modal-content p {
  font-size: 0.95rem;
  margin-block-end: 0.75rem;
}

.task-modal-content button {
  border: none;
  border-radius: 4px;
  background-color: #8c57ff;
  color: white;
  cursor: pointer;
  margin-block-start: 1rem;
  padding-block: 8px;
  padding-inline: 16px;
}

.task-modal-content button:hover {
  background-color: #6a43d6;
}
</style>
