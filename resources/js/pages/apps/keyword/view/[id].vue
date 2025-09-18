<template>
  <div>
    <!-- Header Section -->
    <VCard class="mb-6 overflow-hidden" elevation="3" color="grey-lighten-5">
      <VCardText class="pa-8">
        <VRow align="center" justify="space-between" class="mb-4">
          <VCol cols="12" md="8">
            <div class="d-flex align-center mb-4">
              <VAvatar color="primary" size="48" class="me-4">
                <VIcon icon="tabler-key" size="24" />
              </VAvatar>
              <div>
                <h1 class="text-h3 font-weight-bold mb-2">{{ keywordText }}</h1>
                <p class="text-subtitle-1 text-medium-emphasis mb-0">Keyword Analysis Timeline</p>
              </div>
            </div>
            <div class="d-flex align-center gap-3 flex-wrap">
              <VChip color="primary" variant="elevated" size="large">
                <VIcon icon="tabler-history" class="me-2" />
                {{ totalReports }} Total Reports
              </VChip>
              <VChip color="success" variant="flat" size="large" v-if="latestReport">
                <VIcon icon="tabler-clock" class="me-2" />
                Last Updated: {{ formatDate(latestReport.created_at) }}
              </VChip>
            </div>
          </VCol>
          <VCol cols="12" md="4" class="d-flex justify-end">
            <VBtn
              color="primary"
              variant="flat"
              size="large"
              :to="{ name: 'apps-keyword-list' }"
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
            <VProgressCircular indeterminate color="primary" size="64" width="6" />
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
          <VAvatar size="120" color="error" variant="tonal" class="mb-6 mx-auto">
            <VIcon icon="tabler-alert-circle" size="64" />
          </VAvatar>
          <h4 class="text-h4 mb-4">Failed to Load History</h4>
          <p class="text-body-1 text-medium-emphasis mb-6 max-width-400 mx-auto">
            {{ error || 'There was an error loading the keyword history. Please try again.' }}
          </p>
          <VBtn color="primary" size="large" @click="handleRetry">
            <VIcon icon="tabler-refresh" class="me-2" />
            Retry
          </VBtn>
        </VCardText>
      </VCard>
    </template>

    <!-- Content when data is loaded -->
    <template v-else-if="historyItems.length > 0">
      
      <!-- Latest Report Card with All LLM Responses -->
      <VCard elevation="4" class="mb-8 latest-report-card" v-if="latestReport">
        <VCardTitle class="pa-6 pb-4 bg-gradient-primary text-white">
          <div class="d-flex align-center justify-space-between">
            <div class="d-flex align-center">
              <VAvatar color="white" variant="flat" size="56" class="me-4">
                <VIcon icon="tabler-star-filled" color="primary" size="28" />
              </VAvatar>
              <div>
                <h3 class="text-h3 mb-2">Latest Analysis Report</h3>
                <p class="text-body-1 mb-0 opacity-90">
                  Report ID: #{{ latestReport.report_id || latestReport.id }}
                </p>
              </div>
            </div>
            <VChip color="white" variant="flat" size="large">
              <VIcon icon="tabler-calendar" class="me-2" />
              {{ formatDateTime(latestReport.created_at) }}
            </VChip>
          </div>
        </VCardTitle>

        <VDivider />

        <!-- Report Overview Stats -->
        <VCardText class="pa-6 pb-4">
          <VRow class="mb-6">
            <VCol cols="12" md="3">
              <VCard elevation="2" color="primary" variant="tonal" class="text-center pa-4">
                <VAvatar size="48" color="primary" class="mb-3 mx-auto">
                  <VIcon icon="tabler-flag" size="24" />
                </VAvatar>
                <p class="text-caption text-medium-emphasis mb-1">Status</p>
                <h6 class="text-h6 font-weight-bold">
                  {{ getStatusText(latestReport.status) }}
                </h6>
              </VCard>
            </VCol>
            <VCol cols="12" md="3">
              <VCard elevation="2" color="info" variant="tonal" class="text-center pa-4">
                <VAvatar size="48" color="info" class="mb-3 mx-auto">
                  <VIcon icon="tabler-brain" size="24" />
                </VAvatar>
                <p class="text-caption text-medium-emphasis mb-1">LLM Models</p>
                <h6 class="text-h6 font-weight-bold">
                  {{ llmResponsesCount }} Responses
                </h6>
              </VCard>
            </VCol>
            <VCol cols="12" md="3">
              <VCard elevation="2" :color="domainFoundCount > 0 ? 'success' : 'warning'" variant="tonal" class="text-center pa-4">
                <VAvatar size="48" :color="domainFoundCount > 0 ? 'success' : 'warning'" class="mb-3 mx-auto">
                  <VIcon :icon="domainFoundCount > 0 ? 'tabler-check' : 'tabler-alert'" size="24" />
                </VAvatar>
                <p class="text-caption text-medium-emphasis mb-1">Domain Found</p>
                <h6 class="text-h6 font-weight-bold">
                  {{ domainFoundCount }}/{{ llmResponsesCount }}
                </h6>
              </VCard>
            </VCol>
            <VCol cols="12" md="3">
              <VCard elevation="2" color="deep-purple" variant="tonal" class="text-center pa-4">
                <VAvatar size="48" color="deep-purple" class="mb-3 mx-auto">
                  <VIcon icon="tabler-world" size="24" />
                </VAvatar>
                <p class="text-caption text-medium-emphasis mb-1">Domain Authority</p>
                <h6 class="text-h6 font-weight-bold">
                  {{ latestReport.domain?.domain_authority || "N/A" }}
                </h6>
              </VCard>
            </VCol>
          </VRow>
        </VCardText>

        <VDivider />

        <!-- All LLM Responses for Latest Report -->
        <VCardText class="pa-6">
          <div class="d-flex align-center mb-6">
            <VIcon icon="tabler-message-circle-2" size="24" class="me-3" />
            <h4 class="text-h4">LLM Responses</h4>
          </div>

          <VRow>
            <!-- GPT Response -->
            <VCol cols="12" lg="4" v-if="latestReport.gpt_response">
              <VCard elevation="3" class="llm-response-card h-100">
                <VCardTitle class="pa-4 pb-3 bg-error text-white">
                  <div class="d-flex align-center">
                    <VAvatar color="white" variant="flat" size="32" class="me-3">
                      <VIcon icon="tabler-brain" color="error" size="18" />
                    </VAvatar>
                    <span class="text-h6">GPT Response</span>
                  </div>
                </VCardTitle>
                <VDivider />
                <VCardText class="pa-4">
                  <div class="mb-3">
                    <VChip :color="latestReport.gpt_domain_found ? 'success' : 'error'" variant="flat" size="small">
                      <VIcon :icon="latestReport.gpt_domain_found ? 'tabler-check' : 'tabler-x'" class="me-1" size="14" />
                      {{ latestReport.gpt_domain_found ? "Domain Found" : "Domain Not Found" }}
                    </VChip>
                  </div>
                  <div class="response-content">
                    <div class="formatted-response" v-html="formatLLMResponse(latestReport.gpt_response)"></div>
                  </div>
                </VCardText>
              </VCard>
            </VCol>

            <!-- Gemini Response -->
            <VCol cols="12" lg="4" v-if="latestReport.gemini_response">
              <VCard elevation="3" class="llm-response-card h-100">
                <VCardTitle class="pa-4 pb-3 bg-info text-white">
                  <div class="d-flex align-center">
                    <VAvatar color="white" variant="flat" size="32" class="me-3">
                      <VIcon icon="tabler-brain" color="info" size="18" />
                    </VAvatar>
                    <span class="text-h6">Gemini Response</span>
                  </div>
                </VCardTitle>
                <VDivider />
                <VCardText class="pa-4">
                  <div class="mb-3">
                    <VChip :color="latestReport.gemini_domain_found ? 'success' : 'error'" variant="flat" size="small">
                      <VIcon :icon="latestReport.gemini_domain_found ? 'tabler-check' : 'tabler-x'" class="me-1" size="14" />
                      {{ latestReport.gemini_domain_found ? "Domain Found" : "Domain Not Found" }}
                    </VChip>
                  </div>
                  <div class="response-content">
                    <div class="formatted-response" v-html="formatLLMResponse(latestReport.gemini_response)"></div>
                  </div>
                </VCardText>
              </VCard>
            </VCol>

            <!-- Cohere Response -->
            <VCol cols="12" lg="4" v-if="latestReport.cohere_response">
              <VCard elevation="3" class="llm-response-card h-100">
                <VCardTitle class="pa-4 pb-3 bg-warning text-white">
                  <div class="d-flex align-center">
                    <VAvatar color="white" variant="flat" size="32" class="me-3">
                      <VIcon icon="tabler-brain" color="warning" size="18" />
                    </VAvatar>
                    <span class="text-h6">Cohere Response</span>
                  </div>
                </VCardTitle>
                <VDivider />
                <VCardText class="pa-4">
                  <div class="mb-3">
                    <VChip :color="latestReport.cohere_domain_found ? 'success' : 'error'" variant="flat" size="small">
                      <VIcon :icon="latestReport.cohere_domain_found ? 'tabler-check' : 'tabler-x'" class="me-1" size="14" />
                      {{ latestReport.cohere_domain_found ? "Domain Found" : "Domain Not Found" }}
                    </VChip>
                  </div>
                  <div class="response-content">
                    <div class="formatted-response" v-html="formatLLMResponse(latestReport.cohere_response)"></div>
                  </div>
                </VCardText>
              </VCard>
            </VCol>
          </VRow>

          <!-- Domain Information -->
          <VCard elevation="2" color="teal" variant="tonal" class="mt-6">
            <VCardTitle class="pa-4 pb-3">
              <VIcon icon="tabler-world" class="me-2" />
              Domain Information
            </VCardTitle>
            <VDivider />
            <VCardText class="pa-4">
              <VRow>
                <VCol cols="12" md="3">
                  <div class="info-item">
                    <p class="text-caption text-medium-emphasis mb-1">Domain Title</p>
                    <p class="text-body-2 font-weight-medium">
                      {{ latestReport.domain?.title || "Not specified" }}
                    </p>
                  </div>
                </VCol>
                <VCol cols="12" md="3">
                  <div class="info-item">
                    <p class="text-caption text-medium-emphasis mb-1">Target URL</p>
                    <p class="text-body-2 font-weight-medium">
                      {{ latestReport.domain?.target_url || "Not specified" }}
                    </p>
                  </div>
                </VCol>
                <VCol cols="12" md="3">
                  <div class="info-item">
                    <p class="text-caption text-medium-emphasis mb-1">Country</p>
                    <p class="text-body-2 font-weight-medium">
                      {{ latestReport.domain?.country || "N/A" }}
                    </p>
                  </div>
                </VCol>
                <VCol cols="12" md="3">
                  <div class="info-item">
                    <p class="text-caption text-medium-emphasis mb-1">Total Price</p>
                    <p class="text-body-2 font-weight-medium">
                      ${{ latestReport.domain?.total_price || "N/A" }}
                    </p>
                  </div>
                </VCol>
              </VRow>
            </VCardText>
          </VCard>
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
            <VChip color="indigo" variant="flat" size="large">
              {{ historicalReports.length }} Previous Reports
            </VChip>
          </div>
        </VCardTitle>

        <VDivider />

        <VCardText class="pa-6">
          <VDataTableServer
            v-model:items-per-page="itemsPerPage"
            v-model:page="page"
            :headers="headers"
            :items="historicalReports"
            :items-length="totalItems"
            :loading="tableLoading"
            class="elevation-1"
            item-value="id"
            :search="search"
          >
            <!-- Search slot -->
            <template v-slot:top>
              <VToolbar flat>
                <VToolbarTitle>Report History</VToolbarTitle>
                <VDivider class="mx-4" inset vertical></VDivider>
                <VSpacer></VSpacer>
                <VTextField
                  v-model="search"
                  append-icon="tabler-search"
                  label="Search reports..."
                  single-line
                  hide-details
                  variant="outlined"
                  density="compact"
                  style="max-width: 300px;"
                ></VTextField>
              </VToolbar>
            </template>

            <!-- Custom columns -->
            <template v-slot:item.report_id="{ item }">
              <VChip color="primary" variant="flat" size="small">
                #{{ item.report_id || item.id }}
              </VChip>
            </template>

            <template v-slot:item.status="{ item }">
              <VChip 
                :color="getStatusColor(item.status)" 
                variant="flat" 
                size="small"
              >
                <VIcon :icon="getStatusIcon(item.status)" class="me-1" size="14" />
                {{ getStatusText(item.status) }}
              </VChip>
            </template>

            <template v-slot:item.llm_responses="{ item }">
              <div class="d-flex gap-1 flex-wrap">
                <VChip 
                  v-if="item.gpt_response" 
                  color="error" 
                  variant="outlined" 
                  size="x-small"
                >
                  GPT
                </VChip>
                <VChip 
                  v-if="item.gemini_response" 
                  color="info" 
                  variant="outlined" 
                  size="x-small"
                >
                  Gemini
                </VChip>
                <VChip 
                  v-if="item.cohere_response" 
                  color="warning" 
                  variant="outlined" 
                  size="x-small"
                >
                  Cohere
                </VChip>
              </div>
            </template>

            <template v-slot:item.domain_found="{ item }">
              <div class="d-flex gap-1">
                <VIcon 
                  v-if="item.gpt_domain_found" 
                  icon="tabler-check" 
                  color="success" 
                  size="16"
                />
                <VIcon 
                  v-if="item.gemini_domain_found" 
                  icon="tabler-check" 
                  color="success" 
                  size="16"
                />
                <VIcon 
                  v-if="item.cohere_domain_found" 
                  icon="tabler-check" 
                  color="success" 
                  size="16"
                />
                <span class="text-caption text-medium-emphasis ms-2">
                  {{ getDomainFoundCount(item) }}/{{ getLLMResponseCount(item) }}
                </span>
              </div>
            </template>

            <template v-slot:item.created_at="{ item }">
              <div>
                <p class="text-body-2 mb-0">{{ formatDate(item.created_at) }}</p>
                <p class="text-caption text-medium-emphasis mb-0">
                  {{ formatTime(item.created_at) }}
                </p>
              </div>
            </template>

            <template v-slot:item.actions="{ item }">
              <VBtn
                icon="tabler-eye"
                variant="text"
                size="small"
                @click="viewReportDetails(item)"
              >
              </VBtn>
            </template>
          </VDataTableServer>
        </VCardText>
      </VCard>
    </template>

    <!-- No Data State -->
    <template v-else>
      <VCard elevation="2" class="mb-6">
        <VCardText class="text-center py-16">
          <VAvatar size="120" color="grey-lighten-2" class="mb-6 mx-auto">
            <VIcon icon="tabler-history-off" size="64" />
          </VAvatar>
          <h4 class="text-h4 mb-4">No History Available</h4>
          <p class="text-body-1 text-medium-emphasis mb-6 max-width-400 mx-auto">
            No keyword history records found for this keyword.
          </p>
          <VBtn
            color="primary"
            size="large"
            :to="{ name: 'apps-keyword-list' }"
          >
            <VIcon icon="tabler-arrow-left" class="me-2" />
            Back to Keywords
          </VBtn>
        </VCardText>
      </VCard>
    </template>

    <!-- Report Details Dialog -->
    <VDialog v-model="reportDetailsDialog" max-width="1200" persistent>
      <VCard v-if="selectedReport">
        <VCardTitle class="pa-4">
          <div class="d-flex align-center justify-space-between w-100">
            <h4 class="text-h4">Report Details - #{{ selectedReport.report_id || selectedReport.id }}</h4>
            <VBtn icon="tabler-x" variant="text" @click="reportDetailsDialog = false"></VBtn>
          </div>
        </VCardTitle>
        
        <VDivider />
        
        <VCardText class="pa-6" style="max-height: 600px; overflow-y: auto;">
          <VRow>
            <VCol cols="12" md="4" v-if="selectedReport.gpt_response">
              <VCard elevation="2" class="mb-4">
                <VCardTitle class="pa-3 bg-error text-white text-body-1">
                  <VIcon icon="tabler-brain" class="me-2" />
                  GPT Response
                </VCardTitle>
                <VCardText class="pa-3">
                  <div class="formatted-response small-text" v-html="formatLLMResponse(selectedReport.gpt_response)"></div>
                </VCardText>
              </VCard>
            </VCol>
            
            <VCol cols="12" md="4" v-if="selectedReport.gemini_response">
              <VCard elevation="2" class="mb-4">
                <VCardTitle class="pa-3 bg-info text-white text-body-1">
                  <VIcon icon="tabler-brain" class="me-2" />
                  Gemini Response
                </VCardTitle>
                <VCardText class="pa-3">
                  <div class="formatted-response small-text" v-html="formatLLMResponse(selectedReport.gemini_response)"></div>
                </VCardText>
              </VCard>
            </VCol>
            
            <VCol cols="12" md="4" v-if="selectedReport.cohere_response">
              <VCard elevation="2" class="mb-4">
                <VCardTitle class="pa-3 bg-warning text-white text-body-1">
                  <VIcon icon="tabler-brain" class="me-2" />
                  Cohere Response
                </VCardTitle>
                <VCardText class="pa-3">
                  <div class="formatted-response small-text" v-html="formatLLMResponse(selectedReport.cohere_response)"></div>
                </VCardText>
              </VCard>
            </VCol>
          </VRow>
        </VCardText>
      </VCard>
    </VDialog>
  </div>
