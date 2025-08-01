<template>
  <div class="calendar-page">
    <!-- Header Section -->
    <div class="header">
      <h1>Calendar</h1>
      <div class="actions">
        <button @click="showTaskModal = true">Add Task</button>
        <button @click="showAppointmentModal = true">Create Appointment</button>
      </div>
    </div>

    <!-- Calendar Container - Make sure this is properly structured -->
    <div class="calendar-container">
      <div class="calendar">
        <div class="calendar-header">
          <button @click="prevMonth">Prev</button>
          <span>{{ formattedMonth }} {{ currentYear }}</span>
          <button @click="nextMonth">Next</button>
        </div>
        <div class="weekdays">
          <div v-for="day in weekdays" :key="day" class="weekday">{{ day }}</div>
        </div>
        <div class="calendar-days">

        <div
          class="calendar-day"
          :class="{
            'current-day': isCurrentDay(day.date),
            'empty-day': day.isPadding
          }"
          v-for="(day, index) in daysInMonth"
          :key="index"
          @click="!day.isPadding && handleDateClick(day)"
        >
          <span v-if="!day.isPadding">{{ day.date }}</span>

        <div 
          v-for="event in day.events" 
          :key="event.id"
          class="event"
          @click.stop="openTaskDetail(event)"
        >
          <div class="fw-bold">{{ event.lead_name }}</div>
          <div class="text-muted small"> {{ event.title }}</div>
        </div>

        </div>
      </div>
    </div>
  </div>

    <!-- Modals -->
    <TaskDetailModal 
      v-if="selectedTask" 
      :task="selectedTask" 
      @close="selectedTask = null" 
    />

    <TaskModal
      v-if="showTaskModal"
      :leads="leads"
      @close="showTaskModal = false"
      @save="addTask"
    />
    
    <AppointmentModal
      v-if="showAppointmentModal"
      :leads="leads"
      @close="showAppointmentModal = false"
      @save="addAppointment"
    />

  </div>
</template>


<script>
import { computed, onMounted, ref } from "vue";
import AppointmentModal from './components/calendarmodels/AppointmentModal.vue';
import TaskDetailModal from './components/calendarmodels/TaskDetailModal.vue';
import TaskModal from './components/calendarmodels/TaskModal.vue';


