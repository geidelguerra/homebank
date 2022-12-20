<template>
  <div class="flex-1 flex flex-col p-4">
    <div class="mb-4 flex justify-between">
      <Button
        color="primary"
        @click="$inertia.visit(route('transactions.create'))"
      >
        Add transaction
      </Button>
      <Button @click="isFiltersOpen = !isFiltersOpen">
        {{ isFiltersOpen ? 'Hide filters' : 'Show filters' }}
      </Button>
    </div>
    <Card
      v-if="isFiltersOpen"
      title="Filters"
      class="mb-4"
    >
      <div class="grid row-auto grid-cols-4 gap-4">
        <FormElement label="By date">
          <Select
            v-model="filters.datePreset"
            nullable
            :items="availableDatePresets"
          />
        </FormElement>
        <FormElement label="By account">
          <Select
            v-model="filters.accounts"
            multiple
            nullable
            :items="availableAccounts"
          />
        </FormElement>
        <FormElement label="By category">
          <Select
            v-model="filters.categories"
            multiple
            nullable
            :items="availableCategories"
          />
        </FormElement>
        <FormElement label="By type">
          <Select
            v-model="filters.type"
            nullable
            :items="availableTypes"
          />
        </FormElement>
      </div>
    </Card>
    <div class="flex flex-col space-y-2 flex-1 pb-[36px]">
      <template
        v-for="transaction in transactions.data"
        :key="transaction.id"
      >
        <Card
          tag="button"
          class="border-2 border-transparent hover:border-indigo-500"
          @click="$inertia.visit(route('transactions.edit', [transaction]))"
        >
          <div class="flex items-center mb-2">
            <div>
              <div class="text-sm text-left">
                {{ formatDate(new Date(transaction.date), 'P') }} ({{ transaction.date }})
              </div>
              <div class="text-left">
                {{ transaction.category.name }}
              </div>
            </div>
            <div class="flex-1" />
            <div class="flex flex-col items-end">
              <div class="text-sm">
                {{ transaction.account.name }}
              </div>
              <div class="font-bold">
                {{ formatMoney(transaction.amount, transaction.account.currency) }} {{ transaction.account.currency_code }}
              </div>
            </div>
          </div>
          <div class="flex space-x-1">
            <div class="text-left text-sm text-slate-600 flex-1">
              {{ transaction.description }}
            </div>
            <div>
              <Button
                color="danger"
                @click.stop="deleteTransaction(transaction)"
              >
                Delete transaction
              </Button>
            </div>
          </div>
        </Card>
      </template>
    </div>
    <div class="fixed bottom-0 p-2 left-80 right-0 bg-[#E7E9F2] flex space-x-2 items-center">
      <div class="text-xs font-medium flex-1">
        Page {{ transactions.current_page }} of {{ transactions.last_page }} ({{ transactions.per_page }} items per page) - Showing {{ transactions.total }} items
      </div>
      <Pagination :links="transactions.links" />
    </div>
  </div>
  <ConfirmDialog ref="confirmDialog" />
</template>

<script setup>
import Card from '@/components/Card.vue'
import Button from '@/components/Button.vue'
import Pagination from '@/components/Pagination.vue'
import { ref, watch, reactive } from 'vue'
import FormElement from '@/components/FormElement.vue'
import Select from '@/components/Select.vue'
import { router } from '@inertiajs/vue3'
import ConfirmDialog from '@/components/ConfirmDialog.vue'

const props = defineProps({
  transactions: { type: Object, default: null },
  availableDatePresets: { type: Array, default: () => [] },
  availableAccounts: { type: Array, default: () => [] },
  availableCategories: { type: Array, default: () => [] },
  availableTypes: { type: Array, default: () => [] }
})

const isFiltersOpen = ref(false)

const confirmDialog = ref(null)

const filters = reactive({
  datePreset: undefined,
  accounts: [],
  categories: [],
  type: undefined
})

const reloadWithFilters = () => router.reload({
  data: {
    filtered_date_preset: filters.datePreset,
    filtered_type: filters.type,
    filtered_accounts: filters.accounts.length > 0 ? filters.accounts.join(',') : undefined,
    filtered_categories: filters.categories.length > 0 ? filters.categories.join(',') : undefined,
  }
})

const deleteTransaction = (transaction) => confirmDialog.value
  .show({
    title: 'Warning',
    message: 'Proceed to delete this transaction?',
    color: 'danger'
  })
  .then(() => router.delete(route('transactions.destroy', [transaction])))

watch(filters, () => reloadWithFilters())
</script>