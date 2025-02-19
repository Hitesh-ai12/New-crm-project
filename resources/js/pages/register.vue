<script setup>
import logo from '@images/logo.svg?raw';
import authV1MaskDark from '@images/pages/auth-v1-mask-dark.png';
import authV1MaskLight from '@images/pages/auth-v1-mask-light.png';
import authV1Tree2 from '@images/pages/auth-v1-tree-2.png';
import authV1Tree from '@images/pages/auth-v1-tree.png';
import axios from 'axios';
import { computed, ref } from 'vue';
import { useTheme } from 'vuetify';

// Form data and state
const form = ref({
  email: '',
  otp: ''
});

const vuetifyTheme = useTheme();
const authThemeMask = computed(() => {
  return vuetifyTheme.global.name.value === 'light' ? authV1MaskLight : authV1MaskDark;
});

const isOtpSent = ref(false);
const isOtpVerified = ref(false);

// Send OTP
const sendOtp = async () => {
  try {
  
    const response = await axios.post('/api/send-otp', { email: form.value.email });
    if (response.data.success) {
      isOtpSent.value = true;
      alert('OTP sent to your email!');
    } else {
      alert('Failed to send OTP. Please try again.');
    }
  } catch (error) {
    console.error('Error sending OTP:', error);
    alert('Failed to send OTP. Please try again.');
  }
};

// Verify OTP
const verifyOtp = async () => {
  try {

    const response = await axios.post('/api/verify-otp', { email: form.value.email, otp: form.value.otp });
    if (response.data.success) {
      isOtpVerified.value = true;
      await axios.post('/api/send-password', { email: form.value.email });
      alert('Password has been sent to your email!');
    } else {
      alert('Invalid OTP. Please try again.');
    }
  } catch (error) {
    console.error('Error verifying OTP:', error);
    alert('Failed to verify OTP. Please try again.');
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
            Materio
          </h2>
        </RouterLink>
      </VCardItem>

      <VCardText class="pt-2">
        <h4 class="text-h4 mb-1">Adventure starts here 🚀</h4>
        <p class="mb-0">Enter your email to receive an OTP!</p>
      </VCardText>

      <VCardText>
        <VForm @submit.prevent="isOtpSent ? verifyOtp() : sendOtp()">
          <VRow>
            <!-- Email Field -->
            <VCol cols="12">
              <VTextField
                v-model="form.email"
                label="Email"
                type="email"
                :disabled="isOtpSent"
                required
              />
            </VCol>

            <!-- OTP Field -->
            <VCol cols="12" v-if="isOtpSent && !isOtpVerified">
              <VTextField
                v-model="form.otp"
                label="OTP"
                placeholder="Enter the OTP"
                required
              />
            </VCol>

            <!-- Button for Sending OTP or Verifying OTP -->
            <VCol cols="12">
              <VBtn block type="submit">
                {{ isOtpSent ? 'Verify OTP' : 'Send OTP' }}
              </VBtn>
            </VCol>

            <!-- Success Message -->
            <VCol cols="12" v-if="isOtpVerified" class="text-center text-base">
              <span>Password has been sent to your email!</span>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>
    </VCard>

    <!-- Background Images -->
    <VImg class="auth-footer-start-tree d-none d-md-block" :src="authV1Tree" :width="250" />
    <VImg :src="authV1Tree2" class="auth-footer-end-tree d-none d-md-block" :width="350" />
    <VImg class="auth-footer-mask d-none d-md-block" :src="authThemeMask" />
  </div>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth";
</style>
