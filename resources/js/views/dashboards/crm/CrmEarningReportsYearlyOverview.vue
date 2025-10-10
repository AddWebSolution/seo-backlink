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


console.log('chartSeries', chartSeries);

const chartConfigs = computed(() => {
  const currentTheme = vuetifyTheme.current.value.colors
  const variableTheme = vuetifyTheme.current.value.variables
  const labelPrimaryColor = `rgba(${hexToRgb(currentTheme.primary)},${variableTheme['dragged-opacity']})`
  const legendColor = `rgba(${hexToRgb(currentTheme['on-background'])},${variableTheme['high-emphasis-opacity']})`
  const borderColor = `rgba(${hexToRgb(String(variableTheme['border-color']))},${variableTheme['border-opacity']})`
  const labelColor = `rgba(${hexToRgb(currentTheme['on-surface'])},${variableTheme['disabled-opacity']})`

  return chartSeries.value.map((seriesItem) => ({
    title: seriesItem.name.charAt(0).toUpperCase() + seriesItem.name.slice(1),
    icon:  moduleIcons[seriesItem.name.toLowerCase()],
    chartOptions: {
      chart: { parentHeightOffset: 0, type: 'bar', toolbar: { show: false } },
      plotOptions: { bar: { columnWidth: '32%', borderRadiusApplication: 'end', borderRadius: 4, distributed: true, dataLabels: { position: 'top' } } },
      grid: { show: false, padding: { top: 0, bottom: 0, left: -10, right: -10 } },
      colors: moduleColors[seriesItem.name.toLowerCase()] ? [`rgba(var(--v-theme-${moduleColors[seriesItem.name.toLowerCase()]}),1)`] : [labelPrimaryColor],
      dataLabels: {
        enabled: true,
        formatter(val) { return val },
        offsetY: -25,
        style: { fontSize: '15px', colors: [legendColor], fontWeight: '600', fontFamily: 'Public Sans' }
      },
      legend: { show: false },
      tooltip: { enabled: true },
      xaxis: { categories: months, axisBorder: { show: true, color: borderColor }, axisTicks: { show: false }, labels: { style: { colors: labelColor, fontSize: '13px', fontFamily: 'Public Sans' } } },
      yaxis: { labels: { offsetX: -15, style: { fontSize: '13px', colors: labelColor, fontFamily: 'Public Sans' } }, min: 0, tickAmount: 6 },
      responsive: [
        { breakpoint: 1441, options: { plotOptions: { bar: { columnWidth: '41%' } } } },
        { breakpoint: 590, options: { plotOptions: { bar: { columnWidth: '61%' } }, yaxis: { labels: { show: false } }, grid: { padding: { right: 0, left: -20 } }, dataLabels: { style: { fontSize: '12px', fontWeight: '400' } } } }
      ]
    },
    series: [seriesItem]
  }))
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
      height="390"
      class="mt-5"
    />
  </VCardText>
</template>
