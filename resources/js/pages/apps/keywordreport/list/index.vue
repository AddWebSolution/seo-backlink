<script setup>
import { IconReport, IconChartLine } from "@tabler/icons-vue";
import { useKeywordReportApi } from "@/composables/useKeywordReportApi";
import { ref, computed, watch, onMounted } from "vue";

const headers = [
  { title: "ID", key: "id", width: "80px" },
  { title: "Run ID", key: "run_id", width: "120px" },
  // { title: "Run At", key: "run_at", width: "160px" },
  { title: "Domain Count", key: "domain_count", width: "130px" },
  { title: "Total Keywords", key: "total_keywords", width: "140px" },
  { title: "Accepted", key: "accepted_keywords", width: "120px" },
  { title: "Rejected", key: "rejected_keywords", width: "120px" },
  { title: "Success Rate", key: "success_rate", width: "130px" },
  { title: "Actions", key: "actions", sortable: false, width: "100px" },
];

const { 
  reports, 
  pagination,
  loading, 
  error,
  fetchKeywordReports, 
  exportKeywordReports,
  showAlert 
} = useKeywordReportApi()

const selectedStatus = ref();
const selectedDateRange = ref();
const searchQuery = ref("");
const selectedRows = ref([]);
const showAdvancedFilters = ref(false);

const itemsPerPage = ref(10);
const page = ref(1);
const sortBy = ref();
const orderBy = ref();

const buildFilters = () => {
  const filters = {};

  if (selectedStatus.value) filters.status = selectedStatus.value;

  const params = {
    pageNumber: pagination.value.page,
    perPage: pagination.value.itemsPerPage,
  };

  if (searchQuery.value) params.searchTerm = searchQuery.value;

  if (sortBy.value) params.sortField = sortBy.value;
  if (orderBy.value) params.sortOrder = orderBy.value;

  if (Object.keys(filters).length > 0) {
    params.filters = filters;
  }

  return params;
};

const loadReports = async () => {
  const filters = buildFilters()
  await fetchKeywordReports(filters)
}

const clearAllFilters = async () => {
  selectedStatus.value = null;
  selectedDateRange.value = null;
  searchQuery.value = "";
  page.value = 1
  await loadReports();
};

function generateBacklinkReportFilename() {
  const now = new Date();

  const day = String(now.getDate()).padStart(2, "0");
  const monthNames = [
    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
  ];
  const month = monthNames[now.getMonth()];
  const year = now.getFullYear();

  const hours = String(now.getHours()).padStart(2, "0");
  const minutes = String(now.getMinutes()).padStart(2, "0");
  const seconds = String(now.getSeconds()).padStart(2, "0");

  return `BacklinkReport_${day}-${month}-${year}_${hours}-${minutes}-${seconds}.xlsx`;
}

const hasActiveFilters = computed(() => {
  return selectedStatus.value || selectedDateRange.value || searchQuery.value;
});

const updateOptions = async (options) => {
  sortBy.value = options.sortBy[0]?.key;
  orderBy.value = options.sortBy[0]?.order;
  page.value = options.page
  itemsPerPage.value = options.itemsPerPage
  await loadReports();
};

// onMounted(() => {
//   loadReports()
// })

const totalReports = computed(() => reports.value?.length ?? 0);

watch(selectedRows, (val) => {
});

const reportsWithStats = computed(() => {
  return reports.value.map((report) => ({
    ...report,
    success_rate:
      report.total_keywords > 0
        ? Math.round((report.accepted_keywords / report.total_keywords) * 100)
        : 0,
  }));
});

const handleExportReports = async () => {
  if (!selectedRows.value.length) {
    showAlert("Please select at least one report to export.", "warning");
    return;
  }

  const reportIds = selectedRows.value;
  const requestBody = { report_ids: reportIds };

  try {
    const blob = await exportKeywordReports(requestBody);
    const filename = generateBacklinkReportFilename();

    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = filename;
    a.style.display = "none";
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
  } catch (error) {
    console.error("Export failed:", error);
  }
};

