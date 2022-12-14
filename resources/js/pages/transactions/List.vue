<template>
  <div>
    <div class="mb-4">
      <Button
        color="primary"
        @click="$inertia.visit(route('transactions.create'))"
      >
        Add transaction
      </Button>
    </div>
    <div class="flex flex-col space-y-2">
      <template
        v-for="transaction in transactions"
        :key="transaction.id"
      >
        <Card
          tag="button"
          class="border-2 border-transparent hover:border-indigo-500"
          @click="$inertia.visit(route('transactions.edit', [transaction]))"
        >
          <div class="flex items-center">
            <div>
              <div class="text-sm">
                {{ formatDate(new Date(transaction.date), 'P') }}
              </div>
              <div class="text-left">
                {{ transaction.category.name }}
              </div>
            </div>
            <div class="flex-1" />
            <div>{{ formatMoney(transaction.amount, transaction.account.currency) }} {{ transaction.account.currency_code }}</div>
          </div>
        </Card>
      </template>
    </div>
  </div>
</template>

<script setup>
import Card from '@/components/Card.vue'
import Button from '@/components/Button.vue'

const props = defineProps({
  transactions: { type: Array, default: null }
})
</script>