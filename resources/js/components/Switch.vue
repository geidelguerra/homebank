<template>
  <SwitchGroup>
    <div class="flex items-center space-x-2">
      <Switch
        :model-value="modelValue"
        :class="modelValue ? 'bg-blue-600' : 'bg-gray-200'"
        class="relative inline-flex h-6 w-11 items-center rounded-full transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        @update:model-value="val => emit('update:modelValue', val)"
      >
        <span
          :class="modelValue ? 'translate-x-6' : 'translate-x-1'"
          class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
        />
      </Switch>
      <SwitchLabel
        v-if="label"
        class="mr-4 select-none cursor-pointer text-sm font-medium text-slate-600"
      >
        {{ label }}
      </SwitchLabel>
    </div>
  </SwitchGroup>
</template>

<script setup>
import { Switch, SwitchGroup, SwitchLabel } from '@headlessui/vue'

const props = defineProps({
  label: { type: String, default: null },
  modelValue: Boolean
})

const emit = defineEmits(['update:modelValue'])
</script>