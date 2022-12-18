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
      <div>{{ selectedDateRange }}</div>
      <div class="grid row-auto grid-cols-2">
        <LineChart
          :data="incomeVsExpense"
          :scale-formatter="moneyScaleFormatter"
          class="w-full h-full"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import FormElement from '@/components/FormElement.vue'
import LineChart from '@/components/LineChart.vue'
import Select from '@/components/Select.vue'
import { Inertia } from '@inertiajs/inertia'
import { reactive, watch } from 'vue'
import { formatMoney } from '@/utils'

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

const reloadWithFilters = () => Inertia.reload({
  data: {
    date_range_preset: filters.dateRangePreset,
    filtered_currency: filters.currency
  }
})

const moneyScaleFormatter = (val) => {
  try {
    val = formatMoney(val, props.selectedCurrency)

    return `$${val}`
  } catch (error) {
    return val
  }
}

watch(filters, () => reloadWithFilters())
</script>