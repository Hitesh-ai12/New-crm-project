<template>
  <div>
    <h2>Generate API Key</h2>

    <!-- Permissions Selector -->
    <div>
      <label>
        <input type="checkbox" v-model="permissions" value="create" /> Create
      </label>
      <label>
        <input type="checkbox" v-model="permissions" value="read" /> Read
      </label>
      <label>
        <input type="checkbox" v-model="permissions" value="update" /> Update
      </label>
      <label>
        <input type="checkbox" v-model="permissions" value="delete" /> Delete
      </label>
    </div>

    <!-- Generate Token Button -->
    <button @click="generateApiKey">Generate API Key</button>

    <!-- Display Generated Token and Endpoint -->
    <div v-if="apiKey && endpoints.length > 0" class="output-section">
      <h3>Generated API Key</h3>
      <p><strong>API Key:</strong> {{ apiKey }}</p>
      <h3>Generated Endpoints</h3>
      <ul>
        <li v-for="(endpoint, index) in endpoints" :key="index">
          <strong>Endpoint:</strong> {{ endpoint }}
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      permissions: [], 
      apiKey: null, 
      endpoints: [], 
    };
  },
  methods: {
    async generateApiKey() {
      try {
       
        if (this.permissions.length === 0) {
          alert("Please select at least one permission.");
          return;
        }

      
        const authToken = localStorage.getItem("auth_token");
        if (!authToken) {
          alert("You are not authenticated. Please log in.");
          return;
        }


        const response = await axios.post(
          "/api/generate-api-key",
          {
            permissions: this.permissions, 
          },
          {
            headers: {
              Authorization: `Bearer ${authToken}`,
            },
          }
        );

        const { api_key, endpoints } = response.data;

        this.apiKey = api_key;
        this.endpoints = endpoints;
        localStorage.setItem("api_key", api_key);

        alert("API key generated successfully.");
      } catch (error) {
        console.error("Error generating API key:", error);
        alert("Failed to generate API key.");
      }
    },
  },
};
</script>


<style scoped>
.output-section {
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 5px;
  background-color: #f9f9f9;
  margin-block-start: 20px;
}

h2 {
  color: #333;
}

button {
  border: none;
  background-color: #007bff;
  color: white;
  cursor: pointer;
  margin-block-start: 10px;
  padding-block: 10px;
  padding-inline: 15px;
}

button:hover {
  background-color: #0056b3;
}

label {
  margin-inline-end: 15px;
}
</style>
