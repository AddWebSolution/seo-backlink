<template>
  <div>
    <!-- Modern Header with Gradient Background -->
    <VCard class="mb-8 overflow-hidden" elevation="2" color="grey lighten-4">
      <VCardText class="pa-8">
        <VRow align="center" justify="space-between" class="mb-4">
          <!-- Left Column: Heading and Chips -->
          <VCol cols="12" md="8">
            <h2 class="text-h3 font-weight-bold mb-3">Keyword Data Details</h2>

            <div class="d-flex align-center gap-3 flex-wrap">
              <VChip color="primary" variant="elevated" size="large">
                <VIcon icon="tabler-id" class="me-2" />
                ID: {{ keywordData.id }}
              </VChip>

              <VChip color="primary" variant="flat" size="large">
                <VIcon icon="tabler-file-report" class="me-2" />
                Report ID: {{ keywordData.report_id }}
              </VChip>

              <VChip color="primary" variant="flat" size="large">
                <VIcon icon="tabler-key" class="me-2" />
                Keyword ID: {{ keywordData.keyword_id }}
              </VChip>

              <VChip color="primary" variant="flat" size="large">
                <VIcon :icon="getStatusIcon(keywordData.status)" class="me-2" />
                <span class="text-capitalize">{{
                  getStatusText(keywordData.status)
                  }}</span>
              </VChip>
            </div>
          </VCol>

          <!-- Right Column: Inline Back Button -->
          <VCol cols="12" md="4" class="d-flex justify-end">
            <VBtn color="primary" variant="flat" :to="{
                name: 'keywordreport-view',
                params: { id: keywordData.report_id },
              }">
              <VIcon icon="tabler-arrow-autofit-left" size= "x-large" class="me-1"/>
