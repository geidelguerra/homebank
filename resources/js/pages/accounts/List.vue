<template>
  <div class="p-4">
    <div class="mb-4 flex space-x-4">
      <Button
        color="primary"
        @click="$inertia.visit(route('accounts.create'))"
      >
        Add account
      </Button>
      <Button @click="isImportFromFileOpen = true">
        Import accounts from file
      </Button>
    </div>
    <div class="flex flex-col space-y-2 flex-1 pb-[36px]">
      <template
        v-for="account in accounts"
        :key="account.id"
      >
        <Card
          tag="button"
          class="border-2 border-transparent hover:border-indigo-500"
          @click="$inertia.visit(route('accounts.edit', [account]))"
        >
          <div class="flex">
            <div>
              {{ account.name }}
            </div>
            <div class="flex-1" />
            <div>{{ formatMoney(account.amount, account.currency) }} {{ account.currency_code }}</div>
          </div>
        </Card>
      </template>
    </div>
  </div>
  <ImportAccountsFromFileDialog
    :open="isImportFromFileOpen"
    :available-timezones="availableTimezones"
    @close="isImportFromFileOpen = false"
  />
</template>

<script setup>
import Card from '@/components/Card.vue'
import Button from '@/components/Button.vue'
import ImportAccountsFromFileDialog from '@/components/ImportAccountsFromFileDialog.vue';
import { ref } from 'vue';

const props = defineProps({
  accounts: { type: Array, default: null },
  availableTimezones: { type: Array, default: () => [] }
})

const isImportFromFileOpen = ref(false)
</script>