</template>

<script setup>
import { computed, onMounted, watch, ref } from 'vue';
import { useKeywordApi } from '@/composables/KeywordApi.js';
import { useRoute } from "vue-router";

const route = useRoute();
const keywordId = computed(() => route.params.id);

const {
  KeywordHistory,
  loading,
  error,
  fetchKeywordHistory,
  showAlert
} = useKeywordApi();

// Table state
const page = ref(1);
const itemsPerPage = ref(10);
const search = ref('');
const tableLoading = ref(false);
const reportDetailsDialog = ref(false);
const selectedReport = ref(null);

// Data table headers
const headers = [
  { title: 'Report ID', key: 'report_id', sortable: true, width: '120px' },
  { title: 'Status', key: 'status', sortable: true, width: '120px' },
  { title: 'LLM Models', key: 'llm_responses', sortable: false, width: '150px' },
  { title: 'Domain Found', key: 'domain_found', sortable: false, width: '120px' },
  { title: 'Created', key: 'created_at', sortable: true, width: '140px' },
  { title: 'Actions', key: 'actions', sortable: false, width: '80px', align: 'center' }
];

// Watch for route changes
watch(keywordId, async (newId, oldId) => {
  if (newId && newId !== oldId) {
    await loadReportData();
  }
});

// Load data on mount
onMounted(async () => {
  if (keywordId.value) {
    await loadReportData();
  }
});

