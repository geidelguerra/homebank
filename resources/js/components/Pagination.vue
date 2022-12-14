<template>
  <div class="flex space-x-2">
    <template
      v-for="link in links"
      :key="link.label"
    >
      <Button
        :disabled="link.active || link.url === null"
        @click="$inertia.visit(link.url)"
      >
        <span v-html="link.label" />
      </Button>
    </template>
  </div>
</template>

<script setup>
import Button from './Button.vue';

defineProps({
  links: { type: Array, default: () => [] }
})
</script>