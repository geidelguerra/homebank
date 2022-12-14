<template>
  <button
    v-bind="$attrs"
    :type="type"
    class="px-4 py-2 text-sm rounded transition-all focus:outline-none focus:ring-2 focus:ring-offset-2"
    :class="{
      'bg-slate-100 hover:bg-slate-50 text-slate-800 focus:ring-indigo-500': color === 'default',
      'bg-green-600 hover:bg-green-500 text-white focus:ring-indigo-500': color === 'success',
      'bg-indigo-600 hover:bg-indigo-500 text-white focus:ring-indigo-500': color === 'primary',
      'bg-red-600 hover:bg-red-500 text-white focus:ring-indigo-500': color === 'danger',
      'pointer-events-none opacity-50': disabled
    }"
  >
    <slot />
  </button>
</template>

<script setup>
const props = defineProps({
  disabled: Boolean,
  type: { type: String, default: 'button' },
  color: { type: String, default: 'default' }
})
</script>