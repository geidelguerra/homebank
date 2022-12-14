<template>
  <RadioGroup
    :model-value="modelValue"
    @update:model-value="val => emit('update:modelValue', val)"
  >
    <div class="grip auto-rows-auto columns-5 gap-2 row">
      <RadioGroupOption
        v-for="item in items"
        :key="getItemKey(item)"
        v-slot="{ active, checked }"
        as="template"
        :value="getItemKey(item)"
      >
        <div
          :class="{
            'bg-indigo-600 text-white': active || checked,
            'bg-slate-100 text-slate-800': !checked && !active
          }"
          class="cursor-pointer px-4 py-2 text-sm rounded transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 hover:bg-indigo-500 hover:text-white"
        >
          <RadioGroupLabel>
            {{ getItemValue(item) }}
          </RadioGroupLabel>
        </div>
      </RadioGroupOption>
    </div>
  </RadioGroup>
</template>

<script setup>
import {
  RadioGroup,
  RadioGroupLabel,
  RadioGroupOption,
} from '@headlessui/vue'

const props = defineProps({
  items: { type: Array, default: () => [] },
  modelValue: { type: [String, Number, Object], default: null },
  itemKey: { type: String, default: 'id' },
  itemValue: { type: String, default: 'name' },
})

const getItemKey = (item) => {
  if (item !== null && item !== undefined && typeof item === 'object') {
    return item[props.itemKey]
  }

  return item
}

const getItemValue = (item) => {
  if (item !== null && item !== undefined && typeof item === 'object') {
    return item[props.itemValue]
  }

  return item
}

const emit = defineEmits(['update:modelValue'])
</script>
