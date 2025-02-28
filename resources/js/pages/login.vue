<script setup>

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
  <div class="auth-wrapper d-flex align-center justify-center pa-4 user_login_form">
    <VCard class="auth-card pa-4 pt-7 login_v_card" max-width="448">
      <VCardItem class="justify-center login_v_item">
        <RouterLink to="/" class="d-flex align-center gap-3">
          <a href="/build/" class="d-flex align-center gap-3"><img src="https://jsithub.com/wp-content/uploads/2024/03/Logo-V2-Final.png"></a>
        </RouterLink>
      </VCardItem>

      <VCardText>
   
        <VForm @submit.prevent="login">
          <div class="title-container">
        
          </div>
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

            <VCol cols="12 login_form_btn">
              <VBtn :loading="isLoading" block type="submit" color="primary" >
                Login
              </VBtn>
            </VCol>

            <div class="social-container">
              <a href="https://www.facebook.com/profile.php?id=61558514121185" class="social" target="_blank"><i class="fab fa-facebook-f"></i></a>
              <a href="https://www.instagram.com/jsithub_private_limited/" class="social" target="_blank"><i class="fab fa-instagram"></i></a>
              <a href="https://www.linkedin.com/company/103449402/admin/feed/posts/?feedType=following" class="social" target="_blank"><i class="fab fa-linkedin-in"></i></a>
            </div>
          </VRow>
        </VForm>
      </VCardText>
    </VCard>
  </div>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth";

.auth-card {
  display: flex !important;
  justify-content: center !important;
  padding: 20px !important;
  border-radius: 10px !important;
  background-color: #f4f5fa !important;
  block-size: 450px !important;
  box-shadow: 0 4px 23px rgba(0, 0, 0, 25%) !important;
  inline-size: 100% !important;
  max-inline-size: 1080px !important;
}

.v-card-item {
  display: flex !important;
  align-items: center !important;
  inline-size: 50% !important;
  text-align: center !important;
}

.v-card .v-card-text {
  position: relative !important;
  display: flex !important;
  align-items: center !important;
  padding: 50px !important;
  inline-size: 50% !important;
  margin-block-end: 0 !important;
  text-align: center !important;
}

.v-col-12:nth-child(3) {
  display: flex !important;
  justify-content: center !important;
}

.v-col-12:nth-child(3) .v-btn--density-default {
  max-inline-size: 160px !important;
  min-inline-size: 160px !important;
}

.v-col-12:nth-child(2) .my-6 {
  margin-block-end: 0 !important;
}

.v-card-item__content img {
  inline-size: 100% !important;
  margin-block: 0 !important;
  margin-inline: auto !important;
  max-inline-size: 250px !important;
}

.social-container {
  inline-size: 100% !important;
  padding-block-start: 25px !important;
}

.social-container a {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  border-radius: 50% !important;
  block-size: 40px !important;
  box-shadow: 0 0 15px 1px #cbcbcb8c !important;
  color: #8c57ff !important;
  font-size: 14px !important;
  inline-size: 40px !important;
  margin-block: 0 !important;
  margin-inline: 5px !important;
  text-decoration: none !important;
  transition: 0.4s ease !important;
}

.title-container h2 {
  color: #202020 !important;
  font-size: 40px !important;
  font-weight: 700 !important;
  margin-block-end: 10px !important;
  text-align: center !important;
}

.title-container p {
  font-size: 16px !important;
  line-height: 1.5 !important;
  margin-block-end: 0 !important;
  padding-block: 10px 0 !important;
  padding-inline: 12px !important;
  text-align: center !important;
}

.swal2-popup {
  position: relative;
  display: none;
  box-sizing: border-box;
  padding: 30px;
  border: none;
  border-radius: 5px;
  background: #fff;
  color: #545454;
  font-family: inherit;
  font-size: 1rem;
  grid-template-columns: minmax(0, 100%);
  inline-size: 32em;
  max-inline-size: 100%;
}

.swal2-icon {
  position: relative;
  box-sizing: content-box;
  justify-content: center;
  border: 0.25em solid rgba(0, 0, 0, 0%);
  border-radius: 50%;
  block-size: 5em;
  cursor: default;
  font-family: inherit;
  inline-size: 5em;
  line-height: 5em;
  margin-block: 10px;
  margin-inline: auto;
  user-select: none;
}

.swal2-title {
  position: relative;
  padding: 0;
  margin: 0;
  color: inherit;
  font-size: 1.875em;
  font-weight: 600;
  max-inline-size: 100%;
  text-align: center;
  text-transform: none;
  word-wrap: break-word;
}

.swal2-html-container {
  z-index: 1;
  overflow: auto;
  justify-content: center;
  margin: 0;
  color: inherit;
  font-size: 1.125em;
  font-weight: normal;
  line-height: normal;
  padding-block: 10px;
  padding-inline: 0;
  text-align: center;
  word-break: break-word;
  word-wrap: break-word;
}

.swal2-actions {
  z-index: 1;
  display: flex;
  box-sizing: border-box;
  flex-wrap: wrap;
  align-items: center;
  justify-content: center;
  padding: 0;
  margin: 0;
  inline-size: auto;
}

</style>