const handleExportSingleReport = async (reportId) => {
  if (!reportId) {
    showAlert("No report selected for export.", "warning");
    return;
  }
  const requestBody = { report_ids: [reportId] };

  try {
    const blob = await exportKeywordReports(requestBody);
    const filename = generateBacklinkReportFilename();

    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
  } catch (error) {
    console.error("Export failed:", error);
  }
};

const handleDeleteReport = async (id) => {
  try {
    await deleteReport(id);
    const index = selectedRows.value.findIndex((row) => row == id);
    if (index !== -1) selectedRows.value.splice(index, 1);
  } catch (error) {
    console.error("Delete failed:", error);
  }
};

// Status options
const statusOptions = [
  { title: "High Success (>80%)", value: "high", color: "success" },
  { title: "Medium Success (50-80%)", value: "medium", color: "warning" },
  { title: "Low Success (<50%)", value: "low", color: "error" },
];

const dateRangeOptions = [
  { title: "Today", value: "today" },
  { title: "This Week", value: "week" },
  { title: "This Month", value: "month" },
  { title: "Last 3 Months", value: "quarter" },
];

const summaryStats = computed(() => {
  const totalKeywords = reports.value.reduce(
    (sum, report) => sum + (report.total_keywords || 0),
    0
  );
  const totalAccepted = reports.value.reduce(
    (sum, report) => sum + (report.accepted_keywords || 0),
    0
  );
  const totalRejected = reports.value.reduce(
    (sum, report) => sum + (report.rejected_keywords || 0),
    0
  );
  const overallSuccessRate =
    totalKeywords > 0 ? Math.round((totalAccepted / totalKeywords) * 100) : 0;

  return {
    totalReports: reports.value.length,
    totalKeywords,
    totalAccepted,
    totalRejected,
    overallSuccessRate,
  };
});

const formatDate = (dateString) => {
  if (!dateString) return "N/A";
  return new Date(dateString).toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
};

const applyFilters = async () => {
  page.value = 1 // Reset to first page when filtering
  await loadReports()
}
</script>

