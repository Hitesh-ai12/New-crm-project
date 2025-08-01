<template>
  <div class="container-fluid">
    <div class="row">
      
      <!-- Sidebar Cards -->
      <div class="col-md-3">

        <!-- Leads Card -->
        <div class="card-box leads">
          <div class="card-title"><i class="bi bi-people-fill me-1"></i> Leads</div>
          <div class="d-flex justify-content-between">
            <div class="inner-card">
              <span>Today</span>
              <strong>{{ leads.today }}</strong>
            </div>
            <div class="inner-card">
              <span>Active</span>
              <strong>{{ leads.active }}</strong>
            </div>
          </div>
        </div>

        <!-- Action Plan Card -->
        <div class="card-box action-plan">
          <div class="card-title"><i class="bi bi-stickies-fill me-1"></i> Action Plan</div>
          <div class="d-flex justify-content-start">
            <div class="inner-card w-100">
              <span>Active</span>
              <strong>{{ actionPlan.active }}</strong>
            </div>
          </div>
        </div>

        <!-- Inbox Card -->
        <div class="card-box inbox">
          <div class="card-title"><i class="bi bi-inbox-fill me-1"></i> Inbox</div>
          <div class="d-flex justify-content-between">
            <div class="inner-card">
              <span>E-Mail</span>
              <strong> : {{ inbox.email }}</strong>
            </div>
            <div class="inner-card">
              <span>SMS</span>
              <strong> : {{ inbox.sms }}</strong>
            </div>
          </div>
        </div>

        <!-- Today's Events Card -->
        <div class="card-box todays-events">
          <div class="card-title"><i class="bi bi-calendar-event-fill me-1"></i> Todays Events</div>
          <div class="d-flex justify-content-between">
            <div class="inner-card">
              <span>Tasks</span>
              <strong> : {{ todaysEvents.tasks }}</strong>
            </div>
            <div class="inner-card">
              <span>Appointments</span>
              <strong> : {{ todaysEvents.appointments }}</strong>
            </div>
          </div>
        </div>

      </div>

      <!-- Main Content with Image Slider -->
      <div class="col-md-9">
        <div id="dashboardCarousel" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner rounded shadow">
            <div class="carousel-item active">
              <img src="/storage/attachments/CSt6OyZ1AYP7WsDCGoqr3nLsrGPlffUNUzfbNeel.jpg" class="d-block w-100" alt="Ad 1">
            </div>
            <div class="carousel-item">
              <img src="/storage/attachments/CSt6OyZ1AYP7WsDCGoqr3nLsrGPlffUNUzfbNeel.jpg" class="d-block w-100" alt="Ad 2">
            </div>
            <div class="carousel-item">
              <img src="/storage/attachments/CSt6OyZ1AYP7WsDCGoqr3nLsrGPlffUNUzfbNeel.jpg" class="d-block w-100" alt="Ad 3">
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'

import 'bootstrap'
import 'bootstrap/dist/css/bootstrap.min.css'

const leads = { today: 0, active: 3 }
const actionPlan = { active: 0 }
const inbox = ref({ sms: 0, email: 0 });


const todaysEvents = ref({ tasks: 0, appointments: 0 });

const fetchTodayCounts = async () => {
  try {
    const response = await fetch('/api/events/today-counts', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
      }
    });

    const result = await response.json();

    todaysEvents.value.tasks = result.tasks;
    todaysEvents.value.appointments = result.appointments;

  } catch (error) {
    console.error('❌ Failed to fetch today\'s event counts:', error);
  }
};

const fetchInboxCounts = async () => {
  try {
    const response = await fetch('/api/dashboard/message-counts', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
      }
    });

    const result = await response.json();
    inbox.value.sms = result.sms;
    inbox.value.email = result.email;
    
  } catch (error) {
    console.error("❌ Failed to fetch inbox counts:", error);
  }
};
onMounted(() => {
  fetchTodayCounts();
  fetchInboxCounts();
});

</script>

<style scoped>
.card-box {
  padding: 15px;
  border-radius: 15px;
  color: white;
  margin-block-end: 15px;
}

.card-title {
  font-size: 1.2rem;
  font-weight: 600;
  margin-block-end: 10px;
}

.inner-card {
  border: 1px solid rgba(255, 255, 255, 40%);
  border-radius: 10px;
  padding-block: 8px;
  padding-inline: 12px;
  text-align: center;
}

.inner-card span {
  font-size: 0.9rem;
}

.inner-card strong {
  font-size: 1.1rem;
}

.leads {
  background-color: #f44336;
}

.action-plan {
  background-color: #673ab7;
}

.inbox {
  background-color: #009688;
}

.todays-events {
  background-color: #ff9800;
}

.col-md-3 {
  inline-size: max-content;
}
</style>
