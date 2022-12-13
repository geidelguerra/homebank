<template>
  <button
    v-bind="$attrs"
    class="px-4 py-2 bg-indigo-500 text-white text-sm rounded transition-all hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
    :class="{
      'pointer-events-none opacity-50': disabled
    }"
  >
    <slot />
  </button>
</template>

<script setup>
const props = defineProps({
  disabled: Boolean
})
</script>