export default {
  components: {
    TaskModal,
    AppointmentModal,
    TaskDetailModal
  },
  setup() {
    const currentMonth = ref(new Date().getMonth());
    const currentYear = ref(new Date().getFullYear());
    const showTaskModal = ref(false);
    const showAppointmentModal = ref(false);
    const events = ref([]);
    const leads = ref([]);
    const weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    const selectedTask = ref(null);

    const openTaskDetail = (task) => {
      selectedTask.value = task;
    };
    const formattedMonth = computed(() => {
      const monthNames = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
      ];
      return monthNames[currentMonth.value];
    });

      const daysInMonth = computed(() => {
        const days = [];
        const firstDayOfMonth = new Date(currentYear.value, currentMonth.value, 1);
        const startDay = firstDayOfMonth.getDay(); 

        for (let i = 0; i < startDay; i++) {
          days.push({
            date: null,
            fullDate: null,
            events: [],
            isPadding: true
          });
        }

        const date = new Date(currentYear.value, currentMonth.value, 1);
        const month = date.getMonth();

        while (date.getMonth() === month) {
          const dayDate = new Date(date);
          days.push({
            date: dayDate.getDate(),
            fullDate: new Date(dayDate),
            events: events.value.filter(event => {
              const eventDate = new Date(event.due_date || event.date);
              return (
                eventDate.getFullYear() === dayDate.getFullYear() &&
                eventDate.getMonth() === dayDate.getMonth() &&
                eventDate.getDate() === dayDate.getDate()
              );
            }),
            isPadding: false
          });
          date.setDate(date.getDate() + 1);
        }

        return days;
      });


    const prevMonth = () => {
      if (currentMonth.value === 0) {
        currentMonth.value = 11;
        currentYear.value -= 1;
      } else {
        currentMonth.value -= 1;
      }
    };

    const nextMonth = () => {
      if (currentMonth.value === 11) {
        currentMonth.value = 0;
        currentYear.value += 1;
      } else {
        currentMonth.value += 1;
      }
    };

    const isCurrentDay = (day) => {
      const today = new Date();
      return (
        day === today.getDate() &&
        currentMonth.value === today.getMonth() &&
        currentYear.value === today.getFullYear()
      );
    };

    const handleDateClick = (day) => {
      console.log("📅 Clicked date:", day.fullDate);
    };

    const fetchLeads = async () => {
      try {
        const response = await fetch('/api/leads', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
          }
        });

        const data = await response.json();
        leads.value = data;

        // 🎯 Build event list from lead.tasks[]
        events.value = [];
        data.forEach(lead => {
          if (Array.isArray(lead.tasks)) {
            lead.tasks.forEach(task => {
              events.value.push({
                id: task.id,
                title: task.title,
                type: task.type,
                due_date: task.due_date,
                lead_id: lead.id,
                lead_name: `${lead.first_name}`
              });
            });
          }
        });
      } catch (error) {
        console.error('❌ Error fetching leads:', error);
      }
    };

    const addTask = async (taskData) => {
      try {
        const response = await fetch('/api/tasks', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json' 
          },
          body: JSON.stringify(taskData)
        });

        const contentType = response.headers.get("content-type");

        if (!contentType || !contentType.includes("application/json")) {
          throw new Error("❌ Server did not return JSON. Check auth or endpoint.");
        }

        const result = await response.json();

        console.log("✅ Task response:", result);

        if (result?.task) {
          const t = result.task;
          events.value.push({
            id: t.id,
            title: t.title,
            type: t.type,
            due_date: t.due_date, 
            lead_id: t.lead_id
          });
        } else {
          console.error('❌ Task creation failed:', result);
        }
      } catch (error) {
        console.error('❌ Error adding task:', error);
      }
    };

    const fetchAppointments = async () => {
      try {
        const response = await fetch('/api/appointments', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
          }
        });

        const appointmentData = await response.json();

        appointmentData.forEach(appt => {
          events.value.push({
            id: appt.id,
            title: appt.title,
            type: appt.type,
            due_date: appt.date,
            lead_id: appt.lead_id,
            lead_name: appt.lead_name
          });
        });

      } catch (error) {
        console.error('❌ Error fetching appointments:', error);
      }
    };

    const addAppointment = (appointment) => {
      events.value.push(appointment);
    };

    onMounted(() => {
      fetchLeads();
      fetchAppointments();
    });

    return {
      selectedTask,
      openTaskDetail,
      currentMonth,
      currentYear,
      showTaskModal,
      showAppointmentModal,
      events,
      leads,
      weekdays,
      formattedMonth,
      daysInMonth,
      prevMonth,
      nextMonth,
      isCurrentDay,
      handleDateClick,
      addTask,
      addAppointment
    };
  }
};
</script>



<style scoped>
.calendar {
  inline-size: 100%;
}

.calendar-days {
  display: grid;
  gap: 10px;
  grid-template-columns: repeat(7, 1fr);
}

.weekdays {
  display: grid;
  font-weight: bold;
  grid-template-columns: repeat(7, 1fr);
  margin-block: 10px;
  margin-inline: 0;
  text-align: center;
}

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

.calendar-header {
  display: flex;
  justify-content: space-between;
  padding: 10px;
  border-radius: 4px;
  background-color: #8c57ff;
  color: white;
}

.calendar-day {
  position: relative;
  padding: 50px;
  border-radius: 4px;
  background-color: #f0f0f0;
  block-size: 200px;
  cursor: pointer;
  font-size: large;
  overflow-x: auto;
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
</style>
