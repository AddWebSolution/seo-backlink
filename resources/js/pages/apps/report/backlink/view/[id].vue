<template>
  <div>
    <!-- Modern Header with Gradient Background -->
    <VCard class="mb-8 overflow-hidden" elevation="2" color="grey lighten-4">
      <VCardText class="pa-8">
        <VRow align="center" justify="space-between">
          <!-- Left Column: Backlink Info -->
          <VCol cols="12" md="8">
            <div class="mb-4">
              <h2 class="text-h3 font-weight-bold mb-3">Backlink Details</h2>

              <div class="d-flex gap-3">
                <VChip color="primary" variant="elevated" size="large">
                  <VIcon icon="tabler-id" class="me-2" />
                  ID: {{ backlink.id }}
                </VChip>

                <VChip color="primary" variant="flat" size="large">
                  <VIcon icon="tabler-play" class="me-2" />
                  Report ID: {{ backlink.report_id }}
                </VChip>

                <VChip color="primary" variant="flat" size="large">
                  <VIcon :icon="getStatusIcon(backlink.status)" class="me-2" />
                  <span class="text-capitalize">{{ backlink.status }}</span>
                </VChip>
              </div>
            </div>
          </VCol>

          <!-- Right Column: Quick Actions -->
          <VCol cols="12" md="4" class="d-flex justify-end">
            <VBtn color="primary" variant="flat" :to="{
              name: 'report-view',
              params: { id: backlink.report_id },
            }">
              <VIcon icon="tabler-arrow-autofit-left" size= "large" />
              Back to Report Details
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
            <h6 class="text-h6 mt-6 mb-2">Loading backlink details...</h6>
            <p class="text-body-2 text-medium-emphasis mb-0">
              Please wait while we fetch the information
            </p>
          </div>
        </VCardText>
      </VCard>
    </template>

    <!-- Backlink Details -->
    <template v-else-if="backlink.id">
      <!-- Status Overview Cards -->
      <VRow class="mb-8">
        <VCol cols="12" md="3">
          <VCard elevation="2" class="h-100" :color="getStatusColor(backlink.status)" variant="tonal">
            <VCardText class="pa-6 text-center">
              <VAvatar size="56" :color="getStatusColor(backlink.status)" class="mb-3">
                <component :is="getStatusIcon(backlink.status)" size="28" />
              </VAvatar>
              <p class="text-body-2 text-medium-emphasis mb-1">Status</p>
              <h5 class="text-h5 font-weight-bold">
                {{ getStatusText(backlink.status) }}
              </h5>
            </VCardText>
          </VCard>
        </VCol>

        <VCol cols="12" md="3">
          <VCard elevation="2" class="h-100" :color="getTierColor(backlink.tier)" variant="tonal">
            <VCardText class="pa-6 text-center">
              <VAvatar size="56" :color="getTierColor(backlink.tier)" class="mb-3">
                <VIcon icon="tabler-star" size="28" />
              </VAvatar>
              <p class="text-body-2 text-medium-emphasis mb-1">Tier Level</p>
              <h5 class="text-h5 font-weight-bold">
                Tier {{ backlink.tier || 0 }}
              </h5>
            </VCardText>
          </VCard>
        </VCol>

        <VCol cols="12" md="3">
          <VCard elevation="2" class="h-100" :color="backlink.do_follow ? 'success' : 'warning'" variant="tonal">
            <VCardText class="pa-6 text-center">
              <VAvatar size="56" :color="backlink.do_follow ? 'success' : 'warning'" class="mb-3">
                <VIcon :icon="backlink.do_follow ? 'tabler-link' : 'tabler-link-off'" size="28" />
              </VAvatar>
              <p class="text-body-2 text-medium-emphasis mb-1">Link Type</p>
              <h5 class="text-h5 font-weight-bold">
                {{ backlink.do_follow ? "DoFollow" : "NoFollow" }}
              </h5>
            </VCardText>
          </VCard>
        </VCol>

        <VCol cols="12" md="3">
          <VCard elevation="2" class="h-100" :color="backlink.is_broken ? 'error' : 'success'" variant="tonal">
            <VCardText class="pa-6 text-center">
              <VAvatar size="56" :color="backlink.is_broken ? 'error' : 'success'" class="mb-3">
                <VIcon :icon="backlink.is_broken ? 'tabler-link-off' : 'tabler-link'" size="28" />
              </VAvatar>
              <p class="text-body-2 text-medium-emphasis mb-1">Link Health</p>
              <h5 class="text-h5 font-weight-bold">
                {{ backlink.is_broken ? "Broken" : "Active" }}
              </h5>
            </VCardText>
          </VCard>
        </VCol>
      </VRow>

      <!-- URL Information -->
      <VCard elevation="2" class="mb-8">
        <VCardTitle class="pa-6 pb-4">
          <div class="d-flex align-center">
            <VAvatar color="info" variant="tonal" class="me-3">
              <VIcon icon="tabler-world" />
            </VAvatar>
            <h5 class="text-h5">URL Information</h5>
          </div>
        </VCardTitle>

        <VDivider />

        <VCardText class="pa-6">
          <VRow>
            <VCol cols="12" md="6">

               <div class="info-item mb-6">
                <div class="d-flex align-center mb-2">
                  <VIcon icon="tabler-target" class="me-2 text-success" size="20" />
                  <span class="text-body-2 font-weight-medium text-high-emphasis">Client Domain URL</span>
                </div>
                <div v-if="backlink.target_url" class="ml-7">
                  <a :href="backlink.target_url" target="_blank"
                    class="text-success text-decoration-none d-flex align-center">
                    <span class="text-body-1 me-2" style="word-break: break-all">{{ backlink.domain_url }}</span>
                    <VIcon icon="tabler-external-link" size="16" />
                  </a>
                </div>
                <p v-else class="text-body-1 text-medium-emphasis mb-0 ml-7">
                  Not specified
                </p>
              </div>

              <div class="info-item mb-6">
                <div class="d-flex align-center mb-2">
                  <VIcon icon="tabler-target" class="me-2 text-success" size="20" />
                  <span class="text-body-2 font-weight-medium text-high-emphasis">Target URL</span>
                </div>
                <div v-if="backlink.target_url" class="ml-7">
                  <a :href="backlink.target_url" target="_blank"
                    class="text-success text-decoration-none d-flex align-center">
                    <span class="text-body-1 me-2" style="word-break: break-all">{{ backlink.target_url }}</span>
                    <VIcon icon="tabler-external-link" size="16" />
                  </a>
                </div>
                <p v-else class="text-body-1 text-medium-emphasis mb-0 ml-7">
                  Not specified
                </p>
              </div>

              <div class="info-item mb-6">
                <div class="d-flex align-center mb-2">
                  <VIcon icon="tabler-world-www" class="me-2 text-info" size="20" />
                  <span class="text-body-2 font-weight-medium text-high-emphasis">Source URL</span>
                </div>
                <div v-if="backlink.url" class="ml-7">
                  <a :href="backlink.url" target="_blank" class="text-info text-decoration-none d-flex align-center">
                    <span class="text-body-1 me-2" style="word-break: break-all">{{ backlink.url }}</span>
                    <VIcon icon="tabler-external-link" size="16" />
                  </a>
                </div>
                <p v-else class="text-body-1 text-medium-emphasis mb-0 ml-7">
                  Not specified
                </p>
              </div>

              <div class="info-item mb-6">
                <div class="d-flex align-center mb-2">
                  <VIcon icon="tabler-link" class="me-2 text-warning" size="20" />
                  <span class="text-body-2 font-weight-medium text-high-emphasis">From URL</span>
                </div>
                <div v-if="backlink.from_url" class="ml-7">
                  <a :href="backlink.from_url" target="_blank"
                    class="text-warning text-decoration-none d-flex align-center">
                    <span class="text-body-1 me-2" style="word-break: break-all">{{ backlink.from_url }}</span>
                    <VIcon icon="tabler-external-link" size="16" />
                  </a>
                </div>
                <p v-else class="text-body-1 text-medium-emphasis mb-0 ml-7">
                  Not specified
                </p>
              </div>
            </VCol>

            <VCol cols="12" md="6">

               <div class="info-item mb-6">
                <div class="d-flex align-center mb-2">
                  <VIcon icon="tabler-world" class="me-2 text-primary" size="20" />
                  <span class="text-body-2 font-weight-medium text-high-emphasis">Client Domain</span>
                </div>
                <p class="text-body-1 mb-0 ml-7">
                  {{ backlink.domain || "Not specified" }}
                </p>
              </div>

              <div class="info-item mb-6">
                <div class="d-flex align-center mb-2">
                  <VIcon icon="tabler-world" class="me-2 text-primary" size="20" />
                  <span class="text-body-2 font-weight-medium text-high-emphasis">Target Domain</span>
                </div>
                <p class="text-body-1 mb-0 ml-7">
                  {{ backlink.target_domain || "Not specified" }}
                </p>
              </div>

              <div class="info-item mb-6">
                <div class="d-flex align-center mb-2">
                  <VIcon icon="tabler-world-upload" class="me-2 text-secondary" size="20" />
                  <span class="text-body-2 font-weight-medium text-high-emphasis">Source Domain</span>
                </div>
                <p class="text-body-1 mb-0 ml-7">
                  {{ backlink.from_domain || "Not specified" }}
                </p>
              </div>

              <div class="info-item mb-6">
                <div class="d-flex align-center mb-2">
                  <VIcon icon="tabler-file-text" class="me-2 text-indigo" size="20" />
                  <span class="text-body-2 font-weight-medium text-high-emphasis">Page Title</span>
                </div>
                <p class="text-body-1 mb-0 ml-7" style="word-break: break-word">
                  {{ backlink.page_title || "Not specified" }}
                </p>
              </div>
            </VCol>
          </VRow>
        </VCardText>
      </VCard>

      <!-- SEO Metrics -->
      <VCard elevation="2" class="mb-8">
        <VCardTitle class="pa-6 pb-4">
          <div class="d-flex align-center">
            <VAvatar color="warning" variant="tonal" class="me-3">
              <VIcon icon="tabler-chart-line" />
            </VAvatar>
            <h5 class="text-h5">SEO Metrics & Rankings</h5>
          </div>
        </VCardTitle>

        <VDivider />

        <VCardText class="pa-6">
          <VRow>
            <VCol cols="12" md="3">
              <VCard variant="tonal" color="primary" class="pa-6 text-center h-100">
                <VAvatar size="64" color="primary" class="mb-4 mx-auto">
                  <VIcon icon="tabler-trophy" size="32" />
                </VAvatar>
                <p class="text-body-2 text-medium-emphasis mb-2">Domain Rank</p>
                <h3 class="text-h3 font-weight-bold text-primary">
                  {{ backlink.domain_rank || "0" }}
                </h3>
                <VProgressLinear :model-value="backlink.domain_rank || 0" max="100" color="primary" class="mt-3" />
              </VCard>
            </VCol>

            <VCol cols="12" md="3">
              <VCard variant="tonal" color="success" class="pa-6 text-center h-100">
                <VAvatar size="64" color="success" class="mb-4 mx-auto">
                  <VIcon icon="tabler-trending-up" size="32" />
                </VAvatar>
                <p class="text-body-2 text-medium-emphasis mb-2">Page Rank</p>
                <h3 class="text-h3 font-weight-bold text-success">
                  {{ backlink.rank || "0" }}
                </h3>
                <VProgressLinear :model-value="backlink.rank || 0" max="100" color="success" class="mt-3" />
              </VCard>
            </VCol>

            <VCol cols="12" md="3">
              <VCard variant="tonal" color="warning" class="pa-6 text-center h-100">
                <VAvatar size="64" color="warning" class="mb-4 mx-auto">
                  <VIcon icon="tabler-shield-check" size="32" />
                </VAvatar>
                <p class="text-body-2 text-medium-emphasis mb-2">Spam Score</p>
                <h3 class="text-h3 font-weight-bold text-warning">
                  {{ backlink.spam_score || "0" }}%
                </h3>
                <VProgressLinear :model-value="backlink.spam_score || 0" max="100" color="warning" class="mt-3" />
              </VCard>
            </VCol>

            <VCol cols="12" md="3">
              <VCard variant="tonal" color="info" class="pa-6 text-center h-100">
                <VAvatar size="64" color="info" class="mb-4 mx-auto">
                  <VIcon icon="tabler-star" size="32" />
                </VAvatar>
                <p class="text-body-2 text-medium-emphasis mb-2">
                  Quality Score
                </p>
                <h3 class="text-h3 font-weight-bold text-info">
                  {{ backlink.score || "0" }}
                </h3>
                <VProgressLinear :model-value="backlink.score || 0" max="100" color="info" class="mt-3" />
              </VCard>
            </VCol>
          </VRow>
        </VCardText>
      </VCard>

      <!-- Link Details -->
      <VCard elevation="2" class="mb-8">
        <VCardTitle class="pa-6 pb-4">
          <div class="d-flex align-center">
            <VAvatar color="purple" variant="tonal" class="me-3">
              <VIcon icon="tabler-anchor" />
            </VAvatar>
            <h5 class="text-h5">Link Details</h5>
          </div>
        </VCardTitle>

        <VDivider />

        <VCardText class="pa-6">
          <VRow>
            <VCol cols="12" md="6">
              <div class="info-card pa-4 rounded border mb-4">
                <div class="d-flex align-center mb-3">
                  <VIcon icon="tabler-anchor" class="me-2 text-purple" size="24" />
                  <span class="text-body-1 font-weight-bold">Anchor Text</span>
                </div>
                <p class="text-body-1 mb-0" style="word-break: break-word">
                  {{ backlink.anchor || "No anchor text specified" }}
                </p>
              </div>

              <div class="info-card pa-4 rounded border">
                <div class="d-flex align-center mb-3">
                  <VIcon icon="tabler-category" class="me-2 text-orange" size="24" />
                  <span class="text-body-1 font-weight-bold">Link Type</span>
                </div>
                <p class="text-body-1 mb-0">
                  {{ backlink.link_type || "Not specified" }}
                </p>
              </div>
            </VCol>

            <VCol cols="12" md="6">
              <div class="info-card pa-4 rounded border mb-4">
                <div class="d-flex align-center mb-3">
                  <VIcon icon="tabler-message-circle" class="me-2 text-teal" size="24" />
                  <span class="text-body-1 font-weight-bold">Reason/Notes</span>
                </div>
                <p class="text-body-1 mb-0" style="word-break: break-word">
                  {{ backlink.reason || "No reason specified" }}
                </p>
              </div>

              <div class="info-card pa-4 rounded border">
                <div class="d-flex align-center mb-3">
                  <VIcon icon="tabler-list-details" class="me-2 text-cyan" size="24" />
                  <span class="text-body-1 font-weight-bold">Key Highlights</span>
                </div>
                <div v-if="backlink.key_highlights">
                  <VChip v-for="highlight in getKeyHighlights(
                      backlink.key_highlights
                    )" :key="highlight" color="primary" variant="outlined" size="small" class="me-2 mb-2">
                    {{ highlight }}
                  </VChip>
                </div>
                <p v-else class="text-body-1 text-medium-emphasis mb-0">
                  No key highlights
                </p>
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
                  <VIcon icon="tabler-eye" class="me-2 text-green" size="20" />
                  <span class="text-body-2 font-weight-medium text-high-emphasis">First Seen</span>
                </div>
                <p class="text-body-1 mb-0 ml-7">
                  {{ formatDateTime(backlink.first_seen) || "Not recorded" }}
                </p>
              </div>

              <div class="info-item mb-6">
                <div class="d-flex align-center mb-2">
                  <VIcon icon="tabler-eye-check" class="me-2 text-blue" size="20" />
                  <span class="text-body-2 font-weight-medium text-high-emphasis">Last Seen</span>
                </div>
                <p class="text-body-1 mb-0 ml-7">
                  {{ formatDateTime(backlink.last_seen) || "Not recorded" }}
                </p>
              </div>
            </VCol>

            <VCol cols="12" md="6">
              <div class="info-item mb-6">
                <div class="d-flex align-center mb-2">
                  <VIcon icon="tabler-calendar-plus" class="me-2 text-purple" size="20" />
                  <span class="text-body-2 font-weight-medium text-high-emphasis">Created At</span>
                </div>
                <p class="text-body-1 mb-0 ml-7">
                  {{ formatDateTime(backlink.created_at) || "Not recorded" }}
                </p>
              </div>

              <div class="info-item mb-6">
                <div class="d-flex align-center mb-2">
                  <VIcon icon="tabler-calendar-event" class="me-2 text-orange" size="20" />
                  <span class="text-body-2 font-weight-medium text-high-emphasis">Updated At</span>
                </div>
                <p class="text-body-1 mb-0 ml-7">
                  {{ formatDateTime(backlink.updated_at) || "Not recorded" }}
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
          <VIcon icon="tabler-link-off" size="64" />
        </VAvatar>
        <h4 class="text-h4 mb-4">Backlink Not Found</h4>
        <p class="text-body-1 text-medium-emphasis mb-6 max-width-400 mx-auto">
          The backlink you're looking for doesn't exist or may have been
          deleted. Please check the ID and try again.
        </p>
        <VBtn color="primary" size="large" :to="{ name: 'apps-backlink-list' }">
          <VIcon icon="tabler-arrow-autofit-left" size= "x-large" class="me-1"/>
