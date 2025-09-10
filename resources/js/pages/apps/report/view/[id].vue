<script setup>
import { useReportShow } from "@/composables/reportApi";
import { useDomainList } from "@/composables/domainApi";
import { reactive, computed, ref, watch, unref } from "vue";
import { useRoute } from "vue-router";

const route = useRoute();
const reportId = computed(() => route.params.id);

// Search and filters
const searchQuery = ref("");
const statusFilter = ref("");
const selectedDomain = ref("");
const allSeenDomains = ref(new Set());


const uiState = reactive({
  page: 1,
  itemsPerPage: 10,
});

// request body
const requestBody = computed(() => ({
  page: uiState.page,
  per_page: uiState.itemsPerPage,
  search: searchQuery.value,
  status: statusFilter.value,
  domain: selectedDomain.value,
}));


watch(
  requestBody,
  () => {
    refresh();
    console.log("after request body", unref(requestBody));
  },
  { deep: true }
);

watch([searchQuery, statusFilter, selectedDomain], () => {
  uiState.page = 1;
});

const {
  data: reportData,
  isLoading,
  refresh,
} = useReportShow(reportId.value, requestBody);

const { data: domainResponse, isFetching: isDomainLoading } = useDomainList();

const report = computed(() => reportData.value?.report ?? {});
const backlinksDomains = computed(() => reportData.value?.domains ?? {});

const accepted_backlinks = computed(() => {
  return parseInt(report.value?.accepted_backlinks || 0, 10);
});

const rejected_backlinks = computed(() => {
  return parseInt(report.value?.rejected_backlinks || 0, 10);
});


const backlinksData = computed(
  () =>
    reportData.value?.backlinks ?? {
      data: [],
      current_page: 1,
      last_page: 1,
      per_page: requestBody.per_page,
      total: 0,
      from: 0,
      to: 0,
    }
);

watch(
  () => backlinksData.value.current_page,
  (newPage) => {
    if (newPage && newPage !== uiState.page) {
      uiState.page = newPage;
    }
  }
);

const allBacklinks = computed(() => backlinksData.value.data ?? []);

const availableDomains = computed(() => {
  return Array.from(allSeenDomains.value).sort();
});


watch(
  () => allBacklinks.value,
  (newBacklinks) => {
    if (newBacklinks && newBacklinks.length > 0) {
      newBacklinks.forEach((backlink) => {
        const domain = backlink.domain || backlink.from_domain || "Unknown Domain";
        allSeenDomains.value.add(domain);
      });
    }
  },
  { immediate: true, deep: true }
);

const domainOptions = computed(() => {
  const options = [{ title: "All Domains", value: "" }];
  Object.entries(backlinksDomains.value).forEach(([title, targetUrl]) => {
    options.push({
      title,
      value: targetUrl,
    });
  });

  return options;
});

// watch([domainOptions, searchQuery], ([domains, search]) => {
//   if (search) {
//     const match = domains.find(
//       (d) =>
//         d.title.toLowerCase().includes(search.toLowerCase()) ||
//         d.value.toLowerCase().includes(search.toLowerCase())
//     );
//     if (match && match.value !== selectedDomain.value) {
//       selectedDomain.value = match.value;
//     }
//   }
// });


const currentBacklinks = computed(() => allBacklinks.value);

const getStatusConfig = (status) => {
  if (status === "accepted")
    return { color: "success", icon: "tabler-progress-check", text: "Accepted" };
  if (status === "rejected")
    return { color: "error", icon: "tabler-progress-x", text: "Rejected" };
  return { color: "warning", icon: "tabler-clock", text: "Pending" };
};

// Get tier color
const getTierColor = (tier) => {
  if (tier >= 3) return "success";
  if (tier >= 2) return "warning";
  return "error";
};

// Get quality score color
const getScoreColor = (score) => {
  if (score >= 80) return "success";
  if (score >= 60) return "warning";
  if (score >= 40) return "info";
  return "error";
};