Back
            </VBtn>
          </VCol>
        </VRow>
      </VCardText>
    </VCard>

    <!-- Loading State -->
    <template v-if="isLoading">
      <VCard class="mb-6" elevation="2">
        <VCardText class="text-center py-16">
          <div class="d-flex flex-column align-center">
            <VProgressCircular indeterminate color="primary" size="64" width="6" />
            <h6 class="text-h6 mt-6 mb-2">Loading keyword data details...</h6>
            <p class="text-body-2 text-medium-emphasis mb-0">
              Please wait while we fetch the information
            </p>
          </div>
        </VCardText>
      </VCard>
    </template>

    <!-- Keyword Data Details -->
    <template v-else-if="keywordData.id">
      <!-- Status Overview Cards -->
      <VRow class="mb-8">
        <VCol cols="12" md="3">
          <VCard elevation="2" class="h-100" color="primary" variant="tonal">
            <VCardText class="pa-6 text-center">
              <VAvatar size="56" color="primary" class="mb-3">
                <VIcon icon="tabler-key" size="28"></VIcon>
              </VAvatar>
              <p class="text-body-2 text-medium-emphasis mb-1">Keyword</p>
              <h5 class="text-h5 font-weight-bold">
                {{keywordData.keyword.keyword }}
              </h5>
            </VCardText>
          </VCard>
        </VCol>

        <VCol cols="12" md="3">
          <VCard elevation="2" class="h-100" :color="getLLMColor(keywordData.llm_type)" variant="tonal">
            <VCardText class="pa-6 text-center">
              <VAvatar size="56" :color="getLLMColor(keywordData.llm_type)" class="mb-3">
                <VIcon icon="tabler-brain" size="28" />
              </VAvatar>
              <p class="text-body-2 text-medium-emphasis mb-1">LLM Type</p>
              <h5 class="text-h5 font-weight-bold text-uppercase">
                {{ keywordData.llm_type || "N/A" }}
              </h5>
            </VCardText>
          </VCard>
        </VCol>


        <VCol cols="12" md="3">
          <VCard elevation="2" class="h-100" :color="keywordData.domain_found_in_response == 1 ? 'success' : 'error'"
            variant="tonal">
            <VCardText class="pa-6 text-center">
              <VAvatar size="56" :color="keywordData.domain_found_in_response == 1 ? 'success' : 'error'" class="mb-3">
                <VIcon :icon="keywordData.domain_found_in_response == 1 ? 'tabler-circle-check' : 'tabler-circle-x'"
                  size="28" />
              </VAvatar>
              <p class="text-body-2 text-medium-emphasis mb-1">Domain Found</p>
              <h5 class="text-h5 font-weight-bold">
                {{ keywordData.domain_found_in_response ==1 ? "Yes" : "No" }}
              </h5>
            </VCardText>
          </VCard>
        </VCol>

        <VCol cols="12" md="3">
          <VCard elevation="2" class="h-100" :color="keywordData.created_at ? 'info' : 'grey'" variant="tonal">
            <VCardText class="pa-6 text-center">
              <VAvatar size="56" :color="keywordData.created_at ? 'info' : 'grey'" class="mb-3">
                <VIcon icon="tabler-clock" size="28" />
              </VAvatar>
              <p class="text-body-2 text-medium-emphasis mb-1">Created</p>
              <h5 class="text-h5 font-weight-bold">
                {{ formatDateTime(keywordData.created_at) }}
              </h5>
            </VCardText>
          </VCard>
        </VCol>
      </VRow>

      <!-- LLM Response -->
      <VCard elevation="2" class="mb-8" v-if="keywordData.llm_response">
        <VCardTitle class="pa-6 pb-4">
          <div class="d-flex align-center">
            <VAvatar color="deep-purple" variant="tonal" class="me-3">
              <VIcon icon="tabler-message-circle-2" />
            </VAvatar>
            <h5 class="text-h5">LLM Response</h5>
          </div>
        </VCardTitle>

        <VDivider />

        <VCardText class="pa-6">
          <div class="llm-response-container">
            <div class="response-header mb-4">
              <VChip :color="getLLMColor(keywordData.llm_type)" variant="elevated" size="small" class="me-2">
                <VIcon icon="tabler-brain" class="me-1" size="16" />
                {{ keywordData.llm_type?.toUpperCase() || "UNKNOWN" }}
              </VChip>
              <VChip :color="
                  keywordData.domain_found_in_response == 1 ? 'success' : 'error'
                " variant="flat" size="small">
                <VIcon :icon="
                    keywordData.domain_found_in_response == 1
                      ? 'tabler-check'
                      : 'tabler-x'
                  " class="me-1" size="28" />
              </VChip>
            </div>

            <div class="response-content pa-4 rounded">
              <div class="formatted-response" v-html="formatLLMResponse(keywordData.llm_response)"></div>
            </div>
          </div>
        </VCardText>
      </VCard>

      <!-- Highlights Section -->
      <VCard elevation="2" class="mb-8" v-if="keywordData.highlights">
        <VCardTitle class="pa-6 pb-4">
          <div class="d-flex align-center">
            <VAvatar color="orange" variant="tonal" class="me-3">
              <VIcon icon="tabler-highlight" />
            </VAvatar>
            <h5 class="text-h5">Key Highlights</h5>
          </div>
        </VCardTitle>

        <VDivider />

        <VCardText class="pa-6">
           <div class="response-content pa-4 rounded">
              <div class="formatted-response" v-html="formatLLMResponse(keywordData.highlights)"></div>
            </div>
        </VCardText>
      </VCard>

      <!-- Related Information -->
      <VCard elevation="2" class="mb-8">
        <VCardTitle class="pa-6 pb-4">
          <div class="d-flex align-center">
            <VAvatar color="teal" variant="tonal" class="me-3">
              <VIcon icon="tabler-link" />
            </VAvatar>
            <h5 class="text-h5">Related Information</h5>
          </div>
        </VCardTitle>

        <VDivider />

        <VCardText class="pa-6">
          <VRow>
            <VCol cols="12" md="6">
              <div class="info-item mb-6">
                <div class="d-flex align-center mb-2">
                  <VIcon icon="tabler-file-report" class="me-2 text-primary" size="20" />
                  <span class="text-body-2 font-weight-medium text-high-emphasis">Report ID</span>
                </div>
                <p class="text-body-1 mb-0 ml-7">
                  {{ keywordData.report_id || "Not specified" }}
                </p>
              </div>

              <div class="info-item mb-6">
                <div class="d-flex align-center mb-2">
                  <VIcon icon="tabler-key" class="me-2 text-success" size="20" />
                  <span class="text-body-2 font-weight-medium text-high-emphasis">Keyword</span>
                </div>
                <p class="text-body-1 mb-0 ml-7">
                  {{ keywordData.keyword.keyword || "Not specified" }}
                </p>
              </div>
            </VCol>

            <VCol cols="12" md="6">
              <div class="info-item mb-6">
                <div class="d-flex align-center mb-2">
                  <VIcon icon="tabler-world" class="me-2 text-info" size="20" />
                  <span class="text-body-2 font-weight-medium text-high-emphasis">Domain</span>
                </div>
                <p class="text-body-1 mb-0 ml-7">
                  {{ keywordData.domain.title || "Not specified" }}
                </p>
              </div>

              <div class="info-item mb-6">
                <div class="d-flex align-center mb-2">
                  <VIcon icon="tabler-brain" class="me-2 text-deep-purple" size="20" />
                  <span class="text-body-2 font-weight-medium text-high-emphasis">LLM Model</span>
                </div>
                <div class="ml-7">
                  <VChip :color="getLLMColor(keywordData.llm_type)" variant="elevated" size="small">
                    {{ keywordData.llm_type?.toUpperCase() || "UNKNOWN" }}
                  </VChip>
                </div>
              </div>
            </VCol>
          </VRow>
        </VCardText>
      </VCard>

      <!-- Timestamps -->
      <VCard elevation="2" class="mb-8">
        <VCardTitle class="pa-6 pb-4">
          <div class="d-flex align-center">
            <VAvatar color="indigo" variant="tonal" class="me-3">
              <VIcon icon="tabler-clock" />
            </VAvatar>
            <h5 class="text-h5">Timeline Information</h5>
          </div>
        </VCardTitle>

        <VDivider />

        <VCardText class="pa-6">
          <VRow>
            <VCol cols="12" md="6">
              <div class="info-item mb-6">
                <div class="d-flex align-center mb-2">
                  <VIcon icon="tabler-clock-play" class="me-2 text-success" size="20" />
                  <span class="text-body-2 font-weight-medium text-high-emphasis">Processed At</span>
                </div>
                <p class="text-body-1 mb-0 ml-7">
                  {{
                  formatDateTime(keywordData.processed_at) ||
                  "Not processed yet"
                  }}
                </p>
              </div>

              <div class="info-item mb-6">
                <div class="d-flex align-center mb-2">
                  <VIcon icon="tabler-calendar-plus" class="me-2 text-purple" size="20" />
                  <span class="text-body-2 font-weight-medium text-high-emphasis">Created At</span>
                </div>
                <p class="text-body-1 mb-0 ml-7">
                  {{ formatDateTime(keywordData.created_at) || "Not recorded" }}
                </p>
              </div>
            </VCol>

            <VCol cols="12" md="6">
              <div class="info-item mb-6">
                <div class="d-flex align-center mb-2">
                  <VIcon icon="tabler-calendar-event" class="me-2 text-orange" size="20" />
                  <span class="text-body-2 font-weight-medium text-high-emphasis">Updated At</span>
                </div>
                <p class="text-body-1 mb-0 ml-7">
                  {{ formatDateTime(keywordData.updated_at) || "Not recorded" }}
                </p>
              </div>

              <div class="info-item mb-6" v-if="keywordData.deleted_at">
                <div class="d-flex align-center mb-2">
                  <VIcon icon="tabler-calendar-x" class="me-2 text-error" size="20" />
                  <span class="text-body-2 font-weight-medium text-high-emphasis">Deleted At</span>
                </div>
                <p class="text-body-1 mb-0 ml-7">
                  {{ formatDateTime(keywordData.deleted_at) }}
                </p>
              </div>
            </VCol>
          </VRow>
        </VCardText>
      </VCard>
    </template>

    <!-- Enhanced Error State -->
    <VCard v-else elevation="2" class="mb-6">
      <VCardText class="text-center py-16">
        <VAvatar size="120" color="error" variant="tonal" class="mb-6 mx-auto">
          <VIcon icon="tabler-search-off" size="64" />
        </VAvatar>
        <h4 class="text-h4 mb-4">Keyword Data Not Found</h4>
        <p class="text-body-1 text-medium-emphasis mb-6 max-width-400 mx-auto">
          The keyword data you're looking for doesn't exist or may have been
          deleted. Please check the ID and try again.
        </p>
        <VBtn color="primary" size="large" :to="{ name: 'keyword  -list' }">
          <VIcon icon="tabler-arrow-autofit-left" size= "x-large" class="me-1"/>
