<script setup>
import { computed, onMounted, watch, ref } from "vue";
import { useKeywordApi } from "@/composables/KeywordApi.js";
import { useRoute } from "vue-router";

const route = useRoute();
const keywordId = computed(() => route.params.id);

const { KeywordHistory, loading, error, fetchKeywordHistory, showAlert } =
  useKeywordApi();

// Table state
const page = ref(1);
const keywordData = ref([]);
const itemsPerPage = ref(10);
const search = ref("");
const tableLoading = ref(false);
const reportDetailsDialog = ref(false);
const selectedReport = ref(null);

// Data table headers
const headers = [
  { title: "Report ID", key: "report_id", sortable: true, width: "120px" },
  { title: "Status", key: "status", sortable: true, width: "120px" },
  { title: "LLM Models", key: "llm_models", align:"start", sortable: false, width: "180px" },
  { title: "Success Rate", key: "success_rate", sortable: false, width: "150px" },
  { title: "Created", key: "created_at", sortable: true, width: "200px" },
  {
    title: "Actions",
    key: "actions",
    sortable: false,
    width: "80px",
    align: "center",
  },
];

const viewReportDetails = (report) => {
  console.log("selected report", report);
  selectedReport.value = report;
  reportDetailsDialog.value = true;
};

const handleRetry = async () => {
  if (keywordId.value) {
    await loadReportData();
  }
};

// Fixed computed properties
const historyItems = computed(() => {
  if (!KeywordHistory.value?.history?.data) return [];
  
  const reports = {};
  
  KeywordHistory.value.history.data.forEach(item => {
    const reportId = item.report_id;
    if (!reports[reportId]) {
      reports[reportId] = {
        id: reportId,
        report_id: reportId,
        created_at: item.created_at,
        keyword_data: [],
        status: 1 
      };
    }
    reports[reportId].keyword_data.push(item);
  });

  return Object.values(reports).map(report => {
    const successCount = report.keyword_data.filter(data => data.domain_found_in_response).length;
    const totalCount = report.keyword_data.length;
    
    if (successCount === totalCount && totalCount > 0) {
      report.status = 3; // success
    } else if (successCount > 0) {
      report.status = 2; // partial success
    } else {
      report.status = 1; // failed/pending
    }
    
    report.success_rate = `${successCount}/${totalCount}`;
    report.llm_models = report.keyword_data.map(data => data.llm_type).join(', ');
    
    return report;
  }).sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
});

const latestReport = computed(() => {
  if (!historyItems.value.length) return null;
  return historyItems.value[0]; 
});

const totalReports = computed(() => historyItems.value.length);

const keywordText = computed(() => KeywordHistory?.value?.keyword?.keyword || "");

const getLatestReportSummary = (report) => {
  if (!report?.keyword_data) return { total: 0, success: 0, models: [] };
  
  const total = report.keyword_data.length;
  const success = report.keyword_data.filter(data => data.domain_found_in_response).length;
  const models = [...new Set(report.keyword_data.map(data => data.llm_type))];
  
  return { total, success, models };
};

// Helper functions
const getStatusColor = (status) => {
  switch (status) {
    case 3:
      return "success";
    case 2:
      return "warning";
    case 1:
    default:
      return "error";
  }
};

const getStatusIcon = (status) => {
  switch (status) {
    case 3:
      return "tabler-check";
    case 2:
      return "tabler-alert-triangle";
    case 1:
    default:
      return "tabler-x";
  }
};

const getStatusText = (status) => {
  switch (status) {
    case 3:
      return "Success";
    case 2:
      return "Partial";
    case 1:
    default:
      return "Failed";
  }
};

const getLLMIcon = (llmType) => {
  switch (llmType?.toLowerCase()) {
    case 'gpt':
      return 'tabler-brand-openai';
    case 'gemini':
      return 'tabler-brand-google';
    case 'cohere':
      return 'tabler-robot';
    default:
      return 'tabler-brain';
  }
};

const getLLMColor = (llmType) => {
  switch (llmType?.toLowerCase()) {
    case 'gpt':
      return 'secondary';
    case 'gemini':
      return 'info';
    case 'cohere':
      return 'warning';
    default:
      return 'primary';
  }
};

const formatDate = (dateString) => {
  if (!dateString) return "N/A";
  try {
    return new Date(dateString).toLocaleDateString("en-US", {
      month: "short",
      day: "numeric",
      year: "numeric",
    });
  } catch (e) {
    return "Invalid Date";
  }
};