// Stats using report data
const stats = computed(() => {
  return {
    total: report.value.total_backlink || 0,
    accepted: report.value.accepted_backlinks || accepted_backlinks,
    rejected: report.value.rejected_backlinks || rejected_backlinks,
    pending:
      (report.value.total_backlink || 0) -
      (report.value.accepted_backlinks || accepted_backlinks) -
      (report.value.rejected_backlinks || rejected_backlinks),
    domains: report.value.domain_count || 0,
  };
});


// Format date
const formatDate = (dateString) => {
  if (!dateString) return "Not recorded";
  return new Date(dateString).toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

// Status options
const statusOptions = [
  { title: "All Status", value: "" },
  { title: "Accepted", value: "accepted" },
  { title: "Rejected", value: "rejected" },
  { title: "Pending", value: "pending" },
];

// Clear all filters
const clearFilters = () => {
  searchQuery.value = "";
  statusFilter.value = "";
  selectedDomain.value = "";
  uiState.page = 1;
};

// Search scope options
const searchScope = ref("global"); // 'global' or 'domain'

// Update search scope based on domain selection
watch(selectedDomain, (newDomain) => {
  if (newDomain) {
    searchScope.value = "domain";
  } else {
    searchScope.value = "global";
  }
  selectedDomain.value = newDomain;
  console.log("searchScope changed:", newDomain);
  console.log("searchScope:", selectedDomain);
});

// Handle search scope change
watch(searchScope, (newScope) => {
  if (newScope === "global") {
    selectedDomain.value = "";
  }
});
console.log("searchScope", searchScope);

const handleSearch = () => {
  if (
    searchScope.value === "domain" &&
    !selectedDomain.value &&
    availableDomains.value.length > 0
  ) {
    selectedDomain.value = availableDomains.value[0];
  }
};

// Pagination info
const paginationInfo = computed(() => {
  const current = backlinksData.value.current_page || 1;
  const total = backlinksData.value.last_page || 1;
  const from = backlinksData.value.from || 0;
  const to = backlinksData.value.to || 0;
  const totalItems = backlinksData.value.total || 0;

  return {
    current,
    total,
    from,
    to,
    totalItems,
    showing: `${from}-${to} of ${totalItems}`,
  };
});
</script>

<template>
  <div>
    <!-- Header -->
    <VCard class="mb-8 overflow-hidden" elevation="2">
      <div class="pa-8">
        <VRow align="start" justify="space-between">
          <VCol cols="12" md="8">
            <!-- Back button -->
            <VBtn variant="outlined" color="primary" class="mb-6" :to="{ name: 'apps-report-list' }">
              <VIcon icon="tabler-arrow-left" class="me-2" />
              Back to Reports
            </VBtn>

            <div>
              <!-- Heading -->
              <h2 class="text-h3 font-weight-bold mb-3">Report Analysis</h2>

              <!-- Chips -->
              <div class="d-flex align-center gap-3 flex-wrap">
                <VChip color="primary" variant="elevated" size="large">
                  <VIcon icon="tabler-id" class="me-2" />
                  ID: {{ report.id }}
                </VChip>

                <VChip color="primary" variant="outlined" size="large">
                  <VIcon icon="tabler-play" class="me-2" />
                  Run: {{ report.run_id }}
                </VChip>

                <VChip color="primary" variant="outlined" size="large">
                  <VIcon icon="tabler-calendar" class="me-2" />
                  {{ formatDate(report.created_at) }}
                </VChip>
              </div>
            </div>
          </VCol>
        </VRow>
      </div>
    </VCard>

    <!-- Loading -->
    <template v-if="isLoading">
      <VCard class="mb-6" elevation="2">
        <VCardText class="text-center py-16">
          <VProgressCircular indeterminate color="primary" size="64" width="6" />
          <h6 class="text-h6 mt-6 mb-2">Loading report...</h6>
          <p class="text-body-2 text-medium-emphasis">
            Fetching backlink data, please wait.
          </p>
        </VCardText>
      </VCard>
    </template>

    <!-- Report Content -->
    <template v-else-if="report.id">
      <!-- Stats Cards -->
      <VRow class="mb-8">
        <VCol cols="12" md="2" sm="6">
          <VCard elevation="2" class="h-100" color="primary" variant="tonal">
            <VCardText class="pa-6 text-center">
              <VAvatar size="56" color="primary" class="mb-3">
                <VIcon icon="tabler-link" size="28" />
              </VAvatar>
              <p class="text-body-2 text-medium-emphasis mb-1">Total</p>
              <h4 class="text-h4 font-weight-bold text-primary">
                {{ stats.total }}
              </h4>
            </VCardText>
          </VCard>
        </VCol>

        <VCol cols="12" md="2" sm="6">
          <VCard elevation="2" class="h-100" color="success" variant="tonal">
            <VCardText class="pa-6 text-center">
              <VAvatar size="56" color="success" class="mb-3">
                <VIcon icon="tabler-check" size="28" />
              </VAvatar>
              <p class="text-body-2 text-medium-emphasis mb-1">Accepted</p>
              <h4 class="text-h4 font-weight-bold text-success">
                {{ stats.accepted }}
              </h4>
            </VCardText>
          </VCard>
        </VCol>

        <VCol cols="12" md="2" sm="6">
          <VCard elevation="2" class="h-100" color="error" variant="tonal">
            <VCardText class="pa-6 text-center">
              <VAvatar size="56" color="error" class="mb-3">
                <VIcon icon="tabler-x" size="28" />
              </VAvatar>
              <p class="text-body-2 text-medium-emphasis mb-1">Rejected</p>
              <h4 class="text-h4 font-weight-bold text-error">
                {{ stats.rejected }}
              </h4>
            </VCardText>
          </VCard>
        </VCol>

        <VCol cols="12" md="2" sm="6">
          <VCard elevation="2" class="h-100" color="warning" variant="tonal">
            <VCardText class="pa-6 text-center">
              <VAvatar size="56" color="warning" class="mb-3">
                <VIcon icon="tabler-clock" size="28" />
              </VAvatar>
              <p class="text-body-2 text-medium-emphasis mb-1">Pending</p>
              <h4 class="text-h4 font-weight-bold text-warning">
                {{ stats.pending }}
              </h4>
            </VCardText>
          </VCard>
        </VCol>

        <VCol cols="12" md="2" sm="6">
          <VCard elevation="2" class="h-100" color="info" variant="tonal">
            <VCardText class="pa-6 text-center">
              <VAvatar size="56" color="info" class="mb-3">
                <VIcon icon="tabler-world" size="28" />
              </VAvatar>
              <p class="text-body-2 text-medium-emphasis mb-1">Domains</p>
              <h4 class="text-h4 font-weight-bold text-info">
                {{ stats.domains }}
              </h4>
            </VCardText>
          </VCard>
        </VCol>

        <VCol cols="12" md="2" sm="6">
          <VCard elevation="2" class="h-100" color="secondary" variant="tonal">
            <VCardText class="pa-6 text-center">
              <VAvatar size="56" color="secondary" class="mb-3">
                <VIcon icon="tabler-percentage" size="28" />
              </VAvatar>
              <p class="text-body-2 text-medium-emphasis mb-1">Success Rate</p>
              <h4 class="text-h4 font-weight-bold text-secondary">
                {{
                  stats.total
                    ? Math.round((accepted_backlinks / stats.total) * 100)
                : 0
                }}%
              </h4>
            </VCardText>
          </VCard>
        </VCol>
      </VRow>

      <!-- Enhanced Filters Card -->
      <VCard elevation="2" class="mb-6">
        <VCardTitle class="d-flex align-center pa-6">
          <VAvatar color="primary" variant="tonal" class="me-3">
            <VIcon icon="tabler-filter" />
          </VAvatar>
          <div>
            <h5 class="text-h5">Search & Filters</h5>
            <p class="text-body-2 text-medium-emphasis">
              Filter and search through backlinks
            </p>
          </div>
        </VCardTitle>

        <VCardText class="pa-6">
          <!-- Search Scope Toggle -->
          <VRow class="mb-4">
            <VCol cols="12">
              <VBtnToggle v-model="searchScope" color="primary" variant="outlined" mandatory class="w-100">
                <VBtn value="global" class="flex-grow-1">
                  <VIcon icon="tabler-world" class="me-2" />
                  Search All Backlinks
                </VBtn>
                <VBtn value="domain" class="flex-grow-1" :disabled="domainOptions.length === 0">
                  <VIcon icon="tabler-sitemap" class="me-2" />
                  Search in Domain
                </VBtn>
              </VBtnToggle>
            </VCol>
          </VRow>

          <VRow>
            <!-- Domain Filter -->
            <VCol cols="12" md="3">
              <VSelect v-model="selectedDomain" :items="domainOptions" item-title="title" item-value="value"
                variant="outlined" label="Select Domain" hide-details clearable :disabled="searchScope === 'global'">
                <template #selection="{ item }">
                  <div class="d-flex align-center">
                    <VIcon icon="tabler-world" class="me-2" size="16" />
                    <span>{{ item.title }}</span>
                  </div>
                </template>
              </VSelect>
            </VCol>

            <!-- Search Field -->
            <VCol cols="12" md="4">
              <VTextField v-model="searchQuery" variant="outlined" label="Search backlinks..." :placeholder="searchScope === 'domain' && selectedDomain
                ? `Search in ${selectedDomain}...`
                : 'Search by domain, URL, or anchor text'
                " hide-details clearable prepend-inner-icon="tabler-search" @keyup.enter="handleSearch" />
            </VCol>

            <!-- Status Filter -->
            <VCol cols="12" md="2">
              <VSelect v-model="statusFilter" :items="statusOptions" item-title="title" item-value="value"
                variant="outlined" label="Status" hide-details clearable prepend-inner-icon="tabler-flag" />
            </VCol>

            <!-- Items per page -->
            <VCol cols="12" md="2">
              <VSelect v-model="uiState.itemsPerPage" :items="[5, 10, 25, 50, 100, 500]" variant="outlined"
                label="Per page" hide-details prepend-inner-icon="tabler-list" />
            </VCol>

            <!-- Actions -->
            <VCol cols="12" md="1" class="d-flex align-center">
              <VBtn variant="outlined" color="secondary" @click="clearFilters"
                :disabled="!searchQuery && !statusFilter && !selectedDomain" icon="tabler-filter-x" class="w-100" />
            </VCol>
          </VRow>

          <!-- Active Filters Display -->
          <div v-if="searchQuery || statusFilter || selectedDomain" class="mt-4">
            <div class="d-flex align-center gap-2 flex-wrap">
              <span class="text-body-2 text-medium-emphasis">Active filters:</span>

              <VChip v-if="selectedDomain" size="small" color="primary" variant="tonal" closable
                @click:close="selectedDomain = ''">
                <VIcon icon="tabler-world" class="me-1" size="14" />
                Domain: {{ selectedDomain }}
              </VChip>

              <VChip v-if="searchQuery" size="small" color="info" variant="tonal" closable
                @click:close="searchQuery = ''">
                <VIcon icon="tabler-search" class="me-1" size="14" />
                Search: {{ searchQuery }}
              </VChip>

              <VChip v-if="statusFilter" size="small" color="warning" variant="tonal" closable
                @click:close="statusFilter = ''">
                <VIcon icon="tabler-flag" class="me-1" size="14" />
                Status:
                {{statusOptions.find((s) => s.value === statusFilter)?.title}}
              </VChip>
            </div>
          </div>
        </VCardText>
      </VCard>

      <!-- Results Card -->
      <VCard elevation="2" class="mb-6">
        <VCardTitle class="d-flex align-center justify-space-between pa-6">
          <div class="d-flex align-center">
            <VAvatar color="primary" variant="tonal" class="me-3">
              <VIcon icon="tabler-link" />
            </VAvatar>
            <div>
              <h5 class="text-h5">Backlinks Results</h5>
              <p class="text-body-2 text-medium-emphasis">
                Showing {{ paginationInfo.showing }} results
                <template v-if="selectedDomain">
                  in {{ selectedDomain }}
                </template>
              </p>
            </div>
          </div>

          <!-- Results Summary -->
          <div class="text-end">
            <VChip color="primary" variant="outlined" size="small">
              Page {{ paginationInfo.current }} of {{ paginationInfo.total }}
            </VChip>
          </div>
        </VCardTitle>

        <!-- Backlinks Display -->
        <VCardText class="pa-6">
          <div v-if="currentBacklinks.length === 0" class="text-center py-12">
            <VAvatar size="120" color="grey-lighten-2" class="mb-6">
              <VIcon icon="tabler-link-off" size="60" />
            </VAvatar>
            <h5 class="text-h5 mb-2">No Backlinks Found</h5>
            <p class="text-body-2 text-medium-emphasis">
              {{
                searchQuery || statusFilter || selectedDomain
                  ? "No backlinks match your current filters."
                  : "No backlinks available for this report."
              }}
            </p>
            <VBtn v-if="searchQuery || statusFilter || selectedDomain" variant="outlined" color="primary"
              @click="clearFilters" class="mt-4">
              <VIcon icon="tabler-filter-x" class="me-2" />
              Clear Filters
            </VBtn>
          </div>

          <!-- Backlinks Cards -->
          <div v-else class="backlinks-container">
            <VCard v-for="backlink in currentBacklinks" :key="backlink.id" elevation="1" compact
              class="backlink-card mb-2" border>
              <VCardText class="pa-3">
                <VRow align="center" no-gutters class="g-2">

                  <!-- Domain & URL Section - Combined -->
                  <VCol cols="12" md="5" class="domain-url-section">
                    <div class="d-flex align-center mb-2">
                      <VAvatar size="32" color="primary" variant="tonal" class="me-3 flex-shrink-0">
                        <VIcon icon="tabler-world" size="16" />
                      </VAvatar>
                      <div class="flex-grow-1 min-width-0">
                        <div class="responsive-text pa-2 font-weight-bold text-primary mb-0 text-truncate">
                          {{
                            backlink.domain ||
                            backlink.target_url ||
                            "Unknown"
                          }}
                        </div>
                      </div>
                    </div>

                    <!-- Target URL -->
                    <div class="mb-1 pa-4">
                      <a :href="backlink.target_url" target="_blank"
                        class="text-success text-decoration-none d-flex align-center text-body-1"
                        style="max-width: 100%" :title="backlink.target_url">
                        <VIcon icon="tabler-target" class="me-1 flex-shrink-0" size="26" />
                        <span class="text-truncate responsive-url me-1">{{
                          backlink.target_url
                          }}</span>
                        <VIcon icon="tabler-external-link" size="10" class="flex-shrink-0" />
                      </a>
                    </div>

                    <!-- Page title if exists -->

                    <div v-if="backlink.page_title" class="d-flex mt-2 pa-2">
                      <VTooltip :text="backlink.page_title">
                        <template #activator="{ props }">
                          <VIcon v-bind="props" icon="tabler-brand-pagekit" class="me-1 text-secondary" size="22"
                            style="cursor: pointer;" />
                        </template>
                      </VTooltip>
                    </div>

                    <!-- Anchor if exists -->
                    <div v-if="backlink.anchor" class="mt-2 pa-2 justify-space-between">
                      <VTooltip :text="backlink.anchor">
                        <template #activator="{ props }">
                          <VIcon v-bind="props" icon="tabler-anchor" class="me-1 text-secondary" size="22"
                            style="cursor: pointer;" />
                        </template>
                      </VTooltip>
                    </div>
                  </VCol>

                  <!-- Metrics Section - Compact -->
                  <VCol cols="12" md="6" class="metrics-section">
                    <VRow no-gutters class="text-center g-2">

                      <!-- Tier -->
                      <VCol cols="2">
                        <div class="metric-item-compact">
                          <VIcon icon="tabler-star" color="error" size="22" />
                          <div class="matric-text  text-medium-emphasis">Tier</div>
                          <div class="matric-text  font-weight-bold">{{ backlink.tier || 0 }}</div>
                        </div>
                      </VCol>

                      <!-- Score -->
                      <VCol cols="2">
                        <div class="metric-item-compact">
                          <VIcon icon="tabler-chart-line" color="info" size="22" />
                          <div class="matric-text  text-medium-emphasis">Score</div>
                          <div class="matric-text  font-weight-bold">{{ backlink.score || 0 }}</div>
                        </div>
                      </VCol>

                      <!-- Type -->
                      <VCol cols="2">
                        <div class="metric-item-compact">
                          <VIcon :icon="backlink.do_follow ? 'tabler-circles-relation' : 'tabler-space-off'" size="22"
                            :color="backlink.do_follow ? 'success' : 'error'" />
                          <div class="matric-text  text-medium-emphasis">Type</div>
                          <div class="matric-text  font-weight-bold">
                            {{ backlink.do_follow ? "DoFollow" : "NoFollow" }}
                          </div>
                        </div>
                      </VCol>

                      <!-- Spam -->
                      <VCol cols="2">
                        <div class="metric-item-compact">
                          <VIcon icon="tabler-flag" size="22" color="error" />
                          <div class="matric-text  text-medium-emphasis">Spam</div>
                          <div class="matric-text  font-weight-bold">{{ backlink.spam_score || 0 }}%</div>
                        </div>
                      </VCol>

                      <!-- Page Rank -->
                      <VCol cols="2">
                        <div class="metric-item-compact">
                          <VIcon icon="tabler-arrow-badge-up" color="warning" size="26" />
                          <div class="matric-text text-medium-emphasis">Page Rank</div>
                          <div class="matric-text font-weight-bold">{{ backlink.rank || 0 }}</div>
                        </div>
                      </VCol>

                      <!-- Domain Rank -->
                      <VCol cols="2">
                        <div class="metric-item-compact">
                          <VIcon icon="tabler-network" size="26" color="default" />
                          <div class="matric-text text-medium-emphasis">Domain Rank</div>
                          <div class="matric-text font-weight-bold">{{ backlink.domain_rank || 0 }}</div>
                        </div>
                      </VCol>
                    </VRow>
                  </VCol>

                  <VCol cols="12" md="1" class="d-flex flex-wrap align-center justify-center gap-3">
                    <!-- Status -->
                    <VIcon :icon="getStatusConfig(backlink.status).icon" :color="getStatusConfig(backlink.status).color"
                      class="me-1 icon-size" />

                    <!-- Broken link indicator -->
                    <VIcon v-if="backlink.is_broken" color="error" icon="tabler-link-off" class="icon-size" />

                    <VIcon v-if="!backlink.is_broken" color="success" icon="tabler-link" class="icon-size" />

                    <!-- Details button -->
                    <router-link :to="{
                      name: 'apps-report-backlink-view',
                      params: { id: backlink.id },
                    }">
                      <VIcon icon="tabler-eye" class="icon-size" />
                    </router-link>
                  </VCol>
                </VRow>
              </VCardText>
            </VCard>
          </div>
        </VCardText>

        <!-- Enhanced Pagination -->
        <div>
          <VDivider />
          <VCardActions class="justify-space-between pa-6">
            <div class="d-flex align-center gap-4">
              <span class="text-body-2 text-medium-emphasis">
                Page {{ paginationInfo.current }} of {{ paginationInfo.total }}
              </span>
              <span class="text-body-2 text-medium-emphasis">
                {{ paginationInfo.showing }} results
              </span>
            </div>

            <VPagination v-model="uiState.page" :length="paginationInfo.total" :total-visible="7" color="primary"
              active-color="primary" variant="outlined" size="large" :disabled="isLoading" />

            <div class="d-flex align-center gap-3">
              <span class="text-body-2 text-medium-emphasis">Items per page:</span>
              <VSelect v-model="uiState.itemsPerPage" :items="[5, 10, 25, 50, 100, 500]" variant="outlined"
                density="compact" hide-details style="width: 80px" />
            </div>
          </VCardActions>
        </div>
      </VCard>
    </template>

    <!-- Error State -->
    <VCard v-if="!report" elevation="2" class="mb-6">
      <VCardText class="text-center py-16">
        <VAvatar size="120" color="error" variant="tonal" class="mb-6 mx-auto">
          <VIcon icon="tabler-database-off" size="64" />
        </VAvatar>
        <h4 class="text-h4 mb-4">Report Not Found</h4>
        <p class="text-body-1 text-medium-emphasis mb-6 max-width-400 mx-auto">
          The report you're looking for doesn't exist or may have been deleted.
        </p>
        <VBtn color="primary" size="large" :to="{ name: 'apps-report-list' }">
          <VIcon icon="tabler-arrow-left" class="me-2" />
          Back to Reports
        </VBtn>
      </VCardText>
    </VCard>
  </div>
