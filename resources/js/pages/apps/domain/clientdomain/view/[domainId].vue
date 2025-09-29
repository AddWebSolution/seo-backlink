<script setup>
import { useDomainApi } from '@/composables/domainApi';
import DomainEditDrawer from '@/views/apps/domain/DomainEditDrawer.vue';
import { IconCheck, IconClock, IconX } from '@tabler/icons-vue';
import { computed, ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const { 
  currentDomain, 
  loading, 
  error, 
  fetchDomain,
  updateDomain, 
  deleteDomain: deleteDomainApi,
  showAlert 
} = useDomainApi()

const domainId = computed(() => route.params.domainId);
const clientId = computed(() => route.params.clientId);

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
    router.push({ name: 'apps-domain-list' })
  } catch (error) {
    // Error handling is done in the composable
    console.error('Delete failed:', error)
    showDeleteDialog.value = false
  }
}

const getStatusConfig = (status) => {
  return status == 1
    ? { color: 'green', icon: IconCheck, text: 'Available' }
    : { color: 'red', icon: IconX, text: 'Unavailable' }
}

const getApprovalStatusConfig = (status) => {
  if (status == 1) return { color: 'orange', icon: IconClock, text: 'Pending' }
  if (status == 2) return { color: 'red', icon: IconX, text: 'Rejected' }
  return { color: 'green', icon: IconCheck, text: 'Approved' }
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
    <!-- Modern Header with Gradient Background -->
    <VCard class="mb-8 overflow-hidden" elevation="2" color="grey lighten-4">
      <VCardText class="pa-8">
        <VRow align="start" justify="space-between">
          <!-- Left Column: Domain Info -->
          <VCol cols="12" md="8">
            <VBtn color="primary" variant="flat" :to="{ name: 'apps-domain-clientdomain-list',params: { id: clientId } }">
              <VIcon icon="tabler-arrow-left" class="me-2" />
              Back to Client Domains
            </VBtn>

            <div class="mb-8 pt-6">
              <h2 class="text-h3 font-weight-bold mb-2">
                {{ domain.title || 'Domain Details' }}
              </h2>

              <div class="d-flex align-center gap-3">
                <VChip color="primary" variant="outlined" class="text-primary" small>
                  ID: {{ domainId }}
                </VChip>

                <VChip color="secondary" variant="outlined" class="text-secondary" small>
                  {{ domain.country || 'No Country' }}
                </VChip>
              </div>
            </div>
          </VCol>

          <!-- Right Column: Actions -->
          <VCol cols="12" md="4" class="d-flex justify-end gap-3">
            <VBtn variant="flat" @click="isEditDrawerOpen = true">
              <VIcon icon="tabler-edit" class="me-2" />
              Edit Domain
            </VBtn>

            <VBtn color="error" variant="elevated" @click="showDeleteDialog = true">
              <VIcon icon="tabler-trash" class="me-2" />
              Delete
            </VBtn>
          </VCol>
        </VRow>
      </VCardText>
    </VCard>



    <!-- Loading State with Skeleton -->
    <template v-if="isLoading">
      <VCard class="mb-6" elevation="2">
        <VCardText class="text-center py-16">
          <div class="d-flex flex-column align-center">
            <VProgressCircular indeterminate color="primary" size="64" width="6" />
            <h6 class="text-h6 mt-6 mb-2">Loading domain details...</h6>
            <p class="text-body-2 text-medium-emphasis mb-0">Please wait while we fetch the information</p>
          </div>
        </VCardText>
      </VCard>
    </template>

    <!-- Domain Details with Enhanced Cards -->
    <template v-else-if="domain.id">
      <!-- Status Overview Cards -->
      <VRow class="mb-8">
        <VCol cols="12" md="6">
          <VCard elevation="2" class="h-100" color="success" variant="tonal">
            <VCardText class="pa-6">
              <div class="d-flex align-center">
                <VAvatar size="56" :color="getStatusConfig(domain.status).color" variant="tonal" class="me-4">
                  <component :is="getStatusConfig(domain.status).icon" size="28" />
                </VAvatar>

                <div>
                  <p class="text-body-2 text-medium-emphasis mb-1">Domain Status</p>
                  <h5 class="text-h5 font-weight-bold">
                    {{ getStatusConfig(domain.status).text }}
                  </h5>
                </div>
              </div>
            </VCardText>
          </VCard>
        </VCol>

        <VCol cols="12" md="6">
          <VCard elevation="2" class="h-100" color="warning" variant="tonal">
            <VCardText class="pa-6">
              <div class="d-flex align-center">
                <VAvatar size="56" :color="getApprovalStatusConfig(domain.approval_status).color" variant="tonal"
                  class="me-4">
                  <component :is="getApprovalStatusConfig(domain.approval_status).icon" size="28" />
                </VAvatar>
                <div>
                  <p class="text-body-2 text-medium-emphasis mb-1">Approval Status</p>
                  <h5 class="text-h5 font-weight-bold">
                    {{ getApprovalStatusConfig(domain.approval_status).text }}
                  </h5>
                </div>
              </div>
            </VCardText>
          </VCard>
        </VCol>
      </VRow>

      <!-- Basic Information with Modern Layout -->
      <VCard elevation="2" class="mb-8">
        <VCardTitle class="pa-6 pb-4">
          <div class="d-flex align-center">
            <VAvatar color="primary" variant="tonal" class="me-3">
              <VIcon icon="tabler-info-circle" />
            </VAvatar>
            <h5 class="text-h5">Basic Information</h5>
          </div>
        </VCardTitle>

        <VDivider />

        <VCardText class="pa-6">
          <VRow>
            <VCol cols="12" md="6">
              <div class="info-item mb-6">
                <div class="d-flex align-center mb-2">
                  <VIcon icon="tabler-tag" class="me-2 text-primary" size="20" />
                  <span class="text-body-2 font-weight-medium text-high-emphasis">Client ID</span>
                </div>
                <p class="text-h6 mb-0 ml-7">{{ domain.client_id || 'Not specified' }}</p>
              </div>

              <div class="info-item mb-6">
                <div class="d-flex align-center mb-2">
                  <VIcon icon="tabler-map-pin" class="me-2 text-primary" size="20" />
                  <span class="text-body-2 font-weight-medium text-high-emphasis">Country</span>
                </div>
                <p class="text-h6 mb-0 ml-7">{{ domain.country || 'Not specified' }}</p>
              </div>
            </VCol>

            <VCol cols="12" md="6">
              <div class="info-item mb-6">
                <div class="d-flex align-center mb-2">
                  <VIcon icon="tabler-clock" class="me-2 text-primary" size="20" />
                  <span class="text-body-2 font-weight-medium text-high-emphasis">Turnaround Time</span>
                </div>
                <p class="text-h6 mb-0 ml-7">{{ domain.turnaround_time || 'Not specified' }}</p>
              </div>
            </VCol>
          </VRow>
        </VCardText>
      </VCard>

      <!-- URLs & Links with Enhanced Design -->
      <VCard elevation="2" class="mb-8">
        <VCardTitle class="pa-6 pb-4">
          <div class="d-flex align-center">
            <VAvatar color="info" variant="tonal" class="me-3">
              <VIcon icon="tabler-link" />
            </VAvatar>
            <h5 class="text-h5">URLs & Links</h5>
          </div>
        </VCardTitle>

        <VDivider />

        <VCardText class="pa-6">
          <VRow>
            <VCol cols="12" md="6">
              <VCard variant="outlined" class="pa-4" color="primary" elevation="0">
                <div class="d-flex align-center mb-3">
                  <VIcon icon="tabler-world" class="me-2 text-primary" size="24" />
                  <span class="text-body-1 font-weight-bold">Source URL</span>
                </div>
                <div v-if="domain.source_url">
                  <a :href="domain.source_url" target="_blank"
                    class="text-primary text-decoration-none d-flex align-center">
                    <span class="text-body-2 me-2">{{ domain.source_url }}</span>
                    <VIcon icon="tabler-external-link" size="16" />
                  </a>
                </div>
                <p v-else class="text-body-2 text-medium-emphasis mb-0">Not specified</p>
              </VCard>
            </VCol>

            <VCol cols="12" md="6">
              <VCard variant="outlined" class="pa-4" color="success" elevation="0">
                <div class="d-flex align-center mb-3">
                  <VIcon icon="tabler-target" class="me-2 text-success" size="24" />
                  <span class="text-body-1 font-weight-bold">Target URL</span>
                </div>
                <div v-if="domain.target_url">
                  <a :href="domain.target_url" target="_blank"
                    class="text-success text-decoration-none d-flex align-center">
                    <span class="text-body-2 me-2">{{ domain.target_url }}</span>
                    <VIcon icon="tabler-external-link" size="16" />
                  </a>
                </div>
                <p v-else class="text-body-2 text-medium-emphasis mb-0">Not specified</p>
              </VCard>
            </VCol>
          </VRow>
        </VCardText>
      </VCard>

      <!-- SEO Metrics with Visual Indicators -->
      <VCard elevation="2" class="mb-8">
        <VCardTitle class="pa-6 pb-4">
          <div class="d-flex align-center">
            <VAvatar color="warning" variant="tonal" class="me-3">
              <VIcon icon="tabler-chart-line" />
            </VAvatar>
            <h5 class="text-h5">SEO Metrics</h5>
          </div>
        </VCardTitle>

        <VDivider />

        <VCardText class="pa-6">
          <VRow>
            <VCol cols="12" md="4">
              <VCard variant="tonal" color="primary" class="pa-6 text-center h-100">
                <VAvatar size="64" color="primary" class="mb-4 mx-auto">
                  <VIcon icon="tabler-award" size="32" />
                </VAvatar>
                <p class="text-body-2 text-medium-emphasis mb-2">Domain Authority</p>
                <h3 class="text-h3 font-weight-bold text-primary">
                  {{ domain.domain_authority || '0' }}
                </h3>
                <VProgressLinear :model-value="domain.domain_authority || 0" max="100" color="primary" class="mt-3" />
              </VCard>
            </VCol>

            <VCol cols="12" md="4">
              <VCard variant="tonal" color="success" class="pa-6 text-center h-100">
                <VAvatar size="64" color="success" class="mb-4 mx-auto">
                  <VIcon icon="tabler-trending-up" size="32" />
                </VAvatar>
                <p class="text-body-2 text-medium-emphasis mb-2">Domain Rating</p>
                <h3 class="text-h3 font-weight-bold text-success">
                  {{ domain.domain_rating || '0' }}
                </h3>
                <VProgressLinear :model-value="domain.domain_rating || 0" max="100" color="success" class="mt-3" />
              </VCard>
            </VCol>

            <VCol cols="12" md="4">
              <VCard variant="tonal" color="info" class="pa-6 text-center h-100">
                <VAvatar size="64" color="info" class="mb-4 mx-auto">
                  <VIcon icon="tabler-users" size="32" />
                </VAvatar>
                <p class="text-body-2 text-medium-emphasis mb-2">Organic Traffic</p>
                <h3 class="text-h3 font-weight-bold text-info">
                  {{ formatNumber(domain.organic_traffic) }}
                </h3>
                <p class="text-body-2 text-medium-emphasis mb-0 mt-2">Monthly Visitors</p>
              </VCard>
            </VCol>
          </VRow>
        </VCardText>
      </VCard>

      <!-- Pricing with Modern Cards -->
      <VCard elevation="2" class="mb-8">
        <VCardTitle class="pa-6 pb-4">
          <div class="d-flex align-center justify-space-between">
            <div class="d-flex align-center">
              <VAvatar color="error" variant="tonal" class="me-3">
                <VIcon icon="tabler-currency-dollar" />
              </VAvatar>
              <h5 class="text-h5">Pricing Information</h5>
            </div>
            <VChip color="error" variant="elevated" size="large">
              Total: {{ formatCurrency(domain.total_price) }}
            </VChip>
          </div>
        </VCardTitle>

        <VDivider />

        <VCardText class="pa-6">
          <VRow>
            <VCol cols="12" md="4">
              <VCard variant="outlined" class="pa-4 text-center" color="primary">
                <VIcon icon="tabler-cash" size="32" color="primary" class="mb-3" />
                <p class="text-body-2 text-medium-emphasis mb-1">Price NE</p>
                <h4 class="text-h4 font-weight-bold text-primary">
                  {{ formatCurrency(domain.price_ne) }}
                </h4>
              </VCard>
            </VCol>

            <VCol cols="12" md="4">
              <VCard variant="outlined" class="pa-4 text-center" color="success">
                <VIcon icon="tabler-credit-card" size="32" color="success" class="mb-3" />
                <p class="text-body-2 text-medium-emphasis mb-1">Price GP</p>
                <h4 class="text-h4 font-weight-bold text-success">
                  {{ formatCurrency(domain.price_gp) }}
                </h4>
              </VCard>
            </VCol>

            <VCol cols="12" md="4">
              <VCard variant="outlined" class="pa-4 text-center" color="info">
                <VIcon icon="tabler-coins" size="32" color="info" class="mb-3" />
                <p class="text-body-2 text-medium-emphasis mb-1">Base Price</p>
                <h4 class="text-h4 font-weight-bold text-info">
                  {{ formatCurrency(domain.price) }}
                </h4>
              </VCard>
            </VCol>
          </VRow>
        </VCardText>
      </VCard>

      <!-- Additional Information -->
      <VCard elevation="2" class="mb-8">
        <VCardTitle class="pa-6 pb-4">
          <div class="d-flex align-center">
            <VAvatar color="secondary" variant="tonal" class="me-3">
              <VIcon icon="tabler-notes" />
            </VAvatar>
            <h5 class="text-h5">Additional Information</h5>
          </div>
        </VCardTitle>

        <VDivider />

        <VCardText class="pa-6">
          <VRow>
            <VCol cols="12" md="6">
              <div class="info-card pa-4 rounded border">
                <div class="d-flex align-center mb-3">
                  <VIcon icon="tabler-anchor" class="me-2 text-secondary" size="24" />
                  <span class="text-body-1 font-weight-bold">Anchor Text</span>
                </div>
                <p class="text-body-1 mb-0" style="word-break: break-word;">
                  {{ domain.anchor_text || 'No anchor text specified' }}
                </p>
              </div>
            </VCol>

            <VCol cols="12" md="6">
              <div class="info-card pa-4 rounded border">
                <div class="d-flex align-center mb-3">
                  <VIcon icon="tabler-list-details" class="me-2 text-secondary" size="24" />
                  <span class="text-body-1 font-weight-bold">Special Requirements</span>
                </div>
                <p class="text-body-1 mb-0" style="word-break: break-word;">
                  {{ domain.special_requirements || 'No special requirements' }}
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
          <VIcon icon="tabler-database-off" size="64" />
        </VAvatar>
        <h4 class="text-h4 mb-4">Domain Not Found</h4>
        <p class="text-body-1 text-medium-emphasis mb-6 max-width-400 mx-auto">
          The domain you're looking for doesn't exist or may have been deleted.
          Please check the ID and try again.
        </p>
        <VBtn color="primary" size="large" :to="{ name: 'apps-domain-list' }">
          <VIcon icon="tabler-arrow-left" class="me-2" />
          Back to Domains
        </VBtn>
      </VCardText>
    </VCard>

    <!-- Delete Confirmation Dialog -->
    <VDialog v-model="showDeleteDialog" max-width="500">
      <VCard>
        <VCardTitle class="pa-6">
          <div class="d-flex align-center">
            <VAvatar color="error" variant="tonal" class="me-3">
              <VIcon icon="tabler-trash" />
            </VAvatar>
            <span class="text-h5">Delete Domain</span>
          </div>
        </VCardTitle>

        <VCardText class="pa-6 pt-0">
          <p class="text-body-1 mb-4">
            Are you sure you want to delete this domain? This action cannot be undone.
          </p>
          <VAlert color="warning" variant="tonal" class="mb-0">
            <div class="d-flex align-center">
              <VIcon icon="tabler-alert-triangle" class="me-2" />
              <span class="text-body-2">
                Domain: <strong>{{ domain.title || `ID ${domainId}` }}</strong>
              </span>
            </div>
          </VAlert>
        </VCardText>

        <VCardActions class="pa-6 pt-0">
          <VSpacer />
          <VBtn variant="outlined" @click="showDeleteDialog = false">
            Cancel
          </VBtn>
          <VBtn color="error" @click="deleteDomain">
            Delete Domain
          </VBtn>
        </VCardActions>
      </VCard>
    </VDialog>

    <!-- Edit Drawer -->
    <DomainEditDrawer v-model:is-drawer-open="isEditDrawerOpen" :domain="domain" @success="handleEditSuccess" />
  </div>
</template>

<style scoped>
.info-card {
  border: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
  transition: all 0.3s ease;
}

.info-card:hover {
  border-color: rgb(var(--v-theme-primary));
  box-shadow: 0 4px 12px rgba(var(--v-theme-primary), 0.15);
}

.max-width-400 {
  max-width: 400px;
}

/* Custom gradient animation */
@keyframes gradient-shift {
  0% {
    background-position: 0% 50%;
  }

  50% {
    background-position: 100% 50%;
  }

  100% {
    background-position: 0% 50%;
  }
}
</style>
