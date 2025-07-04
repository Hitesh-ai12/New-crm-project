<template>
  <div class="email-verify-page container py-5">
    <div class="card mx-auto" style="max-inline-size: 500px;">
      <div class="card-body">
        <h4 class="card-title text-center mb-4">Verify Your Gmail</h4>
        <form @submit.prevent="handleAuth">
          <div class="mb-3">
            <label class="form-label">Enter your Gmail</label>
            <input
              v-model="email"
              type="email"
              class="form-control"
              placeholder="you@gmail.com"
              required
            />
          </div>

          <button type="submit" class="btn btn-primary w-100">
            Grant Gmail Access
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const email = ref('')

const handleAuth = () => {
  if (!email.value.includes('@gmail.com')) {
    alert('Please enter a valid Gmail address')
    return
  }

  localStorage.setItem('user_gmail', email.value)

  window.location.href = '/gmail/auth'
}
</script>
