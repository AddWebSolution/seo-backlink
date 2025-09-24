<script setup>
import { useReportApi } from "@/composables/reportApi";
import { reactive, computed, ref, watch, unref, onMounted } from "vue";
import { useRoute } from "vue-router";

const route = useRoute();
const reportId = computed(() => route.params.id);

const {
  currentReport,
  reportBacklinks,
  loading: reportLoading,
  error: reportError,
  fetchReportBacklinks,
  showAlert,
} = useReportApi();

const searchQuery = ref("");
const statusFilter = ref("");
const selectedDomain = ref("");
const allSeenDomains = ref(new Set());

const uiState = reactive({
  page: 1,
  itemsPerPage: 10,
  sortBy: [{ key: "id", order: "desc" }], // Default sort
});

const requestBody = computed(() => ({
  page: uiState.page,
  per_page: uiState.itemsPerPage,
  search: searchQuery.value,
  status: statusFilter.value,
  domain: selectedDomain.value,
  sort_by: uiState.sortBy[0]?.key || "id",
  sort_order: uiState.sortBy[0]?.order || "desc",
}));

onMounted(async () => {
  if (reportId.value) {
    await loadReportData();
  }
});

const loadReportData = async () => {
  try {
    await fetchReportBacklinks(reportId.value, requestBody.value);
  } catch (error) {
    console.error("Failed to load report:", error);
  }
};

watch(
  requestBody,
  async () => {
    if (reportId.value) {
      await loadReportData();
      console.log("after request body", unref(requestBody));
    }
  },
  { deep: true }
);

watch([searchQuery, statusFilter, selectedDomain], () => {
  uiState.page = 1;
});

const report = computed(() => currentReport.value?.report ?? {});
const backlinksDomains = computed(() => currentReport.value?.domains ?? {});

const accepted_backlinks = computed(() => {
  return parseInt(report.value?.accepted_backlinks || 0, 10);
});

const rejected_backlinks = computed(() => {
  return parseInt(report.value?.rejected_backlinks || 0, 10);
});

const currentPage = computed({
  get: () => backlinksData.value.current_page || 1,
  set: (value) => {
    uiState.page = value;
  },
});

const totalItems = computed(() => backlinksData.value.total || 0);
const itemsPerPage = computed(
  () => backlinksData.value.per_page || uiState.itemsPerPage
);
const lastPage = computed(() => backlinksData.value.last_page || 1);

const backlinksData = computed(() => {
  const data = currentReport.value?.backlinks;
  if (!data) {
    return {
      data: [],
      current_page: 1,
      last_page: 1,
      per_page: itemsPerPage,
      total: 0,
      from: 0,
      to: 0,
    };
  }
  return data;
});

const allBacklinks = computed(() => backlinksData.value.data ?? []);

const availableDomains = computed(() => {
  return Array.from(allSeenDomains.value).sort();
});

