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
          <div class="form-group">
            <label for="role">Role</label>
            <select id="role" v-model="form.role" required>
              <option value="superadmin">Superadmin</option>
              <option value="admin">Admin</option>
              <option value="user">User</option>
              <option value="custom">Custom</option>
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
import axios from 'axios';
import Swal from 'sweetalert2'; // Import SweetAlert2

export default {
  data() {
    return {
      form: {
        first_name: '',
        email1: '',
        email2: '',
        email3: '',
        phone1: '',
        phone2: '',
        phone3: '',
        password: '',
        role: ''
      }
    };
  },
  methods: {
    async handleSubmit() {
      try {
        const response = await axios.post('/api/form-submit', this.form);
        console.log('Form submitted successfully', response.data);
        
        // Show SweetAlert on success
        Swal.fire({
          icon: 'success',
          title: 'Form submitted successfully!',
          text: 'Your data has been successfully saved.',
        });
      } catch (error) {
        console.error('There was an error submitting the form:', error);
        
        // Show SweetAlert on error
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'There was an error submitting the form. Please try again.',
        });
      }
    }
  }
};
</script>


<style scoped>
.profile-image {
  border-radius: 50%;
  block-size: 100px;
  inline-size: 100px;
  margin-block-end: 10px;
  object-fit: cover;
}

.container {
  display: flex;
  margin: 20px;
  gap: 20px;
}

.card {
  padding: 75px;
  border-radius: 8px;
  background-color: #f9f9f9;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 10%);
  inline-size: 500px;
}

.card h3 {
  margin-block-end: 15px;
  text-align: center;
}

.card p {
  font-size: 14px;
  margin-block-end: 10px;
}

.card a {
  color: #06c;
}

.form-container {
  flex-grow: 1;
  padding: 20px;
  border-radius: 8px;
  background-color: #f9f9f9;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 10%);
}

.form-group {
  margin-block-end: 15px;
}

label {
  display: block;
  font-size: 14px;
  margin-block-end: 5px;
}

input,
select {
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 14px;
  inline-size: 100%;
}

button {
  padding: 10px;
  border: none;
  border-radius: 4px;
  background-color: #a16efd;
  color: white;
  cursor: pointer;
  font-size: 16px;
  inline-size: 18%;
}

button:hover {
  background-color: #8156d1;
}

/* Two-column layout for the form */
.form-grid {
  display: grid;
  gap: 20px;
  grid-template-columns: repeat(2, 1fr);
}

.form-group {
  display: flex;
  flex-direction: column;
}
</style>
