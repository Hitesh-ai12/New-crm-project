<template>
  <div>
    <h2>{{ modalTitle }}</h2>
    <select :value="selectedOption" @change="updateSelectedOption($event.target.value)">
      <option v-for="option in modalOptions" :key="option.value" :value="option.value">
        {{ option.text }}
      </option>
    </select>
  </div>
</template>

<script>
export default {
  props: {
    selectedOption: String,
    modalOptions: Array,
    modalTitle: String
  },
  methods: {
    updateSelectedOption(value) {
      this.$emit('update:selectedOption', value);
    }
  }
};
</script>
