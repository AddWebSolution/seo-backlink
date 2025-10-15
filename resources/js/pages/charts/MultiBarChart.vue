<script setup>
import { computed } from 'vue'
import { useTheme } from 'vuetify'
import VueApexCharts from 'vue3-apexcharts'

const props = defineProps({
    history: {
        type: Array,
        default: () => [],
    },
    rivals: {
        type: Object,
        default: () => ({}),
    },
    metric: {
        type: String,
        default: 'value',
    },
    viewMode: {
        type: String,
        default: 'client',
    },
    title: {
        type: String,
        default: 'Chart',
    },
    reversedY: {
        type: Boolean,
        default: false,
    },
})

const vuetifyTheme = useTheme()
const isDark = computed(() => vuetifyTheme.global.current.value.dark)

const chartData = computed(() => {
    if (!props.history.length) return { series: [], categories: [] }

    const allDates = [
        ...new Set(
            props.history.map((h) => h.history_date).concat(
                Object.values(props.rivals || {}).flatMap((r) => (r || []).map((h) => h.history_date))
            )
        ),
    ].sort((a, b) => new Date(a) - new Date(b))

    const categories = allDates.map((d) =>
        new Date(d).toLocaleString('default', { month: 'short', year: 'numeric' })
    )

    const buildSeriesData = (history) =>
        allDates.map((date) => {
            const record = history.find((h) => h.history_date === date)
            return record ? Number(record[props.metric] || 0) : 0
        })

    const series = []

    if (['client', 'comparison'].includes(props.viewMode)) {
        series.push({
            name: props.history[0]?.target || 'Client',
            data: buildSeriesData(props.history),
        })
    }

    if (props.viewMode === 'comparison') {
        Object.entries(props.rivals || {}).forEach(([domain, history]) => {
            series.push({
                name: domain,
                data: buildSeriesData(history || []),
            })
        })
    }

    return { series, categories }
})

const chartOptions = computed(() => ({
    chart: { type: 'bar', height: 400, background: 'transparent', toolbar: { show: true } },
    plotOptions: {
        bar: {
            horizontal: false, columnWidth: '65%', endingShape: 'rounded', dataLabels: {
                position: "top",
            },
        }
    },
    colors: ['#7367F0', '#FF9F43', '#28C76F', '#EA5455', '#00CFE8'],
    dataLabels: {
        enabled: true,
        offsetY: -18,
        style: {
            fontSize: '13px',
            colors: isDark.value ? ['#E0E0E0'] : ['#000'],
        },
    },
    stroke: { show: true, width: 4, colors: ['transparent'] },
    xaxis: { categories: chartData.value.categories, labels: { style: { colors: isDark.value ? '#E0E0E0' : '#6E6B7B' } } },
    yaxis: { reversed: props.reversedY, labels: { style: { colors: isDark.value ? '#E0E0E0' : '#6E6B7B' } } },
    legend: { position: 'bottom', labels: { colors: isDark.value ? '#E0E0E0' : '#6E6B7B' } },
    grid: { borderColor: isDark.value ? '#444' : '#E0E0E0' },
    tooltip: { theme: isDark.value ? 'dark' : 'light' },
    animations: {
    enabled: true,
    easing: 'easein',
    speed: 500,  
    animateGradually: { enabled: true, delay: 0 },
    dynamicAnimation: { enabled: true, speed: 500 },
  },
}))
</script>

<template>
    <VCardTitle class="text-h6">{{ title }}</VCardTitle>
    <VueApexCharts v-if="chartData.series.length" type="bar" height="400" :options="chartOptions"
        :series="chartData.series" />
    <p v-else class="text-center py-8">No data available</p>
</template>
