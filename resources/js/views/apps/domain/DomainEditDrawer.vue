<script setup>
import { useDomainApi } from '@/composables/domainApi'
import { ref } from 'vue'
import {getCodes} from "country-list";

const { 
  currentDomain, 
  loading, 
  error, 
  updateDomain, 
  showAlert 
} = useDomainApi()

const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
  domain: {
    type: Object,
    default: () => ({}),
  },
})

const countryCodes = getCodes();

const emit = defineEmits(['update:isDrawerOpen', 'success'])

// Form data
const formData = ref({
  client_id: '',
  source_url: '',
  title: '',
  target_url: '',
  domain_authority: null,
  domain_rating: null,
  organic_traffic: null,
  price_ne: null,
  price_gp: null,
  price: null,
  total_price: null,
  turnaround_time: '',
  status: 1,
  approval_status: 1,
  country: '',
  platform_type: '',
  categories: [],
  categories_input: '',
  anchor_text: '',
  special_requirements: '',
})

formData.value.categories_input = Array.isArray(formData.value.categories)
    ? formData.value.categories.join(', ')
    : '';

// Form state
const isSubmitting = ref(false)
const refVForm = ref()
const currentStep = ref(1)
const totalSteps = 6
const showSuccessAlert = ref(false)
const errorMessage = ref('')

// Computed
const isEdit = computed(() => Boolean(props.domain?.id))
const drawerTitle = computed(() => isEdit.value ? 'Edit Domain' : 'Add New Domain')
const progressPercentage = computed(() => (currentStep.value / totalSteps) * 100)

// Status options
const statusOptions = [
  { title: 'Available', value: 1, color: 'success', icon: 'tabler-check-circle' },
  { title: 'Unavailable', value: 2, color: 'error', icon: 'tabler-x-circle' },
]

const approvalStatusOptions = [
  { title: 'Pending', value: 1, color: 'warning', icon: 'tabler-clock' },
  { title: 'Rejected', value: 2, color: 'error', icon: 'tabler-x-circle' },
  { title: 'Approved', value: 3, color: 'success', icon: 'tabler-check-circle' },
]

// Form steps configuration
const formSteps = [
  {
    id: 1,
    title: 'Basic Info',
    icon: 'tabler-info-circle',
    color: 'primary',
    fields: ['client_id', 'title', 'turnaround_time']
  },
  {
    id: 2,
    title: 'Platform & Categories',
    icon: 'tabler-hierarchy',
    color: 'secondary',
    fields: ['platform_type','country', 'categories']
  },
  {
    id: 3,
    title: 'Status & Approval',
    icon: 'tabler-shield-check',
    color: 'success',
    fields: ['status', 'approval_status']
  },
  {
    id: 4,
    title: 'URLs & Links',
    icon: 'tabler-link',
    color: 'info',
    fields: ['source_url', 'target_url']
  },
  {
    id: 5,
    title: 'SEO Metrics',
    icon: 'tabler-chart-line',
    color: 'warning',
    fields: ['domain_authority', 'domain_rating', 'organic_traffic']
  },
  {
    id: 6,
    title: 'Pricing & Details',
    icon: 'tabler-currency-dollar',
    color: 'error',
    fields: ['price_ne', 'price_gp', 'price', 'anchor_text', 'special_requirements']
  }
]