Back
        </VBtn>
      </VCardText>
    </VCard>
  </div>
</template>

<script setup>
import { useKeywordShow } from "@/composables/keywordDataApi";
import {
  IconCheck,
  IconX,
  IconClock,
  IconAlertCircle,
} from "@tabler/icons-vue";

const route = useRoute();
const router = useRouter();

const keywordDataId = computed(() => route.params.id);

const {
  data: keywordDataResponse,
  execute: fetchKeywordData,
  pending: isLoading,
} = await useKeywordShow(keywordDataId.value);

const keywordData = computed(() => keywordDataResponse.value?.data ?? {});

const getStatusColor = (status) => {
  switch (status) {
    case 3:
      return "success"; // Approved
    case 2:
      return "error"; // Rejected
    case 1:
    default:
      return "warning"; // Pending
  }
};

const getStatusIcon = (status) => {
  switch (status) {
    case 3:
      return IconCheck; // Approved
    case 2:
      return IconX; // Rejected
    case 1:
    default:
      return IconClock; // Pending
  }
};


const parseHighlight = (highlight) => {
  const parts = highlight.split(" - ");
  return {
    title: parts[0]?.trim() || highlight,
    url: parts[1]?.trim() || ""
  };
};

const getStatusText = (status) => {
  switch (status) {
    case 3:
      return "Success";
    case 2:
      return "Faild";
    case 1:
    default:
      return "Pending";
  }
};