const formatTime = (dateString) => {
  if (!dateString) return "N/A";
  try {
    return new Date(dateString).toLocaleTimeString("en-US", {
      hour: "2-digit",
      minute: "2-digit",
    });
  } catch (e) {
    return "Invalid Time";
  }
};

const formatDateTime = (dateString) => {
  if (!dateString) return "N/A";
  try {
    return new Date(dateString).toLocaleString("en-US", {
      year: "numeric",
      month: "short",
      day: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    });
  } catch (e) {
    return "Invalid Date";
  }
};

const formatLLMResponse = (response) => {
  if (!response) return "No response available";

  try {
    // Handle JSON responses
    if (typeof response === "object") {
      response = JSON.stringify(response, null, 2);
    }

    let formattedResponse = response
      // Headers
      .replace(/^### (.*$)/gm, "<h5 class='mb-2 mt-3'>$1</h5>")
      .replace(/^## (.*$)/gm, "<h4 class='mb-2 mt-3'>$1</h4>")
      .replace(/^# (.*$)/gm, "<h3 class='mb-2 mt-3'>$1</h3>")
      // Bold and italic
      .replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>")
      .replace(/__(.*?)__/g, "<strong>$1</strong>")
      .replace(/\*(.*?)\*/g, "<em>$1</em>")
      .replace(/_(.*?)_/g, "<em>$1</em>")
      // Lists
      .replace(/^[\-\*\+] (.*)$/gm, "<li class='ml-4'>$1</li>")
      .replace(/^\d+\. (.*)$/gm, "<li class='ml-4'>$1</li>")
      // Links
      .replace(
        /\[([^\]]+)\]\(([^)]+)\)/g,
        '<a href="$2" target="_blank" rel="noopener noreferrer" class="text-primary">$1</a>'
      )
      // Code
      .replace(
        /```([^`]+)```/g,
        "<pre class='code-block pa-3 mb-2'><code>$1</code></pre>"
      )
      .replace(/`([^`]+)`/g, "<code class='inline-code'>$1</code>")
      // Paragraphs
      .replace(/\n\n+/g, "</p><p class='mb-2'>")
      .replace(/\n/g, "<br>");

    // Wrap in paragraph if not already formatted
    if (
      !formattedResponse.includes("<p>") &&
      !formattedResponse.includes("<h")
    ) {
      formattedResponse = "<p class='mb-2'>" + formattedResponse + "</p>";
    }

    // Wrap lists in ul tags
    formattedResponse = formattedResponse.replace(
      /(<li.*?<\/li>)/gs,
      (match) => {
        return `<ul class='mb-2'>${match}</ul>`;
      }
    );

    return formattedResponse;
  } catch {
    return response;
  }
};

const loadReportData = async () => {
  try {
    tableLoading.value = true;
    await fetchKeywordHistory(keywordId.value);
  } catch (err) {
    console.error("Failed to load report:", err);
  } finally {
    tableLoading.value = false;
  }
};

onMounted(async () => {
  await loadReportData();
});
</script>

<template>
  <div>
    <!-- Header Section -->
    <VCard class="mb-6 overflow-hidden" elevation="3">
      <VCardText class="pa-8">
        <VRow align="center" justify="space-between" class="mb-4">
          <VCol cols="12" md="8">
            <div class="d-flex align-center mb-4">
              <VAvatar color="white" size="56" class="me-4 elevation-3">
                <VIcon icon="tabler-key" size="28" color="primary" />
              </VAvatar>
              <div>
                <h1 class="text-h4 font-weight-bold mb-2">{{ keywordText.toUpperCase() }}</h1>
                <p class="text-subtitle-1 mb-0">
                  Comprehensive Keyword Analysis Timeline
                </p>
              </div>
            </div>
            <div class="d-flex align-center gap-3 flex-wrap">
              <VChip
                color="primary"
                variant="flat"
                size="large"
                class="elevation-2"
              >
                <VIcon icon="tabler-chart-bar" class="me-2" />
                <span class="font-weight-medium"
                  >{{ totalReports }} Total Reports</span
                >
              </VChip>
              <VChip
                color="primary"
                variant="flat"
                size="large"
                class="elevation-2"
                v-if="latestReport"
              >
                <VIcon icon="tabler-clock-check" class="me-2" />
                Last Updated: {{ formatDate(latestReport.created_at) }}
              </VChip>
            </div>
          </VCol>
          <VCol cols="12" md="4" class="d-flex justify-end">
            <VBtn
              variant="flat"
              size="large"
              :to="{ name: 'apps-keyword-list' }"
              class="elevation-3"
            >
              <VIcon icon="tabler-arrow-left" class="me-2" />
              Back to Keywords
            </VBtn>
          </VCol>
        </VRow>
      </VCardText>
    </VCard>

    <!-- Loading State -->
    <template v-if="loading">
      <VCard class="mb-6" elevation="2">
        <VCardText class="text-center py-16">
          <div class="d-flex flex-column align-center">
            <VProgressCircular
              indeterminate
              color="primary"
              size="64"
              width="6"
            />
            <h6 class="text-h6 mt-6 mb-2">Loading keyword history...</h6>
            <p class="text-body-2 text-medium-emphasis mb-0">
              Fetching latest reports and analysis data
            </p>
          </div>
        </VCardText>
      </VCard>
    </template>

    <!-- Error State -->
    <template v-else-if="error">
      <VCard elevation="2" class="mb-6">
        <VCardText class="text-center py-16">
          <VAvatar
            size="120"
            color="error"
            variant="tonal"
            class="mb-6 mx-auto"
          >
            <VIcon icon="tabler-alert-circle" size="64" />
          </VAvatar>
          <h4 class="text-h4 mb-4">Failed to Load History</h4>
          <p
            class="text-body-1 text-medium-emphasis mb-6 max-width-400 mx-auto"
          >
            {{
              error ||
              "There was an error loading the keyword history. Please try again."
            }}
          </p>
          <VBtn color="primary" size="large" @click="handleRetry">
            <VIcon icon="tabler-refresh" class="me-2" />
            Retry
          </VBtn>
        </VCardText>
      </VCard>
    </template>

    <!-- Content when data is loaded -->
    <template v-else>
      <!-- Latest Report Card -->
      <VCard elevation="3" class="mb-6" v-if="latestReport">
        <VCardTitle class="pa-4 pb-4">
          <div class="d-flex align-center">
            <VAvatar variant="flat" size="48" class="me-4">
              <VIcon icon="tabler-star" size="24" />
            </VAvatar>
            <div>
              <h4 class="text-h4 mb-1">Latest Report</h4>
              <p class="text-body-2 mb-0">
                Most recent analysis for this keyword
              </p>
            </div>
          </div>
        </VCardTitle>

        <VCardText class="pa-4">
          <VRow class="mb-4">
            <VCol cols="12" md="3">
              <div class="stat-card">
                <VIcon
                  icon="tabler-file-report"
                  size="32"
                  color="primary"
                  class="mb-3"
                />
                <h5 class="text-h5 font-weight-bold mb-1">
                  Report #{{ latestReport.report_id }}
                </h5>
                <p class="text-body-2 text-medium-emphasis">Report ID</p>
              </div>
            </VCol>
            <VCol cols="12" md="3">
              <div class="stat-card">
                <VIcon
                  icon="tabler-clock"
                  size="32"
                  color="info"
                  class="mb-3"
                />
                <h5 class="text-h5 font-weight-bold mb-1">
                  {{ formatDate(latestReport.created_at) }}
                </h5>
                <p class="text-body-2 text-medium-emphasis">
                  {{ formatTime(latestReport.created_at) }}
                </p>
              </div>
            </VCol>
            <VCol cols="12" md="3">
              <div class="stat-card">
                <VIcon
                  :icon="getStatusIcon(latestReport.status)"
                  size="32"
                  :color="getStatusColor(latestReport.status)"
                  class="mb-3"
                />
                <h5 class="text-h5 font-weight-bold mb-1">
                  {{ getLatestReportSummary(latestReport).success }}/{{ getLatestReportSummary(latestReport).total }}
                </h5>
                <p class="text-body-2 text-medium-emphasis">Success Rate</p>
              </div>
            </VCol>
            <VCol cols="12" md="3">
              <div class="stat-card">
                <VIcon
                  icon="tabler-brain"
                  size="32"
                  color="secondary"
                  class="mb-3"
                />
                <h5 class="text-h5 font-weight-bold mb-1">
                  {{ getLatestReportSummary(latestReport).total }}
                </h5>
                <p class="text-body-2 text-medium-emphasis">LLM Models</p>
              </div>
            </VCol>
          </VRow>

          <!-- LLM Models Summary -->
          <VDivider class="my-4" />
          <div class="mb-4">
            <h6 class="text-h6 mb-3">LLM Models Tested</h6>
            <div class="d-flex gap-2 flex-wrap">
              <VChip
                v-for="model in getLatestReportSummary(latestReport).models"
                :key="model"
                :color="getLLMColor(model)"
                variant="tonal"
                size="small"
              >
                <VIcon :icon="getLLMIcon(model)" class="me-1" size="16" />
                {{ model.toUpperCase() }}
              </VChip>
            </div>
          </div>

          <VDivider class="my-4" />

          <div class="d-flex justify-center">
            <VBtn
              color="primary"
              variant="flat"
              size="large"
              @click="viewReportDetails(latestReport)"
            >
              <VIcon icon="tabler-eye" class="me-2" />
              View Full Report Details
            </VBtn>
          </div>
        </VCardText>
      </VCard>

      <!-- Historical Data Table -->
      <VCard elevation="3" class="history-table-card">
        <VCardTitle class="pa-6 pb-4">
          <div class="d-flex align-center justify-space-between w-100">
            <div class="d-flex align-center">
              <VAvatar color="indigo" variant="tonal" size="48" class="me-4">
                <VIcon icon="tabler-history" size="24" />
              </VAvatar>
              <div>
                <h4 class="text-h4 mb-1">Historical Reports</h4>
                <p class="text-body-2 text-medium-emphasis mb-0">
                  Complete analysis history for this keyword
                </p>
              </div>
            </div>
          </div>
        </VCardTitle>

        <VDivider />

        <VDataTableServer
          v-model:items-per-page="itemsPerPage"
          v-model:page="page"
          :headers="headers"
          :items="historyItems"
          :items-length="totalReports"
          :loading="tableLoading"
          class="elevation-1"
          item-value="id"
          :search="search"
          hover
        >
          <!-- Report ID Column -->
          <template v-slot:item.report_id="{ item }">
            <VChip
              color="primary"
              variant="tonal"
              size="small"
              class="font-weight-medium"
            >
              #{{ item.report_id }}
            </VChip>
          </template>

          <!-- Status Column -->
          <template v-slot:item.status="{ item }">
            <VChip
              :color="getStatusColor(item.status)"
              variant="flat"
              size="small"
            >
              <VIcon
                :icon="getStatusIcon(item.status)"
                class="me-1"
                size="20"
              />
            </VChip>
          </template>

          <!-- LLM Models Column -->
          <template v-slot:item.llm_models="{ item }">
            <div class="d-flex gap-1 flex-wrap">
              <VChip
                v-for="model in getLatestReportSummary(item).models"
                :key="model"
                :color="getLLMColor(model)"
                variant="tonal"
                size="x-small"
              >
                <VIcon :icon="getLLMIcon(model)" size="20" class="me-1" />
              </VChip>
            </div>
          </template>

          <!-- Success Rate Column -->
          <template v-slot:item.success_rate="{ item }">
            <VChip
              :color="getStatusColor(item.status)"
              variant="tonal"
              size="small"
            >
              {{ item.success_rate }}
            </VChip>
          </template>

          <!-- Created Date Column -->
          <template v-slot:item.created_at="{ item }">
              <p class="text-body-2 mb-0 font-weight-medium">
                {{ formatDate(item.created_at) }}
              </p>
              <p class="text-caption text-medium-emphasis mb-0">
                {{ formatTime(item.created_at) }}
              </p>
          </template>

          <!-- Actions Column -->
          <template v-slot:item.actions="{ item }">
            <VBtn
              icon
              variant="text"
              size="small"
              @click="viewReportDetails(item)"
              color="primary"
            >
              <VIcon icon="tabler-eye" />
              <VTooltip activator="parent" location="top"
                >View Details</VTooltip
              >
            </VBtn>
          </template>

          <!-- Loading slot -->
          <template v-slot:loading>
            <VProgressLinear indeterminate color="primary" height="3" />
          </template>

          <!-- No data slot -->
          <template v-slot:no-data>
            <div class="text-center py-8">
              <VIcon
                icon="tabler-database-off"
                size="48"
                color="grey"
                class="mb-4"
              />
              <p class="text-body-1 text-medium-emphasis">No reports found</p>
            </div>
          </template>

          <!-- <template #bottom>
        <TablePagination
          v-model:page="pagination.page"
          :items-per-page="pagination.itemsPerPage"
          :total-items="totalKeywords"
        />
      </template> -->
        </VDataTableServer>
      </VCard>
    </template>

    <!-- Report Details Dialog -->
    <VDialog
      v-model="reportDetailsDialog"
      max-width="1400"
      persistent
      scrollable
    >
      <VCard v-if="selectedReport" elevation="8">
        <VCardTitle class="pa-5">
          <div class="d-flex align-center justify-space-between w-100">
            <div class="d-flex align-center">
              <VAvatar color="white" size="40" class="me-3">
                <VIcon icon="tabler-file-report" color="primary" />
              </VAvatar>
              <div>
                <h4 class="text-h5">
                  Report Details - #{{ selectedReport.report_id }}
                </h4>
                <p class="text-caption mb-0">
                  Created: {{ formatDateTime(selectedReport.created_at) }}
                </p>
              </div>
            </div>
            <VBtn
              icon="tabler-x"
              variant="text"
              color="white"
              @click="reportDetailsDialog = false"
            ></VBtn>
          </div>
        </VCardTitle>

        <VDivider />

        <VCardText class="pa-6" style="max-height: 70vh; overflow-y: auto">
          <!-- Report Metadata -->
          <VRow class="mb-4">
            <VCol cols="12">
              <div class="d-flex gap-3 flex-wrap mb-4">
                <!-- <VChip
                  :color="getStatusColor(selectedReport.status)"
                  variant="flat"
                >
                  <VIcon
                    :icon="getStatusIcon(selectedReport.status)"
                    class="me-1"
                    size="16"
                  />
                  Status: {{ getStatusText(selectedReport.status) }}
                </VChip> -->
                <VChip color="primary" variant="tonal">
                  <VIcon icon="tabler-brain" class="me-1" size="16" />
                  {{ getLatestReportSummary(selectedReport).total }} LLM Models
                </VChip>
                <VChip color="success" variant="tonal">
                  <VIcon icon="tabler-target" class="me-1" size="16" />
                  {{ getLatestReportSummary(selectedReport).success }}/{{ getLatestReportSummary(selectedReport).total }} Success Rate
                </VChip>
              </div>
            </VCol>
          </VRow>

          <!-- LLM Responses -->
          <VRow>
            <VCol 
              cols="12" 
              :md="selectedReport.keyword_data?.length === 1 ? 12 : selectedReport.keyword_data?.length === 2 ? 6 : 4"
              v-for="keywordData in selectedReport.keyword_data"
              :key="keywordData.id"
            >
              <VCard elevation="3" class="llm-response-card h-100">
                <VCardTitle :class="`pa-4 bg-${getLLMColor(keywordData.llm_type)}`">
                  <div class="d-flex align-center justify-space-between">
                    <div class="d-flex align-center">
                      <VIcon :icon="getLLMIcon(keywordData.llm_type)" class="me-2" />
                      <span class="font-weight-medium text-sm">
                        {{ keywordData.llm_type?.toUpperCase() }} Response
                      </span>
                    </div>
                    <VChip
                      :color="keywordData.domain_found_in_response ? 'success' : 'error'"
                      size="small"
                      variant="flat"
                    >
                      <VIcon 
                        :icon="keywordData.domain_found_in_response ? 'tabler-check' : 'tabler-x'" 
                        size="14" 
                        class="me-1" 
                      />
                      {{ keywordData.domain_found_in_response ? 'Found' : 'Not Found' }}
                    </VChip>
                  </div>
                </VCardTitle>
                <VCardText class="pa-4">
                  <div
                    class="formatted-response"
                    v-html="formatLLMResponse(keywordData.llm_response)"
                  ></div>
                </VCardText>
              </VCard>
            </VCol>
          </VRow>

          <!-- No Responses Message -->
          <VAlert
            v-if="!selectedReport.keyword_data?.length"
            type="info"
            variant="tonal"
            class="mt-4"
          >
            <VIcon icon="tabler-info-circle" class="me-2" />
            No LLM responses available for this report.
          </VAlert>
        </VCardText>

        <VDivider />

        <VCardActions class="pa-4">
          <VSpacer />
          <VBtn
            color="primary"
            variant="text"
            @click="reportDetailsDialog = false"
          >
            Close
          </VBtn>
        </VCardActions>
      </VCard>
    </VDialog>
  </div>
</template>

<style scoped>
.stat-card {
  text-align: center;
  padding: 1rem;
  border-radius: 8px;
  border: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
}

.llm-response-card {
  height: 100%;
}

.formatted-response {
  max-height: 300px;
  overflow-y: auto;
}

.formatted-response h3,
.formatted-response h4,
.formatted-response h5 {
  color: rgb(var(--v-theme-primary));
}

.formatted-response pre {
  background-color: rgba(var(--v-theme-surface), 0.8);
  border-radius: 4px;
  font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
}

.formatted-response code.inline-code {
  background-color: rgba(var(--v-theme-primary), 0.1);
  padding: 2px 4px;
  border-radius: 3px;
  font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
}
</style>