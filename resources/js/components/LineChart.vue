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
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Filler
} from 'chart.js'
import Card from '@/components/Card.vue'

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Filler
)

const props = defineProps({
  data: { type: Object, default: null },
  scaleFormatter: { type: Function, default: (val) => val }
})

const options = {
  elements: {
    line: {
      tension: 0.1,
      borderColor: 'transparent',
      borderWidth: 2
    },
    point: {
      radius: 0,
      backgroundColor: 'transparent'
    },
  },
  scales: {
    x: {
      ticks: {
        font: {
          family: 'Inter',
          size: 14,
          weight: '600',
        },
        padding: 6,
        color: 'rgba(71,85,105)'
      },
      grid: {
        display: true,
        tickWidth: 0,
        tickLength: 5,
        offset: false
      }
    },
    y: {
      ticks: {
        font: {
          family: 'Inter',
          size: 14,
          weight: '600'
        },
        padding: 6,
        color: 'rgba(71,85,105)',
        callback: props.scaleFormatter
      },
      grid: {
        display: false,
      }
    }
  },
  plugins: {
    filler: {
      propagate: true
    },
    tooltip: {
      enabled: false
    },
    legend: {
      labels: {
        font: {
          family: 'Inter',
          size: 14,
          weight: '600'
        },
        color: 'rgba(71,85,105)',
      }
    }
    // multiply: {
    //   beforeDatasetsDraw: function(chart, options, el) {
    //     chart.ctx.globalCompositeOperation = 'multiply';
    //   },
    //   afterDatasetsDraw: function(chart, options) {
    //     chart.ctx.globalCompositeOperation = 'source-over';
    //   },
    // }
  }
}
</script>