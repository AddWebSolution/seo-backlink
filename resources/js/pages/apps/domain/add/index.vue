<script setup>
import { useDomainStore } from '@/composables/domainApi.js'
import { useRouter } from 'vue-router'

const router = useRouter()

// Form data
const form = ref({
  client_id: '',
  source_url: '',
  title: '',
  target_url: '',
  domain_authority: null,
  domain_rating: null,
  organic_traffic: null,
  price_ne: null,
  price_gp: null,
  total_price: null,
  turnaround_time: '',
  status: 1,
  approval_status: 1,
  country: '',
  anchor_text: '',
  special_requirements: '',
  price: null,
})

// Form state
const submitting = ref(false)
const formRef = ref()
const currentStep = ref(1)
const totalSteps = 5
const showSuccessAlert = ref(false)
const errorMessage = ref('')

// Computed
const progressPercentage = computed(() => (currentStep.value / totalSteps) * 100)

// Status options
const statusOptions = [
  { title: 'Available', value: 1, color: 'success', icon: 'mdi-check-circle' },
  { title: 'Unavailable', value: 2, color: 'error', icon: 'mdi-close-circle' },
]

const approvalStatusOptions = [
  { title: 'Pending', value: 1, color: 'warning', icon: 'mdi-clock' },
  { title: 'Rejected', value: 2, color: 'error', icon: 'mdi-close-circle' },
  { title: 'Approved', value: 3, color: 'success', icon: 'mdi-check-circle' },
]

// Form steps configuration
const formSteps = [
  {
    id: 1,
    title: 'Basic Info',
    icon: 'mdi-information',
    color: 'primary',
    fields: ['client_id', 'title', 'country', 'turnaround_time']
  },
  {
    id: 2,
    title: 'Status & Approval',
    icon: 'mdi-shield-check',
    color: 'success',
    fields: ['status', 'approval_status']
  },
  {
    id: 3,
    title: 'URLs & Links',
    icon: 'mdi-link',
    color: 'info',
    fields: ['source_url', 'target_url']
  },
  {
    id: 4,
    title: 'SEO Metrics',
    icon: 'mdi-chart-line',
    color: 'warning',
    fields: ['domain_authority', 'domain_rating', 'organic_traffic']
  },
  {
    id: 5,
    title: 'Pricing & Details',
    icon: 'mdi-currency-usd',
    color: 'error',
    fields: ['price_ne', 'price_gp', 'price', 'anchor_text', 'special_requirements']
  }
]

// Enhanced validation rules
const rules = {
  required: v => !!v || 'This field is required',
  url: v => !v || /^https?:\/\/.+/.test(v) || 'Must be a valid URL',
  number: v => !v || /^\d*\.?\d+$/.test(v) || 'Must be a valid number',
  positiveNumber: v => !v || (parseFloat(v) > 0) || 'Must be greater than 0',
  maxLength: max => v => !v || v.length <= max || `Maximum ${max} characters allowed`,
  domainAuthority: [
    v => !v || (v >= 0 && v <= 100) || 'Domain Authority must be between 0 and 100',
    v => !v || Number.isInteger(Number(v)) || 'Domain Authority must be a whole number',
  ],
  domainRating: [
    v => !v || (v >= 0 && v <= 100) || 'Domain Rating must be between 0 and 100',
    v => !v || Number.isInteger(Number(v)) || 'Domain Rating must be a whole number',
  ],
}

// Step navigation
const goToStep = (step) => {
  currentStep.value = step
}

const nextStep = () => {
  if (currentStep.value < totalSteps) {
    currentStep.value++
  }
}

const prevStep = () => {
  if (currentStep.value > 1) {
    currentStep.value--
  }
}

// Submit handler
const submitForm = async () => {
  if (!formRef.value) return

  const { valid } = await formRef.value.validate()
  if (!valid) return

  submitting.value = true
  errorMessage.value = ''

  try {
    // Prepare payload
    const payload = { ...form.value }

    // Convert empty strings to null for numeric fields
    const numericFields = ['domain_authority', 'domain_rating', 'organic_traffic', 'price_ne', 'price_gp', 'price', 'total_price']
    numericFields.forEach(field => {
      if (payload[field] === '') payload[field] = null
    })

    await useDomainStore(payload)
    showSuccessAlert.value = true

    // Navigate after short delay
    setTimeout(() => {
      router.push({ name: 'apps-domain-list' })
    }, 1500)

  } catch (err) {
    console.error(err)
    errorMessage.value = err.response?.data?.message || 'An error occurred while creating the domain. Please try again.'
  } finally {
    submitting.value = false
  }
}

