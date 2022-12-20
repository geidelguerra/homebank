<template>
  <canvas
    v-bind="$attrs"
    ref="canvas"
  />
</template>

<script setup>
import { formatNumber } from '@/utils'
import { Chart, LineController } from 'chart.js'
import { merge } from 'lodash'
import { onBeforeUnmount, onMounted, ref, watch, toRaw, computed } from 'vue'
import {
  Chart as ChartJS,
  Legend,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Filler
} from 'chart.js'

ChartJS.register(
  Legend,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  LineController,
  Filler
)

const props = defineProps({
  type: { type: String, required: true },
  data: { type: Object, default: null },
  options: { type: Object, default: null },
  plugins: { type: Object, default: null },
  updateMode: { type: String, default: 'none' },
  scaleFormatter: { type: Function, default: null },
  colors: { type: Array, default: null }
})

const canvas = ref()

let chart = null

const defaultOptions = {
  responsive: true,
  maintainAspectRatio: true,
  layout: {
    padding: {
      top: 12,
      left: 12,
      bottom: 12
    }
  },
  scales: {
    x: {
      ticks: {
        font: {
          family: 'Inter',
          size: 12,
          weight: '600',
        },
        padding: 6,
        color: 'rgba(71,85,105)'
      },
      grid: {
        display: false,
        tickWidth: 0,
        tickLength: 5,
        offset: false
      }
    },
    y: {
      ticks: {
        font: {
          family: 'Inter',
          size: 12,
          weight: '600'
        },
        padding: 6,
        color: 'rgba(71,85,105)',
        callback: function (val) {
          if (typeof props.scaleFormatter === 'function') {
            return props.scaleFormatter(val)
          }

          return val
        }
      },
      grid: {
        display: true,
      }
    }
  },
  plugins: {
    hover: {
      mode: 'nearest',
      animationDuration: 400,
    },
    tooltip: {
      enabled: false
    },
    datalabels: {
      display: true,
      color: '#7b8db5',
      anchor: 'end',
      align: 'end',
      offset: -3,
      font: {
        style: ' bold',
      },
    },
    legend: {
      position: 'bottom',
      labels: {
        boxWidth: 35,
        padding: 12,
        usePointStyle: true,

        generateLabels: function (chart) {
          return chart.data.datasets.map(function (dataset, i) {
            return {
              text: dataset.label,
              lineCap: dataset.borderCapStyle,
              lineDash: [],
              lineDashOffset: 0,
              lineJoin: dataset.borderJoinStyle,
              pointStyle: 'circle',
              fillStyle: dataset.backgroundColor,
              strokeStyle: dataset.borderColor,
              lineWidth: 0,
              lineDash: dataset.borderDash,
            }
          })
        },
      },
    },
  },
  elements: {
    rectangle: {
      borderWidth: 3,
      borderSkipped: 'top',
    },
    point: {
      radius: 3,
      borderColor: '#fff',
      borderWidth: 2,
      hoverRadius: 3,
      hoverBorderWidth: 2,
    },
    line: {
      tension: 0.43,
      borderWidth: 3,
      spanGaps: true
    },
  },
}

const defaultDatasetsColors = [
  'red',
  'green',
  'blue'
]

const parsedData = () => {
  let data = toRaw(props.data || {})

  const datasetsColors = props.colors ? props.colors : defaultDatasetsColors

  data = {
    ...data,
    datasets: (data.datasets || []).map((dataset, i) => {
      const color = datasetsColors[i] || datasetsColors[0]

      return {
        ...dataset,
        backgroundColor: color,
        borderColor: color,
        pointBorderColor: 'white'
      }
    })
  }

  return data
}

const parsedOptions = () => {
  return merge({}, toRaw(defaultOptions), toRaw(props.options))
}

const updateChart = () => {
  chart.update(toRaw(props.updateMode))
}

watch(() => props.data, () => {
  chart.data = parsedData()

  updateChart()
})

watch(() => props.options, () => {
  chart.options = parsedOptions()

  updateChart()
})

watch(() => props.plugins, (plugins) => {
  chart.plugins = toRaw(plugins)

  updateChart()
})

watch(() => props.scaleFormatter, () => {
  chart.options = parsedOptions()

  updateChart()
})

watch(() => props.colors, () => {
  chart.data = parsedData()

  updateChart()
})

onMounted(() => {
  chart = new Chart(canvas.value, {
    type: toRaw(props.type),
    data: parsedData(),
    options: parsedOptions(),
    plugins: toRaw(props.plugins) || {}
  })
})

onBeforeUnmount(() => {
  chart.destroy()
})
</script>