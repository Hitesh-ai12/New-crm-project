<!-- components/calendarmodels/TaskDetailModal.vue -->
<template>
  <div class="modal-overlay">
    <div class="modal-content">
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
<style>
.event.lead-name {
  color: #007bff;
  cursor: pointer;
  font-weight: bold;
}

.modal-overlay {
  position: fixed;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 40%);
  inset: 0;
}

.modal-content {
  border-radius: 10px;
  background: white;
  padding-block: 1rem;
  padding-inline: 2rem;
}

</style>
