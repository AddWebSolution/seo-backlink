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

const chartOptions = computed(() => {
    const seriesCount = chartData.value.series.length
    const categoryCount = chartData.value.categories.length

    const isHorizontal = categoryCount > 10
    
    // Calculate optimal column width based on number of series
    let columnWidth
    if (seriesCount <= 3) {
        columnWidth = '65%'
    } else if (seriesCount <= 6) {
        columnWidth = '55%'
    } else if (seriesCount <= 10) {
        columnWidth = '45%'
    } else {
        columnWidth = '35%'
    }
    
    // Disable data labels when there are too many series
    const showDataLabels = seriesCount <= 8
    
    // Calculate dynamic height based on bars and categories
    const baseHeight = 400
    const heightMultiplier = categoryCount > 10 ? Math.min(2.5, 1 + (categoryCount / 20)) : 1
    const dynamicHeight = Math.round(baseHeight * heightMultiplier)
    
    return {
        chart: {
            type: 'bar',
            height: dynamicHeight,
            background: 'transparent',
            toolbar: {
                show: true,
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
                horizontal: categoryCount > 10,
                columnWidth: columnWidth,
                endingShape: 'rounded',
                dataLabels: { position: 'top' },
                distributed: false,
            },
        },
        colors: [
            '#7367F0', '#FF9F43', '#28C76F', '#EA5455', '#00CFE8',
            '#8A2BE2', '#FF1493', '#20B2AA', '#FFD700', '#FF4500',
            '#9C27B0', '#00BCD4', '#4CAF50', '#FF5722', '#795548',
            '#607D8B', '#E91E63', '#3F51B5', '#FFEB3B', '#009688'
        ],
        dataLabels: {
            enabled: showDataLabels,
            offsetY: categoryCount > 10 ? 0 : -12,
            offsetX: categoryCount > 10 ? 5 : 0,
            style: {
                fontSize: seriesCount > 10 ? '8px' : seriesCount > 6 ? '9px' : '11px',
                colors: isDark.value ? ['#E0E0E0'] : ['#000'],
                fontWeight: 500,
            },
            formatter: val => val || '',
        },
        stroke: { show: true, width: 2, colors: ['transparent'] },
        xaxis: {
            categories: chartData.value.categories,
            labels: {
                rotate: categoryCount > 5 ? -45 : 0,
                style: { 
                    colors: isDark.value ? '#E0E0E0' : '#6E6B7B',
                    fontSize: '11px'
                },
                trim: true,
            },
        },
        yaxis: {
            reversed: props.reversedY,
            labels: { 
                style: { 
                    colors: isDark.value ? '#E0E0E0' : '#6E6B7B',
                    fontSize: '11px'
                } 
            },
        },
        legend: {
            position: 'bottom',
            horizontalAlign: 'center',
            floating: false,
            fontSize: seriesCount > 10 ? '10px' : '12px',
            labels: { colors: isDark.value ? '#E0E0E0' : '#6E6B7B' },
            markers: {
                width: 10,
                height: 10,
            },
            itemMargin: {
                horizontal: 8,
                vertical: 4,
            },
        },
        grid: { 
            borderColor: isDark.value ? '#444' : '#E0E0E0',
            padding: {
                top: showDataLabels ? 20 : 10,
                bottom: 10,
            }
        },
        tooltip: { 
            theme: isDark.value ? 'dark' : 'light',
            shared: false,
            intersect: true,
        },
        animations: {
            enabled: true,
            easing: 'easein',
            speed: 500,
            animateGradually: { enabled: true, delay: 0 },
            dynamicAnimation: { enabled: true, speed: 500 },
        },
    }
})
</script>

<template>
    <VCardTitle class="text-h6">{{ title }}</VCardTitle>
    <VueApexCharts 
        v-if="chartData.series.length" 
        type="bar" 
        :height="chartOptions.chart.height" 
        :options="chartOptions"
        :series="chartData.series" 
    />
    <p v-else class="text-center py-8">No data available</p>
</template>