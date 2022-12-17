<template>
  <div class="flex-1 p-4">
    <div>
      <LineChart
        :data="incomeVsExpense"
        class="w-full h-full"
      />
      </div">
    </div>
  </div>
</template>

<script setup>
import LineChart from '@/components/LineChart.vue'

const props = defineProps({
  incomeVsExpense: { type: Object, default: null }
})

</script>