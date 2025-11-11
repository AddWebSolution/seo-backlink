<script setup>
import { ref, computed, onMounted } from 'vue'
import { useDashboardApi } from '@/composables/dashboardApi'
import useAuthStore from '@/router/store/auth'
import CrmEarningReportsYearlyOverview from '@/views/dashboards/crm/CrmEarningReportsYearlyOverview.vue'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()
const user = authStore.user

const { monthlyStats, dashboard, fetchStats, fetchMonthlyStats, loading } = useDashboardApi()

const showAdvanced = ref(true)

const showCharts = ref(false)


const stats = computed(() => ({
  totalClients: dashboard.value?.total_clients || 0,
  activeDomains: dashboard.value?.Domains?.active || 0,
  totalReports: dashboard.value?.backlink?.total_backlink_report || 0,
  keywordReports: dashboard.value?.keyword?.total_keyword_report || 0,
  rivalDomains: dashboard.value?.rivalDomains?.total || 0
}))

const recentActivity = ref([
  { title: 'New client registered', time: '2 hours ago', icon: 'tabler-user-plus', color: 'success' },
  { title: 'Report generated', time: '5 hours ago', icon: 'tabler-file-text', color: 'info' },
  { title: 'Domain verified', time: '1 day ago', icon: 'tabler-shield-check', color: 'primary' },
  { title: 'User updated profile', time: '2 days ago', icon: 'tabler-edit', color: 'warning' }
])

const quickActions = ref(
  user.role == 1
    ? [
      { title: 'Add Client', icon: 'tabler-user-plus', color: 'primary', route: 'client-add' },
      { title: 'View Reports', icon: 'tabler-chart-bar', color: 'success', route: 'apps-report-list' },
      { title: 'Manage Domains', icon: 'tabler-world', color: 'info', route: 'clientdomain-list' },
      { title: 'Settings', icon: 'tabler-settings', color: 'warning', route: 'settings' }
    ]
    : [
      { title: 'My Domains', icon: 'tabler-world', color: 'primary', route: 'clientdomain-list' },
      { title: 'View Reports', icon: 'tabler-chart-bar', color: 'success', route: 'apps-report-list' },
      { title: 'Analytics', icon: 'tabler-chart-line', color: 'info', route: 'analytics' },
      { title: 'Support', icon: 'tabler-help', color: 'warning', route: 'support' }
    ]
)

watch(showAdvanced, (val) => {
  if (!val) showCharts.value = false
})

const statistics = computed(() => {
  if (!dashboard.value) return []

  if (user.role === 1) {
    return [
      {
        title: 'Total Users',
        stats: dashboard.value.total_users,
        icon: 'tabler-users-group',
        color: 'primary',
        trend: '+12%',
        trendUp: true,
      },
      {
        title: 'Total Clients',
        stats: dashboard.value.total_clients,
        icon: 'tabler-briefcase',
        color: 'info',
        trend: '+8%',
        trendUp: true,
      },
      {
        title: 'Total Domains',
        stats: dashboard.value.Domains?.total,
        icon: 'tabler-world',
        color: 'success',
        trend: '+15%',
        trendUp: true,
      },
      {
        title: 'Total Rival Domains',
        stats: dashboard.value.rivalDomains?.total,
        icon: 'tabler-world',
        color: 'success',
        trend: '+15%',
        trendUp: true,
      },
      {
        title: 'Backlinks',
        stats: dashboard.value.backlink?.total,
        icon: 'tabler-link',
        color: 'warning',
      },
      {
        title: 'Total Backlinks Reports',
        stats: dashboard.value.backlink?.total_backlink_report,
        icon: 'tabler-report-analytics',
        color: 'warning',
      },
      {
        title: 'Accepted Backlinks',
        stats: dashboard.value.backlink?.accepted_backlink,
        icon: 'tabler-circle-check',
        color: 'success',
      },
      {
        title: 'Rejected Backlinks',
        stats: dashboard.value.backlink?.rejected_backlink,
        icon: 'tabler-circle-x',
        color: 'error',
      },
      {
        title: 'Keywords',
        stats: dashboard.value.keyword?.total,
        icon: 'tabler-report-analytics',
        color: 'secondary',
      },
      {
        title: 'Keyword Reports',
        stats: dashboard.value.keyword?.total_keyword_report,
        icon: 'tabler-report-analytics',
        color: 'secondary',
      },
      {
        title: 'Active Domains',
        stats: dashboard.value.Domains?.active,
        icon: 'tabler-bolt',
        color: 'teal',
      },
    ]
  } else {
    return [
      {
        title: 'My Domains',
        stats: dashboard.value.Domains?.total,
        icon: 'tabler-world',
        color: 'primary',
      },
      {
        title: 'Total Rival Domains',
        stats: dashboard.value.rivalDomains?.total,
        icon: 'tabler-world',
        color: 'success',
        trend: '+15%',
        trendUp: true,
      },
      {
        title: 'Active Domains',
        stats: dashboard.value.Domains?.active,
        icon: 'tabler-bolt',
        color: 'success',
      },
      {
        title: 'Backlinks',
        stats: dashboard.value.backlink?.total,
        icon: 'tabler-link',
        color: 'warning',
      },
      {
        title: 'Accepted Backlinks',
        stats: dashboard.value.backlink?.accepted_backlink,
        icon: 'tabler-circle-check',
        color: 'success',
      },
      {
        title: 'Rejected Backlinks',
        stats: dashboard.value.backlink?.rejected_backlink,
        icon: 'tabler-circle-x',
        color: 'error',
      },
      {
        title: 'Keywords',
        stats: dashboard.value.keyword?.total,
        icon: 'tabler-report-analytics',
        color: 'secondary',
      },
      {
        title: 'Keyword Reports',
        stats: dashboard.value.keyword?.total_keyword_report,
        icon: 'tabler-report-analytics',
        color: 'secondary',
      },
    ]
  }
})

