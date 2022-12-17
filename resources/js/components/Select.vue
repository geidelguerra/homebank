<template>
  <Combobox
    :model-value="modelValue"
    :multiple="multiple"
    :nullable="nullable"
    @update:model-value="val => emit('update:modelValue', val)"
  >
    <div class="relative mt-1">
      <div
        class="relative w-full cursor-default rounded"
      >
        <ComboboxInput
          class="p-2 bg-white rounded text-sm font-medium transition-all border border-gray-400 w-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          :display-value="item => getInputValue(item)"
          @change="query = $event.target.value"
        />
        <ComboboxButton
          class="absolute inset-y-0 right-0 flex items-center pr-2 selection-none"
        >
          <div
            class="w-6 h-6"
            aria-hidden="true"
          >
            ↡
          </div>
        </ComboboxButton>
      </div>
      <TransitionRoot
        leave="transition ease-in duration-100"
        leave-from="opacity-100"
        leave-to="opacity-0"
        @after-leave="query = ''"
      >
        <ComboboxOptions
          class="absolute z-40 flex flex-col space-y-1 mt-1 max-h-60 w-full overflow-auto rounded bg-white p-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
        >
          <div
            v-if="filteredItems.length === 0 && query !== ''"
            class="relative cursor-default select-none py-2 px-4 text-gray-700"
          >
            Nothing found.
          </div>

          <ComboboxOption
            v-for="item in filteredItems"
            :key="getItemKey(item)"
            v-slot="{ selected, active }"
            as="template"
            :value="getItemKey(item)"
          >
            <li
              class="relative cursor-pointer select-none p-2 rounded-sm"
              :class="{
                'bg-indigo-500 text-white': active || selected,
                'text-gray-900': !active && !selected,
              }"
            >
              <span
                class="block truncate"
                :class="{ 'font-medium': selected, 'font-normal': !selected }"
              >
                {{ getItemValue(item) }}
              </span>
            </li>
          </ComboboxOption>
        </ComboboxOptions>
      </TransitionRoot>
    </div>
  </Combobox>
</template>

<script setup>
import {
  Combobox,
  ComboboxInput,
  ComboboxButton,
  ComboboxOptions,
  ComboboxOption,
  TransitionRoot,
} from '@headlessui/vue'
import { ref, computed } from 'vue';

const props = defineProps({
  modelValue: { type: [String, Number, Object, Array], default: null },
  items: { type: Array, default: () => [] },
  itemKey: { type: String, default: 'id' },
  itemValue: { type: String, default: 'name' },
  multiple: Boolean,
  nullable: Boolean,
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

const getInputValue = (item) => {
  if (typeof item === 'undefined' || item === null) {
    return item
  }

  if (Array.isArray(item)) {
    return item.map((val) => getItemValue(props.items.find((other) => getItemKey(other) === val)))
  }

  return getItemValue(props.items.find((other) => getItemKey(other) === item))
}

const query = ref('')

const filteredItems = computed(() => {
  if (query.value === '') {
    return props.items
  }

  return props.items.filter((item) => getItemValue(item).toLowerCase().includes(query.value.toLowerCase()))
})

const emit = defineEmits(['update:modelValue'])
</script>
