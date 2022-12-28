<template>
  <button
    v-bind="$attrs"
    :type="type"
    class="rounded transition-all focus:outline-none focus:ring-2 focus:ring-offset-2"
    :class="{
      'px-4 py-2 text-sm': size === 'md',
      'px-2 py-1 text-xs': size === 'sm',
      'bg-slate-100 hover:bg-slate-50 text-slate-800 focus:ring-indigo-500': color === 'default',
      'bg-green-600 hover:bg-green-500 text-white focus:ring-indigo-500': color === 'success',
      'bg-indigo-600 hover:bg-indigo-500 text-white focus:ring-indigo-500': color === 'primary',
      'bg-red-600 hover:bg-red-500 text-white focus:ring-indigo-500': color === 'danger',
      'pointer-events-none opacity-50': disabled,
      'px-3 py-2 text-sm rounded': size === 'md' && !iconOnly,
      'px-2 py-1 text-xs rounded': size === 'sm' && !iconOnly,
      'p-2 text-sm rounded-full': size === 'md' && iconOnly,
      'p-1 text-xs rounded-full': size === 'sm' && iconOnly,
    }"
  >
    <Icon
      v-if="icon || loading"
      :icon="loading ? 'fa-solid fa-circle-notch' : icon"
      :spin="loading"
    />
    <span v-if="$slots.default"><slot /></span>
  </button>
</template>

<script setup>
import Icon from '@/components/Icon.vue'

const props = defineProps({
  disabled: Boolean,
  loading: Boolean,
  type: { type: String, default: 'button' },
  color: { type: String, default: "default" },
  size: { type: String, default: "md" },
  icon: { type: String, default: null },
  iconOnly: Boolean
})
</script>