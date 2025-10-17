<script setup>
import { computed } from 'vue'
import { useTheme } from 'vuetify'
import VueApexCharts from 'vue3-apexcharts'

const props = defineProps({
  title: { type: String, default: 'Chart' },
  data: {
    type: Object,
    default: () => ({ categories: [], values: [] ,domains: []}),
  },
  distributed: { type: Boolean, default: true },
})

const vuetifyTheme = useTheme()
const isDark = computed(() => vuetifyTheme.global.current.value.dark)

const series = computed(() => {


  if (props.data.domains && props.data.domains.length > 0) {
    return props.data.domains.map((domain, i) => ({
      name: domain,
      data: props.data.values[i] || [],
    }))
  }

  return [
    {
      name: 'Links',
      data: props.data.values || [],
    },
  ]
})


const chartOptions = computed(() => {
  const categoryCount = props.data.categories?.length || 0
  
  const isHorizontal = categoryCount > 10
  
  let dynamicHeight = 400
  if (isHorizontal && categoryCount > 10) {
    dynamicHeight = Math.min(800, 300 + (categoryCount * 35))
  }
  
  let barWidth
  if (categoryCount <= 5) {
    barWidth = '65%'
  } else if (categoryCount <= 10) {
    barWidth = '55%'
  } else if (categoryCount <= 15) {
    barWidth = '50%'
  } else {
    barWidth = '80%' 
  }
  
  const showDataLabels = categoryCount <= 20
  
  const dataLabelFontSize = categoryCount > 15 ? '9px' : categoryCount > 10 ? '10px' : '12px'
  const axisLabelFontSize = categoryCount > 15 ? '9px' : '10px'
  
  return {
    chart: {
      type: 'bar',
      height: dynamicHeight,
      background: 'transparent',
      toolbar: {
        show: categoryCount > 8,
        tools: {
          zoom: true,
          zoomin: true,
          zoomout: true,
          pan: true,
          reset: true,
        },
      },
      zoom: {
        enabled: true,
        type: isHorizontal ? 'y' : 'x',
        autoScaleYaxis: true
      },
    },
    plotOptions: {
      bar: {
        horizontal: isHorizontal,
        distributed: props.distributed,
        dataLabels: { position: isHorizontal ? 'right' : 'top' },
        columnWidth: barWidth,
        barHeight: barWidth,
        endingShape: 'rounded',
      },
    },
    colors: [
      '#7367F0', '#FF9F43', '#28C76F', '#EA5455', '#00CFE8',
      '#826AF9', '#FDB528', '#8A2BE2', '#FF1493', '#20B2AA',
      '#FFD700', '#FF4500', '#9C27B0', '#00BCD4', '#4CAF50',
      '#FF5722', '#795548', '#607D8B', '#E91E63', '#3F51B5',
      '#FFEB3B', '#009688', '#673AB7', '#FF6F00', '#00695C'
    ],
    dataLabels: {
      enabled: showDataLabels,
      offsetY: isHorizontal ? 0 : -20,
      offsetX: isHorizontal ? 5 : 0,
      style: {
        fontSize: dataLabelFontSize,
        colors: isDark.value ? ['#E0E0E0'] : ['#000'],
        fontWeight: 500,
      },
      formatter: val => val || '',
    },
    xaxis: {
      categories: props.data.categories || [],
      labels: {
        rotate: !isHorizontal && categoryCount > 5 ? -45 : 0,
        rotateAlways: !isHorizontal && categoryCount > 8,
        trim: true,
        style: {
          colors: isDark.value ? '#E0E0E0' : '#6E6B7B',
          fontSize: axisLabelFontSize,
        },
        maxHeight: !isHorizontal && categoryCount > 8 ? 120 : undefined,
      },
      tickPlacement: 'on',
    },
    yaxis: {
      labels: {
        style: {
          colors: isDark.value ? '#E0E0E0' : '#6E6B7B',
          fontSize: axisLabelFontSize,
        },
        maxWidth: isHorizontal ? 180 : undefined,
      },
    },
    grid: {
      borderColor: isDark.value ? '#444' : '#E0E0E0',
      padding: {
        top: showDataLabels && !isHorizontal ? 25 : 10,
        right: showDataLabels && isHorizontal ? 30 : 10,
        bottom: !isHorizontal && categoryCount > 8 ? 20 : 10,
        left: 10,
      },
    },
    tooltip: {
      theme: isDark.value ? 'dark' : 'light',
      y: {
        formatter: val => val,
      },
    },
    legend: {
      show: false,
    },
  }
})
</script>

<template>
    <VCardTitle class="text-h6">{{ title }}</VCardTitle>
    <VueApexCharts 
      v-if="series[0].data.length" 
      type="bar" 
      :height="chartOptions.chart.height" 
      :options="chartOptions" 
      :series="series" 
    />
    <p v-else class="text-center py-8">No data available</p>
</template>