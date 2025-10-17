<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useTheme } from 'vuetify'
import { useDashboardApi } from '@/composables/dashboardApi'
import { hexToRgb } from '@layouts/utils'

const vuetifyTheme = useTheme()
const currentTab = ref(0)
const refVueApexChart = ref()

const { monthlyStats , fetchMonthlyStats, loading } = useDashboardApi()

const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']

onMounted(async () => {
  await fetchMonthlyStats()
})

const moduleIcons = {
  domains: 'tabler-server',
  rivaldomains: 'tabler-world',
  backlinks: 'tabler-link',
  keywords: 'tabler-key',
  users: 'tabler-user',
  reports: 'tabler-file',
  keywordreports: 'tabler-report',
}

const moduleColors = {
  domains: 'primary',
  rivaldomains: 'success',
  backlinks: 'info',
  keywords: 'warning',
  users: 'error',
  reports: 'secondary',
  keywordreports: 'teal',
}

const chartSeries = computed(() => {
  if (!monthlyStats.value) return []
  return Object.entries(monthlyStats.value).map(([key, values]) => ({
    name: key,
    data: Object.values(values)
  }))
})

console.log('chartSeries', chartSeries)

const chartConfigs = computed(() => {
  const currentTheme = vuetifyTheme.current.value.colors
  const variableTheme = vuetifyTheme.current.value.variables
  const labelPrimaryColor = `rgba(${hexToRgb(currentTheme.primary)},${variableTheme['dragged-opacity']})`
  const legendColor = `rgba(${hexToRgb(currentTheme['on-background'])},${variableTheme['high-emphasis-opacity']})`
  const borderColor = `rgba(${hexToRgb(String(variableTheme['border-color']))},${variableTheme['border-opacity']})`
  const labelColor = `rgba(${hexToRgb(currentTheme['on-surface'])},${variableTheme['disabled-opacity']})`

  return chartSeries.value.map((seriesItem) => {
    const dataPoints = seriesItem.data.length
    
    // Calculate responsive column width based on data points
    let columnWidth
    if (dataPoints <= 6) {
      columnWidth = '45%'
    } else if (dataPoints <= 12) {
      columnWidth = '32%'
    } else if (dataPoints <= 24) {
      columnWidth = '25%'
    } else {
      columnWidth = '18%'
    }
    
    // Determine if horizontal orientation is better
    const isHorizontal = dataPoints > 24
    
    // Dynamic height
    let chartHeight = 390
    if (isHorizontal) {
      chartHeight = Math.min(700, 300 + (dataPoints * 20))
    }
    
    // Data labels settings
    const showDataLabels = dataPoints <= 36
    const dataLabelFontSize = dataPoints > 24 ? '11px' : dataPoints > 12 ? '13px' : '15px'
    const dataLabelOffset = dataPoints > 24 ? -18 : dataPoints > 12 ? -22 : -25
    
    // Axis label font size
    const axisLabelFontSize = dataPoints > 24 ? '10px' : dataPoints > 12 ? '11px' : '13px'
    
    return {
      title: seriesItem.name.charAt(0).toUpperCase() + seriesItem.name.slice(1),
      icon: moduleIcons[seriesItem.name.toLowerCase()],
      height: chartHeight,
      chartOptions: {
        chart: {
          parentHeightOffset: 0,
          type: 'bar',
          toolbar: {
            show: dataPoints > 12,
            tools: {
              zoom: true,
              zoomin: true,
              zoomout: true,
              pan: true,
              reset: true,
            },
          },
          zoom: {
            enabled: dataPoints > 12,
            type: isHorizontal ? 'y' : 'x',
            autoScaleYaxis: true,
          },
        },
        plotOptions: {
          bar: {
            horizontal: isHorizontal,
            columnWidth: columnWidth,
            barHeight: columnWidth,
            borderRadiusApplication: 'end',
            borderRadius: 4,
            distributed: true,
            dataLabels: {
              position: isHorizontal ? 'right' : 'top',
            },
          },
        },
        grid: {
          show: false,
          padding: {
            top: 0,
            bottom: 0,
            left: isHorizontal ? 0 : -10,
            right: isHorizontal && showDataLabels ? 30 : -10,
          },
        },
        colors: moduleColors[seriesItem.name.toLowerCase()]
          ? [`rgba(var(--v-theme-${moduleColors[seriesItem.name.toLowerCase()]}),1)`]
          : [labelPrimaryColor],
        dataLabels: {
          enabled: showDataLabels,
          formatter(val) { return val },
          offsetY: isHorizontal ? 0 : dataLabelOffset,
          offsetX: isHorizontal ? 5 : 0,
          style: {
            fontSize: dataLabelFontSize,
            colors: [legendColor],
            fontWeight: dataPoints > 24 ? '500' : '600',
            fontFamily: 'Public Sans',
          },
        },
        legend: { show: false },
        tooltip: { enabled: true },
        xaxis: {
          categories: months.slice(0, dataPoints),
          axisBorder: { show: true, color: borderColor },
          axisTicks: { show: false },
          labels: {
            rotate: !isHorizontal && dataPoints > 12 ? -45 : 0,
            rotateAlways: !isHorizontal && dataPoints > 18,
            trim: true,
            style: {
              colors: labelColor,
              fontSize: axisLabelFontSize,
              fontFamily: 'Public Sans',
            },
            maxHeight: !isHorizontal && dataPoints > 18 ? 100 : undefined,
          },
        },
        yaxis: {
          labels: {
            offsetX: -15,
            style: {
              fontSize: axisLabelFontSize,
              colors: labelColor,
              fontFamily: 'Public Sans',
            },
            maxWidth: isHorizontal ? 150 : undefined,
          },
          min: 0,
          tickAmount: dataPoints > 24 ? 6 : 8,
        },
        responsive: [
          {
            breakpoint: 1441,
            options: {
              plotOptions: {
                bar: {
                  columnWidth: dataPoints <= 12 ? '41%' : dataPoints <= 24 ? '32%' : '25%',
                },
              },
            },
          },
          {
            breakpoint: 590,
            options: {
              plotOptions: {
                bar: {
                  columnWidth: dataPoints <= 12 ? '61%' : dataPoints <= 24 ? '45%' : '35%',
                },
              },
              yaxis: { labels: { show: false } },
              grid: { padding: { right: 0, left: -20 } },
              dataLabels: {
                style: {
                  fontSize: '10px',
                  fontWeight: '400',
                },
              },
            },
          },
        ],
      },
      series: [seriesItem],
    }
  })
})
</script>

