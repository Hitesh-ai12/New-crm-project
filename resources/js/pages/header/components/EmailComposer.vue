<template>
  <div class="row">
    <div class="col-md-4 border-end">
      <div class="list-group">
        <button
          v-for="email in emailList"
          :key="email.id"
          class="list-group-item list-group-item-action"
          @click="selected = email"
        >
          {{ email.subject }}
        </button>
      </div>
    </div>

    <div class="col-md-8">
      <div v-if="selected" class="p-3 border rounded">
        <h5>{{ selected.subject }}</h5>
        <div class="text-muted small">{{ selected.time }}</div>
        <p>{{ selected.body }}</p>
      </div>
    </div>
    
  </div>
</template>
<script>
export default {
  name: "Email",
  data() {
    return {
      emailList: [
        { id: 1, subject: "Welcome Email", time: "10:00 AM" },
        { id: 2, subject: "Meeting Reminder", time: "12:30 PM" },
      ],
      emailBody: [
        { id: 1, subject: "Welcome Email", time: "10:00 AM", body: "Welcome to our service!" },
      ],
    };
  }
};
</script>