const formatLLMResponse = (response) => {
  if (!response) return "";

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

    .replace(
      /\[([^\]]+)\]\(([^)]+)\)/g,
      '<a href="$2" target="_blank" rel="noopener noreferrer">$1</a>'
    )

    .replace(/`([^`]+)`/g, "<code>$1</code>")

    .replace(/\n\n+/g, "</p><p>")
    .replace(/\n/g, "<br>");

  formattedResponse = formattedResponse.replace(
    /(<li>.*?<\/li>)(\s*<li>.*?<\/li>)*/gs,
    (match) => {
      const originalText = response.substring(
        response.indexOf(match.replace(/<[^>]*>/g, "").substring(0, 20))
      );
      const isNumberedList = /^\d+\./.test(originalText);

      const listTag = isNumberedList ? "ol" : "ul";
      return `<${listTag}>${match}</${listTag}>`;
    }
  );

  if (!formattedResponse.includes("<p>") && !formattedResponse.includes("<h")) {
    formattedResponse = "<p>" + formattedResponse + "</p>";
  }

  return formattedResponse;
};

const getLLMColor = (llmType) => {
  switch (llmType?.toLowerCase()) {
    case "gpt":
      return "secondary";
    case "gemini":
      return "info";
    case "cohere":
      return "warning";
    default:
      return "grey";
  }
};

// Format date and time
const formatDateTime = (dateString) => {
  if (!dateString) return null;
  return new Date(dateString).toLocaleString("en-US", {
    year: "numeric",
    month: "long",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
};

// Parse highlights
const getHighlights = (highlights) => {
  if (!highlights) return [];
  try {
    return Array.isArray(highlights) ? highlights : JSON.parse(highlights);
  } catch {
    return typeof highlights === "string" ? [highlights] : [];
  }
};
</script>

<style scoped>
.response-content {
  background: linear-gradient(
    135deg,
    rgba(var(--v-theme-surface-variant), 0.1) 0%,
    rgba(var(--v-theme-primary), 0.05) 100%
  );
  border-left: 4px solid rgb(var(--v-theme-primary));
  max-height: 600px;
  overflow-y: auto;
  border-radius: 8px;
}

.formatted-response {
  line-height: 1.7;
  color: rgba(var(--v-theme-on-surface), 0.87);
}

.formatted-response h1,
.formatted-response h2,
.formatted-response h3,
.formatted-response h4,
.formatted-response h5,
.formatted-response h6 {
  color: rgb(var(--v-theme-primary));
  font-weight: 600;
  margin: 1.5rem 0 1rem 0;
  line-height: 1.3;
}

.formatted-response h1 {
  font-size: 1.5rem;
}
.formatted-response h2 {
  font-size: 1.35rem;
}
.formatted-response h3 {
  font-size: 1.2rem;
}
.formatted-response h4 {
  font-size: 1.1rem;
}
.formatted-response h5 {
  font-size: 1rem;
}
.formatted-response h6 {
  font-size: 0.95rem;
}

.formatted-response p {
  margin: 1rem 0;
  text-align: justify;
}

.formatted-response ul,
.formatted-response ol {
  margin: 1rem 0;
  padding-left: 1.5rem;
}

.formatted-response li {
  margin: 0.5rem 0;
  line-height: 1.6;
}

.formatted-response ul li {
  position: relative;
}

.formatted-response ul li::marker {
  color: rgb(var(--v-theme-primary));
}

.formatted-response ol li::marker {
  color: rgb(var(--v-theme-primary));
  font-weight: 600;
}

.formatted-response strong {
  color: rgb(var(--v-theme-primary));
  font-weight: 600;
}

.formatted-response em {
  font-style: italic;
  color: rgba(var(--v-theme-on-surface), 0.75);
}

.formatted-response a {
  color: rgb(var(--v-theme-secondary));
  text-decoration: none;
  font-weight: 500;
  border-bottom: 1px solid transparent;
  transition: all 0.2s ease;
}

.formatted-response a:hover {
  color: rgb(var(--v-theme-primary));
  border-bottom-color: rgb(var(--v-theme-primary));
}

.formatted-response blockquote {
  margin: 1.5rem 0;
  padding: 1rem 1.5rem;
  background: rgba(var(--v-theme-primary), 0.05);
  border-left: 4px solid rgb(var(--v-theme-primary));
  border-radius: 0 4px 4px 0;
  font-style: italic;
}

.formatted-response code {
  background: rgba(var(--v-theme-surface-variant), 0.3);
  padding: 0.2rem 0.4rem;
  border-radius: 4px;
  font-family: "Courier New", Consolas, monospace;
  font-size: 0.9em;
  color: rgb(var(--v-theme-secondary));
}

.formatted-response pre {
  background: rgba(var(--v-theme-surface-variant), 0.2);
  padding: 1rem;
  border-radius: 8px;
  overflow-x: auto;
  margin: 1rem 0;
  border: 1px solid rgba(var(--v-theme-outline), 0.2);
}

.formatted-response pre code {
  background: none;
  padding: 0;
}

/* Responsive design */
@media (max-width: 768px) {
  .response-content {
    max-height: 400px;
    padding: 1rem !important;
  }

  .formatted-response {
    font-size: 0.9rem;
  }

  .formatted-response h1 {
    font-size: 1.3rem;
  }
  .formatted-response h2 {
    font-size: 1.2rem;
  }
  .formatted-response h3 {
    font-size: 1.1rem;
  }
}

/* Custom scrollbar */
.response-content::-webkit-scrollbar {
  width: 8px;
}

.response-content::-webkit-scrollbar-track {
  background: rgba(var(--v-theme-surface-variant), 0.1);
  border-radius: 4px;
}

.response-content::-webkit-scrollbar-thumb {
  background: rgba(var(--v-theme-primary), 0.3);
  border-radius: 4px;
}

.response-content::-webkit-scrollbar-thumb:hover {
  background: rgba(var(--v-theme-primary), 0.5);
}
.info-card {
  border: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
  transition: all 0.3s ease;
}

.info-card:hover {
  border-color: rgb(var(--v-theme-primary));
  box-shadow: 0 4px 12px rgba(var(--v-theme-primary), 0.15);
}

.v-card {
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;
}

.max-width-400 {
  max-width: 400px;
}

.info-item {
  min-height: 60px;
}

.llm-response-container {
  max-width: 100%;
}

.response-content {
  max-height: 400px;
  overflow-y: auto;
}

.highlights-container {
  min-height: 100px;
}

/* Custom animations */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.v-card {
  animation: fadeIn 0.5s ease-out;
}

/* Responsive text */
@media (max-width: 768px) {
  .response-content {
    max-height: 300px;
    font-size: 0.875rem;
  }
}
</style>
