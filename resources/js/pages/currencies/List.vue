<template>
  <div>
    <div class="flex flex-col space-y-2">
      <template
        v-for="currency in currencies"
        :key="currency.id"
      >
        <Card
          tag="button"
          @click="$inertia.visit(route('currencies.edit', [currency.code]))"
        >
          <div class="flex">
            <div>
              {{ currency.code }}
            </div>
          </div>
        </Card>
      </template>
    </div>
  </div>
</template>

<script setup>
import Card from '@/components/Card.vue';

const props = defineProps({
  currencies: { type: Array, default: null }
})
</script>