</template>

<style lang="scss" scoped>
$border-color: rgba(var(--v-theme-on-surface), 0.12);
$border-opacity: var(--v-border-opacity);
$transition-duration: 0.2s;
$animation-duration: 0.5s;

.backlink-card {
  transition: all $transition-duration ease;

  &:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
  }
}

.responsive-url {
  display: none;

  @media (min-width: 2356px) {
    display: inline;
  }
}

.icon-size {
  font-size: 28px;

  @media (max-width: 1916px) {
    font-size: 24px;
  }

  @media (max-width: 1676px) {
    font-size: 20px;
  }
}


// Metric Item Styles
.metric-item-compact {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1px;
}

.metric-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}

.matric-text {
  font-size: 13px;

  @media (max-width: 1916px) {
    font-size: 12px;
  }

  @media (max-width: 1676px) {
    font-size: 10px;
  }
}


// Section Styles
.domain-url-section {
  min-width: 0;
  display: inline-flex;
  justify-content: space-around;
}

.metrics-section {
  border-left: 1px solid $border-color;
  border-right: 1px solid $border-color;
}

.domain-section {
  border-right: 1px solid rgba(var(--v-border-color), $border-opacity);
}

.url-section {
  border-right: 1px solid rgba(var(--v-border-color), $border-opacity);
}

