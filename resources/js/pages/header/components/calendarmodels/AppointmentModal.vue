<template>
  <div class="appointment_modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Create Appointment</h2>
        <button @click="$emit('close')">×</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="appointment-lead">Lead Name:</label>
          <select id="appointment-lead" v-model="appointmentData.lead">
            <option disabled value="">Select Lead</option>
            <option 
              v-for="lead in leads" 
              :key="lead.id" 
              :value="lead.id"
            >
              {{ lead.first_name }} {{ lead.last_name }}
            </option>
          </select>
        </div>

        <div class="form-group">
          <label for="appointment-title">Title:</label>
          <input type="text" id="appointment-title" v-model="appointmentData.title" placeholder="Enter title" />
        </div>

        <div class="form-group">
          <label for="appointment-description">Description:</label>
          <textarea id="appointment-description" v-model="appointmentData.description" placeholder="Enter description"></textarea>
        </div>

        <div class="form-group">
          <label for="appointment-location">Location:</label>
          <input type="text" id="appointment-location" v-model="appointmentData.location" placeholder="Enter location" />
        </div>

        <div class="form-group">
          <label for="appointment-date">Date:</label>
          <input type="datetime-local" id="appointment-date" v-model="appointmentData.date" />
        </div>

        <div class="form-actions">
          <button @click="handleFormSubmit">Create Appointment</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'

export default {
  props: ['leads'],
  emits: ['close', 'save'],
  setup(props, { emit }) {
    const appointmentData = ref({
      lead: '',
      title: '',
      description: '',
      location: '',
      date: ''
    })

    const handleFormSubmit = async () => {
      try {
        const response = await fetch('/api/appointments', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify(appointmentData.value)
        })

        const result = await response.json()

        if (response.ok && result?.appointment) {
          emit('save', {
            id: result.appointment.id,
            title: result.appointment.title,
            type: 'Appointment',
            due_date: result.appointment.date,
            lead_id: result.appointment.lead_id,
            lead_name: result.appointment.lead_name
          })

          emit('close')
        } else {
          console.error('❌ Appointment creation failed:', result)
        }
      } catch (error) {
        console.error('❌ Error creating appointment:', error)
      }
    }

    return {
      appointmentData,
      handleFormSubmit
    }
  }
}
</script>


<style scoped>
/* Same styles as TaskModal */
.appointment_modal {
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

