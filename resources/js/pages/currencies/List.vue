<template>
  <div class="flex-1 flex flex-col p-4">
    <div class="mb-4 flex justify-between">
      <Button
        color="primary"
        @click="$inertia.visit(route('currencies.create'))"
      >
        Add currency
      </Button>
    </div>
    <div class="flex flex-col space-y-2">
      <template
        v-for="currency in currencies"
        :key="currency.code"
      >
        <Card
          tag="button"
          @click="$inertia.visit(route('currencies.edit', [currency.code]))"
        >
          <div class="flex items-center">
            <div>
              {{ currency.code }}
            </div>
            <div class="flex-1" />
            <div class="text-sm text-slate-400">
              {{ currency.base }}, {{ currency.exponent }}
            </div>
          </div>
        </Card>
      </template>
    </div>
  </div>
</template>

<script setup>
import Button from '@/components/Button.vue';
import Card from '@/components/Card.vue';

const props = defineProps({
  currencies: { type: Array, default: null }
})
</script>