// Form validation rules
const rules = {
  title: [
    v => !v || v.length >= 3 || 'Title must be at least 3 characters',
    v => !v || v.length <= 100 || 'Title must be less than 100 characters',
  ],
  source_url: [
    v => !v || (() => { try { new URL(v); return true } catch { return 'Please enter a valid URL (include http:// or https://)' } })(),
  ],
  target_url: [
    v => !v || (() => { try { new URL(v); return true } catch { return 'Please enter a valid URL (include http:// or https://)' } })(),
  ],
  domain_authority: [
    v => !v || (v >= 0 && v <= 100) || 'Domain Authority must be between 0 and 100',
    v => !v || Number.isInteger(Number(v)) || 'Domain Authority must be a whole number',
  ],
  domain_rating: [
    v => !v || (v >= 0 && v <= 100) || 'Domain Rating must be between 0 and 100',
    v => !v || Number.isInteger(Number(v)) || 'Domain Rating must be a whole number',
  ],
  organic_traffic: [
    v => !v || v >= 0 || 'Organic Traffic must be a positive number',
    v => !v || Number.isInteger(Number(v)) || 'Organic Traffic must be a whole number',
  ],
  price_ne: [v => !v || v >= 0 || 'Price NE must be a positive number'],
  price_gp: [v => !v || v >= 0 || 'Price GP must be a positive number'],
  price: [v => !v || v >= 0 || 'Price must be a positive number'],
  // total_price: [v => !v || v >= 0 || 'Total Price must be a positive number'],
  country: [v => !v || v.length <= 2 || 'Country name must be less than 3 characters'],
  anchor_text: [v => !v || v.length <= 200 || 'Anchor text must be less than 200 characters'],
  special_requirements: [v => !v || v.length <= 500 || 'Special requirements must be less than 500 characters'],
}


// Watchers
watch(() => props.isDrawerOpen, (val) => {
  if (val) {
    resetForm()
    if (isEdit.value) {
      populateForm()
    }
    // Reset to first step when opening
    currentStep.value = 1
    showSuccessAlert.value = false
    errorMessage.value = ''
  }
})

// Methods
const resetForm = () => {
  formData.value = {
    client_id: '',
    source_url: '',
    title: '',
    target_url: '',
    domain_authority: null,
    domain_rating: null,
    organic_traffic: null,
    price_ne: null,
    price_gp: null,
    price: null,
    total_price: null,
    turnaround_time: '',
    status: 1,
    approval_status: 1,
    country: '',
    platform_type: '',
    categories: [],
    categories_input: '',
    anchor_text: '',
    special_requirements: '',
  }

  nextTick(() => {
    refVForm.value?.resetValidation()
  })
}

const populateForm = () => {
  if (props.domain) {
    Object.keys(formData.value).forEach(key => {
      if (props.domain[key] !== undefined) {
        formData.value[key] = props.domain[key]
      }
    })

    if (Array.isArray(formData.value.categories)) {
      formData.value.categories_input = formData.value.categories.join(', ')
    } else {
      formData.value.categories_input = ''
    }
  }
}

