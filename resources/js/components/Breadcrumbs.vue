<template>
  <div>
    <ul class="flex space-x-2">
      <template
        v-for="(item, i) in items"
        :key="i"
      >
        <li
          v-if="i > 0"
          class="text-slate-200"
        >
          ↦
        </li>
        <li>
          <Link
            v-if="item.url"
            :href="item.url"
            class="transition-all text-sm text-slate-100 p-2 rounded hover:bg-black hover:text-white hover:bg-opacity-10"
          >
            {{ item.text }}
          </Link>
          <span
            v-else
            class="text-sm text-white p-2"
          >
            {{ item.text }}
          </span>
        </li>
      </template>
    </ul>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  items: { type: Array, default: () => [] }
})
</script>