<template>
  <VCardText>
    <VSlideGroup v-model="currentTab" show-arrows mandatory class="mb-10">
      <VSlideGroupItem
        v-for="(report, index) in chartConfigs"
        :key="report.title"
        v-slot="{ isSelected, toggle }"
        :value="index"
      >
        <div
          style="block-size: 150px; inline-size: 140px;"
          :style="isSelected ? 'border-color:rgb(var(--v-theme-primary)) !important' : ''"
          :class="isSelected ? 'border' : 'border border-dashed'"
          class="d-flex flex-column justify-center align-center cursor-pointer rounded py-4 px-5 me-4"
          @click="toggle"
        >
          <VAvatar rounded size="38" :color="isSelected ? 'primary' : ''" variant="tonal" class="mb-2">
            <VIcon size="22" :icon="report.icon" />
          </VAvatar>
          <h6 class="text-base font-weight-medium mb-0">{{ report.title }}</h6>
        </div>
      </VSlideGroupItem>

      <VSlideGroupItem>
        <div style="block-size: 100px; inline-size: 110px;" class="d-flex flex-column justify-center align-center rounded border border-dashed py-4 px-5">
          <VAvatar rounded size="38" variant="tonal">
            <VIcon size="22" icon="tabler-plus" />
          </VAvatar>
        </div>
      </VSlideGroupItem>
    </VSlideGroup>

    <VueApexCharts
      v-if="chartConfigs.length"
      ref="refVueApexChart"
      :key="currentTab"
      :options="chartConfigs[Number(currentTab)].chartOptions"
      :series="chartConfigs[Number(currentTab)].series"
      :height="chartConfigs[Number(currentTab)].height"
      class="mt-5"
    />
  </VCardText>
</template>