<script setup>
import { useDomainApi } from "@/composables/domainApi";
import { reactive, computed, ref, onMounted, watch, unref } from "vue";
import { useRoute } from "vue-router";
import DonutChart from "@/pages/charts/DonutChart.vue";
import VueApexCharts from "vue3-apexcharts";
import MultiBarChart from "@/pages/charts/MultiBarChart.vue";
import { useTheme } from "vuetify"
import BarChart from "@/pages/charts/BarChart.vue";
import { useAbility } from "@casl/vue";
import useAuthStore from "@/router/store/auth";


const route = useRoute();
const theme = useTheme();
const ability = useAbility();
const authStore  = useAuthStore();

const clientDomainId = computed(() => route.params.id);

const view = computed(() => route.params.view);


const {
  backlinkhistory,
  loading,
  error,
  fetchClientDomainsHistory,
} = useDomainApi();

const filters = reactive({
  date_from: null,
  date_to: null,
});

const selectedMetric = ref("backlinks");
const selectedChart = ref("trend");
const selectedHistoryDate = ref(null);
const viewMode = ref("comparison");
const selectedRivalDomain = ref(null);


const clientId = computed(() => backlinkhistory.value?.client_id);


onMounted(async () => {
  if (clientDomainId.value) {
    await loadComparisonData();
  }
});

const loadComparisonData = async () => {
  try {
    await fetchClientDomainsHistory(clientDomainId.value, filters);
    // Auto-select the latest history date
    if (availableHistoryDates.value.length > 0 && !selectedHistoryDate.value) {
      selectedHistoryDate.value = availableHistoryDates.value[availableHistoryDates.value.length - 1];
    }
    // Auto-select first rival domain
    if (availableRivalDomains.value.length > 0 && !selectedRivalDomain.value) {
      selectedRivalDomain.value = availableRivalDomains.value[0];
    }
  } catch (err) {
    console.error("Failed to load comparison data:", err);
  }
};

watch(filters, async () => {
  await loadComparisonData();
});

const clientHistory = computed(() => {
  const history = backlinkhistory.value?.client_history || {};
  return Object.entries(history)
    .map(([date, data]) => ({ ...data, history_date: date }))
    .sort((a, b) => new Date(a.history_date) - new Date(b.history_date));
});

const rivalHistory = computed(() => backlinkhistory.value?.rival_history || []);

const availableHistoryDates = computed(() => {
  return clientHistory.value.map(h => h.history_date).sort();
});

const rivalsByDomain = computed(() => {
  const grouped = {};
  rivalHistory.value.forEach((rival) => {
    if (!grouped[rival.target]) {
      grouped[rival.target] = [];
    }
    grouped[rival.target].push(rival);
  });
  return grouped;
});

const availableRivalDomains = computed(() => {
  return Object.keys(rivalsByDomain.value);
});

const formatDate = (dateString) => {
  if (!dateString) return "";
  return new Date(dateString).toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
  });
};

const formatNumber = (num) => {
  return new Intl.NumberFormat("en-US").format(num || 0);
};

// Chart Options
const metricOptions = [
  { title: "Backlinks", value: "backlinks" },
  { title: "Referring Domains", value: "referring_domains" },
  { title: "Domain Rank", value: "rank" },
  { title: "New Backlinks", value: "new_backlinks" },
  { title: "Lost Backlinks", value: "lost_backlinks" },
  { title: "Internal Links", value: "internal_links_count" },
  { title: "External Links", value: "external_links_count" },
  { title: "Broken Backlinks", value: "broken_backlinks" },
];

const chartTypeOptions = [
  { title: "Trend Analysis", value: "trend" },
  { title: "Current Comparison", value: "comparison" },
  { title: "Link Types Distribution", value: "types" },
  { title: "Link Attributes", value: "attributes" },
  { title: "Platform Types", value: "platforms" },
  { title: "Geographic Distribution", value: "geography" },
  { title: "TLD Distribution", value: "tld" },
];

const selectedClientData = computed(() => {
  return clientHistory.value.find(h => h.history_date === selectedHistoryDate.value) || {};
});

