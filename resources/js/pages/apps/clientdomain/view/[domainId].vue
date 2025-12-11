<script setup>
import { useDomainApi } from '@/composables/domainApi';
import DomainEditDrawer from '@/views/apps/domain/DomainEditDrawer.vue';
import { IconCheck, IconClock, IconX } from '@tabler/icons-vue';
import { computed, ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import RecommendedBacklinks from "@/components/dialogs/RecommendedBacklinks.vue";

const route = useRoute()
const router = useRouter()

const { 
  currentDomain, 
  loading, 
  error, 
  fetchDomain,
  recommendedBacklinks,
  updateDomain, 
  deleteDomain: deleteDomainApi,
  showAlert 
} = useDomainApi()

const domainId = computed(() => route.params.domainId);
const clientId = computed(() => route.params.clientId);

const showRecommendationsModal = ref(false)
const recommended = ref([])

const loadRecommendedBacklinks = async () => {
  recommended.value = await recommendedBacklinks(domainId.value)
  showRecommendationsModal.value = true
}

const isEditDrawerOpen = ref(false)
const showDeleteDialog = ref(false)

const domain = computed(() => currentDomain.value || {})

onMounted(async () => {
  if (domainId.value) {
    await fetchDomain(domainId.value)
  }
})

// Handle edit success
const handleEditSuccess = async (updatedDomain) => {
  try {
    await updateDomain(domainId.value, updatedDomain)
    isEditDrawerOpen.value = false
    await fetchDomain(domainId.value)
  } catch (error) {
    console.error('Update failed:', error)
  }
}

const handleDeleteDomain = async () => {
  try {
    await deleteDomainApi(domainId.value)
    showDeleteDialog.value = false
    router.push({ name: 'domain-list' })
  } catch (error) {
    // Error handling is done in the composable
    console.error('Delete failed:', error)
    showDeleteDialog.value = false
  }
}

const getStatusConfig = (status) => {
  return status == 1
      ? { color: 'success', icon: IconCheck, text: 'Available' }
      : { color: 'error', icon: IconX, text: 'Unavailable' }
}

const getApprovalStatusConfig = (status) => {
  if (status == 1) return { color: 'warning', icon: IconClock, text: 'Pending' }
  if (status == 2) return { color: 'error', icon: IconX, text: 'Rejected' }
  return { color: 'success', icon: IconCheck, text: 'Approved' }
}


const formatCurrency = (value) => {
  if (!value || value === '0') return '$0.00'
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(value)
}

const formatNumber = (value) => {
  if (!value) return '0'
  return new Intl.NumberFormat().format(value)
}

const isLoading = computed(() => loading.value)
const hasError = computed(() => error.value)
const hasDomain = computed(() => Object.keys(domain.value).length > 0)
</script>
<template>
  <div>
    <!-- Compact Header -->
    <VCard class="mb-4" elevation="2">
      <VCardText class="pa-4">
        <div class="d-flex align-center justify-space-between flex-wrap gap-3">
          <!-- Domain Title & Info -->
          <div class="d-flex align-center gap-3">
            <VAvatar color="primary" variant="tonal" size="40">
              <VIcon icon="tabler-world" size="20" />
            </VAvatar>
            <div>
              <h4 class="text-h5 mb-1">{{ domain.title || 'Domain Details' }}</h4>
              <div class="d-flex gap-2">
                <VChip size="x-small" :color="getStatusConfig(domain.status).color">
                  {{ getStatusConfig(domain.status).text }}
                </VChip>
                <VChip size="x-small" :color="getApprovalStatusConfig(domain.approval_status).color">
                  {{ getApprovalStatusConfig(domain.approval_status).text }}
                </VChip>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="d-flex gap-2 flex-wrap">
            <VBtn variant="flat" color="primary" @click="isEditDrawerOpen = true">
              <VIcon icon="tabler-edit" size="18" class="me-1" />
              Edit
            </VBtn>
            <VBtn variant="flat" color="info" @click="loadRecommendedBacklinks">
              <VIcon icon="tabler-link" size="18" class="me-1" />
              Backlinks
            </VBtn>
            <VBtn variant="flat" :to="{ name: 'clientdomain-list', params: { id: clientId } }">
              <VIcon icon="tabler-arrow-autofit-left" size="18" class="me-1" />
              Back
            </VBtn>
          </div>
        </div>
      </VCardText>
    </VCard>

    <!-- Loading State -->
    <template v-if="isLoading">
      <VCard class="mb-4" elevation="2">
        <VCardText class="text-center py-8">
          <VProgressCircular indeterminate color="primary" size="48" />
          <p class="text-body-2 mt-3 mb-0">Loading domain details...</p>
        </VCardText>
      </VCard>
    </template>

    <!-- Compact Domain Details -->
    <template v-else-if="domain.id">
      <VRow>
        <!-- Left Column -->
        <VCol cols="12" md="8">
          <!-- Basic Info Card -->
          <VCard elevation="2" class="mb-4">
            <VCardTitle class="pa-3 bg-primary-lighten">
              <div class="d-flex align-center">
                <VIcon icon="tabler-info-circle" size="20" class="me-2" />
                <span class="text-subtitle-1">Basic Information</span>
              </div>
            </VCardTitle>
            <VCardText class="pa-4">
              <VRow dense>
                <VCol cols="6" sm="4">
                  <div class="info-compact mb-3">
                    <p class="text-caption text-medium-emphasis mb-1">Client</p>
                    <p class="text-body-1 font-weight-medium mb-0">{{ domain.client_name || '-' }}</p>
                  </div>
                </VCol>
                <VCol cols="6" sm="4">
                  <div class="info-compact mb-3">
                    <p class="text-caption text-medium-emphasis mb-1">Turnaround</p>
                    <p class="text-body-1 font-weight-medium mb-0">{{ domain.turnaround_time || '-' }}</p>
                  </div>
                </VCol>
              </VRow>
            </VCardText>
          </VCard>

          <!-- Platform & Categories Card -->
          <VCard elevation="2" class="mb-4">
            <VCardTitle class="pa-3 bg-secondary-lighten">
              <div class="d-flex align-center">
                <VIcon icon="tabler-hierarchy" size="20" class="me-2" />
                <span class="text-subtitle-1">Platform & Categories</span>
              </div>
            </VCardTitle>
            <VCardText class="pa-4">
              <VRow dense>
                <VCol cols="6" sm="4">
                  <div class="info-compact mb-3">
                    <p class="text-caption text-medium-emphasis mb-1">Platform</p>
                    <p class="text-body-1 font-weight-medium mb-0">{{ domain.platform_type || '-' }}</p>
                  </div>
                </VCol>
                <VCol cols="6" sm="2">
                  <div class="info-compact mb-3">
                    <p class="text-caption text-medium-emphasis mb-1">Country</p>
                    <p class="text-body-1 font-weight-medium mb-0">{{ domain.country || '-' }}</p>
                  </div>
                </VCol>
                <VCol cols="6" sm="6">
                  <div class="info-compact">
                    <p class="text-caption text-medium-emphasis mb-1">Categories</p>
                    <p class="text-body-1 font-weight-medium mb-0">
                      {{ domain.categories ? domain.categories.join(', ') : '-' }}
                    </p>
                  </div>
                </VCol>
              </VRow>
            </VCardText>
          </VCard>

          <!-- URLs Card -->
          <VCard elevation="2" class="mb-4">
            <VCardTitle class="pa-3 bg-info-lighten">
              <div class="d-flex align-center">
                <VIcon icon="tabler-link" size="20" class="me-2" />
                <span class="text-subtitle-1">URLs & Links</span>
              </div>
            </VCardTitle>
            <VCardText class="pa-4">
              <VRow dense>
                <VCol cols="12" md="6">
                  <div class="info-compact">
                    <p class="text-caption text-medium-emphasis mb-1">Source URL</p>
                    <a v-if="domain.source_url" :href="domain.source_url" target="_blank"
                       class="text-primary text-body-1 text-decoration-none d-inline-flex align-center">
                      {{ domain.source_url }}
                      <VIcon icon="tabler-external-link" size="14" class="ml-1" />
                    </a>
                    <p v-else class="text-body-1 mb-0">-</p>
                  </div>
                </VCol>
                <VCol cols="12" md="6">
                  <div class="info-compact">
                    <p class="text-caption text-medium-emphasis mb-1">Target URL</p>
                    <a v-if="domain.target_url" :href="domain.target_url" target="_blank"
                       class="text-success text-body-1 text-decoration-none d-inline-flex align-center">
                      {{ domain.target_url }}
                      <VIcon icon="tabler-external-link" size="14" class="ml-1" />
                    </a>
                    <p v-else class="text-body-1 mb-0">-</p>
                  </div>
                </VCol>
              </VRow>
            </VCardText>
          </VCard>

          <!-- Additional Info Card -->
          <VCard elevation="2" class="mb-4">
            <VCardTitle class="pa-3 bg-error-lighten">
              <div class="d-flex align-center">
                <VIcon icon="tabler-notes" size="20" class="me-2" />
                <span class="text-subtitle-1">Additional Information</span>
              </div>
            </VCardTitle>
            <VCardText class="pa-4">
              <VRow dense>
                <VCol cols="12" md="4">
                  <div class="info-compact">
                    <p class="text-caption text-medium-emphasis mb-1">Anchor Text</p>
                    <p class="text-body-1 mb-0">{{ domain.anchor_text || '-' }}</p>
                  </div>
                </VCol>
                <VCol cols="12" md="8">
                  <div class="info-compact">
                    <p class="text-caption text-medium-emphasis mb-1">Special Requirements</p>
                    <p class="text-body-1 mb-0">{{ domain.special_requirements || '-' }}</p>
                  </div>
                </VCol>
              </VRow>
            </VCardText>
          </VCard>
        </VCol>

        <!-- Right Column -->
        <VCol cols="12" md="4">
          <!-- SEO Metrics Card -->
          <VCard elevation="2" class="mb-4">
            <VCardTitle class="pa-3 bg-warning-lighten">
              <div class="d-flex align-center">
                <VIcon icon="tabler-chart-line" size="20" class="me-2" />
                <span class="text-subtitle-1">SEO Metrics</span>
              </div>
            </VCardTitle>
            <VCardText class="pa-4">
              <div class="metric-item mb-3 pb-3 border-bottom">
                <div class="d-flex align-center justify-space-between">
                  <p class="text-caption text-medium-emphasis mb-0">Domain Authority</p>
                  <VChip size="small" color="primary" variant="flat">
                    {{ domain.domain_authority || '0' }}
                  </VChip>
                </div>
              </div>

              <div class="metric-item mb-3 pb-3 border-bottom">
                <div class="d-flex align-center justify-space-between">
                  <p class="text-caption text-medium-emphasis mb-0">Domain Rating</p>
                  <VChip size="small" color="success" variant="flat">
                    {{ domain.domain_rating || '0' }}
                  </VChip>
                </div>
              </div>

              <div class="metric-item">
                <div class="d-flex align-center justify-space-between">
                  <p class="text-caption text-medium-emphasis mb-0">Organic Traffic</p>
                  <VChip size="small" color="info" variant="flat">
                    {{ formatNumber(domain.organic_traffic) }}
                  </VChip>
                </div>
              </div>
            </VCardText>
          </VCard>

          <!-- Pricing Card -->
          <VCard elevation="2" class="mb-4">
            <VCardTitle class="pa-3 bg-error-lighten">
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center">
                  <VIcon icon="tabler-currency-dollar" size="20" class="me-2" />
                  <span class="text-subtitle-1">Pricing</span>
                </div>
                <VChip size="small" color="error" variant="elevated">
                  {{ formatCurrency(domain.total_price) }}
                </VChip>
              </div>
            </VCardTitle>
            <VCardText class="pa-4">
              <div class="d-flex align-center justify-space-between mb-2 pb-2 border-bottom">
                <span class="text-caption text-medium-emphasis">Price NE</span>
                <span class="text-body-2 font-weight-bold text-primary">
                  {{ formatCurrency(domain.price_ne) }}
                </span>
              </div>
              <div class="d-flex align-center justify-space-between mb-2 pb-2 border-bottom">
                <span class="text-caption text-medium-emphasis">Price GP</span>
                <span class="text-body-2 font-weight-bold text-success">
                  {{ formatCurrency(domain.price_gp) }}
                </span>
              </div>
              <div class="d-flex align-center justify-space-between">
                <span class="text-caption text-medium-emphasis">Base Price</span>
                <span class="text-body-2 font-weight-bold text-info">
                  {{ formatCurrency(domain.price) }}
                </span>
              </div>
            </VCardText>
          </VCard>
        </VCol>
      </VRow>
    </template>

    <!-- Error State -->
    <VCard v-else elevation="2">
      <VCardText class="text-center py-8">
        <VAvatar size="80" color="error" variant="tonal" class="mb-4 mx-auto">
          <VIcon icon="tabler-database-off" size="40" />
        </VAvatar>
        <h5 class="text-h6 mb-2">Domain Not Found</h5>
        <p class="text-body-2 text-medium-emphasis mb-4">
          The domain doesn't exist or may have been deleted.
        </p>
        <VBtn color="primary" size="small" :to="{ name: 'domain-list' }">
          <VIcon icon="tabler-arrow-left" size="18" class="me-1" />
          Back to List
        </VBtn>
      </VCardText>
    </VCard>

    <!-- Delete Dialog -->
    <VDialog v-model="showDeleteDialog" max-width="400">
      <VCard>
        <VCardTitle class="pa-4 bg-error-lighten">
          <div class="d-flex align-center">
            <VIcon icon="tabler-trash" size="20" class="me-2" />
            <span class="text-subtitle-1">Delete Domain</span>
          </div>
        </VCardTitle>
        <VCardText class="pa-4">
          <p class="text-body-2 mb-3">
            Are you sure you want to delete <strong>{{ domain.title || `ID ${domainId}` }}</strong>?
          </p>
          <VAlert color="warning" variant="tonal" density="compact">
            <span class="text-caption">This action cannot be undone.</span>
          </VAlert>
        </VCardText>
        <VCardActions class="pa-4 pt-0">
          <VSpacer />
          <VBtn size="small" variant="outlined" @click="showDeleteDialog = false">Cancel</VBtn>
          <VBtn size="small" color="error" @click="handleDeleteDomain">Delete</VBtn>
        </VCardActions>
      </VCard>
    </VDialog>

    <!-- Edit Drawer -->
    <DomainEditDrawer
        v-model:is-drawer-open="isEditDrawerOpen"
        :domain="currentDomain"
        @success="handleEditSuccess"
    />

    <!-- Recommended Backlinks Modal -->
    <RecommendedBacklinks
        v-model="showRecommendationsModal"
        max-width="1200"
        :items="recommended"
    />
  </div>
</template>

<style scoped>
.info-compact p {
  line-height: 1.4;
}

.metric-item {
  transition: all 0.2s ease;
}

.border-bottom {
  border-bottom: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
}

/* Light color backgrounds */
.bg-primary-lighten {
  background-color: rgba(var(--v-theme-primary), 0.24) !important;
}

.bg-info-lighten {
  background-color: rgba(var(--v-theme-info), 0.24) !important;
}

.bg-secondary-lighten {
  background-color: rgba(var(--v-theme-secondary), 0.24) !important;
}

.bg-warning-lighten {
  background-color: rgba(var(--v-theme-warning), 0.24) !important;
}

.bg-error-lighten {
  background-color: rgba(var(--v-theme-error), 0.24) !important;
}
</style>