Back
        </VBtn>
      </VCardText>
    </VCard>
  </div>
</template>

<script setup>
import { useBacklinkShow } from "@/composables/backlinkApi";
import { IconCheck, IconX } from "@tabler/icons-vue";

const route = useRoute();
const router = useRouter();

const backlinkId = computed(() => route.params.id);
const isEditDrawerOpen = ref(false);
const showDeleteDialog = ref(false);

// API call to fetch backlink details
const {
  data: backlinkData,
  execute: fetchBacklink,
  pending: isLoading,
} = await useBacklinkShow(backlinkId.value);

const backlink = computed(() => backlinkData.value?.data ?? {});

// Handle edit success
const handleEditSuccess = () => {
  isEditDrawerOpen.value = false;
  fetchBacklink();
};

// Status helpers
const getStatusColor = (status) => {
  return status === "accepted" ? "success" : "error";
};

const getStatusIcon = (status) => {
  return status === "accepted" ? IconCheck : IconX;
};

const getStatusText = (status) => {
  return status === "accepted" ? "Accepted" : "Rejected";
};

const getTierColor = (tier) => {
  if (tier >= 3) return "success";
  if (tier >= 2) return "warning";
  return "error";
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

// Parse key highlights JSON
const getKeyHighlights = (highlights) => {
  if (!highlights) return [];
  try {
    return Array.isArray(highlights) ? highlights : JSON.parse(highlights);
  } catch {
    return typeof highlights === "string" ? [highlights] : [];
  }
};
</script>

<style scoped>
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

/* Link styling */
a {
  transition: all 0.2s ease;
}

a:hover {
  opacity: 0.8;
  transform: translateX(2px);
}
</style>