const selectedRivalData = computed(() => {
  if (!selectedHistoryDate.value) return {};

  const rivals = {};
  Object.entries(rivalsByDomain.value).forEach(([domain, history]) => {
    const rivalAtDate = history.find(h => h.history_date === selectedHistoryDate.value);
    if (rivalAtDate) {
      rivals[domain] = rivalAtDate;
    }
  });
  return rivals;
});


console.log('view',view);

const selectedRivalDomainData = computed(() => {
  if (!selectedRivalDomain.value || !selectedHistoryDate.value) return null;
  const rivalHistory = rivalsByDomain.value[selectedRivalDomain.value] || [];
  return rivalHistory.find(h => h.history_date === selectedHistoryDate.value) || null;
});

const latestClientData = computed(() => {
  return clientHistory.value[clientHistory.value.length - 1] || {};
});

const latestRivalData = computed(() => {
  const latest = {};
  Object.entries(rivalsByDomain.value).forEach(([domain, history]) => {
    latest[domain] = history[history.length - 1] || {};
  });
  return latest;
});

const latestSelectedRivalData = computed(() => {
  if (!selectedRivalDomain.value) return null;
  return latestRivalData.value[selectedRivalDomain.value] || null;
});

const showSideBySide = computed(() => {
  return !['trend', 'comparison'].includes(selectedChart.value) && viewMode.value === 'comparison';
});

// CHART 2: Comparison Bar Chart
const comparisonChartOptions = computed(() => {
  const isDark = theme.global.current.value.dark

  const currentData = selectedHistoryDate.value
    ? selectedClientData.value
    : latestClientData.value
  const currentRivals = selectedHistoryDate.value
    ? selectedRivalData.value
    : latestRivalData.value

  const domains = [currentData.target || "Client"]
  const values = [currentData[selectedMetric.value] || 0]

  if (viewMode.value === "comparison") {
    Object.entries(currentRivals).forEach(([domain, data]) => {
      domains.push(domain)
      values.push(data[selectedMetric.value] || 0)
    })
  }

  const barCount = domains.length
  const isHorizontal = barCount > 10
  
  let dynamicHeight = 400
  if (isHorizontal && barCount > 10) {
    dynamicHeight = Math.min(800, 300 + (barCount * 35))
  }
  
  let barWidth
  if (barCount <= 3) {
    barWidth = '45%'
  } else if (barCount <= 6) {
    barWidth = '40%'
  } else if (barCount <= 10) {
    barWidth = '35%'
  } else {
    barWidth = '80%' 
  }
  
  const showDataLabels = barCount <= 20
  const dataLabelFontSize = barCount > 15 ? '9px' : barCount > 10 ? '10px' : '12px'
  const axisLabelFontSize = barCount > 15 ? '9px' : '10px'

  const seriesData = []

  domains.forEach((domain, i) => {
    seriesData.push({
      name: domain,
      data: [values[i]],
    })
  })


  return {
    series: [
      {
        name: metricOptions.find(m => m.value === selectedMetric.value)?.title,
        data: values,
      },
    ],
    chartOptions: {
      chart: {
        type: "bar",
        height: dynamicHeight,
        toolbar: {
          show: barCount > 6,
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
          autoScaleYaxis: true,
        },
        background: "transparent",
      },
      plotOptions: {
        bar: {
          horizontal: isHorizontal,
          columnWidth: barWidth,
          barHeight: barWidth,
          distributed: true,
          dataLabels: {
            position: isHorizontal ? 'right' : 'top',
          },
          endingShape: 'rounded',
        },
      },
      colors: [
        "#7367F0", "#FF9F43", "#28C76F", "#EA5455", "#00CFE8",
        "#826AF9", "#FDB528", "#8A2BE2", "#FF1493", "#20B2AA",
        "#FFD700", "#FF4500", "#9C27B0", "#00BCD4", "#4CAF50",
        "#FF5722", "#795548", "#607D8B", "#E91E63", "#3F51B5",
        "#FFEB3B", "#009688", "#673AB7", "#FF6F00", "#00695C"
      ],
      xaxis: {
        categories: domains,
        labels: {
          rotate: !isHorizontal && barCount > 5 ? -45 : 0,
          rotateAlways: !isHorizontal && barCount > 8,
          trim: true,
          style: {
            colors: isDark ? "#E0E0E0" : "#6E6B7B",
            fontSize: axisLabelFontSize,
          },
          maxHeight: !isHorizontal && barCount > 8 ? 120 : undefined,
        },
        tickPlacement: 'on',
      },
      yaxis: {
        reversed: selectedMetric.value === "rank",
        labels: {
          style: {
            colors: isDark ? "#E0E0E0" : "#6E6B7B",
            fontSize: axisLabelFontSize,
          },
          maxWidth: isHorizontal ? 180 : undefined,
        },
      },
      legend: {
        show: false, 
        labels: {
          colors: isDark ? "#E0E0E0" : "#6E6B7B",
        },
      },
      grid: {
        borderColor: isDark ? "#444" : "#E0E0E0",
        padding: {
          top: showDataLabels && !isHorizontal ? 25 : 10,
          right: showDataLabels && isHorizontal ? 30 : 10,
          bottom: !isHorizontal && barCount > 8 ? 20 : 10,
          left: 10,
        },
      },
      tooltip: {
        theme: isDark ? "dark" : "light",
        y: {
          formatter: val => val,
        },
      },
      dataLabels: {
        enabled: showDataLabels,
        offsetY: isHorizontal ? 0 : -20,
        offsetX: isHorizontal ? 5 : 0,
        style: {
          colors: [isDark ? "#E0E0E0" : "#000"],
          fontSize: dataLabelFontSize,
          fontWeight: 500,
        },
        formatter: val => val || '',
      },
    },
  }
})


