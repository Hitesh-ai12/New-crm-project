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
      permissions: [], // Selected permissions
      apiKey: null, // API Key generated from backend
      endpoints: [], // Endpoints generated for each permission
    };
  },
  methods: {
    async generateApiKey() {
      try {
        // Validate permissions selection
        if (this.permissions.length === 0) {
          alert("Please select at least one permission.");
          return;
        }

        // Fetch the auth token from localStorage
        const authToken = localStorage.getItem("auth_token");
        if (!authToken) {
          alert("You are not authenticated. Please log in.");
          return;
        }

        // Make the POST request to generate an API key
        const response = await axios.post(
          "/api/generate-api-key",
          {
            permissions: this.permissions, // Send selected permissions to the backend
          },
          {
            headers: {
              Authorization: `Bearer ${authToken}`, // Use login token for authentication
            },
          }
        );

        // Extract the API key and endpoints from the response
        const { api_key, endpoints } = response.data;

        // Update the component state and store the API key in localStorage
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
