<template>
  <input
    v-bind="$attrs"
    class="p-2 bg-white rounded text-sm font-medium transition-all border border-gray-400 w-full focus:outline-none focus:ring-indigo-500 focus:ring-2 focus:ring-offset-2"
    :value="modelValue"
    @input="event => emit('update:modelValue', event.target.value)"
  >
</template>

<script setup>
const props = defineProps({
  modelValue: { type: [String, Number], default: null }
})

const emit = defineEmits(['update:modelValue'])
</script>