const handleDrawerClose = () => {
  emit('update:isDrawerOpen', false)
  resetForm()
  currentStep.value = 1
  errorMessage.value = ''
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

// Validate current step
const validateCurrentStep = async () => {
  const { valid } = await refVForm.value.validate()
  return valid
}

const onSubmit = async () => {
  if (!refVForm.value) return

  const { valid } = await refVForm.value.validate()
  if (!valid) return

  isSubmitting.value = true
  errorMessage.value = ''

  try {
    formData.value.categories = (formData.value.categories_input || '')
        .split(',')
        .map(c => c.trim())
        .filter(c => c.length > 0)
    // Prepare payload
    const payload = { ...formData.value }

    const numericFields = [
      'domain_authority',
      'domain_rating',
      'organic_traffic',
      'price_ne',
      'price_gp',
      'price',
      'total_price'
    ]

    numericFields.forEach(field => {
      if (payload[field] === '') payload[field] = null
    })

    console.log('Payload to submit:', payload)
    console.log('Is Edit Mode:', isEdit.value)
    // Determine whether to update or create
    if (isEdit.value) {
      await updateDomain(props.domain.id, payload)
    }

    showSuccessAlert.value = true

    // Close drawer after short delay
    setTimeout(() => {
      emit('success')
      handleDrawerClose()
    }, 1500)

  } catch (error) {
    console.error('Error saving domain:', error)
    errorMessage.value =
      error.response?.data?.message ||
      'An error occurred while saving the domain. Please try again.'
  } finally {
    isSubmitting.value = false
  }
}

// Calculate total price automatically
watch([() => formData.value.price_ne, () => formData.value.price_gp, () => formData.value.price],
  ([priceNe, priceGp, price]) => {
    const ne = parseFloat(priceNe) || 0
    const gp = parseFloat(priceGp) || 0
    const p = parseFloat(price) || 0
    const total = ne + gp + p
    formData.value.total_price = total > 0 ? total.toFixed(2) : null
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
  <VNavigationDrawer :model-value="isDrawerOpen" temporary location="end" width="700"
    @update:model-value="handleDrawerClose">
    <!-- Enhanced Header with Progress -->
    <div>
      <div class="d-flex align-center justify-space-between pa-6 pb-4">
        <div class="d-flex align-center">
          <VAvatar :color="isEdit ? 'warning' : 'success'" variant="tonal" class="me-3">
            <VIcon :icon="isEdit ? 'tabler-edit' : 'tabler-plus'" />
          </VAvatar>
          <div>
            <h4 class="text-h4 font-weight-bold">{{ drawerTitle }}</h4>
            <p class="text-body-2 text-medium-emphasis mb-0">
              {{ isEdit ? 'Update domain information' : 'Create a new domain entry' }}
            </p>
          </div>
        </div>

        <VBtn variant="text" color="default" icon size="40" class="rounded-lg" @click="handleDrawerClose">
          <VIcon size="20" icon="tabler-x" />
        </VBtn>
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
        <VRow class="d-flex" dense>
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
          <VIcon icon="tabler-check-circle" class="me-2" />
          {{ isEdit ? 'Domain updated successfully!' : 'Domain created successfully!' }}
        </VAlert>
      </div>
    </VExpandTransition>

    <!-- Error Alert -->
    <VExpandTransition>
      <div v-if="errorMessage" class="pa-4">
        <VAlert color="error" variant="tonal" closable @click:close="errorMessage = ''">
          <VIcon icon="tabler-alert-circle" class="me-2" />
          {{ errorMessage }}
        </VAlert>
      </div>
    </VExpandTransition>

    <!-- Form Content -->
    <div class="drawer-content">
      <VForm ref="refVForm" @submit.prevent="onSubmit">

        <!-- Step 1: Basic Information -->
        <div v-show="currentStep === 1" class="pa-6">
          <VCard elevation="0" color="primary" variant="tonal" class="mb-6">
            <VCardTitle class="pa-4">
              <div class="d-flex align-center">
                <VIcon icon="tabler-info-circle" class="me-2" />
                Basic Information
              </div>
            </VCardTitle>
            <VCardText class="pa-4 pt-0">
              <VRow>

                <VCol cols="12">
                  <AppTextField v-model="formData.title" label="Domain Title"
                    placeholder="Enter descriptive domain title" :rules="rules.title"
                    prepend-inner-icon="tabler-heading" variant="outlined" required />
                </VCol>

                <VCol cols="12" md="6">
                  <AppTextField v-model="formData.turnaround_time" label="Turnaround Time"
                    placeholder="e.g., 5-7 business days" :rules="rules.turnaround_time"
                    prepend-inner-icon="tabler-clock" variant="outlined" />
                </VCol>
              </VRow>
            </VCardText>
          </VCard>
        </div>

        <!-- Step 2: Platform & Categories -->
        <div v-show="currentStep === 2" class="pa-6">
          <VCard elevation="0" color="secondary" variant="tonal" class="mb-6">
            <VCardTitle class="pa-4">
              <div class="d-flex align-center">
                <VIcon icon="tabler-hierarchy" class="me-2" />
                Platform & Categories
              </div>
            </VCardTitle>
            <VCardText class="pa-4 pt-0">
              <VRow>
                <VCol cols="12" md="6">
                  <AppTextField v-model="formData.platform_type" label="Platform Type"
                                placeholder="Enter platform type"
                                prepend-inner-icon="tabler-brand-monday" variant="outlined" />
                </VCol>

                <VCol cols="12" md="6" class="mt-6">
                  <VAutocomplete
                      v-model="formData.country"
                      label="Country"
                      :items="countryCodes"
                      variant="outlined"
                      hint="Target country for this domain"
                      persistent-hint
                      clearable
                      :rules="rules.country"
                      prepend-inner-icon="tabler-map-pin"
                  />
                </VCol>

                <VCol cols="12" md="6">
                  <AppTextField v-model="formData.categories_input" label="Categories"
                                placeholder="e.g., Business, Technology"  hint="Add multiple categories separated by commas"
                                prepend-inner-icon="tabler-category-plus" variant="outlined" />
                </VCol>
              </VRow>
            </VCardText>
          </VCard>
        </div>

        <!-- Step 3: Status & Approval -->
        <div v-show="currentStep === 3" class="pa-6">
          <VCard elevation="0" color="success" variant="tonal" class="mb-6">
            <VCardTitle class="pa-4">
              <div class="d-flex align-center">
                <VIcon icon="tabler-shield-check" class="me-2" />
                Status & Approval
              </div>
            </VCardTitle>
            <VCardText class="pa-4 pt-0">
              <VRow>
                <VCol cols="12" md="6">
                  <AppSelect v-model="formData.status" label="Domain Status" :items="statusOptions" variant="outlined"
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
                  <AppSelect v-model="formData.approval_status" label="Approval Status" :items="approvalStatusOptions"
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
                    <VChip :color="statusOptions.find(s => s.value == formData.status)?.color" variant="tonal"
                      size="small" class="me-3">
                      <VIcon :icon="statusOptions.find(s => s.value == formData.status)?.icon" size="14" class="me-1" />
                      {{statusOptions.find(s => s.value == formData.status)?.title}}
                    </VChip>

                    <VChip :color="approvalStatusOptions.find(s => s.value === formData.approval_status)?.color"
                      variant="tonal" size="small">
                      <VIcon :icon="approvalStatusOptions.find(s => s.value === formData.approval_status)?.icon"
                        size="14" class="me-1" />
                      {{approvalStatusOptions.find(s => s.value === formData.approval_status)?.title}}
                    </VChip>
                  </div>

                  <span class="text-body-2 text-medium-emphasis">Current Status</span>
                </div>
              </VCard>
            </VCardText>
          </VCard>
        </div>

        <!-- Step 4: URLs & Links -->
        <div v-show="currentStep === 4" class="pa-6">
          <VCard elevation="0" color="info" variant="tonal" class="mb-6">
            <VCardTitle class="pa-4">
              <div class="d-flex align-center">
                <VIcon icon="tabler-link" class="me-2" />
                URLs & Links
              </div>
            </VCardTitle>
            <VCardText class="pa-4 pt-0">
              <VRow>
                <VCol cols="12">
                  <AppTextField v-model="formData.source_url" label="Source URL"
                    placeholder="https://example.com/source-page" :rules="rules.source_url"
                    prepend-inner-icon="tabler-world" variant="outlined" required />
                </VCol>

                <VCol cols="12">
                  <AppTextField v-model="formData.target_url" label="Target URL"
                    placeholder="https://target.com/destination-page" :rules="rules.target_url"
                    prepend-inner-icon="tabler-target" variant="outlined" />
                </VCol>
              </VRow>

              <!-- URL Preview -->
              <VCard variant="outlined" class="mt-4 pa-4" v-if="formData.source_url || formData.target_url">
                <h6 class="text-h6 mb-3">URL Preview</h6>
                <div class="d-flex flex-column gap-2">
                  <div v-if="formData.source_url" class="d-flex align-center">
                    <VIcon icon="tabler-arrow-right" class="me-2 text-primary" />
                    <a :href="formData.source_url" target="_blank" class="text-primary text-decoration-none">
                      {{ formData.source_url }}
                    </a>
                  </div>
                  <div v-if="formData.target_url" class="d-flex align-center">
                    <VIcon icon="tabler-arrow-right" class="me-2 text-success" />
                    <a :href="formData.target_url" target="_blank" class="text-success text-decoration-none">
                      {{ formData.target_url }}
                    </a>
                  </div>
                </div>
              </VCard>
            </VCardText>
          </VCard>
        </div>

        <!-- Step 5: SEO Metrics -->
        <div v-show="currentStep === 5" class="pa-6">
          <VCard elevation="0" color="warning" variant="tonal" class="mb-6">
            <VCardTitle class="pa-4">
              <div class="d-flex align-center">
                <VIcon icon="tabler-chart-line" class="me-2" />
                SEO Metrics
              </div>
            </VCardTitle>
            <VCardText class="pa-4 pt-0">
              <VRow>
                <VCol cols="12" md="4">
                  <AppTextField v-model="formData.domain_authority" label="Domain Authority" placeholder="0-100"
                    type="number" min="0" max="100" :rules="rules.domain_authority" prepend-inner-icon="tabler-award"
                    variant="outlined" />
                  <VProgressLinear v-if="formData.domain_authority" :model-value="formData.domain_authority" max="100"
                    color="primary" class="mt-2" height="4" rounded />
                </VCol>

                <VCol cols="12" md="4">
                  <AppTextField v-model="formData.domain_rating" label="Domain Rating" placeholder="0-100" type="number"
                    min="0" max="100" :rules="rules.domain_rating" prepend-inner-icon="tabler-trending-up"
                    variant="outlined" />
                  <VProgressLinear v-if="formData.domain_rating" :model-value="formData.domain_rating" max="100"
                    color="success" class="mt-2" height="4" rounded />
                </VCol>

                <VCol cols="12" md="4">
                  <AppTextField v-model="formData.organic_traffic" label="Organic Traffic"
                    placeholder="Monthly visitors" type="number" min="0" :rules="rules.organic_traffic"
                    prepend-inner-icon="tabler-users" variant="outlined" />
                </VCol>
              </VRow>

              <!-- SEO Metrics Summary -->
              <VCard variant="outlined" class="mt-4 pa-4"
                v-if="formData.domain_authority || formData.domain_rating || formData.organic_traffic">
                <h6 class="text-h6 mb-3">SEO Metrics Summary</h6>
                <div class="d-flex justify-space-around">
                  <div v-if="formData.domain_authority" class="text-center">
                    <VAvatar color="primary" variant="tonal" size="32" class="mb-2">
                      <span class="text-caption font-weight-bold">{{ formData.domain_authority }}</span>
                    </VAvatar>
                    <p class="text-body-2 mb-0">DA</p>
                  </div>
                  <div v-if="formData.domain_rating" class="text-center">
                    <VAvatar color="success" variant="tonal" size="32" class="mb-2">
                      <span class="text-caption font-weight-bold">{{ formData.domain_rating }}</span>
                    </VAvatar>
                    <p class="text-body-2 mb-0">DR</p>
                  </div>
                  <div v-if="formData.organic_traffic" class="text-center">
                    <VAvatar color="info" variant="tonal" size="32" class="mb-2">
                      <VIcon icon="tabler-users" size="16" />
                    </VAvatar>
                    <p class="text-body-2 mb-0">{{ Number(formData.organic_traffic).toLocaleString() }}</p>
                  </div>
                </div>
              </VCard>
            </VCardText>
          </VCard>
        </div>

        <!-- Step 6: Pricing & Additional Details -->
        <div v-show="currentStep === 6" class="pa-6">
          <VCard elevation="0" color="error" variant="tonal" class="mb-6">
            <VCardTitle class="pa-4">
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center">
                  <VIcon icon="tabler-currency-dollar" class="me-2" />
                  Pricing & Additional Details
                </div>
                <VChip v-if="formData.total_price" color="error" variant="elevated" size="small">
                  Total: {{ formatCurrency(formData.total_price) }}
                </VChip>
              </div>
            </VCardTitle>
            <VCardText class="pa-4 pt-0">
              <!-- Pricing Section -->
              <h6 class="text-h6 mb-4">Pricing Breakdown</h6>
              <VRow>
                <VCol cols="12" md="4">
                  <AppTextField v-model="formData.price_ne" label="Price NE" placeholder="0.00" type="number"
                    step="0.01" min="0" :rules="rules.price_ne" prepend-inner-icon="tabler-cash" variant="outlined" />
                </VCol>

                <VCol cols="12" md="4">
                  <AppTextField v-model="formData.price_gp" label="Price GP" placeholder="0.00" type="number"
                    step="0.01" min="0" :rules="rules.price_gp" prepend-inner-icon="tabler-credit-card"
                    variant="outlined" />
                </VCol>

                <VCol cols="12" md="4">
                  <AppTextField v-model="formData.price" label="Base Price" placeholder="0.00" type="number" step="0.01"
                    min="0" :rules="rules.price" prepend-inner-icon="tabler-coins" variant="outlined" />
                </VCol>

                <VCol cols="12">
                  <AppTextField v-model="formData.total_price" label="Total Price" :placeholder="formatCurrency(0)"
                    type="number" step="0.01" :rules="rules.total_price" prepend-inner-icon="tabler-calculator"
                    readonly />
                </VCol>
              </VRow>

              <!-- Additional Information Section -->
              <VDivider class="my-6" />
              <h6 class="text-h6 mb-4">Additional Information</h6>
              <VRow>
                <VCol cols="12">
                  <AppTextField v-model="formData.anchor_text" label="Anchor Text"
                    placeholder="Enter the anchor text for the link" :rules="rules.anchor_text"
                    prepend-inner-icon="tabler-anchor" variant="outlined" />
                </VCol>

                <VCol cols="12">
                  <AppTextarea v-model="formData.special_requirements" label="Special Requirements"
                    placeholder="Enter any special requirements or notes..." :rules="rules.special_requirements"
                    rows="4" variant="outlined" counter="500" />
                </VCol>
              </VRow>
            </VCardText>
          </VCard>
        </div>

        <!-- Navigation Footer -->
        <div class="drawer-footer sticky-footer">
          <VDivider />
          <div class="pa-6">
            <div class="d-flex justify-space-between align-center">
              <VBtn v-if="currentStep > 1" variant="outlined" color="default" @click="prevStep">
                <VIcon icon="tabler-arrow-left" class="me-2" />
                Previous
              </VBtn>
              <VSpacer v-else />

              <div class="d-flex gap-3">
                <VBtn v-if="currentStep < totalSteps" color="primary" @click="nextStep">
                  Next
                  <VIcon icon="tabler-arrow-right" class="ms-2" />
                </VBtn>

                <VBtn v-else type="submit" color="success" :loading="isSubmitting" :disabled="showSuccessAlert">
                  <VIcon icon="tabler-check" class="me-2" />
                  {{ isEdit ? 'Update Domain' : 'Create Domain' }}
                </VBtn>

                <VBtn variant="outlined" color="error" @click="handleDrawerClose" :disabled="isSubmitting">
                  Cancel
                </VBtn>
              </div>
            </div>
          </div>
        </div>
      </VForm>
    </div>
  </VNavigationDrawer>
</template>

<style scoped>
.drawer-header {
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.drawer-content {
  height: calc(100vh - 280px);
  overflow-y: auto;
  scrollbar-width: thin;
}

.drawer-content::-webkit-scrollbar {
  width: 6px;
}

.drawer-content::-webkit-scrollbar-track {
  background: transparent;
}

.drawer-content::-webkit-scrollbar-thumb {
  background-color: rgba(0, 0, 0, 0.2);
  border-radius: 3px;
}

.sticky-footer {
  position: sticky;
  bottom: 0;
  background: rgb(var(--v-theme-surface));
  z-index: 1;
  box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.1);
}

:deep(.v-navigation-drawer) {
  overflow-y: hidden;
}

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
  opacity: 0;
  transform: translateX(30px);
}

.v-leave-to {
  opacity: 0;
  transform: translateX(-30px);
}
</style>
