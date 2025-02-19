<template>
    <form @submit.prevent="submitForm">
      <!-- Form fields for newLead -->
      <input v-model="newLead.first_name" placeholder="First Name" />
      <input v-model="newLead.last_name" placeholder="Last Name" />
      <input v-model="newLead.email" placeholder="Email" />
      <input v-model="newLead.phone" placeholder="Phone" />
      <select v-model="newLead.tag">
        <option v-for="tag in tags" :key="tag.id" :value="tag.id">{{ tag.name }}</option>
      </select>
      <select v-model="newLead.stage">
        <option v-for="stage in stages" :key="stage.id" :value="stage.id">{{ stage.name }}</option>
      </select>
      <button type="submit">Submit</button>
    </form>
  </template>
  
  <script>
  export default {
    props: {
      newLead: Object,
      tags: Array,
      stages: Array
    },
    methods: {
      submitForm() {
        this.$emit('submit');
      }
    }
  };
  </script>
  