const loadReportData = async () => {
  try {
    await fetchKeywordHistory(keywordId.value);
  } catch (err) {
    console.error("Failed to load report:", err);
  }
};

const handleRetry = async () => {
  if (keywordId.value) {
    await loadReportData();
  }
};

const keywordData = computed(() => {
  return KeywordHistory.value || { keyword: {}, history: [] };
});

const historyItems = computed(() => {
  const data = keywordData.value;
  if (data.keyword?.keyword_history) {
    return data.keyword.keyword_history;
  }
  if (data.history) {
    return data.history;
  }
  if (Array.isArray(data)) {
    return data;
  }
  
  return [];
});

console.log('hist',historyItems.value);

const sortedHistoryItems = computed(() => {
  const items = historyItems.value;
  if (!Array.isArray(items)) return [];
  
  return [...items].sort((a, b) => {
    const dateA = new Date(a.created_at || 0);
    const dateB = new Date(b.created_at || 0);
    return dateB - dateA;
  });
});

// Latest report (highest report ID)
const latestReport = computed(() => {
  if (!sortedHistoryItems.value.length) return null;
  
  return sortedHistoryItems.value.reduce((latest, current) => {
    const currentReportId = parseInt(current.report_id || current.id || 0);
    const latestReportId = parseInt(latest.report_id || latest.id || 0);
    return currentReportId > latestReportId ? current : latest;
  });
});

