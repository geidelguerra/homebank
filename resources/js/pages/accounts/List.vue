<template>
  <div>
    <div class="flex flex-col space-y-2">
      <template
        v-for="account in accounts"
        :key="account.id"
      >
        <Card
          tag="button"
          @click="$inertia.visit(route('accounts.edit', [account]))"
        >
          <div class="flex">
            <div>
              {{ account.name }}
            </div>
            <div class="flex-1" />
            <div>{{ formatMoney(account.amount, account.currency) }} {{ account.currency }}</div>
          </div>
        </Card>
      </template>
    </div>
  </div>
</template>

<script setup>
import Card from '@/components/Card.vue';

const props = defineProps({
  accounts: { type: Array, default: null }
})
</script>