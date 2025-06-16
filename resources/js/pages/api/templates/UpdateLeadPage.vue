<template>
  <div class="lead-management container mt-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h2>{{ contact.first_name }}</h2>
        <span class="badge bg-primary me-2">{{ contact.stage }}</span>
        <span class="badge bg-secondary me-2">{{ contact.source }}</span>
        <span class="badge bg-info">{{ contact.customTag }}</span>
      </div>
      <div>
        <button class="btn btn-danger me-2"><i class="fas fa-microphone"></i></button>
        <button class="btn btn-danger"><i class="fas fa-times"></i></button>
      </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-3">
      <li class="nav-item">
        <a class="nav-link" :class="{ active: activeTab === 'timeline' }" @click="activeTab = 'timeline'">Timeline</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" :class="{ active: activeTab === 'profile' }" @click="activeTab = 'profile'">Profile</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" :class="{ active: activeTab === 'analytics' }" @click="activeTab = 'analytics'">Analytics</a>
      </li>
    </ul>

    <!-- Dynamic Component Loading -->
    <TimelineTab v-if="activeTab === 'timeline'" />
    <ProfileTab v-if="activeTab === 'profile'" />
    <AnalyticsTab v-if="activeTab === 'analytics'" />
  </div>
</template>

<script setup>
import 'bootstrap'
import 'bootstrap/dist/css/bootstrap.min.css'
import { ref } from 'vue'

import AnalyticsTab from './tabs/AnalyticsTab.vue'
import ProfileTab from './tabs/ProfileTab.vue'
import TimelineTab from './tabs/TimelineTab.vue'


const activeTab = ref('timeline')

const contact = {
  first_name: 'John',
  stage: 'Prospect',
  source: 'Website',
  customTag: 'VIP'
}
</script>
