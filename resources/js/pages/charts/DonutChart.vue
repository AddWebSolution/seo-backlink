<script setup>
import { computed } from 'vue'
import { useTheme } from 'vuetify'
import VueApexCharts from 'vue3-apexcharts'

// Props — you pass chartData + optional title
const props = defineProps({
  data: {
    type: Object,
    default: () => ({ labels: [], series: [] }), // fallback if nothing passed
  },
  title: {
    type: String,
    default: 'Chart',
  },
})


const vuetifyTheme = useTheme()
const isDark = computed(() => vuetifyTheme.global.current.value.dark)

// Chart options
const chartOptions = computed(() => ({
  chart: {
    type: 'donut',
    height: 400,
    background: 'transparent',
  },
  labels: props.data.labels || [],
  colors: ['#7367F0', '#FF9F43', '#28C76F', '#EA5455', '#00CFE8'],
  legend: {
    position: 'bottom',
    labels: {
      colors: isDark.value ? '#E0E0E0' : '#6E6B7B',
    },
  },
  plotOptions: {
    pie: {
      donut: {
        size: '65%',
        labels: {
          show: true,
          name: {
            show: true,
            color: isDark.value ? '#E0E0E0' : '#6E6B7B',
          },
          value: {
            show: true,
            color: isDark.value ? '#E0E0E0' : '#000',
          },
          total: {
            show: true,
            color: isDark.value ? '#E0E0E0' : '#6E6B7B',
          },
        },
      },
    },
  },
  tooltip: {
    theme: isDark.value ? 'dark' : 'light',
  },
  dataLabels: {
  },
}))
</script>

<template>
        <VueApexCharts v-if="props.data.series?.length" type="donut" height="400" :options="chartOptions"
            :series="props.data.series" />
        <p v-else class="text-center py-8">No data available</p>
</template>