// Historical reports (all except the latest)
const historicalReports = computed(() => {
  if (!latestReport.value) return sortedHistoryItems.value;
  
  const latestId = latestReport.value.report_id || latestReport.value.id;
  return sortedHistoryItems.value.filter(item => 
    (item.report_id || item.id) !== latestId
  );
});

const totalReports = computed(() => sortedHistoryItems.value.length);
const totalItems = computed(() => historicalReports.value.length);

const keywordText = computed(() => {
  const data = keywordData.value;
  return data.keyword?.keyword || data.keyword_text || 'Unknown Keyword';
});

// Count LLM responses for latest report
const llmResponsesCount = computed(() => {
  if (!latestReport.value) return 0;
  let count = 0;
  if (latestReport.value.gpt_response) count++;
  if (latestReport.value.gemini_response) count++;
  if (latestReport.value.cohere_response) count++;
  return count;
});

// Count domains found in latest report
const domainFoundCount = computed(() => {
  if (!latestReport.value) return 0;
  let count = 0;
  if (latestReport.value.gpt_domain_found) count++;
  if (latestReport.value.gemini_domain_found) count++;
  if (latestReport.value.cohere_domain_found) count++;
  return count;
});

// Helper functions
const getStatusColor = (status) => {
  switch (status) {
    case 3: return "success";
    case 2: return "error";
    case 1:
    default: return "warning";
  }
};

