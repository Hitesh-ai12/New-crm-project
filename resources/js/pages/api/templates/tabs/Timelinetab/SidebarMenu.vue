<!-- components/SidebarMenu.vue -->
<template>
  <div class="sidebar me-4">
    <div
      class="menu-item"
      :class="{ active: modelValue === 'all' }"
      @click="$emit('update:modelValue', 'all')"
    >
      <div class="icon"><i class="fas fa-headset"></i></div>
      <div class="label">ALL</div>
    </div>
    <div
      class="menu-item"
      :class="{ active: modelValue === 'sms' }"
      @click="$emit('update:modelValue', 'sms')"
    >
      <div class="icon"><i class="fas fa-comment"></i></div>
      <div class="label">SMS</div>
    </div>
    <div
      class="menu-item"
      :class="{ active: modelValue === 'email' }"
      @click="$emit('update:modelValue', 'email')"
    >
      <div class="icon"><i class="fas fa-envelope"></i></div>
      <div class="label">Email</div>
    </div>
    <div
      class="menu-item"
      :class="{ active: modelValue === 'notes' }"
      @click="$emit('update:modelValue', 'notes')"
    >
      <div class="icon"><i class="fas fa-sticky-note"></i></div>
      <div class="label">Notes</div>
    </div>
    <div
      class="menu-item"
      :class="{ active: modelValue === 'callLog' }"
      @click="$emit('update:modelValue', 'callLog')"
    >
      <div class="icon"><i class="fas fa-phone"></i></div>
      <div class="label">Call Log</div>
    </div>
    <div
      class="menu-item"
      :class="{ active: modelValue === 'webActivity' }"
      @click="$emit('update:modelValue', 'webActivity')"
    >
      <div class="icon"><i class="fas fa-globe"></i></div>
      <div class="label">WEB Activity</div>
    </div>
  </div>
</template>

<script setup>
defineProps(['modelValue'])
defineEmits(['update:modelValue'])
</script>

<style scoped>
.menu-item {
  display: flex;
  align-items: center;
  padding: 8px;
  border-radius: 6px;
  cursor: pointer;
  margin-block-end: 6px;
}

.menu-item.active {
  background-color: #007bff;
  color: white;
}

.icon {
  margin-inline-end: 8px;
}
</style>
