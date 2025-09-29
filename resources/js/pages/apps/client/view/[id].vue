<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useClientApi } from '@/composables/clientApi'

const route = useRoute()
const router = useRouter()
const { currentClient, fetchClient, showAlert } = useClientApi()

onMounted(async () => {
  if (route.params.id) {
    try {
      const res = await fetchClient(route.params.id)
    } catch (err) {
      console.error(err)
      showAlert('Failed to load client data', 'error')
      router.push({ name: 'apps-client-list' })
    }
  }
})
</script>

<template>
  <div v-if="currentClient">
    <!-- Header Card -->
    <VCard class="mb-6 header-card" elevation="2">
      <VCardText class="pa-8">
        <div class="d-flex align-center flex-wrap gap-4">
          <VAvatar
            :color="currentClient.status === 1 ? 'success' : 'error'"
            size="100"
            class="text-h2 font-weight-bold avatar-shadow"
          >
            {{ currentClient.name?.charAt(0)?.toUpperCase() || 'C' }}
          </VAvatar>
          
          <div class="flex-grow-1">
            <div class="d-flex align-center gap-3 mb-2">
              <h1 class="text-h3 font-weight-bold">{{ currentClient.name }}</h1>
              <VChip
                :color="currentClient.status === 1 ? 'success' : 'error'"
                size="default"
                variant="flat"
                class="font-weight-medium"
              >
                <VIcon icon="tabler-circle-filled" size="8" class="mr-2" />
                {{ currentClient.status === 1 ? 'Active' : 'Inactive' }}
              </VChip>
            </div>
            <p class="text-h6 text-medium-emphasis mb-0">{{ currentClient.company_name }}</p>
          </div>

          <VBtn
            variant="flat"
            size="large"
            prepend-icon="tabler-arrow-left"
            @click="router.push({ name: 'apps-client-list' })"
          >
            Back to List
          </VBtn>
        </div>
      </VCardText>
    </VCard>

    <!-- Contact Information Card -->
    <VCard class="mb-6 info-card" elevation="2">
      <VCardText class="pa-8">
        <div class="d-flex align-center mb-6">
          <div class="icon-wrapper mr-3">
            <VIcon icon="tabler-user-circle" size="28" color="primary" />
          </div>
          <h2 class="text-h5 font-weight-bold">Contact Information</h2>
        </div>
        
        <VRow class="gy-6">
          <VCol cols="12" md="6">
            <div class="info-box">
              <div class="d-flex align-center mb-3">
                <div class="info-icon-bg success mr-3">
                  <VIcon icon="tabler-mail" size="20" color="white" />
                </div>
                <div>
                  <div class="text-caption text-medium-emphasis text-uppercase font-weight-medium mb-1">Email Address</div>
                  <div class="text-body-1 font-weight-medium">{{ currentClient.email || '—' }}</div>
                </div>
              </div>
            </div>
          </VCol>

          <VCol cols="12" md="6">
            <div class="info-box">
              <div class="d-flex align-center mb-3">
                <div class="info-icon-bg info mr-3">
                  <VIcon icon="tabler-phone" size="20" color="white" />
                </div>
                <div>
                  <div class="text-caption text-medium-emphasis text-uppercase font-weight-medium mb-1">Phone Number</div>
                  <div class="text-body-1 font-weight-medium">{{ currentClient.phone || '—' }}</div>
                </div>
              </div>
            </div>
          </VCol>

          <VCol cols="12" md="6">
            <div class="info-box">
              <div class="d-flex align-center mb-3">
                <div class="info-icon-bg warning mr-3">
                  <VIcon icon="tabler-world" size="20" color="white" />
                </div>
                <div>
                  <div class="text-caption text-medium-emphasis text-uppercase font-weight-medium mb-1">Website</div>
                  <div class="text-body-1 font-weight-medium">
                    <a 
                      v-if="currentClient.website" 
                      :href="currentClient.website" 
                      target="_blank"
                      class="text-decoration-none text-primary hover-underline"
                    >
                      {{ currentClient.website }}
                      <VIcon icon="tabler-external-link" size="16" class="ml-1" />
                    </a>
                    <span v-else>—</span>
                  </div>
                </div>
              </div>
            </div>
          </VCol>

          <VCol cols="12" md="6">
            <div class="info-box">
              <div class="d-flex align-center mb-3">
                <div class="info-icon-bg error mr-3">
                  <VIcon icon="tabler-building" size="20" color="white" />
                </div>
                <div>
                  <div class="text-caption text-medium-emphasis text-uppercase font-weight-medium mb-1">Industry</div>
                  <div class="text-body-1 font-weight-medium">{{ currentClient.industry || '—' }}</div>
                </div>
              </div>
            </div>
          </VCol>
        </VRow>
      </VCardText>
    </VCard>

    <!-- Address Information Card -->
    <VCard class="info-card" elevation="2">
      <VCardText class="pa-8">
        <div class="d-flex align-center mb-6">
          <div class="icon-wrapper mr-3">
            <VIcon icon="tabler-map-pin" size="28" color="primary" />
          </div>
          <h2 class="text-h5 font-weight-bold">Address Information</h2>
        </div>
        
        <VRow class="gy-6">
          <VCol cols="12" md="4">
            <div class="info-box">
              <div class="text-caption text-medium-emphasis text-uppercase font-weight-medium mb-2">City</div>
              <div class="text-h6 font-weight-medium">{{ currentClient.city || '—' }}</div>
            </div>
          </VCol>

          <VCol cols="12" md="4">
            <div class="info-box">
              <div class="text-caption text-medium-emphasis text-uppercase font-weight-medium mb-2">State</div>
              <div class="text-h6 font-weight-medium">{{ currentClient.state || '—' }}</div>
            </div>
          </VCol>

          <VCol cols="12" md="4">
            <div class="info-box">
              <div class="text-caption text-medium-emphasis text-uppercase font-weight-medium mb-2">Zip Code</div>
              <div class="text-h6 font-weight-medium">{{ currentClient.zip_code || '—' }}</div>
            </div>
          </VCol>

          <VCol cols="12">
            <div class="info-box country-box">
              <div class="d-flex align-center">
                <div class="info-icon-bg primary mr-4">
                  <VIcon icon="tabler-flag" size="24" color="white" />
                </div>
                <div>
                  <div class="text-caption text-medium-emphasis text-uppercase font-weight-medium mb-1">Country</div>
                  <div class="text-h6 font-weight-medium">{{ currentClient.country || '—' }}</div>
                </div>
              </div>
            </div>
          </VCol>
        </VRow>
      </VCardText>
    </VCard>
  </div>

  <!-- Loading State -->
  <VCard v-else class="pa-10 text-center">
    <VProgressCircular indeterminate color="primary" size="64" />
    <p class="text-body-1 text-medium-emphasis mt-4">Loading client information...</p>
  </VCard>