// Helper function to get chart data for a domain
const getChartDataForDomain = (domainData, chartType) => {
  if (!domainData) return { series: [], labels: [] };

  switch (chartType) {
    case 'types':
      const typesData = domainData.referring_links_types || {};
      return {
        series: Object.values(typesData),
        labels: Object.keys(typesData).map((l) => l.charAt(0).toUpperCase() + l.slice(1)),
      };
    case 'attributes':
      const attrsData = domainData.referring_links_attributes || {};
      return {
        series: Object.values(attrsData),
        labels: Object.keys(attrsData).map((l) => l.charAt(0).toUpperCase() + l.slice(1)),
      };
    case 'platforms':
      const platformData = domainData.referring_links_platform_types || {};
      return {
        categories: Object.keys(platformData).map((c) => c.charAt(0).toUpperCase() + c.slice(1)),
        values: Object.values(platformData),
      };
    case 'geography':
      const geoData = domainData.referring_links_countries || {};
      const sortedGeo = Object.entries(geoData)
        .filter(([country]) => country !== "")
        .sort(([, a], [, b]) => b - a)
        .slice(0, 10);
      return {
        categories: sortedGeo.map(([country]) => country || "Unknown"),
        values: sortedGeo.map(([, count]) => count),
      };
    case 'tld':
      const tldData = domainData.referring_links_tld || {};
      const sortedTld = Object.entries(tldData)
        .sort(([, a], [, b]) => b - a)
        .slice(0, 10);
      return {
        series: sortedTld.map(([, count]) => count),
        labels: sortedTld.map(([tld]) => tld || "No TLD"),
      };
    default:
      return { series: [], labels: [] };
  }
};

// Stats Cards Data
const statsCards = computed(() => {
  const currentData = selectedHistoryDate.value ? selectedClientData.value : latestClientData.value;
  const currentRivals = selectedHistoryDate.value ? selectedRivalData.value : latestRivalData.value;

  return [
    {
      title: "Backlinks",
      value: formatNumber(currentData.backlinks),
      icon: "tabler-link",
      color: "primary",
      change: currentData.new_backlinks - currentData.lost_backlinks,
    },
    {
      title: "Referring Domains",
      value: formatNumber(currentData.referring_domains),
      icon: "tabler-world",
      color: "success",
      change: currentData.new_referring_domains - currentData.lost_referring_domains,
    },
    {
      title: "Domain Rank",
      value: `#${formatNumber(currentData.rank)}`,
      icon: "tabler-trophy",
      color: "warning",
      inverse: true,
    },
    {
      title: "Competitors",
      value: Object.keys(currentRivals).length,
      icon: "tabler-users",
      color: "info",
    },
  ];
});

</script>

