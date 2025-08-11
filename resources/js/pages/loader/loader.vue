<template>
  <div class="table-loader-overlay">
    <div class="loader"></div>
    <span class="loader-text">
      <slot>Loading...</slot> <!-- Default text, but customizable -->
    </span>
  </div>
</template>

<script setup>
</script>

<style scoped>
.table-loader-overlay {
  position: absolute;
  z-index: 10;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(3px);
  background-color: rgba(255, 255, 255, 40%); /* optional dimming */
  block-size: 100%;
  inline-size: 100%;
  inset-block-start: 0;
  inset-inline-start: 0;
}

.loader-text {
  color: #333;
  font-weight: 500;
  margin-block-start: 8px;
}

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

