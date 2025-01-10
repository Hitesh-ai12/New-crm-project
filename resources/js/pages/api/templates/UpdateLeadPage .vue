<template>
  <div>
    <h2>Update Profile - Lead ID: {{ id }}</h2>
    <!-- Your form or profile update logic -->
  </div>
</template>

<script>
export default {
  props: ['id'],
  created() {
    console.log('Fetching lead data for id:', this.id);
    // You can use the id to fetch lead details
  }
}
</script>
