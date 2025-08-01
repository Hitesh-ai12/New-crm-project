<template>
  <!-- Leads Table -->
  <div v-if="!loading && !error">
    <div>
      <label for="pageSize">Rows per page:</label>
      <select v-model="pageSize" @change="setPageSize(Number($event.target.value))">
        <option value="5">5</option>
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
      </select>
    </div>

    <!-- Display Table Only If There Are Leads -->
    <table id="leadsTable" class="table table-striped table-bordered leads-table">
      <thead>
        <tr>
          <th>First Name</th>
          <th>Last Name</th>
          <th>Phone</th>
          <th>Email</th>
          <th>Activity</th>
          <th>Created</th>
          <th>Tag</th>
          <th>Stage</th>
          <th>Source</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="lead in paginatedLeads" :key="lead.id">
          <td>{{ lead.first_name }}</td>
          <td>{{ lead.last_name }}</td>
          <td>{{ lead.phone }}</td>
          <td>{{ lead.email }}</td>
          <td>{{ lead.activity || 'N/A' }}</td>
          <td>{{ new Date(lead.created_at).toLocaleDateString() }}</td>
          <td>{{ lead.tag || 'N/A' }}</td>
          <td>{{ lead.stage || 'N/A' }}</td>
          <td>{{ lead.source || 'N/A' }}</td>
        </tr>
      </tbody>
    </table>

  </div>

  <!-- Error or Loading State -->
  <div v-if="loading">Loading leads...</div>
  <div v-if="error">{{ error }}</div>

  <!-- Toast Notification -->
  <div v-if="toast?.message" :class="`toast ${toast.type}`">
    {{ toast.message }}
    <button @click="toast.message = ''" class="toast-close-button">X</button>
  </div>

  <!-- Pagination Controls -->
  <div v-if="totalPages > 1">
    <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1">Previous</button>
    <span>Page {{ currentPage }} of {{ totalPages }}</span>
    <button @click="changePage(currentPage + 1)" :disabled="currentPage === totalPages">Next</button>
  </div>
</template>

<script lang="js">
import '@/assets/leads.css';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';

export default {
  name: 'LeadsPage',
  setup() {
    const leads = ref([]);
    const loading = ref(true);
    const error = ref('');
    const searchQuery = ref('');
    const selectedLeads = ref([]);
    const pageSize = ref(10);
    const currentPage = ref(1);
    const toast = ref({ message: '', type: '' });

    // Fetch Leads
    const fetchLeads = async () => {
      try {
        const response = await axios.get('/leads');
        console.log(response.data);
        leads.value = Array.isArray(response.data) ? response.data : [];
      } catch (err) {
        error.value = 'Failed to fetch leads.';
      } finally {
        loading.value = false;
      }
    };

    // Filters
    const filteredLeads = computed(() => {
      // Ensure leads is an array before filtering
      return Array.isArray(leads.value) ? leads.value.filter((lead) =>
        lead.first_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        lead.last_name.toLowerCase().includes(searchQuery.value.toLowerCase())
      ) : [];
    });

    const paginatedLeads = computed(() => {
      const start = (currentPage.value - 1) * pageSize.value;
      const end = start + pageSize.value;
      return filteredLeads.value.slice(start, end);
    });

    // Pagination Calculation
    const totalPages = computed(() => {
      const pages = Math.ceil(filteredLeads.value.length / pageSize.value);
      return pages > 0 ? pages : 1; 
    });

    const changePage = (page) => {
      if (page < 1 || page > totalPages.value) return;
      currentPage.value = page;
    };

    // Set Page Size
    const setPageSize = (size) => {
      pageSize.value = size;
      currentPage.value = 1; 
    };

    onMounted(() => {
      fetchLeads();
    });

    return {
      loading,
      error,
      leads,
      searchQuery,
      selectedLeads,
      pageSize,
      currentPage,
      totalPages,
      paginatedLeads,
      changePage,
      setPageSize,
      toast, 
    };
  }
};
</script>
