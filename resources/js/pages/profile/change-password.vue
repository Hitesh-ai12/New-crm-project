<template>
  <div class="form-container">
    <h2>Reset Password</h2>
    <form @submit.prevent="handleSubmit" class="password-reset-form">
      <!-- Current Password Field -->
      <div class="form-group">
        <label for="current_password">Current Password <span class="required">*</span></label>
        <input type="password" id="current_password" v-model="form.current_password" required />
      </div>

      <!-- New Password Field -->
      <div class="form-group">
        <label for="new_password">New Password <span class="required">*</span></label>
        <input type="password" id="new_password" v-model="form.new_password" required />
      </div>

      <!-- Confirm New Password Field -->
      <div class="form-group">
        <label for="new_password_confirmation">Confirm New Password <span class="required">*</span></label>
        <input
          type="password"
          id="new_password_confirmation"
          v-model="form.new_password_confirmation"
          required
        />
        <!-- Show password mismatch message -->
        <div v-if="!passwordsMatch && form.new_password_confirmation" class="error-message">
          <span>Passwords do not match</span>
        </div>
      </div>

      <!-- Submit Button -->
      <button type="submit" class="submit-btn" :disabled="!passwordsMatch">Submit</button>
    </form>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
export default {
  data() {
    return {
      form: {
        current_password: '',
        new_password: '',
        new_password_confirmation: '',
      },
    };
  },
  computed: {
    passwordsMatch() {
      // Check if the new password matches the confirmation
      return this.form.new_password === this.form.new_password_confirmation;
    },
  },
  methods: {
    async handleSubmit() {
      // Check if new password and confirm password match
      if (!this.passwordsMatch) {
        Swal.fire({
          icon: 'error',
          title: 'Passwords do not match!',
          text: 'Please ensure that the new password and confirmation password are the same.',
        });
        return;
      }

      try {
        // Get the auth token from localStorage or sessionStorage
        const token = localStorage.getItem('auth_token'); 
        
        const response = await axios.post('/api/reset-password', this.form, {
          headers: {
            Authorization: `Bearer ${token}`, 
          },
        });
        console.log('Password reset successfully', response.data);

        // Show SweetAlert on success
        Swal.fire({
          icon: 'success',
          title: 'Password Reset Successful!',
          text: 'Your password has been successfully updated.',
        });
        this.form = {
          current_password: '',
          new_password: '',
          new_password_confirmation: '',
        };

        // Optionally, redirect to login page or another page
        this.$router.push('/login');
      } catch (error) {
        console.error('There was an error resetting the password:', error);

        // Check for error response from backend
        if (error.response) {
          const errorMessage =
            error.response.data.message ||
            'There was an error resetting your password. Please try again.';
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: errorMessage,
          });
        } else {
          // Handle errors
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An unexpected error occurred. Please try again later.',
          });
        }
      }
    },
  },
};
</script>
<style scoped>
/* Add styles for password mismatch message */
.error-message {
  color: red;
  font-size: 12px;
  margin-block-start: 5px;
}

.submit-btn:disabled {
  background-color: grey;
  cursor: not-allowed;
}

.form-container {
  padding: 20px;
  border-radius: 8px;
  background-color: #f9f9f9;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 10%);
  inline-size: 100%;
  margin-block: 0;
  margin-inline: auto;
  max-inline-size: 400px;
}

h2 {
  color: #333;
  font-size: 24px;
  margin-block-end: 20px;
  text-align: center;
}

.password-reset-form {
  display: flex;
  flex-direction: column;
}

.form-group {
  margin-block-end: 15px;
}

label {
  color: #555;
  font-size: 14px;
}

.required {
  color: red;
}

input[type="password"] {
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 16px;
  inline-size: 100%;
  margin-block-start: 5px;
}

input[type="password"]:focus {
  border-color: #a16efd;
  outline: none;
}

button.submit-btn {
  padding: 10px;
  border: none;
  border-radius: 4px;
  background-color: #a16efd;
  color: white;
  cursor: pointer;
  font-size: 16px;
}

button.submit-btn:hover {
  background-color: #9767f0;
}

button:disabled {
  background-color: #aaa;
}

.form-container[data-v-21b74a20] {
  padding: 20px;
  border-radius: 8px;
  background-color: #f9f9f9;
  box-shadow: 0 4px 8px #0000001a;
  inline-size: 100%;
  margin-block: 0;
  margin-inline: auto;
  max-inline-size: 800px;
}

button.submit-btn[data-v-21b74a20] {
  padding: 10px;
  border: none;
  border-radius: 4px;
  background-color: #a16efd;
  color: #fff;
  cursor: pointer;
  font-size: 16px;
  margin-block: 0;
  margin-inline: auto;
}
</style>