// Auto-calculate total price
watch([() => form.value.price_ne, () => form.value.price_gp, () => form.value.price],
  ([priceNe, priceGp, price]) => {
    const ne = parseFloat(priceNe) || 0
    const gp = parseFloat(priceGp) || 0
    const p = parseFloat(price) || 0
    const total = ne + gp + p
    form.value.total_price = total > 0 ? total.toFixed(2) : null
  })

// Format currency display
const formatCurrency = (value) => {
  if (!value || value === '0') return '$0.00'
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(value)
}
</script>

<template>
  <VCard class="mb-6">
    <!-- Enhanced Header with Progress -->
    <div>
      <div class="d-flex align-center justify-space-between pa-6 pb-4">
        <div class="d-flex align-center">
          <VAvatar color="success" variant="tonal" class="me-3">
            <VIcon icon="mdi-plus" />
          </VAvatar>
          <div>
            <h4 class="text-h4 font-weight-bold">Create New Domain</h4>
            <p class="text-body-2 text-medium-emphasis mb-0">
              Create a new domain entry with detailed information
            </p>
          </div>
        </div>
      </div>

      <!-- Progress Bar -->
      <div class="px-6 pb-4">
        <VProgressLinear :model-value="progressPercentage" color="primary" height="8" rounded class="mb-2" />
        <div class="d-flex justify-space-between text-body-2 text-medium-emphasis">
          <span>Step {{ currentStep }} of {{ totalSteps }}</span>
          <span>{{ Math.round(progressPercentage) }}% Complete</span>
        </div>
      </div>

      <!-- Step Navigation -->
      <div class="px-6 pb-4">
        <VRow class="d-flex justify-space-between" dense>
          <VCol v-for="step in formSteps" :key="step.id" cols="auto" class="px-1">
            <VBtn :color="currentStep === step.id ? step.color : 'grey lighten-3'"
              :variant="currentStep === step.id ? 'elevated' : 'text'" size="small"
              class="d-flex align-center text-caption" @click="goToStep(step.id)">
              <VIcon :icon="step.icon" size="16" class="me-1"
                :class="currentStep === step.id ? 'text-white' : 'text-body-1'" />
              <span :class="currentStep === step.id ? 'text-white' : 'text-body-1'">
                {{ step.title }}
              </span>
            </VBtn>
          </VCol>
        </VRow>
      </div>
    </div>

    <VDivider />

    <!-- Success Alert -->
    <VExpandTransition>
      <div v-if="showSuccessAlert" class="pa-4">
        <VAlert color="success" variant="tonal" closable @click:close="showSuccessAlert = false">
          <VIcon icon="mdi-check-circle" class="me-2" />
          Domain created successfully!
        </VAlert>
      </div>
    </VExpandTransition>

    <!-- Error Alert -->
    <VExpandTransition>
      <div v-if="errorMessage" class="pa-4">
        <VAlert color="error" variant="tonal" closable @click:close="errorMessage = ''">
          <VIcon icon="mdi-alert-circle" class="me-2" />
          {{ errorMessage }}
        </VAlert>
      </div>
    </VExpandTransition>

    <!-- Form Content -->
    <VCardText>
      <VForm ref="formRef" @submit.prevent="submitForm">

        <!-- Step 1: Basic Information -->
        <div v-show="currentStep === 1" class="pa-2">
          <VCard elevation="0" class="mb-6">
            <VCardTitle class="pa-4">
              <div class="d-flex align-center">
                <VIcon icon="mdi-information" class="me-2" />
              </div>
            </VCardTitle>
            <VCardText class="pa-4 pt-0">
              <VRow>
                <VCol cols="12">
                  <AppTextField v-model="form.client_id" label="Client ID" placeholder="Enter unique client identifier"
                    :rules="[rules]" prepend-inner-icon="mdi-account" variant="outlined" />
                </VCol>

                <VCol cols="12">
                  <AppTextField v-model="form.title" label="Domain Title" placeholder="Enter descriptive domain title"
                    :rules="[rules, rules.maxLength(100)]" prepend-inner-icon="mdi-format-title" variant="outlined"
                    required />
                </VCol>

                <VCol cols="12" md="6">
                  <AppTextField v-model="form.country" label="Country" placeholder="Enter country"
                    prepend-inner-icon="mdi-flag" variant="outlined" />
                </VCol>

                <VCol cols="12" md="6">
                  <AppTextField v-model="form.turnaround_time" label="Turnaround Time"
                    placeholder="e.g., 5-7 business days" prepend-inner-icon="mdi-clock" variant="outlined" />
                </VCol>
              </VRow>
            </VCardText>
          </VCard>
        </div>

        <!-- Step 2: Status & Approval -->
        <div v-show="currentStep === 2" class="pa-2">
          <VCard elevation="0" class="mb-6">
            <VCardTitle class="pa-4">
              <div class="d-flex align-center">
                <VIcon icon="mdi-shield-check" class="me-2" />
              </div>
            </VCardTitle>
            <VCardText class="pa-4 pt-0">
              <VRow>
                <VCol cols="12" md="6">
                  <AppSelect v-model="form.status" label="Domain Status" :items="statusOptions" variant="outlined"
                    required>
                    <template #selection="{ item }">
                      <div class="d-flex align-center">
                        <VIcon :icon="item.raw.icon" :color="item.raw.color" size="16" class="me-2" />
                        {{ item.raw.title }}
                      </div>
                    </template>
                    <template #item="{ item, props }">
                      <VListItem v-bind="props">
                        <template #prepend>
                          <VIcon :icon="item.raw.icon" :color="item.raw.color" size="16" />
                        </template>
                      </VListItem>
                    </template>
                  </AppSelect>
                </VCol>

                <VCol cols="12" md="6">
                  <AppSelect v-model="form.approval_status" label="Approval Status" :items="approvalStatusOptions"
                    variant="outlined" required>
                    <template #selection="{ item }">
                      <div class="d-flex align-center">
                        <VIcon :icon="item.raw.icon" :color="item.raw.color" size="16" class="me-2" />
                        {{ item.raw.title }}
                      </div>
                    </template>
                    <template #item="{ item, props }">
                      <VListItem v-bind="props">
                        <template #prepend>
                          <VIcon :icon="item.raw.icon" :color="item.raw.color" size="16" />
                        </template>
                      </VListItem>
                    </template>
                  </AppSelect>
                </VCol>
              </VRow>

              <!-- Status Preview -->
              <VCard variant="outlined" class="mt-4 pa-4">
                <div class="d-flex align-center justify-space-between">
                  <div class="d-flex align-center">
                    <VChip :color="statusOptions.find(s => s.value == form.status)?.color" variant="tonal" size="small"
                      class="me-3">
                      <VIcon :icon="statusOptions.find(s => s.value == form.status)?.icon" size="14" class="me-1" />
                      {{statusOptions.find(s => s.value == form.status)?.title}}
                    </VChip>

                    <VChip :color="approvalStatusOptions.find(s => s.value === form.approval_status)?.color"
                      variant="tonal" size="small">
                      <VIcon :icon="approvalStatusOptions.find(s => s.value === form.approval_status)?.icon" size="14"
                        class="me-1" />
                      {{approvalStatusOptions.find(s => s.value === form.approval_status)?.title}}
                    </VChip>
                  </div>

                  <span class="text-body-2 text-medium-emphasis">Current Status</span>
                </div>
              </VCard>
            </VCardText>
          </VCard>
        </div>

        <!-- Step 3: URLs & Links -->
        <div v-show="currentStep === 3" class="pa-2">
          <VCard elevation="0" class="mb-6">
            <VCardTitle class="pa-4">
              <div class="d-flex align-center">
                <VIcon icon="mdi-link" class="me-2" />
              </div>
            </VCardTitle>
            <VCardText class="pa-4 pt-0">
              <VRow>
                <VCol cols="12">
                  <AppTextField v-model="form.source_url" label="Source URL"
                    placeholder="https://example.com/source-page" :rules="[rules.url]" prepend-inner-icon="mdi-web"
                    variant="outlined" />
                </VCol>

                <VCol cols="12">
                  <AppTextField v-model="form.target_url" label="Target URL"
                    placeholder="https://target.com/destination-page" :rules="[rules.url]"
                    prepend-inner-icon="mdi-target" variant="outlined" />
                </VCol>
              </VRow>

              <!-- URL Preview -->
              <VCard variant="outlined" class="mt-4 pa-4" v-if="form.source_url || form.target_url">
                <h6 class="text-h6 mb-3">URL Preview</h6>
                <div class="d-flex flex-column gap-2">
                  <div v-if="form.source_url" class="d-flex align-center">
                    <VIcon icon="mdi-arrow-right" class="me-2 text-primary" />
                    <a :href="form.source_url" target="_blank" class="text-primary text-decoration-none">
                      {{ form.source_url }}
                    </a>
                  </div>
                  <div v-if="form.target_url" class="d-flex align-center">
                    <VIcon icon="mdi-arrow-right" class="me-2 text-success" />
                    <a :href="form.target_url" target="_blank" class="text-success text-decoration-none">
                      {{ form.target_url }}
                    </a>
                  </div>
                </div>
              </VCard>
            </VCardText>
          </VCard>
        </div>

        <!-- Step 4: SEO Metrics -->
        <div v-show="currentStep === 4" class="pa-2">
          <VCard elevation="0" class="mb-6">
            <VCardTitle class="pa-4">
              <div class="d-flex align-center">
                <VIcon icon="mdi-chart-line" class="me-2" />
              </div>
            </VCardTitle>
            <VCardText class="pa-4 pt-0">
              <VRow>
                <VCol cols="12" md="4">
                  <AppTextField v-model="form.domain_authority" label="Domain Authority" placeholder="0-100"
                    type="number" min="0" max="100" :rules="rules.domainAuthority" prepend-inner-icon="mdi-shield-check"
                    variant="outlined" />
                  <VProgressLinear v-if="form.domain_authority" :model-value="form.domain_authority" max="100"
                    color="primary" class="mt-2" height="4" rounded />
                </VCol>

                <VCol cols="12" md="4">
                  <AppTextField v-model="form.domain_rating" label="Domain Rating" placeholder="0-100" type="number"
                    min="0" max="100" :rules="rules.domainRating" prepend-inner-icon="mdi-star" variant="outlined" />
                  <VProgressLinear v-if="form.domain_rating" :model-value="form.domain_rating" max="100" color="success"
                    class="mt-2" height="4" rounded />
                </VCol>

                <VCol cols="12" md="4">
                  <AppTextField v-model="form.organic_traffic" label="Organic Traffic" placeholder="Monthly visitors"
                    type="number" min="0" :rules="[rules.number]" prepend-inner-icon="mdi-trending-up"
                    variant="outlined" />
                </VCol>
              </VRow>

              <!-- SEO Metrics Summary -->
              <VCard variant="outlined" class="mt-4 pa-4"
                v-if="form.domain_authority || form.domain_rating || form.organic_traffic">
                <h6 class="text-h6 mb-3">SEO Metrics Summary</h6>
                <div class="d-flex justify-space-around">
                  <div v-if="form.domain_authority" class="text-center">
                    <VAvatar color="primary" variant="tonal" size="32" class="mb-2">
                      <span class="text-caption font-weight-bold">{{ form.domain_authority }}</span>
                    </VAvatar>
                    <p class="text-body-2 mb-0">DA</p>
                  </div>
                  <div v-if="form.domain_rating" class="text-center">
                    <VAvatar color="success" variant="tonal" size="32" class="mb-2">
                      <span class="text-caption font-weight-bold">{{ form.domain_rating }}</span>
                    </VAvatar>
                    <p class="text-body-2 mb-0">DR</p>
                  </div>
                  <div v-if="form.organic_traffic" class="text-center">
                    <VAvatar color="info" variant="tonal" size="32" class="mb-2">
                      <VIcon icon="mdi-trending-up" size="16" />
                    </VAvatar>
                    <p class="text-body-2 mb-0">{{ Number(form.organic_traffic).toLocaleString() }}</p>
                  </div>
                </div>
              </VCard>
            </VCardText>
          </VCard>
        </div>

        <!-- Step 5: Pricing & Additional Details -->
        <div v-show="currentStep === 5" class="pa-2">
          <VCard elevation="0" class="mb-6">
            <VCardTitle class="pa-4">
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center">
                  <VIcon icon="mdi-currency-usd" class="me-2" />
                </div>
                <VChip v-if="form.total_price" color="error" variant="elevated" size="small">
                  Total: {{ formatCurrency(form.total_price) }}
                </VChip>
              </div>
            </VCardTitle>
            <VCardText class="pa-4 pt-0">
              <!-- Pricing Section -->
              <h6 class="text-h6 mb-4">Pricing Breakdown</h6>
              <VRow>
                <VCol cols="12" md="4">
                  <AppTextField v-model="form.price_ne" label="Price NE" placeholder="0.00" type="number" step="0.01"
                    min="0" :rules="[rules.number]" prepend-inner-icon="mdi-currency-usd" variant="outlined" />
                </VCol>

                <VCol cols="12" md="4">
                  <AppTextField v-model="form.price_gp" label="Price GP" placeholder="0.00" type="number" step="0.01"
                    min="0" :rules="[rules.number]" prepend-inner-icon="mdi-currency-usd" variant="outlined" />
                </VCol>

                <VCol cols="12" md="4">
                  <AppTextField v-model="form.price" label="Base Price" placeholder="0.00" type="number" step="0.01"
                    min="0" :rules="[rules.number]" prepend-inner-icon="mdi-currency-usd" variant="outlined" />
                </VCol>

                <VCol cols="12">
                  <AppTextField v-model="form.total_price" label="Total Price" :placeholder="formatCurrency(0)"
                    type="number" step="0.01" prepend-inner-icon="mdi-calculator" variant="filled" readonly
                    hint="Auto-calculated" />
                </VCol>
              </VRow>

              <!-- Additional Information Section -->
              <VDivider class="my-6" />
              <h6 class="text-h6 mb-4">Additional Information</h6>
              <VRow>
                <VCol cols="12">
                  <AppTextField v-model="form.anchor_text" label="Anchor Text"
                    placeholder="Enter the anchor text for the link" :rules="[rules.maxLength(200)]"
                    prepend-inner-icon="mdi-anchor" variant="outlined" />
                </VCol>

                <VCol cols="12">
                  <AppTextField v-model="form.special_requirements" label="Special Requirements"
                    placeholder="Enter any special requirements or notes..." :rules="[rules.maxLength(500)]"
                    variant="outlined" rows="4" multiline counter />
                </VCol>
              </VRow>
            </VCardText>
          </VCard>
        </div>

        <!-- Navigation Footer -->
        <div class="mt-6">
          <VDivider class="mb-4" />
          <div class="d-flex justify-space-between align-center">
            <VBtn v-if="currentStep > 1" variant="outlined" color="default" @click="prevStep">
              <VIcon icon="mdi-arrow-left" class="me-2" />
              Previous
            </VBtn>
            <VSpacer v-else />

            <div class="d-flex gap-3">
              <VBtn v-if="currentStep < totalSteps" color="primary" @click="nextStep">
                Next
                <VIcon icon="mdi-arrow-right" class="ms-2" />
              </VBtn>

              <VBtn v-else type="submit" color="success" :loading="submitting" :disabled="showSuccessAlert">
                <VIcon icon="mdi-check" class="me-2" />
                Create Domain
              </VBtn>

              <VBtn variant="outlined" color="secondary" @click="router.push({ name: 'apps-domain-list' })"
                :disabled="submitting">
                Cancel
              </VBtn>
            </div>
          </div>
        </div>
      </VForm>
    </VCardText>
  </VCard>
</template>

<style scoped>
:deep(.v-field--variant-outlined) {
  --v-field-border-width: 2px;
}

:deep(.v-field--variant-outlined.v-field--focused) {
  --v-field-border-width: 3px;
}

/* Step transition animations */
.v-enter-active,
.v-leave-active {
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.v-enter-from {
  transform: translateX(30px);
}

.v-leave-to {
  opacity: 0;
  transform: translateX(-30px);
}
</style>
