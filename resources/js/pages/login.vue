<script setup>
import logo from '@images/logo.svg?raw';
import authV1MaskDark from '@images/pages/auth-v1-mask-dark.png';
import authV1MaskLight from '@images/pages/auth-v1-mask-light.png';

import axios from 'axios';
import Swal from 'sweetalert2'; // Import SweetAlert2
import { computed, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useTheme } from 'vuetify';

// Form data and state
const form = ref({
  email: '',
  password: '',
  remember: false,
});

const vuetifyTheme = useTheme();
const router = useRouter();

const authThemeMask = computed(() => {
  return vuetifyTheme.global.name.value === 'light' ? authV1MaskLight : authV1MaskDark;
});

const isPasswordVisible = ref(false);

// Handle login
const login = async () => {
  try {
    const response = await axios.post('/api/login', form.value);
    localStorage.setItem('auth_token', response.data.token);

    // Show success message using SweetAlert
    Swal.fire({
      icon: 'success',
      title: 'Login Successful',
      text: 'Redirecting to your dashboard...',
    });

    // Redirect to a default dashboard
    setTimeout(() => {
      router.push('/dashboard');
    }, 1500); // Delay to show success message before redirecting
  } catch (error) {
    console.error('Login failed:', error);

    // Extract error message from response
    const errorMessage = error.response?.data?.message || 'Login failed. Please check your email and password.';

    // Show error message using SweetAlert
    Swal.fire({
      icon: 'error',
      title: 'Login Failed',
      text: errorMessage,
    });
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

      <!--VCardText class="pt-2">
        <h4 class="text-h4 mb-1">Welcome to CRM! 👋🏻</h4>
        <p class="mb-0">
          Please sign-in to your account and start the adventure
        </p>
      </VCardText-->

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

            <!-- Login button -->
            <VCol cols="12">
              <VBtn block type="submit">Login</VBtn>
            </VCol>

            <!-- Create account link -->
            <!--VCol cols="12" class="text-center text-base">
              <span>New on our platform?</span>
              <RouterLink class="text-primary ms-2" to="/auth/register">
                Create an account
              </RouterLink>
            </VCol-->

            <!-- Divider -->
            <!--VCol cols="12" class="d-flex align-center">
              <VDivider />
              <span class="mx-4">or</span>
              <VDivider />
            </VCol-->
          </VRow>
        </VForm>
      </VCardText>
    </VCard>
  </div>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth";
</style>