<template>
  <!-- Header Section -->
  <VCard class="mb-6 pa-6 overflow-hidden" elevation="2">
    <VContainer fluid>
      <VRow align="center">
        <VCol cols="12" md="8">
          <div class="d-flex align-center">
            <VAvatar size="64" color="primary" variant="elevated" class="me-4">
              <IconReport stroke="{2}" />
            </VAvatar>
            <div>
              <h1 class="text-h3 font-weight-bold mb-1">Keyword Reports</h1>
              <p class="text-body-1 text-medium-emphasis mb-0">
                Track backlink performance and campaign analytics
              </p>
            </div>
          </div>
        </VCol>
      </VRow>
    </VContainer>
  </VCard>

  <!-- Summary Stats Cards -->
  <VRow class="mb-6">
    <VCol cols="12" sm="6" md="3">
      <VCard class="text-center pa-4" elevation="1" color="primary" variant="tonal">
        <VAvatar color="primary" size="40" class="mb-3">
          <VIcon icon="tabler-files" color="white" />
        </VAvatar>
        <div class="text-h5 font-weight-bold text-primary">
          {{ summaryStats.totalReports }}
        </div>
        <div class="text-body-2 text-medium-emphasis">Total Reports</div>
      </VCard>
    </VCol>
    <VCol cols="12" sm="6" md="2">
      <VCard class="text-center pa-4" elevation="1" color="info" variant="tonal">
        <VAvatar color="info" size="40" class="mb-3">
          <VIcon icon="tabler-link" color="white" />
        </VAvatar>
        <div class="text-h5 font-weight-bold text-info">
          {{ summaryStats.totalKeywords.toLocaleString() }}
        </div>
        <div class="text-body-2 text-medium-emphasis">Total Keywords</div>
      </VCard>
    </VCol>
    <VCol cols="12" sm="6" md="2">
      <VCard class="text-center pa-4" elevation="1" color="success" variant="tonal">
        <VAvatar color="success" size="40" class="mb-3">
          <VIcon icon="tabler-check" color="white" />
        </VAvatar>
        <div class="text-h5 font-weight-bold text-success">
          {{ summaryStats.totalAccepted.toLocaleString() }}
        </div>
        <div class="text-body-2 text-medium-emphasis">Accepted</div>
      </VCard>
    </VCol>
    <VCol cols="12" sm="6" md="2">
      <VCard class="text-center pa-4" elevation="1" color="error" variant="tonal">
        <VAvatar color="error" size="40" class="mb-3">
          <VIcon icon="tabler-check" color="white" />
        </VAvatar>
        <div class="text-h5 font-weight-bold text-error">
          {{ summaryStats.totalRejected.toLocaleString() }}
        </div>
        <div class="text-body-2 text-medium-emphasis">Rejected</div>
      </VCard>
    </VCol>
    <VCol cols="12" sm="6" md="3">
      <VCard class="text-center pa-4" elevation="1" color="warning" variant="tonal">
        <VAvatar color="warning" size="40" class="mb-3">
          <VIcon icon="tabler-percentage" color="white" />
        </VAvatar>
        <div class="text-h5 font-weight-bold text-warning">
          {{ summaryStats.overallSuccessRate.toLocaleString() }}%
        </div>
        <div class="text-body-2 text-medium-emphasis">Success Rate</div>
      </VCard>
    </VCol>
  </VRow>

  <!-- Enhanced Search & Filter Section -->
  <VCard class="mb-6" elevation="1">
    <VCardTitle class="d-flex align-center justify-space-between pa-6 pb-4">
      <div class="d-flex align-center">
        <VIcon icon="tabler-filter" class="me-2 text-primary" />
        <span class="text-h6 font-weight-medium">Search & Filters</span>
        <VBadge
          v-if="hasActiveFilters"
          :content="1"
          color="primary"
          class="ml-5"
        />
      </div>
      <div class="d-flex align-center gap-2">
        <VBtn
          v-if="hasActiveFilters"
          variant="text"
          size="small"
          color="error"
          @click="clearAllFilters"
        >
          <VIcon icon="tabler-x" class="me-1" />
          Clear All
        </VBtn>
      </div>
    </VCardTitle>

    <VCardText class="pt-0">
      <!-- Primary Search Bar -->
      <VRow class="mb-4">
        <VCol cols="12" sm="6" md="4">
          <AppTextField
            v-model="searchQuery"
            placeholder="Search by Run ID, date, or any report details..."
            prepend-inner-icon="tabler-search"
            variant="outlined"
            hide-details
            clearable
            class="search-field"
          />
        </VCol>
        <VCol cols="12" sm="6" md="4">
          <AppSelect
            v-model="selectedStatus"
            placeholder="Success Rate"
            :items="statusOptions"
            variant="outlined"
            clearable
            hide-details
            prepend-inner-icon="tabler-chart-line"
          />
        </VCol>
        <VCol cols="12" sm="6" md="4">
          <AppSelect
            v-model="selectedDateRange"
            placeholder="Date Range"
            :items="dateRangeOptions"
            variant="outlined"
            clearable
            hide-details
            prepend-inner-icon="tabler-calendar"
          />
        </VCol>
        <!-- <VCol cols="12" sm="6" md="3">
            <AppTextField label="Min Domain Count" placeholder="0+" variant="outlined" type="number" hide-details
              prepend-inner-icon="tabler-world" />
          </VCol>
          <VCol cols="12" sm="6" md="3">
            <AppTextField label="Min Backlinks" placeholder="0+" variant="outlined" type="number" hide-details
              prepend-inner-icon="tabler-link" />
          </VCol>
          <VCol cols="12" sm="6" md="3">
            <AppTextField label="Min Success Rate" placeholder="0-100%" variant="outlined" type="number" hide-details
              prepend-inner-icon="tabler-percentage" />
          </VCol>
          <VCol cols="12" sm="6" md="3">
            <AppTextField label="Max Success Rate" placeholder="0-100%" variant="outlined" type="number" hide-details
              prepend-inner-icon="tabler-percentage" />
          </VCol> -->
      </VRow>

      <!-- Quick Filters -->
      <!-- <VRow class="d-flex justify-end mb-4">
          <VCol cols="12" sm="12" md="4" class="d-flex">
            <VBtn color="primary" variant="flat" block @click="fetchReports">
              <VIcon icon="tabler-search" class="me-2" />
              Search Reports
            </VBtn>
          </VCol>
        </VRow> -->
    </VCardText>
  </VCard>

  <VCard elevation="1">
    <div class="d-flex flex-wrap gap-4 ma-6">
      <div class="d-flex align-center text-body-1 font-weight-regular my-4">
        <span class="font-weight-medium text-h6">
          {{ totalReports }}
        </span>
        <span class="ml-2">Record Found</span>

        <VChip
          v-if="selectedRows.length"
          color="primary"
          size="small"
          class="ml-4"
          elevation="2"
          outlined
        >
          {{ selectedRows.length }} selected
        </VChip>
      </div>
      <VSpacer />
      <div class="d-flex align-center gap-2">
        <VBtn
          v-if="selectedRows.length"
          variant="text"
          size="small"
          color="error"
        >
          <VIcon icon="tabler-trash" class="me-1" />
          Delete Selected
        </VBtn>
      </div>
      <div class="d-flex gap-4 flex-wrap align-center">
        <AppSelect v-model="itemsPerPage" :items="[5, 10, 20, 25, 50]" />
        <!-- 👉 Export button -->
        <VBtn
          variant="tonal"
          color="secondary"
          prepend-icon="tabler-upload"
          @click="handleExportReports"
        >
          Export
        </VBtn>
      </div>
    </div>

    <VDivider class="mt-4 mb-2" />

    <!-- Enhanced Data Table -->
    <VDataTableServer
      v-model:items-per-page="itemsPerPage"
      v-model:model-value="selectedRows"
      v-model:page="page"
      :headers="headers"
      show-select
      :items="reportsWithStats"
      :loading="loading"
      loading-text="Fetching reports, please wait..."
      :items-length="totalReports"
      class="reports-table"
      hover
      @update:options="updateOptions"
    >
      <template #item.run_id="{ item }">
        <div class="d-flex align-center">
          <VIcon
            icon="tabler-hash"
            size="16"
            class="me-2 text-medium-emphasis"
          />
          <span class="font-weight-medium text-primary">{{ item.run_id }}</span>
        </div>
      </template>

      <template #item.run_at="{ item }">
        <div class="d-flex align-center">
          <VIcon
            icon="tabler-clock"
            size="16"
            class="me-2 text-medium-emphasis"
          />
          <div>
            <div class="font-weight-medium">{{ formatDate(item.run_at) }}</div>
          </div>
        </div>
      </template>

      <template #item.domain_count="{ item }">
        <div class="d-flex align-center">
          <VIcon icon="tabler-world" size="16" class="me-2 text-info" />
          <VChip color="info" variant="tonal" size="small">
            {{ item.domain_count || 0 }} domains
          </VChip>
        </div>
      </template>

      <template #item.total_keywords="{ item }">
        <div class="d-flex align-center">
          <VIcon
            icon="tabler-link"
            size="16"
            class="me-2 text-medium-emphasis"
          />
          <span class="font-weight-bold">{{
            (item.total_keywords || 0).toLocaleString()
          }}</span>
        </div>
      </template>

      <template #item.accepted_keywords="{ item }">
        <div class="d-flex align-center">
          <VIcon icon="tabler-check" size="16" class="me-2 text-success" />
          <span class="font-weight-bold text-success">{{
            (item.accepted_keywords || 0).toLocaleString()
          }}</span>
        </div>
      </template>

      <template #item.rejected_keywords="{ item }">
        <div class="d-flex align-center">
          <VIcon icon="tabler-x" size="16" class="me-2 text-error" />
          <span class="font-weight-bold text-error">{{
            (item.rejected_keywords || 0).toLocaleString()
          }}</span>
        </div>
      </template>

      <template #item.success_rate="{ item }">
        <div class="d-flex align-center">
          <VProgressCircular
            :model-value="item.success_rate"
            size="42"
            width="3"
            :color="
              item.success_rate > 80
                ? 'success'
                : item.success_rate > 50
                ? 'warning'
                : 'error'
            "
            class="me-2"
          >
            <span class="text-xs font-weight-bold"
              >{{ item.success_rate }}%</span
            >
          </VProgressCircular>
          <VChip
            :color="
              item.success_rate > 80
                ? 'success'
                : item.success_rate > 50
                ? 'warning'
                : 'error'
            "
            variant="tonal"
            size="small"
            class="ml-1"
          >
            {{
              item.success_rate > 80
                ? "Excellent"
                : item.success_rate > 50
                ? "Good"
                : "Needs Work"
            }}
          </VChip>
        </div>
      </template>

      <template #item.actions="{ item }">
        <div class="d-flex align-center">
          <VTooltip text="View Report">
            <template #activator="{ props }">
              <IconBtn v-bind="props" size="small">
                <router-link
                  :to="{ name: 'keywordreport-view', params: { id: item.id } }"
                >
                  <VIcon icon="tabler-eye" size="24" />
                </router-link>
              </IconBtn>
            </template>
          </VTooltip>

          <VTooltip text="Download Report">
            <template #activator="{ props }">
              <IconBtn
                v-bind="props"
                size="small"
                @click="handleExportSingleReport(item.id)"
              >
                <VIcon icon="tabler-download" color="info" size="24" />
              </IconBtn>
            </template>
          </VTooltip>
        </div>
      </template>

      <!-- Empty State -->
      <template #no-data>
        <div class="text-center pa-8">
          <VIcon
            icon="tabler-report-off"
            size="48"
            class="text-medium-emphasis mb-4"
          />
          <h3 class="text-h6 mb-2">No reports found</h3>
          <p class="text-body-2 text-medium-emphasis mb-4">
            No reports match your current search criteria. Try adjusting your
            filters or generate a new report.
          </p>
          <VBtn color="primary">
            <VIcon icon="tabler-plus" class="me-2" />
            Generate New Report
          </VBtn>
        </div>
      </template>
      <template #bottom>
        <TablePagination
          v-model:page="page"
          :items-per-page="itemsPerPage"
          :total-items="totalReports"
        />
      </template>
    </VDataTableServer>
  </VCard>
</template>

<style lang="scss" scoped>
// Enhanced search field
.search-field {
  .v-input__control {
    border-radius: 12px;
  }
}

// Reports table improvements
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

// Summary cards
.v-card {
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;
}

// Progress circles
.v-progress-circular {
  margin: 0 !important;
}

// Responsive improvements
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

// Custom scrollbar
.v-data-table__wrapper::-webkit-scrollbar {
  height: 6px;
}

.v-data-table__wrapper::-webkit-scrollbar-track {
  background: rgb(var(--v-theme-surface-variant));
  border-radius: 3px;
}

.v-data-table__wrapper::-webkit-scrollbar-thumb {
  background: rgb(var(--v-theme-primary));
  border-radius: 3px;
}

// Chip improvements
.v-chip {
  font-size: 0.75rem;
  height: 24px;
}
</style>
