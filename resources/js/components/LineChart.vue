<template>
  <Card v-bind="$attrs">
    <Chart
      :data="data"
      :options="options"
    />
  </Card>
</template>

<script setup>
import { Line as Chart } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, PointElement, LineElement } from 'chart.js'
import Card from './Card.vue';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, PointElement, LineElement)

const props = defineProps({
  data: { type: Object, default: null }
})

const options = {
  elements: {
    line: {
      tension: 0,
      borderCapStyle: 'round',
      borderJoinStyle: 'round'
    }
  }
}
</script>