</template>

<style scoped>
.header-card {
  background: linear-gradient(135deg, rgba(var(--v-theme-primary), 0.05) 0%, rgba(var(--v-theme-surface), 1) 100%);
  border-radius: 16px !important;
  overflow: hidden;
}

.avatar-shadow {
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

.info-card {
  border-radius: 16px !important;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.info-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12) !important;
}

.icon-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 56px;
  height: 56px;
  border-radius: 12px;
}

.info-box {
  padding: 20px;
  border-radius: 12px;
  border: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
  transition: all 0.2s ease;
  height: 100%;
}

.info-box:hover {
  border-color: rgba(var(--v-theme-primary), 0.3);
  transform: translateX(4px);
}

.info-icon-bg {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 44px;
  height: 44px;
  border-radius: 10px;
  flex-shrink: 0;
}


.country-box {
  padding: 24px;
}

.hover-underline {
  position: relative;
  transition: color 0.2s ease;
}

.hover-underline::after {
  content: '';
  position: absolute;
  width: 0;
  height: 2px;
  bottom: -2px;
  left: 0;
  background-color: currentColor;
  transition: width 0.3s ease;
}

.hover-underline:hover::after {
  width: 100%;
}

.hover-underline:hover {
  color: rgb(var(--v-theme-primary)) !important;
}
</style>