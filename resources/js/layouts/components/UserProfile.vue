<script setup>
import avatar1 from '@images/avatars/avatar-1.png';
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
  }
};

// Handle logout
const logout = () => {
  Swal.fire({
    title: 'Are you sure?',
    text: 'You will be logged out.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, logout',
    cancelButtonText: 'Cancel',
  }).then((result) => {
    if (result.isConfirmed) {
      localStorage.removeItem('auth_token');

      Swal.fire({
        icon: 'success',
        title: 'Logged Out',
        text: 'You have been logged out successfully.',
      });

      router.push('/login');
    }
  });
};
</script>

<template>
  <VBadge
    dot
    location="bottom right"
    offset-x="3"
    offset-y="3"
    color="success"
    bordered
  >
    <VAvatar
      class="cursor-pointer"
      color="primary"
      variant="tonal"
    >
      <VImg :src="avatar1" />

      <!-- SECTION Menu -->
      <VMenu
        activator="parent"
        width="230"
        location="bottom end"
        offset="14px"
      >
        <VList>
          <!-- 👉 User Avatar & Name -->
          <VListItem>
            <template #prepend>
              <VListItemAction start>
                <VBadge
                  dot
                  location="bottom right"
                  offset-x="3"
                  offset-y="3"
                  color="success"
                >
                  <VAvatar
                    color="primary"
                    variant="tonal"
                  >
                    <VImg :src="avatar1" />
                  </VAvatar>
                </VBadge>
              </VListItemAction>
            </template>

            <VListItemTitle class="font-weight-semibold">
              John Doe
            </VListItemTitle>
            <VListItemSubtitle>Admin</VListItemSubtitle>
          </VListItem>
          <VDivider class="my-2" />

        <!-- 👉 Profile -->
        <VListItem :to="{ name: 'admin-my-profile' }">
          <template #prepend>
            <VIcon
              class="me-2"
              icon="ri-user-line"
              size="22"
            />
          </template>

          <VListItemTitle>Profile</VListItemTitle>
        </VListItem>

          <!-- 👉 Settings -->
          <VListItem link>
            <template #prepend>
              <VIcon
                class="me-2"
                icon="ri-settings-4-line"
                size="22"
              />
            </template>

            <VListItemTitle>Settings</VListItemTitle>
          </VListItem>

          <!-- 👉 Change passwrod -->
          <VListItem :to="{ name: 'changePassword' }">
            <template #prepend>
              <VIcon
                class="me-2"
                icon="ri-money-dollar-circle-line"
                size="22"
              />
            </template>

            <VListItemTitle>Change Password</VListItemTitle>
          </VListItem>
          <!-- 👉 Change passwrod -->
          <!-- <VListItem :to="{ name: 'setsignature' }">
            <template #prepend>
              <VIcon
                class="me-2"
                icon="ri-money-dollar-circle-line"
                size="22"
              />
            </template> -->

            <!-- <VListItemTitle>Email Signature</VListItemTitle>
          </VListItem> -->
          <!-- 👉 FAQ -->
          <VListItem :to="{ name: 'authentication' }">
            <template #prepend>
              <VIcon
                class="me-2"
                icon="ri-question-line"
                size="22"
              />
            </template>

            <VListItemTitle>Authentication</VListItemTitle>
          </VListItem>

          <!-- Divider -->
          <VDivider class="my-2" />

          <!-- 👉 Logout -->
          <VListItem @click="logout">
            <template #prepend>
              <VIcon
                class="me-2"
                icon="ri-logout-box-r-line"
                size="22"
              />
            </template>

            <VListItemTitle>Logout</VListItemTitle>
          </VListItem>
        </VList>
      </VMenu>
      <!-- !SECTION -->
    </VAvatar>
  </VBadge>
</template>

