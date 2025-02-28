<template>
    <div>
        <input
      :value="searchQuery"
      @input="onInputChange"
      placeholder="Search Leads"
    />
      <div v-if="loading">Loading...</div>
      <div v-else>
        <table>
          <tr>
            <th><input type="checkbox" @change="toggleSelectAll" /></th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Tag</th>
            <th>Stage</th>
          </tr>
          <tr v-for="lead in paginatedLeads" :key="lead.id">
            <td><input type="checkbox" :value="lead.id" v-model="selectedLeads" /></td>
            <td>{{ lead.first_name }}</td>
            <td>{{ lead.last_name }}</td>
            <td>{{ lead.email }}</td>
            <td>{{ lead.phone }}</td>
            <td>{{ getTagValue(lead.tag) }}</td>
            <td>{{ getStageValue(lead.stage) }}</td>
          </tr>
        </table>
        <button @click="deleteLeads">Delete</button>
        <button @click="exportLeads">Export</button>
        <pagination :current-page="currentPage" :total-pages="totalPages" @change-page="changePage" @set-page-size="setPageSize" />
      </div>
    </div>
  </template>
  
  <script>
  import Pagination from './Pagination.vue';
  
  export default {
    components: {
      Pagination
    },
    props: {
      leads: Array,
      loading: Boolean,
      searchQuery: String,
      paginatedLeads: Array,
      selectedLeads: Array,
      currentPage: Number,
      pageSize: Number,
      totalPages: Number
    },
    methods: {
      toggleSelectAll(event) {
        this.$emit('toggle-select-all', event.target);
      },
      deleteLeads() {
        this.$emit('delete-leads');
      },
      exportLeads() {
        this.$emit('export-leads');
      },
      getTagValue(id) {
        this.$emit('get-tag-value', id);
      },
      getStageValue(id) {
        this.$emit('get-stage-value', id);
      }
    }
  };
  </script>
  