<template>
  <div>
    <label>
      <div class="text-sm font-medium mb-1 text-slate-600">{{ label }}</div>
      <slot />
    </label>
    <div
      v-show="error"
      class="text-red-500 text-xs"
    >
      {{ error }}
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  label: { type: String, default: null },
  error: { type: String, default: null }
})
</script>