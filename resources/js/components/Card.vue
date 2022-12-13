<template>
  <Component
    v-bind="$attrs"
    :is="tag"
    class="relative bg-white p-4 shadow-md shadow-violet-900/5 rounded-lg overflow-hidden"
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