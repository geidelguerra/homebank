<template>
  <div class="flex-1 flex flex-col p-4">
    <div class="mb-4 flex justify-between">
      <Button
        color="primary"
        @click="$inertia.visit(route('transfers.create'))"
      >
        Add transfer
      </Button>
    </div>

    <div class="flex flex-col space-y-2 flex-1 pb-[36px]">
      <template
        v-for="transfer in transfers.data"
        :key="transfer.id"
      >
        <Card
          tag="button"
          class="border-2 border-transparent hover:border-indigo-500"
          @click="$inertia.visit(route('transfers.edit', [transfer]))"
        >
          <div class="flex items-center mb-2">
            <div>
              <div class="text-sm text-left font-medium text-slate-600">
                {{ $date(transfer.date).format('PPPP (xx)') }}
              </div>
              <div class="text-left">
                {{ transfer.category.name }}
              </div>
            </div>
            <div class="flex-1" />
            <div class="flex flex-col items-end">
              <div class="text-sm">
                {{ transfer.account.name }}
              </div>
              <div
                class="font-bold"
                :class="{
                  'text-red-600': transfer.amount < 0,
                  'text-green-600': transfer.amount > 0,
                }"
              >
                {{ $money(transfer.amount, transfer.account.currency).toDecimal() }} {{ transfer.account.currency_code }}
              </div>
            </div>
          </div>
          <div class="flex space-x-1">
            <div class="text-left text-sm text-slate-600 flex-1">
              {{ transfer.description }}
            </div>
            <div>
              <Button
                color="danger"
                @click.stop="deleteTransaction(transfer)"
              >
                Delete transfer
              </Button>
            </div>
          </div>
        </Card>
      </template>
    </div>
    <div class="fixed bottom-0 p-2 left-80 right-0 bg-[#E7E9F2] flex space-x-2 items-center">
      <div class="text-xs font-medium flex-1">
        Page {{ transfers.current_page }} of {{ transfers.last_page }} ({{ transfers.per_page }} items per page) - Showing {{ transfers.total }} items
      </div>
      <Pagination :links="transfers.links" />
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
  transfers: { type: Object, default: null },
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

const deleteTransaction = (transfer) => confirmDialog.value
  .show({
    title: 'Warning',
    message: 'Proceed to delete this transfer?',
    color: 'danger'
  })
  .then(() => router.delete(route('transfers.destroy', [transfer])))

watch(filters, () => reloadWithFilters())
</script>