watch(
  () => allBacklinks.value,
  (newBacklinks) => {
    if (newBacklinks && newBacklinks.length > 0) {
      newBacklinks.forEach((backlink) => {
        const domain =
          backlink.domain || backlink.from_domain || "Unknown Domain";
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

const getStatusConfig = (status) => {
  if (status === "accepted")
    return {
      color: "success",
      icon: "tabler-progress-check",
      text: "Accepted",
    };
  if (status === "rejected")
    return { color: "error", icon: "tabler-progress-x", text: "Rejected" };
  return { color: "warning", icon: "tabler-clock", text: "Pending" };
};

const getTierColor = (tier) => {
  if (tier >= 3) return "success";
  if (tier >= 2) return "warning";
  return "error";
};

const getScoreColor = (score) => {
  if (score >= 80) return "success";
  if (score >= 60) return "warning";
  if (score >= 40) return "info";
  return "error";
};

const stats = computed(() => {
  return {
    total: report.value.total_backlink || 0,
    accepted: report.value.accepted_backlinks || accepted_backlinks.value,
    rejected: report.value.rejected_backlinks || rejected_backlinks.value,
    pending:
      (report.value.total_backlink || 0) -
      (report.value.accepted_backlinks || accepted_backlinks.value) -
      (report.value.rejected_backlinks || rejected_backlinks.value),
    domains: report.value.domain_count || 0,
  };
});

const formatDate = (dateString) => {
  if (!dateString) return "Not recorded";
  return new Date(dateString).toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

const statusOptions = [
  { title: "All Status", value: "" },
  { title: "Accepted", value: "accepted" },
  { title: "Rejected", value: "rejected" },
  { title: "Pending", value: "pending" },
];

const clearFilters = () => {
  searchQuery.value = "";
  statusFilter.value = "";
  selectedDomain.value = "";
  uiState.page = 1;
  uiState.itemsPerPage = 10;
};

const searchScope = ref("global");

watch(selectedDomain, (newDomain) => {
  if (newDomain) {
    searchScope.value = "domain";
  } else {
    searchScope.value = "global";
  }
  selectedDomain.value = newDomain;
  console.log("searchScope changed:", newDomain);
  console.log("searchScope:", selectedDomain.value);
});

watch(searchScope, (newScope) => {
  if (newScope === "global") {
    selectedDomain.value = "";
  }
});

const handleSearch = () => {
  if (
    searchScope.value === "domain" &&
    !selectedDomain.value &&
    availableDomains.value.length > 0
  ) {
    selectedDomain.value = availableDomains.value[0];
  }
};

const isLoading = computed(() => reportLoading.value);

// DataTable configuration
const headers = computed(() => [
  {
    title: "Domain",
    key: "domain",
    align: "center",
    sortable: true,
    width: "80px",
  },
  {
    title: "Target URL",
    key: "target_url",
    align: "center",
    sortable: true,
    width: "100px",
  },
  {
    title: "Status",
    key: "status",
    align: "center",
    sortable: true,
    width: "120px",
  },
  {
    title: "Tier",
    key: "tier",
    align: "center",
    sortable: true,
    width: "80px",
  },
  {
    title: "Score",
    key: "score",
    align: "center",
    sortable: true,
    width: "80px",
  },
  {
    title: "Type",
    key: "do_follow",
    align: "center",
    sortable: true,
    width: "100px",
  },
  {
    title: "Spam %",
    key: "spam_score",
    align: "center",
    sortable: true,
    width: "80px",
  },
  {
    title: "Page Rank",
    key: "rank",
    align: "center",
    sortable: true,
    width: "100px",
  },
  {
    title: "Domain Rank",
    key: "domain_rank",
    align: "center",
    sortable: true,
    width: "110px",
  },
  {
    title: "Link Status",
    key: "is_broken",
    align: "center",
    sortable: true,
    width: "100px",
  },
  {
    title: "Actions",
    key: "actions",
    align: "center",
    sortable: false,
    width: "80px",
  },
]);

// Handle server-side operations
const handleOptionsUpdate = ({ page, itemsPerPage, sortBy }) => {
  uiState.page = page;
  uiState.itemsPerPage = itemsPerPage;
  uiState.sortBy = sortBy;
};

const serverItems = computed(() => ({
  items: backlinksData.value.data || [],
  total: backlinksData.value.total || 0,
}));
</script>

<template>
  <div>
    <!-- Header -->
    <VCard class="mb-8 overflow-hidden" elevation="2">
      <div class="pa-8">
        <VRow align="center" justify="space-between">
          <VCol cols="12" md="8">
            <h2 class="text-h3 font-weight-bold mb-4">Backlink Analysis</h2>

            <div class="d-flex align-center flex-wrap gap-3">
              <VChip color="primary" variant="elevated" size="large">
                <VIcon icon="tabler-id" class="me-2" />
                ID: {{ report.id }}
              </VChip>

              <VChip color="primary" variant="elevated" size="large">
                <VIcon icon="tabler-play" class="me-2" />
                Run: {{ report.run_id }}
              </VChip>

              <VChip color="primary" variant="elevated" size="large">
                <VIcon icon="tabler-calendar" class="me-2" />
                {{ formatDate(report.created_at) }}
              </VChip>
            </div>
          </VCol>

          <VCol cols="12" md="4" class="d-flex justify-end">
            <VBtn variant="flat" :to="{ name: 'apps-report-list' }">
              <VIcon icon="tabler-arrow-left" class="me-2" />
              Back to Reports
            </VBtn>
          </VCol>
        </VRow>
      </div>
    </VCard>

    <template v-if="report">
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
              <VTextField v-model="searchQuery" variant="outlined" label="Search backlinks..." :placeholder="
                  searchScope === 'domain' && selectedDomain
                    ? `Search in ${selectedDomain}...`
                    : 'Search by domain, URL, or anchor text'
                " hide-details clearable prepend-inner-icon="tabler-search" @keyup.enter="handleSearch" />
            </VCol>

            <!-- Status Filter -->
            <VCol cols="12" md="2">
              <VSelect v-model="statusFilter" :items="statusOptions" item-title="title" item-value="value"
                variant="outlined" label="Status" hide-details clearable prepend-inner-icon="tabler-flag" />
            </VCol>

            <VCol cols="12" md="1">
              <VSelect v-model="uiState.itemsPerPage" :items="[10,20,50,100,200]" item-title="title" item-value="value"
                variant="outlined" label="Per Page" hide-details prepend-inner-icon="tabler-file-description" />
            </VCol>

            <VCol cols="12" md="1">
               <VBtn variant="outlined" color="secondary" @click="clearFilters"
                :disabled="!searchQuery && !statusFilter && !selectedDomain" prepend-icon="tabler-filter-x">
                Clear Filters
              </VBtn>
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
                {{ statusOptions.find((s) => s.value === statusFilter)?.title }}
              </VChip>

            </div>
          </div>
        </VCardText>
      </VCard>

      <!-- DataTable Card -->
      <VCard elevation="2" class="mb-6">
        <VCardTitle class="d-flex align-center justify-space-between pa-6">
          <div class="d-flex align-center">
            <VAvatar color="primary" variant="tonal" class="me-3">
              <VIcon icon="tabler-link" />
            </VAvatar>
            <div>
              <h5 class="text-h5">Backlinks Results</h5>
              <p class="text-body-2 text-medium-emphasis">
                Showing {{ allBacklinks.length }} of
                {{ backlinksData.total }} results
                <template v-if="selectedDomain">
                  in {{ selectedDomain }}
                </template>
              </p>
            </div>
          </div>
        </VCardTitle>

        <VDivider class="mt-4" />

        <!-- Data Table -->
        <VDataTableServer v-model:items-per-page="uiState.itemsPerPage" v-model:page="uiState.page"
          v-model:sort-by="uiState.sortBy" :headers="headers" :items="serverItems.items"
          :items-length="serverItems.total" loading-text="Fetching reports, please wait..." :loading="isLoading"
          :items-per-page-options="[
            { value: 5, title: '5' },
            { value: 10, title: '10' },
            { value: 25, title: '25' },
            { value: 50, title: '50' },
            { value: 100, title: '100' },
            { value: 500, title: '500' },
          ]" class="reports-table">
          <!-- Your existing template slots here... -->
          <!-- Target URL Column -->
          <template #item.target_url="{ item }">
            <a :href="item.target_url" target="_blank" class="text-success text-decoration-none text-body-1"
              :title="item.target_url">
              <VIcon icon="tabler-target" class="me-1 flex-shrink-0" size="16" />
              <span class="text-truncate" style="max-width: 200px">
                {{ item.target_url }}
              </span>
              <VIcon icon="tabler-external-link" size="12" class="flex-shrink-0 ms-1" />
            </a>
          </template>

          <!-- Status Column -->
          <template #item.status="{ item }">
            <VChip :color="getStatusConfig(item.status).color" variant="tonal" size="small" class="ma-1">
              <VIcon :icon="getStatusConfig(item.status).icon" size="14" class="me-1" />
              {{ getStatusConfig(item.status).text }}
            </VChip>
          </template>

          <!-- Tier Column -->
          <template #item.tier="{ item }">
            <VChip :color="getTierColor(item.tier || 0)" variant="tonal" size="small">
              {{ item.tier || 0 }}
            </VChip>
          </template>

          <!-- Score Column -->
          <template #item.score="{ item }">
            <VChip :color="getScoreColor(item.score || 0)" variant="tonal" size="small">
              {{ item.score || 0 }}
            </VChip>
          </template>

          <!-- Type Column -->
          <template #item.do_follow="{ item }">
            <VChip :color="item.do_follow ? 'success' : 'error'" variant="tonal" size="small">
              <VIcon :icon="
                  item.do_follow
                    ? 'tabler-circles-relation'
                    : 'tabler-space-off'
                " size="14" class="me-1" />
              {{ item.do_follow ? "DoFollow" : "NoFollow" }}
            </VChip>
          </template>

          <!-- Spam Score Column -->
          <template #item.spam_score="{ item }">
            <VChip :color="
                (item.spam_score || 0) > 50
                  ? 'error'
                  : (item.spam_score || 0) > 20
                  ? 'warning'
                  : 'success'
              " variant="tonal" size="small">
              {{ item.spam_score || 0 }}%
            </VChip>
          </template>

          <!-- Page Rank Column -->
          <template #item.rank="{ item }">
            <div class="text-center">
              <VIcon icon="tabler-arrow-badge-up" color="warning" size="16" class="me-1" />
              <span class="font-weight-medium">{{ item.rank || 0 }}</span>
            </div>
          </template>

          <!-- Domain Rank Column -->
          <template #item.domain_rank="{ item }">
            <div class="text-center">
              <VIcon icon="tabler-network" size="16" class="me-1" />
              <span class="font-weight-medium">{{
                item.domain_rank || 0
                }}</span>
            </div>
          </template>

          <!-- Link Status Column -->
          <template #item.is_broken="{ item }">
            <VChip :color="item.is_broken ? 'error' : 'success'" variant="tonal" size="small">
              <VIcon :icon="item.is_broken ? 'tabler-link-off' : 'tabler-link'" size="14" class="me-1" />
              {{ item.is_broken ? "Broken" : "Active" }}
            </VChip>
          </template>

          <!-- Actions Column -->
          <template #item.actions="{ item }">
            <VTooltip text="View Details">
            <template #activator="{ props }">
              <IconBtn v-bind="props" size="small">
                <router-link
                  :to="{ name: 'apps-report-backlink-view', params: { id: item.id } }"
                >
                  <VIcon icon="tabler-eye" size="24" />
                </router-link>
              </IconBtn>
            </template>
          </VTooltip>
          </template>

          <!-- No data slot -->
          <template #no-data>
            <div class="text-center py-12">
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
          </template>

          <template #bottom>
            <TablePagination v-model:page="uiState.page" :items-per-page="uiState.itemsPerPage"
              :total-items="serverItems.total" />
          </template>
        </VDataTableServer>
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
.max-width-400 {
  max-width: 400px;
}

.reports-table {
  .v-data-table__wrapper {
    overflow-x: auto;
    border-radius: 8px;

    table {
      min-width: 1200px;

      th {
        background-color: rgb(var(--v-theme-surface));
        font-weight: 600;
        font-size: 0.875rem;
        border-bottom: 2px solid rgb(var(--v-theme-primary)) !important;
      }

      tbody tr {
        transition: all 0.2s ease;

        &:hover {
          background-color: rgba(var(--v-theme-primary), 0.04) !important;
          transform: translateY(-1px);
          box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
      }

      td {
        padding: 16px 12px;
        vertical-align: middle;
        border-bottom: 1px solid rgba(var(--v-theme-on-surface), 0.12);
      }
    }
  }
}

.v-card {
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;
}

.v-progress-circular {
  margin: 0 !important;
}

@media (max-width: 1024px) {
  .reports-table table {
    min-width: 1000px;
  }
}

@media (max-width: 768px) {
  .reports-table {
    .v-data-table__wrapper table {
      min-width: 800px;

      th,
      td {
        padding: 12px 8px;
        font-size: 0.8rem;
      }
    }
  }

  .v-card {
    margin: 0.5rem;
  }
}
</style>