const getStatusIcon = (status) => {
  switch (status) {
    case 3: return "tabler-check";
    case 2: return "tabler-x";
    case 1:
    default: return "tabler-clock";
  }
};

const getStatusText = (status) => {
  switch (status) {
    case 3: return "Success";
    case 2: return "Failed";
    case 1:
    default: return "Pending";
  }
};

const getLLMResponseCount = (item) => {
  let count = 0;
  if (item.gpt_response) count++;
  if (item.gemini_response) count++;
  if (item.cohere_response) count++;
  return count;
};

const getDomainFoundCount = (item) => {
  let count = 0;
  if (item.gpt_domain_found) count++;
  if (item.gemini_domain_found) count++;
  if (item.cohere_domain_found) count++;
  return count;
};

const formatDate = (dateString) => {
  if (!dateString) return "N/A";
  try {
    return new Date(dateString).toLocaleDateString("en-US", {
      month: "short",
      day: "numeric",
      year: "numeric"
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
      minute: "2-digit"
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
      minute: "2-digit"
    });
  } catch (e) {
    return "Invalid Date";
  }
};

const formatLLMResponse = (response) => {
  if (!response) return "No response available";

  try {
    let formattedResponse = response
      .replace(/^### (.*$)/gm, "<h3>$1</h3>")
      .replace(/^## (.*$)/gm, "<h2>$1</h2>")
      .replace(/^# (.*$)/gm, "<h1>$1</h1>")
      .replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>")
      .replace(/__(.*?)__/g, "<strong>$1</strong>")
      .replace(/\*(.*?)\*/g, "<em>$1</em>")
      .replace(/_(.*?)_/g, "<em>$1</em>")
      .replace(/^[\-\*\+] (.*)$/gm, "<li>$1</li>")
      .replace(/^\d+\. (.*)$/gm, "<li>$1</li>")
      .replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer">$1</a>')
      .replace(/`([^`]+)`/g, "<code>$1</code>")
      .replace(/\n\n+/g, "</p><p>")
      .replace(/\n/g, "<br>");

    if (!formattedResponse.includes("<p>") && !formattedResponse.includes("<h")) {
      formattedResponse = "<p>" + formattedResponse + "</p>";
    }

    return formattedResponse;
  } catch (e) {
    return response;
  }
};

const viewReportDetails = (item) => {
  selectedReport.value = item;
  reportDetailsDialog.value = true;
};

</script>