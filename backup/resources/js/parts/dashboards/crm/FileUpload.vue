<template>
    <div>
      <input type="file" @change="handleFileUpload" />
      <button @click="submitCsvFile">Upload</button>
      <p v-if="fileError">{{ fileError }}</p>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      fileError: String
    },
    methods: {
      handleFileUpload(event) {
        this.$emit('file-upload', event.target.files[0]);
      },
      submitCsvFile() {
        this.$emit('submit-csv-file');
      }
    }
  };
  </script>
  