// Utility Classes
.max-width-400 {
  max-width: 400px;
}

.min-width-0 {
  min-width: 0;
}

// Container Styles
.backlinks-container {
  max-width: 100%;
}

// Animations
.v-card {
  animation: fadeInUp $animation-duration ease-out;
}

.responsive-text {
  font-size: 1.125rem;

  @media (max-width: 1825px) {
    font-size: 0.875rem;
  }

  @media (max-width: 640px) {
    font-size: 0.875rem;
  }
}


@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

// Media Queries
@media (max-width: 1000px) {
  .metrics-section {
    border-left: none;
    border-right: none;
    border-top: 1px solid $border-color;
    border-bottom: 1px solid $border-color;
    margin: 8px 0;
    padding: 8px 0;
  }

  .v-row.v-row--no-gutters .metrics-section {
    padding: 12px 12px !important;
  }

  .domain-section,
  .url-section {
    border-right: none !important;
    border-bottom: 1px solid rgba(var(--v-border-color), $border-opacity);
    padding-bottom: 16px !important;
    margin-bottom: 16px !important;
  }

  .actions-section {
    border-bottom: none !important;
    padding-bottom: 0 !important;
    margin-bottom: 0 !important;
  }

  .action-chip {
    min-height: 32px;
    padding: 0 8px;
    display: flex;
    align-items: center;
  }
}
</style>