<script setup>
import { computed } from 'vue'
import { useTheme } from 'vuetify'
import VueApexCharts from 'vue3-apexcharts'

const props = defineProps({
  title: { type: String, default: 'Chart' },
  data: {
    type: Object,
    default: () => ({ categories: [], values: [] }),
  },
  distributed: { type: Boolean, default: true },
})

const vuetifyTheme = useTheme()
const isDark = computed(() => vuetifyTheme.global.current.value.dark)

const series = computed(() => [
  {
    name: 'Links',
    data: props.data.values || [],
  },
])

const chartOptions = computed(() => ({
  chart: { type: 'bar', height: 400, background: 'transparent' },
  plotOptions: {
    bar: {
      horizontal: false,
      distributed: props.distributed,
      dataLabels: { position: 'top' },
      columnWidth: '65%',
      endingShape: 'rounded',
    },
  },
  colors: ['#7367F0', '#FF9F43', '#28C76F', '#EA5455', '#00CFE8', '#826AF9', '#FDB528'],
  dataLabels: {
    enabled: true,
    offsetY: -18,
    style: { fontSize: '13px', colors: isDark.value ? ['#E0E0E0'] : ['#000'] },
  },
  xaxis: {
    categories: props.data.categories || [],
    labels: { style: { colors: isDark.value ? '#E0E0E0' : '#6E6B7B' } },
  },
  yaxis: { labels: { style: { colors: isDark.value ? '#E0E0E0' : '#6E6B7B' } } },
  grid: { borderColor: isDark.value ? '#444' : '#E0E0E0' },
  tooltip: { theme: isDark.value ? 'dark' : 'light' },
  legend: { show: false },
}))
</script>

<template>
    <VCardTitle class="text-h6">{{ title }}</VCardTitle>
    <VueApexCharts v-if="series[0].data.length" type="bar" height="400" :options="chartOptions" :series="series" />
    <p v-else class="text-center py-8">No data available</p>
</template>
