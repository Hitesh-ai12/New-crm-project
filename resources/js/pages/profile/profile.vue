<template>
  <div class="container">
    <!-- Data Card on Left -->
    <div class="card">
      <img src="https://supervisa4u.com/wp-content/uploads/2024/07/form-pic.png" alt="Profile Image" class="profile-image">
      <h3>SuperVisa4u</h3>
      <p><strong>Company Name:</strong> SuperVisa4u</p>
      <p><strong>Website:</strong> <a href="https://supervisa4u.com" target="_blank">supervisa4u.com</a></p>
      <p><strong>Email:</strong> <a href="mailto:info@supervisa4u.com">info@supervisa4u.com</a></p>
      <p><strong>Revenue:</strong> 0/5000 per month</p>
      <p><strong>Alternate Email:</strong> <a href="mailto:info@Supervisa4u.com">info@Supervisa4u.com</a></p>
      <p><strong>Contact Email:</strong> <a href="mailto:jazz.chaggar@gmail.com">jazz.chaggar@gmail.com</a></p>
      <p><strong>Phone:</strong> +18737102623</p>
      <p><strong>Revenue:</strong> 0/500 per month</p>
      <p><strong>Additional Phone:</strong> 4165531010</p>
    </div>

    <div class="form-container">
      <form @submit.prevent="handleSubmit">
        <div class="form-grid">
          
          <!-- First Name -->
          <div class="form-group">
            <label for="first_name">First Name <span style="color: red;">*</span></label>
            <input type="text" id="first_name" v-model="form.first_name" required />
          </div>

          <!-- Email Fields -->
          <div class="form-group">
            <label for="email1">Email 1 <span style="color: red;">*</span></label>
            <input type="email" id="email1" v-model="form.email1" required />
          </div>

          <div class="form-group">
            <label for="email2">Email 2</label>
            <input type="email" id="email2" v-model="form.email2" />
          </div>

          <div class="form-group">
            <label for="email3">Email 3</label>
            <input type="email" id="email3" v-model="form.email3" />
          </div>

          <!-- Phone Number Fields -->
          <div class="form-group">
            <label for="phone1">Phone 1 <span style="color: red;">*</span></label>
            <input type="tel" id="phone1" v-model="form.phone1" required />
          </div>

          <div class="form-group">
            <label for="phone2">Phone 2</label>
            <input type="tel" id="phone2" v-model="form.phone2" />
          </div>

          <div class="form-group">
            <label for="phone3">Phone 3</label>
            <input type="tel" id="phone3" v-model="form.phone3" />
          </div>

          <!-- Password Field -->
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" v-model="form.password" required />
          </div>

        <!-- Role Field -->
          <div class="form-group" v-if="showRoleField">
            <label for="role">Role</label>
            <select id="role" v-model="form.role" required>
              <option v-for="role in filteredRoles" :key="role.value" :value="role.value">
                {{ role.label }}
              </option>
            </select>
          </div>
        </div>
        <!-- Submit Button -->
        <button type="submit">Submit</button>
      </form>
    </div>
  </div>
</template>

<script>
import '@/assets/css/profile/profile.css';
import ProfileScript from "@/assets/js/profile/profile.js";

export default {
  ...ProfileScript, 
};
</script>
