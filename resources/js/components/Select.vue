<template>
  <Combobox
    :model-value="modelValue"
    @update:model-value="val => emit('update:modelValue', val)"
  >
    <div class="relative mt-1">
      <div
        class="relative w-full cursor-default overflow-hidden rounded"
      >
        <ComboboxInput
          class="p-2 bg-white rounded text-sm font-medium transition-all border border-gray-400 w-full focus:outline-indigo-500"
          :display-value="getItemDisplayValue"
          @change="query = $event.target.value"
        />
        <ComboboxButton
          class="absolute inset-y-0 right-0 flex items-center pr-2"
        >
          <ChevronUpDownIcon
            class="h-5 w-5 text-gray-400"
            aria-hidden="true"
          />
        </ComboboxButton>
      </div>
      <TransitionRoot
        leave="transition ease-in duration-100"
        leave-from="opacity-100"
        leave-to="opacity-0"
        @after-leave="query = ''"
      >
        <ComboboxOptions
          class="absolute flex flex-col space-y-1 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
        >
          <div
            v-if="filteredItems.length === 0 && query !== ''"
            class="relative cursor-default select-none py-2 px-4 text-gray-700"
          >
            Nothing found.
          </div>

          <ComboboxOption
            v-for="item in filteredItems"
            :key="getItemId(item)"
            v-slot="{ selected, active }"
            as="template"
            :value="item"
          >
            <li
              class="relative cursor-pointer select-none py-2 pl-10 pr-4"
              :class="{
                'bg-indigo-500 text-white': active || selected,
                'text-gray-900': !active && !selected,
              }"
            >
              <span
                class="block truncate"
                :class="{ 'font-medium': selected, 'font-normal': !selected }"
              >
                {{ getItemDisplayValue(item) }}
              </span>
              <span
                v-if="selected"
                class="absolute inset-y-0 left-0 flex items-center pl-3"
                :class="{ 'text-white': active, 'text-indigo-500': !active }"
              >
                <CheckIcon
                  class="h-5 w-5"
                  aria-hidden="true"
                />
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
  modelValue: { type: [String, Number, Object], default: null },
  items: { type: Array, default: () => [] }
})

const emit = defineEmits(['update:modelValue'])

const query = ref('')

const filteredItems = computed(() => {
  if (query.value === '') {
    return props.items
  }

  return props.items.filter((item) => item.toLowerCase().includes(query.value.toLowerCase()))
})

const getItemId = (item) => item

const getItemDisplayValue = (item) => item
</script>
