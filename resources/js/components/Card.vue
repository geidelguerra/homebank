<template>
  <Component
    v-bind="$attrs"
    :is="tag"
    class="transition-all bg-white p-4 shadow-md shadow-violet-900/5 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2"
  >
    <header
      v-if="title"
      class="mb-2"
    >
      <h3 class="text-lg font-light">
        {{ title }}
      </h3>
    </header>
    <main>
      <slot />
    </main>
  </Component>
</template>

<script setup>
const props = defineProps({
  tag: { type: String, default: 'div' },
  title: { type: String, default: null }
})
</script>