<template>
  <div>
    <!-- Header -->
    <VCard class="mb-6" elevation="2">
      <VCardText class="pa-6">
        <VRow align="center">
          <VCol cols="12" md="8">
            <div class="d-flex align-center mb-4">
              <VAvatar color="primary" variant="tonal" size="56" class="me-4">
                <VIcon icon="tabler-chart-line" size="32" />
              </VAvatar>
              <div>
                <h2 class="text-h3 font-weight-bold">Domain Comparison Analytics</h2>
                <p class="text-body-1 text-medium-emphasis">
                  Compare {{ latestClientData.target || "your domain" }} with competitors over time
                </p>
              </div>
            </div>
          </VCol>
          <VCol cols="12" md="4" class="d-flex justify-end">
            <VBtn variant="flat" color="primary" :to="{
              name: ability.can('view', 'client')
                ? (view === 'client' ? 'domain-clientdomain-list' : 'clientdomain-list')
                : 'clientdomain-list',
              params: ability.can('view', 'client') ? { id: clientId } : {},
            }">
              <VIcon icon="tabler-arrow-autofit-left" size= "x-large" class="me-1"/>
              {{ ability.can('view', 'client') ? 'Back' : 'Back' }}
            </VBtn>
          </VCol>
        </VRow>

        <VDivider class="my-4" />

        <!-- View Mode Toggle -->
        <VRow class="mb-4">
          <VCol cols="12">
            <VBtnToggle v-model="viewMode" color="primary" variant="outlined" divided mandatory class="w-100">
              <VBtn value="client" class="flex-grow-1">
                <VIcon icon="tabler-user" class="me-2" />
                {{ latestClientData?.target?.toUpperCase() }}
              </VBtn>
              <VBtn value="comparison" class="flex-grow-1">
                <VIcon icon="tabler-users" class="me-2" />
                Compare with Rivals
              </VBtn>
            </VBtnToggle>
          </VCol>
        </VRow>

        <!-- Filters -->
        <VRow class="g-3">
          <VCol cols="12" sm="3">
            <VSelect v-model="selectedHistoryDate" :items="availableHistoryDates" label="History Date"
              variant="outlined" prepend-inner-icon="tabler-calendar" hide-details>
              <template #item="{ props, item }">
                <VListItem v-bind="props" :title="formatDate(item.value)" />
              </template>
              <template #selection="{ item }">
                {{ formatDate(item.value) }}
              </template>
            </VSelect>
          </VCol>
          <VCol cols="12" sm="3">
            <VSelect v-model="selectedChart" :items="chartTypeOptions" item-title="title" item-value="value"
              label="Chart Type" variant="outlined" prepend-inner-icon="tabler-chart-bar" hide-details />
          </VCol>
          <VCol cols="12" sm="2">
            <VSelect v-model="selectedMetric" :items="metricOptions" item-title="title" item-value="value"
              label="Metric" variant="outlined" prepend-inner-icon="tabler-list" hide-details
              :disabled="!['trend', 'comparison'].includes(selectedChart)" />
          </VCol>
          <VCol cols="12" :sm="showSideBySide && availableRivalDomains.length > 0 ? '2' : '4'">
            <VBtn color="primary" variant="flat" @click="loadComparisonData" block :loading="loading">
              <VIcon icon="tabler-refresh" class="me-2" />
              Refresh
            </VBtn>
          </VCol>
        </VRow>
      </VCardText>
    </VCard>

    <!-- Loading State -->
    <VCard v-if="loading" elevation="2" class="mb-6">
      <VCardText class="text-center py-16">
        <VProgressCircular indeterminate color="primary" size="64" class="mb-4" />
        <p class="text-body-1">Loading comparison data...</p>
      </VCardText>
    </VCard>

    <!-- Error State -->
    <VCard v-else-if="error" elevation="2" class="mb-6">
      <VCardText class="text-center py-16">
        <VAvatar size="120" color="error" variant="tonal" class="mb-6">
          <VIcon icon="tabler-alert-triangle" size="60" />
        </VAvatar>
        <h4 class="text-h4 mb-4">Failed to Load Data</h4>
        <p class="text-body-1 text-medium-emphasis mb-6">{{ error.message }}</p>
        <VBtn color="primary" @click="loadComparisonData">
          <VIcon icon="tabler-refresh" class="me-2" />
          Try Again
        </VBtn>
      </VCardText>
    </VCard>

    <!-- Main Content -->
    <template v-else-if="clientHistory.length > 0">
      <!-- Stats Cards -->
      <VRow class="mb-6">
        <VCol v-for="(stat, index) in statsCards" :key="index" cols="12" sm="6" md="3">
          <VCard elevation="2" :color="stat.color" variant="tonal" class="h-100">
            <VCardText class="pa-6 text-center">
              <VAvatar :color="stat.color" size="56" class="mb-3">
                <VIcon :icon="stat.icon" size="28" />
              </VAvatar>
              <p class="text-body-2 text-medium-emphasis mb-1">{{ stat.title }}</p>
              <h4 class="text-h4 font-weight-bold mb-2" :class="`text-${stat.color}`">
                {{ stat.value }}
              </h4>
              <VChip v-if="stat.change !== undefined"
                :color="stat.inverse ? (stat.change < 0 ? 'success' : 'error') : (stat.change > 0 ? 'success' : 'error')"
                variant="tonal" size="small">
                <VIcon :icon="stat.change > 0 ? 'tabler-arrow-up' : 'tabler-arrow-down'" size="14" class="me-1" />
                {{ Math.abs(stat.change) }}
              </VChip>
            </VCardText>
          </VCard>
        </VCol>
      </VRow>

      <!-- Charts - Side by Side Layout for non-trend/comparison charts in comparison mode -->
      <VRow v-if="showSideBySide" class="mb-6">
        <!-- Client Chart -->
        <VCol cols="12" md="6">
          <VCard elevation="2">
            <VCardTitle class="d-flex align-center pa-6">
              <VAvatar color="primary" variant="tonal" class="me-3">
                <VIcon icon="tabler-chart-dots" />
              </VAvatar>
              <div class="flex-grow-1">
                <h5 class="text-h5 mb-1">
                  {{ latestClientData.target.toUpperCase() || 'Client Domain' }}
                </h5>
                <p class="text-body-2 text-medium-emphasis mb-0">
                  {{chartTypeOptions.find((c) => c.value === selectedChart)?.title}}
                  {{ selectedHistoryDate ? `- ${formatDate(selectedHistoryDate)}` : '' }}
                </p>
              </div>
            </VCardTitle>

            <VDivider />

            <VCardText class="pa-6">
              <!-- Types Chart -->
              <div v-if="selectedChart === 'types'">
                <DonutChart :data="getChartDataForDomain(selectedClientData, 'types')" title="Client Link Types" />
              </div>

              <!-- Attributes Chart -->
              <div v-else-if="selectedChart === 'attributes'">
                <DonutChart :data="getChartDataForDomain(selectedClientData, 'attributes')"
                  title="Client Link Attributes" />
              </div>

              <!-- Platforms Chart -->
              <div v-else-if="selectedChart === 'platforms'">
                <BarChart title="Platform Types (Client)"
                  :data="getChartDataForDomain(selectedClientData, 'platforms')" />
              </div>

              <!-- Geography Chart -->
              <div v-else-if="selectedChart === 'geography'">
                <BarChart title="Geographic Distribution (Client)"
                  :data="getChartDataForDomain(selectedClientData, 'geography')" />
              </div>

              <!-- TLD Chart -->
              <div v-else-if="selectedChart === 'tld'">
                <DonutChart :data="getChartDataForDomain(selectedClientData, 'attributes')" title="Client TLD Chart" />
              </div>
            </VCardText>
          </VCard>
        </VCol>

        <!-- Rival Chart -->
        <VCol cols="12" md="6">
          <VCard elevation="2">
            <VRow align="center">
              <VCol>
                <VCardTitle class="d-flex align-center pa-6">
                  <VAvatar color="warning" variant="tonal" class="me-3">
                    <VIcon icon="tabler-chart-dots" />
                  </VAvatar>
                  <div class="flex-grow-1">
                    <h5 class="text-h5 mb-1">
                      {{ selectedRivalDomain.toUpperCase() || 'Rival Domain' }}
                    </h5>
                    <p class="text-body-2 text-medium-emphasis mb-0">
                      {{chartTypeOptions.find((c) => c.value === selectedChart)?.title}}
                      {{ selectedHistoryDate ? `- ${formatDate(selectedHistoryDate)}` : '' }}
                    </p>
                  </div>
                </VCardTitle>
              </VCol>
              <VCol v-if="showSideBySide && availableRivalDomains.length > 0" cols="12" sm="4" class="pe-6">
                <VSelect v-model="selectedRivalDomain" :items="availableRivalDomains" label="Rival Domain"
                  variant="outlined" prepend-inner-icon="tabler-building" hide-details />
              </VCol>
            </VRow>

            <VDivider />

            <VCardText class="pa-6">
              <template v-if="selectedRivalDomainData || latestSelectedRivalData">
                <!-- Types Chart -->
                <div v-if="selectedChart === 'types'">
                  <DonutChart :data="getChartDataForDomain(selectedRivalDomainData, 'types')"
                    title="Rival Link Types" />
                </div>

                <!-- Attributes Chart -->
                <div v-else-if="selectedChart === 'attributes'">
                  <DonutChart :data="getChartDataForDomain(selectedRivalDomainData, 'attributes')"
                    title="Rival Link Attributes" />
                </div>

                <!-- Platforms Chart -->
                <div v-else-if="selectedChart === 'platforms'">
                  <BarChart title="Platform Types (Rival)"
                    :data="getChartDataForDomain(selectedRivalDomainData, 'platforms')" />
                </div>

                <!-- Geography Chart -->
                <div v-else-if="selectedChart === 'geography'">
                  <BarChart title="Geographic Distribution (Rival)"
                    :data="getChartDataForDomain(selectedRivalDomainData, 'geography')" />
                </div>

                <!-- TLD Chart -->
                <div v-else-if="selectedChart === 'tld'">
                  <DonutChart :data="getChartDataForDomain(selectedRivalDomainData, 'attributes')"
                    title="Rival TLD Chart" />
                </div>
              </template>

              <!-- No Rival Data State -->
              <div v-else class="text-center py-16">
                <VAvatar size="80" color="grey-lighten-2" class="mb-4">
                  <VIcon icon="tabler-database-off" size="40" />
                </VAvatar>
                <p class="text-body-1 text-medium-emphasis">
                  No data available for selected rival domain
                </p>
              </div>
            </VCardText>
          </VCard>
        </VCol>
      </VRow>

      <!-- Charts - Single Layout for trend/comparison or client-only mode -->
      <VCard v-else elevation="2" class="mb-6">
        <VCardTitle class="d-flex align-center pa-6">
          <VAvatar color="primary" variant="tonal" class="me-4">
            <VIcon icon="tabler-chart-dots" />
          </VAvatar>

          <div>
            <h5 class="text-h5 mb-1">
              {{ metricOptions.find(c => c.value == selectedMetric)?.title }}
              {{chartTypeOptions.find(c => c.value === selectedChart)?.title}}
            </h5>
            <p class="text-body-2 text-medium-emphasis mb-0">
              {{ viewMode === 'client'
              ? 'Client data analysis'
              : 'Visual analysis of domain performance metrics' }}
              {{ selectedHistoryDate ? `- ${formatDate(selectedHistoryDate)}` : '' }}
            </p>
          </div>
        </VCardTitle>


        <VDivider />

        <VCardText class="pa-6">
          <!-- Trend Chart -->
          <div v-if="selectedChart === 'trend'">
            <MultiBarChart title="Trend" :history="clientHistory" :rivals="rivalsByDomain" :metric="selectedMetric"
              :viewMode="viewMode" />
          </div>

          <!-- Comparison Chart -->
          <div v-else-if="selectedChart === 'comparison'">
            <VueApexCharts type="bar" :height="400" :options="comparisonChartOptions.chartOptions"
              :series="comparisonChartOptions.series" />
          </div>
          <!-- Types Chart -->
          <div v-if="selectedChart === 'types'">
            <DonutChart :data="getChartDataForDomain(selectedClientData, 'types')" title="Client Link Types" />
          </div>

          <!-- Attributes Chart -->
          <div v-else-if="selectedChart === 'attributes'">
            <DonutChart :data="getChartDataForDomain(selectedClientData, 'attributes')"
              title="Client Link Attributes" />
          </div>

          <!-- Platforms Chart -->
          <div v-else-if="selectedChart === 'platforms'">
            <BarChart title="Platform Types (Client)" :data="getChartDataForDomain(selectedClientData, 'platforms')" />
          </div>

          <!-- Geography Chart -->
          <div v-else-if="selectedChart === 'geography'">
            <BarChart title="Geographic Distribution (Client)"
              :data="getChartDataForDomain(selectedClientData, 'geography')" />
          </div>

          <!-- TLD Chart -->
          <div v-else-if="selectedChart === 'tld'">
            <DonutChart :data="getChartDataForDomain(selectedClientData, 'attributes')" title="Client TLD Chart" />
          </div>
        </VCardText>
      </VCard>

      <!-- Detailed Comparison Table (only in comparison mode) -->
      <VCard v-if="viewMode === 'comparison'" elevation="2">
        <VCardTitle class="d-flex align-center pa-6">
          <VAvatar color="info" variant="tonal" class="me-3">
            <VIcon icon="tabler-table" />
          </VAvatar>
          <div>
            <h5 class="text-h5">Metrics Comparison</h5>
            <p class="text-body-2 text-medium-emphasis">
              {{ selectedHistoryDate ? formatDate(selectedHistoryDate) : 'Latest snapshot' }} of all domains
            </p>
          </div>
        </VCardTitle>

        <VDivider />

        <VCardText class="pa-0">
          <div class="table-responsive">
            <table class="comparison-table">
              <thead>
                <tr>
                  <th>Domain</th>
                  <th>Rank</th>
                  <th>Backlinks</th>
                  <th>Ref. Domains</th>
                  <th>New Links</th>
                  <th>Lost Links</th>
                  <th>Broken Links</th>
                </tr>
              </thead>
              <tbody>
                <tr class="client-row">
                  <td>
                    <VChip color="primary" variant="flat" size="small">
                      {{ (selectedHistoryDate ? selectedClientData : latestClientData).target }}
                    </VChip>
                  </td>
                  <td>#{{ formatNumber((selectedHistoryDate ? selectedClientData : latestClientData).rank) }}</td>
                  <td>{{ formatNumber((selectedHistoryDate ? selectedClientData : latestClientData).backlinks) }}</td>
                  <td>{{ formatNumber((selectedHistoryDate ? selectedClientData : latestClientData).referring_domains)
                    }}</td>
                  <td class="text-success">+{{ formatNumber((selectedHistoryDate ? selectedClientData :
                    latestClientData).new_backlinks) }}</td>
                  <td class="text-error">-{{ formatNumber((selectedHistoryDate ? selectedClientData :
                    latestClientData).lost_backlinks) }}</td>
                  <td>{{ formatNumber((selectedHistoryDate ? selectedClientData : latestClientData).broken_backlinks) }}
                  </td>
                </tr>
                <tr v-for="(data, domain) in (selectedHistoryDate ? selectedRivalData : latestRivalData)" :key="domain">
                  <td>{{ domain }}</td>
                  <td>#{{ formatNumber(data.rank) }}</td>
                  <td>{{ formatNumber(data.backlinks) }}</td>
                  <td>{{ formatNumber(data.referring_domains) }}</td>
                  <td class="text-success">+{{ formatNumber(data.new_backlinks) }}</td>
                  <td class="text-error">-{{ formatNumber(data.lost_backlinks) }}</td>
                  <td>{{ formatNumber(data.broken_backlinks) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </VCardText>
      </VCard>

      <!-- Client Only Detailed Stats -->
      <VCard v-else elevation="2">
        <VCardTitle class="d-flex align-center pa-6">
          <VAvatar color="info" variant="tonal" class="me-3">
            <VIcon icon="tabler-info-circle" />
          </VAvatar>
          <div>
            <h5 class="text-h5">Client Domain Details</h5>
            <p class="text-body-2 text-medium-emphasis">
              Detailed metrics for {{ selectedHistoryDate ? formatDate(selectedHistoryDate) : 'latest data' }}
            </p>
          </div>
        </VCardTitle>

        <VDivider />

        <VCardText>
          <VRow>
            <VCol cols="12" md="6">
              <VCard variant="tonal" color="primary">
                <VCardText>
                  <h6 class="text-h6 mb-3">Link Statistics</h6>
                  <div class="d-flex justify-space-between mb-2">
                    <span>Total Backlinks:</span>
                    <strong>{{ formatNumber((selectedHistoryDate ? selectedClientData : latestClientData).backlinks)
                      }}</strong>
                  </div>
                  <div class="d-flex justify-space-between mb-2">
                    <span>Internal Links:</span>
                    <strong>{{ formatNumber((selectedHistoryDate ? selectedClientData :
                      latestClientData).internal_links_count) }}</strong>
                  </div>
                  <div class="d-flex justify-space-between mb-2">
                    <span>External Links:</span>
                    <strong>{{ formatNumber((selectedHistoryDate ? selectedClientData :
                      latestClientData).external_links_count) }}</strong>
                  </div>
                  <div class="d-flex justify-space-between">
                    <span>Broken Backlinks:</span>
                    <strong>{{ formatNumber((selectedHistoryDate ? selectedClientData :
                      latestClientData).broken_backlinks) }}</strong>
                  </div>
                </VCardText>
              </VCard>
            </VCol>
            <VCol cols="12" md="6">
              <VCard variant="tonal" color="success">
                <VCardText>
                  <h6 class="text-h6 mb-3">Domain Statistics</h6>
                  <div class="d-flex justify-space-between mb-2">
                    <span>Referring Domains:</span>
                    <strong>{{ formatNumber((selectedHistoryDate ? selectedClientData :
                      latestClientData).referring_domains) }}</strong>
                  </div>
                  <div class="d-flex justify-space-between mb-2">
                    <span>Referring IPs:</span>
                    <strong>{{ formatNumber((selectedHistoryDate ? selectedClientData : latestClientData).referring_ips)
                      }}</strong>
                  </div>
                  <div class="d-flex justify-space-between mb-2">
                    <span>Referring Subnets:</span>
                    <strong>{{ formatNumber((selectedHistoryDate ? selectedClientData :
                      latestClientData).referring_subnets) }}</strong>
                  </div>
                  <div class="d-flex justify-space-between">
                    <span>Referring Pages:</span>
                    <strong>{{ formatNumber((selectedHistoryDate ? selectedClientData :
                      latestClientData).referring_pages) }}</strong>
                  </div>
                </VCardText>
              </VCard>
            </VCol>
          </VRow>
        </VCardText>
      </VCard>
    </template>

    <!-- No Data State -->
    <VCard v-else elevation="2">
      <VCardText class="text-center py-16">
        <VAvatar size="120" color="grey-lighten-2" class="mb-6">
          <VIcon icon="tabler-database-off" size="60" />
        </VAvatar>
        <h4 class="text-h4 mb-4">No Data Available</h4>
        <p class="text-body-1 text-medium-emphasis mb-6">
          No comparison data found for this domain.
        </p>
        <VBtn color="primary" @click="loadComparisonData">
          <VIcon icon="tabler-refresh" class="me-2" />
          Load Data
        </VBtn>
      </VCardText>
    </VCard>
  </div>
</template>

<style lang="scss" scoped>
.table-responsive {
  overflow-x: auto;
}

.comparison-table {
  width: 100%;
  border-collapse: collapse;

  th,
  td {
    padding: 16px;
    text-align: left;
    border-bottom: 1px solid rgba(var(--v-theme-on-surface), 0.12);
  }

  th {
    background-color: rgba(var(--v-theme-primary), 0.08);
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  tbody tr {
    transition: all 0.2s ease;

    &:hover {
      background-color: rgba(var(--v-theme-primary), 0.04);
    }

    &.client-row {
      background-color: rgba(var(--v-theme-primary), 0.08);
      font-weight: 600;
    }
  }

  .text-success {
    color: rgb(var(--v-theme-success));
    font-weight: 600;
  }

  .text-error {
    color: rgb(var(--v-theme-error));
    font-weight: 600;
  }
}


@media (max-width: 768px) {
  .comparison-table {

    th,
    td {
      padding: 12px 8px;
      font-size: 0.8rem;
    }
  }
}
</style>