<template>
  <div class="flex-1 p-4">
    <div class="flex flex-col space-y-4">
      <div class="grid row-auto grid-cols-4 gap-4">
        <FormElement label="Date Range">
          <Select
            v-model="filters.dateRangePreset"
            :items="availableDateRangePresets"
          />
        </FormElement>
        <FormElement label="Currency">
          <Select
            v-model="filters.currency"
            :items="availableCurrencies"
            item-key="code"
            item-value="code"
          />
        </FormElement>
      </div>
      <div class="grid grid-cols-2">
        <Card class="shadow-none text-center">
          <span class="text-sm font-medium text-slate-500">{{ formatDate(selectedDateRange.startDate, 'd MMM Y (z)') }} - {{ formatDate(selectedDateRange.endDate, 'd MMM Y (z)') }}</span>
        </Card>
      </div>
      <div class="grid row-auto grid-cols-2">
        <Card title="Income vs Expense">
          <Chart
            type="line"
            :data="incomeVsExpense"
            :scale-formatter="moneyScaleFormatter"
            :colors="['blue', 'red']"
          />
        </Card>
      </div>
    </div>
  </div>
</template>

<script setup>
import FormElement from '@/components/FormElement.vue'
import Select from '@/components/Select.vue'
import { router } from '@inertiajs/vue3'
import { reactive, watch } from 'vue'
import { formatMoney, formatNumber } from '@/utils'
import Card from '@/components/Card.vue'
import Chart from '@/components/Chart.vue'

const props = defineProps({
  incomeVsExpense: { type: Object, default: null },
  availableDateRangePresets: { type: Array, default: () => [] },
  availableCurrencies: { type: Array, default: () => [] },
  selectedDateRange: { type: Object, required: true },
  selectedDateRangePreset: { type: String, required: true },
  selectedCurrency: { type: Object, required: true }
})

const filters = reactive({
  dateRangePreset: props.selectedDateRangePreset,
  currency: props.selectedCurrency?.code
})

const reloadWithFilters = () => router.reload({
  data: {
    date_range_preset: filters.dateRangePreset,
    filtered_currency: filters.currency
  }
})

const moneyScaleFormatter = (val) => {
  try {
    val = formatNumber(formatMoney(val, props.selectedCurrency))

    return `$${val}`
  } catch (error) {
    console.log(error)
    return val
  }
}

watch(filters, () => reloadWithFilters())
</script>