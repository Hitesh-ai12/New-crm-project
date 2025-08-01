
<template>
  <div class="loader"></div>
</template>

<script setup>

</script>

<style scoped>
.loader {
  display: grid;
  aspect-ratio: 1;
  inline-size: 50px;
}

.loader::before,
.loader::after {
  animation: l12 1s infinite;
  background:
    var(--c) 50% 0,
    var(--c) 50% 100%,
    var(--c) 100% 50%,
    var(--c) 0 50%;
  background-size: 12px 12px;
  content: "";
  grid-area: 1/1;

  --c: no-repeat radial-gradient(farthest-side, #25b09b 92%, #0000);
}

.loader::before {
  margin: 4px;
  animation-timing-function: linear;
  background-size: 8px 8px;
  filter: hue-rotate(45deg);
}

@keyframes l12 {
  100% {
    transform: rotate(0.5turn);
  }
}
</style>
