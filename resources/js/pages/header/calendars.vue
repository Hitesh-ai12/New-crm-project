<template>
  <div class="calendar-page">
    <!-- Header Section -->
    <div class="header">
      <h1>Calendar</h1>
      <div class="actions">
        <button @click="openTaskModal">Add Task</button>
        <button @click="openAppointmentModal">Create Appointment</button>
      </div>
    </div>

    <!-- Calendar Container -->
    <div class="calendar-container">
      <div class="calendar">
        <div class="calendar-header">
          <button @click="prevMonth">Prev</button>
          <span>{{ formattedMonth }} {{ currentYear }}</span>
          <button @click="nextMonth">Next</button>
        </div>
        <div class="calendar-days">
          <div
            class="calendar-day"
            :class="{ 'current-day': isCurrentDay(day.date) }"
            v-for="(day, index) in daysInMonth"
            :key="index"
            @click="handleDateClick(day)"
          >
            <span>{{ day.date }}</span>
            <div v-for="event in day.events" :key="event.id" class="event">{{ event.title }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Task Modal -->
    <div v-if="showTaskModal" class="task_modal">
      <div class="modal-content">
        <div class="modal-header">
          <h2>Add Task</h2>
          <button @click="closeTaskModal">×</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="task-lead">Lead Name:</label>
            <input type="text" id="task-lead" v-model="taskData.lead" placeholder="Enter lead name" />
          </div>
          <div class="form-group">
            <label for="task-title">Title:</label>
            <input type="text" id="task-title" v-model="taskData.title" placeholder="Enter task title" />
          </div>
          <div class="form-group">
            <label for="task-type">Type:</label>
            <select id="task-type" v-model="taskData.type">
              <option value="Meeting">Meeting</option>
              <option value="Call">Call</option>
              <option value="Follow-up">Follow-up</option>
            </select>
          </div>
          <div class="form-group">
            <label for="task-datetime">Date & Time:</label>
            <input type="datetime-local" id="task-datetime" v-model="taskData.date" />
          </div>
          <div class="form-actions">
            <button @click="addTask">Add Task</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Appointment Modal -->
    <div v-if="showAppointmentModal" class="appointment_modal">
      <div class="modal-content">
        <div class="modal-header">
          <h2>Create Appointment</h2>
          <button @click="closeAppointmentModal">×</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="appointment-lead">Lead Name:</label>
            <select id="appointment-lead" v-model="appointmentData.lead">
              <option value="Lead 1">Lead 1</option>
              <option value="Lead 2">Lead 2</option>
              <option value="Lead 3">Lead 3</option>
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
            <button @click="addAppointment">Create Appointment</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { computed, ref } from "vue";

export default {
  setup() {
    const currentMonth = ref(new Date().getMonth());
    const currentYear = ref(new Date().getFullYear());
    const taskData = ref({ lead: "", title: "", type: "", date: "" });
    const appointmentData = ref({ lead: "", title: "", description: "", location: "", date: "" });
    const showTaskModal = ref(false);
    const showAppointmentModal = ref(false);
    const events = ref([]);

    // Modal handling
    const openTaskModal = () => {
      showTaskModal.value = true;
    };
    const openAppointmentModal = () => {
      showAppointmentModal.value = true;
    };
    const closeTaskModal = () => {
      showTaskModal.value = false;
      taskData.value = { lead: "", title: "", type: "", date: "" };
    };
    const closeAppointmentModal = () => {
      showAppointmentModal.value = false;
      appointmentData.value = { lead: "", title: "", description: "", location: "", date: "" };
    };

    // Add Task
    const addTask = () => {
      if (taskData.value.title && taskData.value.date) {
        events.value.push({
          id: Date.now(),
          title: taskData.value.title,
          date: taskData.value.date,
          type: taskData.value.type,
        });
        closeTaskModal();
      }
    };

    // Add Appointment
    const addAppointment = () => {
      if (appointmentData.value.title && appointmentData.value.date) {
        events.value.push({
          id: Date.now(),
          title: appointmentData.value.title,
          description: appointmentData.value.description,
          location: appointmentData.value.location,
          date: appointmentData.value.date,
        });
        closeAppointmentModal();
      }
    };

    // Handle date click
    const handleDateClick = (day) => {
      console.log("Clicked date: ", day);
    };

    // Get the number of days in the current month
    const daysInMonth = computed(() => {
      const days = [];
      const firstDay = new Date(currentYear.value, currentMonth.value, 1);
      const lastDay = new Date(currentYear.value, currentMonth.value + 1, 0);
      const numberOfDays = lastDay.getDate();

      for (let i = 1; i <= numberOfDays; i++) {
        const date = new Date(currentYear.value, currentMonth.value, i);
        days.push({
          date: i,
          events: events.value.filter(
            (event) => new Date(event.date).toDateString() === date.toDateString()
          ),
        });
      }
      return days;
    });

    const isCurrentDay = (day) => {
      const today = new Date();
      return (
        day === today.getDate() &&
        currentMonth.value === today.getMonth() &&
        currentYear.value === today.getFullYear()
      );
    };

    // Switch month
    const prevMonth = () => {
      if (currentMonth.value === 0) {
        currentMonth.value = 11;
        currentYear.value--;
      } else {
        currentMonth.value--;
      }
    };
    const nextMonth = () => {
      if (currentMonth.value === 11) {
        currentMonth.value = 0;
        currentYear.value++;
      } else {
        currentMonth.value++;
      }
    };

    const formattedMonth = computed(() => {
      return new Date(currentYear.value, currentMonth.value).toLocaleString("default", {
        month: "long",
      });
    });

    return {
      currentMonth,
      currentYear,
      formattedMonth,
      daysInMonth,
      showTaskModal,
      showAppointmentModal,
      taskData,
      appointmentData,
      openTaskModal,
      openAppointmentModal,
      closeTaskModal,
      closeAppointmentModal,
      addTask,
      addAppointment,
      handleDateClick,
      prevMonth,
      nextMonth,
      isCurrentDay,
    };
  },
};
</script>

<style scoped>
.calendar-page {
  padding: 20px;
  font-family: Arial, sans-serif;
}

.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-block-end: 20px;
}

.actions button {
  border: 1px solid #ccc;
  border-radius: 4px;
  background-color: #8c57ff;
  color: white;
  cursor: pointer;
  margin-inline-start: 10px;
  padding-block: 10px;
  padding-inline: 20px;
}

.actions button:hover {
  background-color: #6a43d6;
}

.calendar-container {
  display: flex;
  justify-content: center;
  margin-block-start: 20px;
}

.calendar {
  display: flex;
  flex-direction: column;
  inline-size: 100%;
}

.calendar-header {
  display: flex;
  justify-content: space-between;
  padding: 10px;
  border-radius: 4px;
  background-color: #8c57ff;
  color: white;
}

.calendar-days {
  display: grid;
  gap: 5px;
  grid-template-columns: repeat(7, 1fr);
  margin-block-start: 20px;
}

.calendar-day {
  position: relative;
  padding: 50px;
  border-radius: 4px;
  background-color: #f0f0f0;
  cursor: pointer;
  font-size: large;
  text-align: center;
}

.calendar-day:hover {
  background-color: #ddd;
}

.current-day {
  background-color: #8c57ff;
  color: white;
  font-weight: bold;
}

.event {
  padding: 5px;
  border-radius: 4px;
  background-color: #8c57ff;
  color: white;
  font-size: 12px;
  margin-block-start: 5px;
}

.task_modal,
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
