<template>
  <Component
    v-bind="$attrs"
    :is="tag"
    class="relative transition-all bg-white p-4 shadow-md shadow-violet-900/5 rounded-lg overflow-hidden focus:outline-none focus:ring-2 focus:ring-offset-2"
  >
    <main>
      <slot />
    </main>
  </Component>
</template>

<script setup>
const props = defineProps({
  tag: { type: String, default: 'div' }
})
</script>