onMounted(async () => {
  await fetchStats()
  await fetchMonthlyStats()
})
</script>

<template>
  <VCard class="dashboard-card" elevation="2">
    <VCardText class="pa-6">
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h4 class="text-h5 font-weight-bold mb-1">
            {{ user.role === 1 ? 'Admin Overview' : 'My Dashboard' }}
          </h4>
          <p class="text-body-2 text-medium-emphasis mb-0">
            Real-time statistics and insights
          </p>
        </div>

        <div class="d-flex align-center gap-2">
          <VChip color="success" variant="tonal" size="small" prepend-icon="tabler-circle-filled" class="pulse-chip">
            Live
          </VChip>

          <VBtn :color="showAdvanced ? 'primary' : 'default'" variant="tonal" size="small"
            @click="showAdvanced = !showAdvanced">
            <VIcon :icon="showAdvanced ? 'tabler-layout-grid' : 'tabler-layout-dashboard'" start size="18" />
            {{ showAdvanced ? 'Normal View' : 'Advanced View' }}
          </VBtn>

          <VBtn v-if="showAdvanced" :color="showCharts ? 'primary' : 'default'" variant="tonal" size="small"
            @click="showCharts = !showCharts">
            <VIcon :icon="showCharts ? 'tabler-layout-grid' : 'tabler-chart-bar'" start size="18" />
            {{ showCharts ? 'Chart View' : 'Grid View' }}
          </VBtn>
        </div>
      </div>

      <!-- Loading State -->
      <VRow v-if="loading">
        <VCol v-for="n in 6" :key="n" cols="12" sm="6" md="4" lg="3">
          <VSkeletonLoader type="card" />
        </VCol>
      </VRow>


      <!-- Stats Grid -->
      <VRow v-else-if="statistics.length && !showAdvanced">
        <VCol v-for="item in statistics" :key="item.title" cols="12" sm="6" md="4" lg="3">
          <VCard class="stat-card" :class="`stat-card--${item.color}`" elevation="0" border>
            <VCardText class="pa-4">
              <div class="d-flex align-start justify-space-between mb-3">
                <VAvatar :color="item.color" variant="tonal" rounded="lg" size="48" class="stat-avatar">
                  <VIcon :icon="item.icon" size="24" />
                </VAvatar>

                <VChip v-if="item.trend" :color="item.trendUp ? 'success' : 'error'" variant="tonal" size="x-small"
                  class="font-weight-medium">
                  <VIcon :icon="item.trendUp ? 'tabler-trending-up' : 'tabler-trending-down'" size="14" start />
                  {{ item.trend }}
                </VChip>
              </div>

              <div>
                <h3 class="text-h4 font-weight-bold mb-1">
                  {{ item.stats?.toLocaleString() || '0' }}
                </h3>
                <p class="text-body-2 text-medium-emphasis mb-0">
                  {{ item.title }}
                </p>
              </div>
            </VCardText>
          </VCard>
        </VCol>
      </VRow>

      <!-- Empty State -->
      <div v-else-if="!statistics" class="text-center py-8">
        <VIcon icon="tabler-database-off" size="48" class="text-disabled mb-2" />
        <p class="text-body-1 text-disabled">No data available</p>
      </div>

      <div v-if="showAdvanced">
        <div v-if="showCharts">
          <VRow class="mb-6">
            <VCol cols="12" md="8">
              <VCard class="elevation-2" style="height: 100%;">
                <VCardTitle class="d-flex align-center pa-5 pb-4">
                  <VIcon icon="tabler-bolt" size="24" class="me-2 text-primary" />
                  <span class="text-h6">Quick Actions</span>
                </VCardTitle>
                <VCardText class="pb-5">
                  <VRow>
                    <VCol v-for="action in quickActions" :key="action.title" cols="6" sm="6" md="3">
                      <VCard class="action-card text-center pa-4 cursor-pointer" flat :color="`${action.color}10`" hover
                        @click="router.push({ name: action.route })">
                        <VAvatar :color="action.color" size="56" class="mb-3">
                          <VIcon :icon="action.icon" size="32" />
                        </VAvatar>
                        <p class="text-body-2 font-weight-medium mb-0">{{ action.title }}</p>
                      </VCard>
                    </VCol>
                  </VRow>
                </VCardText>
              </VCard>
            </VCol>

            <VCol cols="12" md="4">
              <VCard class="elevation-2" style="height: 100%;">
                <VCardTitle class="d-flex align-center pa-2">
                  <VIcon icon="tabler-activity" size="24" class="me-2 text-info" />
                  <span class="text-h6">Recent Activity</span>
                </VCardTitle>
                <VCardText class="pt-0">
                  <VTimeline density="compact" side="end" align="start" truncate-line="both">
                    <VTimelineItem v-for="(activity, index) in recentActivity" :key="index" :dot-color="activity.color"
                      size="x-small" class="mb-3">
                      <template #icon>
                        <VIcon :icon="activity.icon" size="14" />
                      </template>
                      <div class="d-flex flex-column">
                        <span class="text-body-2 font-weight-medium">{{ activity.title }}</span>
                        <span class="text-caption text-medium-emphasis">{{ activity.time }}</span>
                      </div>
                    </VTimelineItem>
                  </VTimeline>
                </VCardText>
              </VCard>
            </VCol>
          </VRow>

          <VRow>
            <VCol v-if="user.role == 1" cols="12" sm="6" md="3">
              <VCard class="stat-card elevation-2" hover>
                {{ st }}
                <VCardText>
                  <div class="d-flex justify-space-between align-start mb-4">
                    <div>
                      <p class="text-body-2 text-medium-emphasis mb-1">Total Clients</p>
                      <h4 class="text-h4 font-weight-bold">{{ stats.totalClients }}</h4>
                    </div>
                    <VAvatar color="primary" variant="tonal" size="48">
                      <VIcon icon="tabler-users" size="28" />
                    </VAvatar>
                  </div>
                  <div class="d-flex align-center">
                    <VIcon icon="tabler-trending-up" size="16" color="success" class="me-1" />
                    <span class="text-success text-body-2 font-weight-medium">+12%</span>
                    <span class="text-body-2 text-medium-emphasis ms-2">vs last month</span>
                  </div>
                </VCardText>
              </VCard>
            </VCol>

            <VCol cols="12" sm="6" md="3">
              <VCard class="stat-card elevation-2" hover>
                <VCardText>
                  <div class="d-flex justify-space-between align-start mb-4">
                    <div>
                      <p class="text-body-2 text-medium-emphasis mb-1">Active Domains</p>
                      <h4 class="text-h4 font-weight-bold">{{ stats.activeDomains }}</h4>
                    </div>
                    <VAvatar color="success" variant="tonal" size="48">
                      <VIcon icon="tabler-world" size="28" />
                    </VAvatar>
                  </div>
                  <div class="d-flex align-center">
                    <VIcon icon="tabler-trending-up" size="16" color="success" class="me-1" />
                    <span class="text-success text-body-2 font-weight-medium">+8%</span>
                    <span class="text-body-2 text-medium-emphasis ms-2">vs last month</span>
                  </div>
                </VCardText>
              </VCard>
            </VCol>


            <VCol cols="12" sm="6" md="3">
              <VCard class="stat-card elevation-2" hover>
                <VCardText>
                  <div class="d-flex justify-space-between align-start mb-4">
                    <div>
                      <p class="text-body-2 text-medium-emphasis mb-1">Total Reports</p>
                      <h4 class="text-h4 font-weight-bold">{{ stats.totalReports }}</h4>
                    </div>
                    <VAvatar color="info" variant="tonal" size="48">
                      <VIcon icon="tabler-file-text" size="28" />
                    </VAvatar>
                  </div>
                  <div class="d-flex align-center">
                    <VIcon icon="tabler-trending-up" size="16" color="success" class="me-1" />
                    <span class="text-success text-body-2 font-weight-medium">+24%</span>
                    <span class="text-body-2 text-medium-emphasis ms-2">vs last month</span>
                  </div>
                </VCardText>
              </VCard>
            </VCol>

            <VCol cols="12" sm="6" md="3">
              <VCard class="stat-card elevation-2" hover>
                <VCardText>
                  <div class="d-flex justify-space-between align-start mb-4">
                    <div>
                      <p class="text-body-2 text-medium-emphasis mb-1">Keyword Reports</p>
                      <h4 class="text-h4 font-weight-bold">{{ stats.keywordReports }}</h4>
                    </div>
                    <VAvatar color="secondary" variant="tonal" size="48">
                      <VIcon icon="tabler-file-text" size="28" />
                    </VAvatar>
                  </div>
                  <div class="d-flex align-center">
                    <VIcon icon="tabler-trending-up" size="16" color="success" class="me-1" />
                    <span class="text-success text-body-2 font-weight-medium">+18%</span>
                    <span class="text-body-2 text-medium-emphasis ms-2">vs last month</span>
                  </div>
                </VCardText>
              </VCard>
            </VCol>
            
            <VCol cols="12" sm="6" md="3">
              <VCard class="stat-card elevation-2" hover>
                <VCardText>
                  <div class="d-flex justify-space-between align-start mb-2">
                    <div>
                      <p class="text-body-2 text-medium-emphasis mb-1">Rival Domains</p>
                      <h4 class="text-h4 font-weight-bold">{{ stats.rivalDomains }}</h4>
                    </div>
                    <VAvatar color="warning" variant="tonal" size="48">
                      <VIcon icon="tabler-world" size="28" />
                    </VAvatar>
                  </div>
                  <div class="d-flex align-center">
                    <VIcon icon="tabler-trending-down" size="16" color="error" class="me-1" />
                    <span class="text-error text-body-2 font-weight-medium">-5%</span>
                    <span class="text-body-2 text-medium-emphasis ms-2">vs last month</span>
                  </div>
                </VCardText>
              </VCard>
            </VCol>
          </VRow>
        </div>

        <VRow v-else>
          <VCol cols="12" md="12">
            <CrmEarningReportsYearlyOverview :data="monthlyStats" />
          </VCol>
        </VRow>
      </div>
    </VCardText>
  </VCard>
</template>

<style scoped>
.dashboard-card {
  border-radius: 12px;
  overflow: hidden;
}

.stat-card {
  border-radius: 12px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
  position: relative;
  overflow: hidden;
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: currentColor;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.stat-card:hover::before {
  opacity: 1;
}

.stat-card--primary::before {
  background: rgb(var(--v-theme-primary));
}

.stat-card--info::before {
  background: rgb(var(--v-theme-info));
}

.stat-card--success::before {
  background: rgb(var(--v-theme-success));
}

.stat-card--warning::before {
  background: rgb(var(--v-theme-warning));
}

.stat-card--error::before {
  background: rgb(var(--v-theme-error));
}

.stat-card--secondary::before {
  background: rgb(var(--v-theme-secondary));
}

.stat-card--teal::before {
  background: #009688;
}

.stat-avatar {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.text-medium-emphasis {
  color: rgba(var(--v-theme-on-surface), 0.6);
}

.pulse-chip {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {

  0%,
  100% {
    opacity: 1;
  }

  50% {
    opacity: 0.7;
  }
}

/* Responsive adjustments */
@media (max-width: 960px) {
  .stat-card {
    margin-bottom: 0;
  }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .stat-card:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
  }
}
</style>