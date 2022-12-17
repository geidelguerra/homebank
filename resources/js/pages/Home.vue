<template>
  <div class="flex-1 p-4">
    <div class="flex flex-col space-y-4">
      <div class="grid row-auto grid-cols-4">
        <FormInput label="Currency">
          <Select
            v-model="filters.currency"
            :items="availableCurrencies"
            item-key="code"
            item-value="code"
          />
        </FormInput>
      </div>
      <div class="grid row-auto grid-cols-2">
        <LineChart
          :data="incomeVsExpense"
          class="w-full h-full"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import FormInput from '@/components/FormInput.vue';
import LineChart from '@/components/LineChart.vue'
import Select from '@/components/Select.vue'
import { Inertia } from '@inertiajs/inertia'
import { reactive, watch } from 'vue'

const props = defineProps({
  incomeVsExpense: { type: Object, default: null },
  availableCurrencies: { type: Array, default: () => [] },
  filteredCurrency: { type: String, required: true }
})

const filters = reactive({
  currency: props.filteredCurrency
})

const reloadWithFilters = () => Inertia.reload({
  data: {
    filtered_currency: filters.currency
  }
})

watch(filters, () => reloadWithFilters())
</script>