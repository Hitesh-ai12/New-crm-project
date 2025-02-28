<script setup>
import logo from '@images/logo.svg?raw';
import authV1MaskDark from '@images/pages/auth-v1-mask-dark.png';
import authV1MaskLight from '@images/pages/auth-v1-mask-light.png';

import axios from 'axios';
import Swal from 'sweetalert2';
import { computed, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useTheme } from 'vuetify';

// Form data and state
const form = ref({
  email: '',
  password: '',
  remember: false,
});

const isLoading = ref(false); // Loader state
const vuetifyTheme = useTheme();
const router = useRouter();

const authThemeMask = computed(() => {
  return vuetifyTheme.global.name.value === 'light' ? authV1MaskLight : authV1MaskDark;
});

const isPasswordVisible = ref(false);

// Handle login
const login = async () => {
  isLoading.value = true; // Start loader
  try {
    const response = await axios.post('/api/login', form.value);
    localStorage.setItem('auth_token', response.data.token);

    Swal.fire({
      icon: 'success',
      title: 'Login Successful',
      text: 'Redirecting to your dashboard...',
    });

    setTimeout(() => {
      router.push('/dashboard');
    }, 1500);
  } catch (error) {
    console.error('Login failed:', error);

    const errorMessage = error.response?.data?.message || 'Login failed. Please check your email and password.';

    Swal.fire({
      icon: 'error',
      title: 'Login Failed',
      text: errorMessage,
    });
  } finally {
    isLoading.value = false; // Stop loader
  }
};
</script>

<template>
  <div class="auth-wrapper d-flex align-center justify-center pa-4">
    <VCard class="auth-card pa-4 pt-7" max-width="448">
      <VCardItem class="justify-center">
        <RouterLink to="/" class="d-flex align-center gap-3">
          <div class="d-flex" v-html="logo" />
          <h2 class="font-weight-medium text-2xl text-uppercase">
            CRM
          </h2>
        </RouterLink>
      </VCardItem>

      <VCardText>
        <VForm @submit.prevent="login">
          <VRow>
            <!-- Email -->
            <VCol cols="12">
              <VTextField
                v-model="form.email"
                label="Email"
                type="email"
                required
              />
            </VCol>

            <!-- Password -->
            <VCol cols="12">
              <VTextField
                v-model="form.password"
                label="Password"
                placeholder="············"
                :type="isPasswordVisible ? 'text' : 'password'"
                :append-inner-icon="isPasswordVisible ? 'ri-eye-off-line' : 'ri-eye-line'"
                @click:append-inner="isPasswordVisible = !isPasswordVisible"
                required
              />
              <!-- Remember me checkbox -->
              <div class="d-flex align-center justify-space-between flex-wrap my-6">
                <VCheckbox v-model="form.remember" label="Remember me" />
                <a class="text-primary" href="javascript:void(0)">
                  Forgot Password?
                </a>
              </div>
            </VCol>

            <!-- Loader and Login button -->
            <VCol cols="12" class="d-flex align-center justify-center my-4">
              <VProgressCircular
                v-if="isLoading"
                indeterminate
                color="primary"
              />
            </VCol>
            <VCol cols="12">
              <VBtn :loading="isLoading" block type="submit" color="primary">
                Login
              </VBtn>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>
    </VCard>
  </div>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth";
</style>
