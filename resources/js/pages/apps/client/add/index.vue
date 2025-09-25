<script setup>
import { useDomainApi } from '@/composables/domainApi';
import { useRouter } from 'vue-router'
import { IconWorldWww, IconDevicesCog, IconWorldMinus, IconUnlink, IconSeo, IconTag } from '@tabler/icons-vue';
import { ref, reactive, watch, onMounted } from 'vue'

const router = useRouter()

const { createDomain, loading, error, showAlert } = useDomainApi()

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

const state = reactive({
  formTouched: false,
  expandedSections: [true, true, true, true, true], // All sections expanded by default
})

// Refs
const formRef = ref()

const draftSaved = ref(false)
const lastSavedTime = ref(null)

watch(form, debounce(() => {
  if (state.formTouched) {
    saveDraft()
  }
}, 2000), { deep: true })

const saveDraft = () => {
  localStorage.setItem('domain_draft', JSON.stringify(form.value))
  draftSaved.value = true
  lastSavedTime.value = new Date().toLocaleTimeString()
  setTimeout(() => {
    draftSaved.value = false
  }, 2000)
}

onMounted(() => {
  const draft = localStorage.getItem('domain_draft')
  if (draft) {
    const shouldLoad = confirm('A draft was found. Would you like to restore it?')
    if (shouldLoad) {
      Object.assign(form.value, JSON.parse(draft))
    }
  }
})

const markFormTouched = () => {
  state.formTouched = true
}

const toggleSection = (index) => {
  state.expandedSections[index] = !state.expandedSections[index]
}

const statusOptions = [
  { 
    title: 'Available', 
    value: 1, 
    color: 'success', 
    icon: 'tabler-circle-check',
    description: 'Domain is active and ready for use'
  },
  { 
    title: 'Unavailable', 
    value: 2, 
    color: 'error', 
    icon: 'tabler-circle-x',
    description: 'Domain is currently not available'
  },
]

const approvalStatusOptions = [
  { 
    title: 'Pending', 
    value: 1, 
    color: 'warning', 
    icon: 'tabler-clock-hour-3', 
    description: 'Awaiting review from administrator'
  },
  { 
    title: 'Rejected', 
    value: 2, 
    color: 'error', 
    icon: 'tabler-circle-x', 
    description: 'Domain has been rejected'
  },
  { 
    title: 'Approved', 
    value: 3, 
    color: 'success', 
    icon: 'tabler-circle-check',
    description: 'Domain has been approved'
  },
]

// Country options
const countryOptions = [
  'United States',
  'United Kingdom', 
  'Canada',
  'Australia',
  'Germany',
  'France',
  'Japan',
  'India',
  'China',
  'Brazil',
  'Mexico',
  'Spain',
  'Italy',
  'Netherlands',
  'Sweden',
  'Switzerland',
  'Singapore',
  'South Korea',
  'UAE',
  'Saudi Arabia'
]

// Validation rules
const rules = {
  required: v => !!v || 'This field is required',
  url: v => !v || /^https?:\/\/.+\..+/.test(v) || 'Must be a valid URL',
  email: v => !v || /.+@.+\..+/.test(v) || 'Must be a valid email',
  number: v => !v || /^\d*\.?\d+$/.test(v) || 'Must be a valid number',
  positiveNumber: v => !v || (parseFloat(v) >= 0) || 'Must be 0 or greater',
  maxLength: max => v => !v || v.length <= max || `Maximum ${max} characters`,
  minLength: min => v => !v || v.length >= min || `Minimum ${min} characters`,
  domainAuthority: [
    v => !v || (v >= 0 && v <= 100) || 'Must be between 0 and 100',
    v => !v || Number.isInteger(Number(v)) || 'Must be a whole number',
  ],
  domainRating: [
    v => !v || (v >= 0 && v <= 100) || 'Must be between 0 and 100',
    v => !v || Number.isInteger(Number(v)) || 'Must be a whole number',
  ],
}

const submitForm = async () => {
  if (!formRef.value) return

  const { valid } = await formRef.value.validate()
  if (!valid) {
    showAlert('Please fix all validation errors before submitting', 'error')
    const errorElement = document.querySelector('.v-input--error')
    if (errorElement) {
      errorElement.scrollIntoView({ behavior: 'smooth', block: 'center' })
    }
    return
  }

  try {
    const payload = { ...form.value }

    const numericFields = ['domain_authority', 'domain_rating', 'organic_traffic', 'price_ne', 'price_gp', 'price', 'total_price']
    numericFields.forEach(field => {
      if (payload[field] === '') payload[field] = null
    })

    await createDomain(payload)
    
    localStorage.removeItem('domain_draft')
    
    setTimeout(() => {
      router.push({ name: 'apps-domain-list' })
    }, 1500)

  } catch (err) {
    console.error('Create domain failed:', err)
    
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const resetForm = () => {
  formRef.value?.reset()
  localStorage.removeItem('domain_draft')
  state.formTouched = false
}

watch([() => form.value.price_ne, () => form.value.price_gp, () => form.value.price],
  ([priceNe, priceGp, price]) => {
    const ne = parseFloat(priceNe) || 0
    const gp = parseFloat(priceGp) || 0
    const p = parseFloat(price) || 0
    const total = ne + gp + p
    form.value.total_price = total > 0 ? total.toFixed(2) : null
  })

const formatCurrency = (value) => {
  if (!value || value === '0') return '$0.00'
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(value)
}

const getMetricColor = (value, max = 100) => {
  const percentage = (value / max) * 100
  if (percentage >= 70) return 'success'
  if (percentage >= 40) return 'warning'
  return 'error'
}

function debounce(func, wait) {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

const scrollToSection = (sectionId) => {
  const element = document.getElementById(sectionId)
  if (element) {
    element.scrollIntoView({ behavior: 'smooth', block: 'start' })
  }
}
</script>

<template>
  <div class="domain-create-page">
    <!-- Floating Save Indicator -->
    <VSlideYTransition>
      <VChip
        v-if="draftSaved"
        color="success"
        variant="elevated"
        size="small"
        class="save-indicator"
      >
        <VIcon icon="mdi-check" size="12" class="me-1" />
        Draft saved at {{ lastSavedTime }}
      </VChip>
    </VSlideYTransition>

    <!-- Page Header -->
     <VCard class="mb-10 pa-8 overflow-hidden" elevation="2">
      <VContainer fluid>
        <VRow align="center">
          <VCol cols="12" md="8">
            <div class="d-flex align-center">
              <VAvatar
                size="64"
                color="primary"
                variant="elevated"
                class="me-4"
              >
                <IconWorldWww stroke={2} />
              </VAvatar>
              <div>
                <h1 class="text-h3 font-weight-bold mb-1">Create New Client</h1>
                <p class="text-body-1 text-medium-emphasis mb-0">
                  Fill in the domain details below to add a new entry to the system
                </p>
              </div>
            </div>
          </VCol>
          <VCol cols="12" md="4" class="text-md-end">
            <div class="d-flex gap-2 justify-md-end">
              <VBtn
                color="primary"
                variant="flat"
                @click="resetForm"
                :disabled="state.submitting"
              >
                <VIcon icon="tabler-refresh" class="me-2" />
                Reset
              </VBtn>
              <VBtn
                variant="flat"
                @click="router.push({ name: 'apps-domain-list' })"
                :disabled="state.submitting"
              >
                 <VIcon icon="tabler-arrow-left" class="me-2" />
                Back to List
              </VBtn>
            </div>
          </VCol>
        </VRow>

        <!-- Quick Navigation -->
        <!-- <VCard variant="tonal" color="primary" class="mt-6">
          <VCardText class="pa-3">
            <div class="d-flex align-center flex-wrap gap-2">
              <span class="text-body-2 font-weight-medium me-2">Quick Navigation:</span>
              <VChip
                v-for="(section, index) in ['Basic Info', 'Status', 'URLs', 'SEO Metrics', 'Pricing']"
                :key="index"
                size="small"
                variant="elevated"
                color="white"
                @click="scrollToSection(`section-${index + 1}`)"
                class="cursor-pointer"
              >
                {{ section }}
              </VChip>
            </div>
          </VCardText>
        </VCard> -->
      </VContainer>
    </VCard>

    <!-- Main Form Container -->
      <VSlideYTransition>
        <VAlert
          v-if="state.showSuccessAlert"
          type="success"
          variant="elevated"
          class="mb-6"
          closable
          @click:close="state.showSuccessAlert = false"
        >
          <template v-slot:prepend>
            <VIcon icon="mdi-check-circle" />
          </template>
          <VAlertTitle>Success!</VAlertTitle>
          Domain has been created successfully. Redirecting to domain list...
        </VAlert>
      </VSlideYTransition>

      <VSlideYTransition>
        <VAlert
          v-if="state.errorMessage"
          type="error"
          variant="tonal"
          class="mb-6"
          closable
          @click:close="state.errorMessage = ''"
        >
          <template v-slot:prepend>
            <VIcon icon="mdi-alert-circle" />
          </template>
          {{ state.errorMessage }}
        </VAlert>
      </VSlideYTransition>

      <VForm ref="formRef" @submit.prevent="submitForm">
        <!-- Section 1: Basic Information -->
        <VCard id="section-1" class="mb-6 section-card">
          <VCardTitle class="section-header">
            <div class="d-flex align-center justify-space-between">
              <div class="d-flex align-center">
                <VAvatar color="primary" variant="tonal" size="40" class="me-3">
                  <IconWorldMinus stroke={2} />
                </VAvatar>
                <div>
                  <h2 class="text-h5 font-weight-bold">Basic Information</h2>
                  <p class="text-body-2 text-medium-emphasis mb-0">Essential domain details and identification</p>
                </div>
              </div>
              <VBtn
                icon
                variant="text"
                size="small"
                @click="toggleSection(0)"
              >
                <VIcon :icon="state.expandedSections[0] ? 'mdi-chevron-up' : 'mdi-chevron-down'" />
              </VBtn>
            </div>
          </VCardTitle>

          <VExpandTransition>
            <div v-show="state.expandedSections[0]">
              <VDivider />
              <VCardText class="pa-6">
                <VRow>
                  <VCol cols="12" md="6">
                    <AppTextField
                      v-model="form.client_id"
                      label="Client ID"
                      placeholder="e.g., CLT-2024-001"
                      variant="outlined"
                      hint="Unique identifier for the client"
                      persistent-hint
                      @input="markFormTouched"
                    />
                  </VCol>

                  <VCol cols="12" md="6">
                    <AppTextField
                      v-model="form.title"
                      label="Domain Title *"
                      placeholder="Enter a descriptive title"
                      :rules="[rules.required, rules.minLength(3), rules.maxLength(100)]"
                      variant="outlined"
                      hint="Primary identifier for this domain"
                      persistent-hint
                      required
                      @input="markFormTouched"
                    />
                  </VCol>

                  <!-- <VCol cols="12" md="6">
                    <VAutocomplete
                      v-model="form.country"
                      label="Country"
                      :items="countryOptions"
                      prepend-inner-icon="mdi-flag"
                      variant="outlined"
                      hint="Target country for this domain"
                      persistent-hint
                      clearable
                      @update:model-value="markFormTouched"
                    />
                  </VCol> -->

                  <VCol cols="12" md="6">
                    <AppTextField
                      v-model="form.turnaround_time"
                      label="Turnaround Time"
                      placeholder="e.g., 5-7 business days"
                      variant="outlined"
                      hint="Expected delivery timeframe"
                      persistent-hint
                      @input="markFormTouched"
                    />
                  </VCol>
                </VRow>
              </VCardText>
            </div>
          </VExpandTransition>
        </VCard>

        <!-- Section 2: Status Configuration -->
        <VCard id="section-2" class="mb-6 section-card">
          <VCardTitle class="section-header">
            <div class="d-flex align-center justify-space-between">
              <div class="d-flex align-center">
                <VAvatar color="success" variant="tonal" size="40" class="me-3">
                <IconDevicesCog stroke={2} />
                </VAvatar>
                <div>
                  <h2 class="text-h5 font-weight-bold">Status Configuration</h2>
                  <p class="text-body-2 text-medium-emphasis mb-0">Set availability and approval status</p>
                </div>
              </div>
              <VBtn
                icon
                variant="text"
                size="small"
                @click="toggleSection(1)"
              >
                <VIcon :icon="state.expandedSections[1] ? 'mdi-chevron-up' : 'mdi-chevron-down'" />
              </VBtn>
            </div>
          </VCardTitle>

          <VExpandTransition>
            <div v-show="state.expandedSections[1]">
              <VDivider />
              <VCardText class="pa-6">
                <VRow>
                  <VCol cols="12" md="6">
                    <p class="text-body-2 font-weight-medium mb-3">Domain Status</p>
                    <VItemGroup v-model="form.status" mandatory>
                      <VRow>
                        <VCol
                          v-for="status in statusOptions"
                          :key="status.value"
                          cols="12"
                        >
                          <VItem :value="status.value" v-slot="{ isSelected, toggle }">
                            <VCard
                              :color="isSelected ? status.color : undefined"
                              :variant="isSelected ? 'elevated' : 'outlined'"
                              class="status-selection-card pa-4"
                              @click="toggle"
                            >
                              <div class="d-flex align-center">
                                <VAvatar
                                  :color="status.color"
                                  variant="tonal"
                                  size="36"
                                  class="me-3"
                                >
                                  <VIcon :icon="status.icon" size="20" />
                                </VAvatar>
                                <div class="flex-grow-1">
                                  <p class=" font-weight-medium mb-0">
                                    {{ status.title }}
                                  </p>
                                  <p class=" font-weight-medium mb-0">
                                    {{ status.description }}
                                  </p>
                                </div>
                                <VIcon
                                  :icon="isSelected ? 'mdi-radiobox-marked' : 'mdi-radiobox-blank'"
                                  :color="isSelected ? status.color : 'grey'"
                                />
                              </div>
                            </VCard>
                          </VItem>
                        </VCol>
                      </VRow>
                    </VItemGroup>
                  </VCol>

                  <VCol cols="12" md="6">
                    <p class="text-body-2 font-weight-medium mb-3">Approval Status</p>
                    <VItemGroup v-model="form.approval_status" mandatory>
                      <VRow>
                        <VCol
                          v-for="status in approvalStatusOptions"
                          :key="status.value"
                          cols="12"
                        >
                          <VItem :value="status.value" v-slot="{ isSelected, toggle }">
                            <VCard
                              :color="isSelected ? status.color : undefined"
                              :variant="isSelected ? 'elevated' : 'outlined'"
                              class="status-selection-card pa-4"
                              @click="toggle"
                            >
                              <div class="d-flex align-center">
                                <VAvatar
                                  :color="status.color"
                                  variant="tonal"
                                  size="36"
                                  class="me-3"
                                >
                                  <VIcon :icon="status.icon" size="20" />
                                </VAvatar>
                                <div class="flex-grow-1">
                                 <p class=" font-weight-medium mb-0">
                                    {{ status.title }}
                                  </p>
                                  <p class=" font-weight-medium mb-0">
                                    {{ status.description }}
                                  </p>
                                </div>
                                <VIcon
                                  :icon="isSelected ? 'mdi-radiobox-marked' : 'mdi-radiobox-blank'"
                                  :color="isSelected ? status.color : 'grey'"
                                />
                              </div>
                            </VCard>
                          </VItem>
                        </VCol>
                      </VRow>
                    </VItemGroup>
                  </VCol>
                </VRow>
              </VCardText>
            </div>
          </VExpandTransition>
        </VCard>

        <!-- Section 3: URL Management -->
        <VCard id="section-3" class="mb-6 section-card">
          <VCardTitle class="section-header">
            <div class="d-flex align-center justify-space-between">
              <div class="d-flex align-center">
                <VAvatar color="info" variant="tonal" size="40" class="me-3">
                  <IconUnlink stroke={2} />
                </VAvatar>
                <div>
                  <h2 class="text-h5 font-weight-bold">URL Management</h2>
                  <p class="text-body-2 text-medium-emphasis mb-0">Configure source and target URLs</p>
                </div>
              </div>
              <VBtn
                icon
                variant="text"
                size="small"
                @click="toggleSection(2)"
              >
                <VIcon :icon="state.expandedSections[2] ? 'mdi-chevron-up' : 'mdi-chevron-down'" />
              </VBtn>
            </div>
          </VCardTitle>

          <VExpandTransition>
            <div v-show="state.expandedSections[2]">
              <VDivider />
              <VCardText class="pa-6">
                <VRow>
                  <VCol cols="12">
                    <AppTextField
                      v-model="form.source_url"
                      label="Source URL"
                      placeholder="https://example.com/source-page"
                      :rules="[rules.url]"
                      variant="outlined"
                      hint="The URL where the link will be placed"
                      persistent-hint
                      @input="markFormTouched"
                    >
                      <template v-slot:append-inner>
                        <VTooltip location="top">
                          <template v-slot:activator="{ props }">
                            <VIcon v-bind="props" icon="mdi-information-outline" size="20" />
                          </template>
                          <span>This is the website that will link to your target</span>
                        </VTooltip>
                      </template>
                    </AppTextField>
                  </VCol>

                  <VCol cols="12">
                    <AppTextField
                      v-model="form.target_url"
                      label="Target URL*"
                      placeholder="https://target.com/destination-page"
                      :rules="[rules.url,rules.required]"
                      variant="outlined"
                      required
                      hint="The destination URL for the link"
                      persistent-hint
                      @input="markFormTouched"
                    >
                      <template v-slot:append-inner>
                        <VTooltip location="top">
                          <template v-slot:activator="{ props }">
                            <VIcon v-bind="props" icon="mdi-information-outline" size="20" />
                          </template>
                          <span>This is where users will be directed when clicking the link</span>
                        </VTooltip>
                      </template>
                    </AppTextField>
                  </VCol>
                </VRow>

                <!-- URL Preview -->
                <VSlideYTransition>
                  <VCard
                    v-if="form.source_url || form.target_url"
                    color="grey-lighten-5"
                    variant="flat"
                    class="mt-6"
                  >
                    <VCardText>
                      <p class="text-body-2 font-weight-medium mb-4">Link Flow Visualization</p>
                      
                      <div class="url-flow-visualization">
                        <div v-if="form.source_url" class="url-box source">
                          <VIcon icon="mdi-web" color="info" class="me-2" />
                          <div>
                            <p class="text-caption text-medium-emphasis mb-1">Source</p>
                            <a :href="form.source_url" target="_blank" class="text-decoration-none text-info">
                              {{ form.source_url }}
                            </a>
                          </div>
                        </div>

                        <div v-if="form.source_url && form.target_url" class="flow-arrow">
                          <VIcon icon="mdi-arrow-down-thick" size="24" color="primary" />
                        </div>

                        <div v-if="form.target_url" class="url-box target">
                          <VIcon icon="mdi-target" color="success" class="me-2" />
                          <div>
                            <p class="text-caption text-medium-emphasis mb-1">Target</p>
                            <a :href="form.target_url" target="_blank" class="text-decoration-none text-success">
                              {{ form.target_url }}
                            </a>
                          </div>
                        </div>
                      </div>
                    </VCardText>
                  </VCard>
                </VSlideYTransition>
              </VCardText>
            </div>
          </VExpandTransition>
        </VCard>

        <!-- Section 4: SEO Metrics -->
        <VCard id="section-4" class="mb-6 section-card">
          <VCardTitle class="section-header">
            <div class="d-flex align-center justify-space-between">
              <div class="d-flex align-center">
                <VAvatar color="warning" variant="tonal" size="40" class="me-3">
                 <IconSeo stroke={2} />
                </VAvatar>
                <div>
                  <h2 class="text-h5 font-weight-bold">SEO Metrics</h2>
                  <p class="text-body-2 text-medium-emphasis mb-0">Domain authority and performance indicators</p>
                </div>
              </div>
              <VBtn
                icon
                variant="text"
                size="small"
                @click="toggleSection(3)"
              >
                <VIcon :icon="state.expandedSections[3] ? 'mdi-chevron-up' : 'mdi-chevron-down'" />
              </VBtn>
            </div>
          </VCardTitle>

          <VExpandTransition>
            <div v-show="state.expandedSections[3]">
              <VDivider />
              <VCardText class="pa-6">
                <VRow>
                  <VCol cols="12" md="4">
                    <VCard variant="outlined" class="metric-input-card">
                      <VCardText>
                        <div class="d-flex align-center mb-3">
                          <VAvatar color="primary" variant="tonal" size="36" class="me-3">
                            <VIcon icon="tabler-award" size="32" />
                          </VAvatar>
                          <div>
                            <p class="text-body-2 font-weight-medium mb-0">Domain Authority</p>
                            <p class="text-caption text-medium-emphasis mb-0">MOZ metric (0-100)</p>
                          </div>
                        </div>
                        
                        <AppTextField
                          v-model="form.domain_authority"
                          type="number"
                          min="0"
                          max="100"
                          :rules="rules.domainAuthority"
                          variant="solo-filled"
                          placeholder="0"
                          class="metric-input"
                          @input="markFormTouched"
                        />
                        
                        <VProgressLinear
                          v-if="form.domain_authority"
                          :model-value="form.domain_authority"
                          max="100"
                          :color="getMetricColor(form.domain_authority)"
                          height="6"
                          rounded
                          class="mt-3"
                        />
                      </VCardText>
                    </VCard>
                  </VCol>

                  <VCol cols="12" md="4">
                    <VCard variant="outlined" class="metric-input-card">
                      <VCardText>
                        <div class="d-flex align-center mb-3">
                          <VAvatar color="success" variant="tonal" size="36" class="me-3">
                             <VIcon icon="tabler-trending-up" size="32" />
                          </VAvatar>
                          <div>
                            <p class="text-body-2 font-weight-medium mb-0">Domain Rating</p>
                            <p class="text-caption text-medium-emphasis mb-0">Ahrefs metric (0-100)</p>
                          </div>
                        </div>
                        
                        <AppTextField
                          v-model="form.domain_rating"
                          type="number"
                          min="0"
                          max="100"
                          :rules="rules.domainRating"
                          variant="solo-filled"
                          placeholder="0"
                          class="metric-input"
                          @input="markFormTouched"
                        />
                        
                        <VProgressLinear
                          v-if="form.domain_rating"
                          :model-value="form.domain_rating"
                          max="100"
                          :color="getMetricColor(form.domain_rating)"
                          height="6"
                          rounded
                          class="mt-3"
                        />
                      </VCardText>
                    </VCard>
                  </VCol>

                  <VCol cols="12" md="4">
                    <VCard variant="outlined" class="metric-input-card">
                      <VCardText>
                        <div class="d-flex align-center mb-3">
                          <VAvatar color="info" variant="tonal" size="36" class="me-3">
                            <VIcon icon="tabler-users" size="32" />
                          </VAvatar>
                          <div>
                            <p class="text-body-2 font-weight-medium mb-0">Organic Traffic</p>
                            <p class="text-caption text-medium-emphasis mb-0">Monthly visitors</p>
                          </div>
                        </div>
                        
                        <AppTextField
                          v-model="form.organic_traffic"
                          type="number"
                          min="0"
                          :rules="[rules.number, rules.positiveNumber]"
                          variant="solo-filled"
                          placeholder="0"
                          class="metric-input"
                          @input="markFormTouched"
                        />
                      </VCardText>
                    </VCard>
                  </VCol>
                </VRow>

                <!-- Metrics Summary -->
                <VSlideYTransition>
                  <VCard
                    v-if="form.domain_authority || form.domain_rating || form.organic_traffic"
                    color="primary"
                    variant="tonal"
                    class="mt-6"
                  >
                    <VCardText>
                      <p class="text-body-1 font-weight-medium mb-4">SEO Performance Summary</p>
                      <div class="d-flex justify-space-around align-center">
                        <div v-if="form.domain_authority" class="text-center">
                          <VAvatar
                            :color="getMetricColor(form.domain_authority)"
                            size="72"
                            variant="elevated"
                            class="mb-2"
                          >
                            <span class="text-h5 font-weight-bold">{{ form.domain_authority }}</span>
                          </VAvatar>
                          <p class="text-body-2 mb-0">DA Score</p>
                        </div>
                        <div v-if="form.domain_rating" class="text-center">
                          <VAvatar
                            :color="getMetricColor(form.domain_rating)"
                            size="72"
                            variant="elevated"
                            class="mb-2"
                          >
                            <span class="text-h5 font-weight-bold">{{ form.domain_rating }}</span>
                          </VAvatar>
                          <p class="text-body-2 mb-0">DR Score</p>
                        </div>
                        <div v-if="form.organic_traffic" class="text-center">
                          <VAvatar
                            color="info"
                            size="72"
                            variant="elevated"
                            class="mb-2"
                          >
                            <span class="text-h6 font-weight-bold">{{ form.organic_traffic }}</span>
                          </VAvatar>
                          <p class="text-body-2 mb-0">Traffic</p>
                        </div>
                      </div>
                    </VCardText>
                  </VCard>
                </VSlideYTransition>
              </VCardText>
            </div>
          </VExpandTransition>
        </VCard>

        <!-- Section 5: Pricing & Details -->
        <VCard id="section-5" class="mb-6 section-card">
          <VCardTitle class="section-header">
            <div class="d-flex align-center justify-space-between">
              <div class="d-flex align-center">
                <VAvatar color="error" variant="tonal" size="40" class="me-3">
                  <IconTag stroke={2} />
                </VAvatar>
                <div>
                  <h2 class="text-h5 font-weight-bold">Pricing & Additional Details</h2>
                  <p class="text-body-2 text-medium-emphasis mb-0">Cost breakdown and special requirements</p>
                </div>
              </div>
              <VBtn
                icon
                variant="text"
                size="small"
                @click="toggleSection(4)"
              >
                <VIcon :icon="state.expandedSections[4] ? 'mdi-chevron-up' : 'mdi-chevron-down'" />
              </VBtn>
            </div>
          </VCardTitle>

          <VExpandTransition>
            <div v-show="state.expandedSections[4]">
              <VDivider />
              <VCardText class="pa-6">
                <!-- Pricing Grid -->
                <div class="pricing-section mb-6">
                  <div class="d-flex align-center justify-space-between mb-4">
                    <p class="text-body-1 font-weight-medium mb-0">Pricing Components</p>
                    <VChip
                      v-if="form.total_price > 0"
                      color="error"
                      variant="elevated"
                      size="large"
                      class="font-weight-bold"
                    >
                      Total: {{ formatCurrency(form.total_price) }}
                    </VChip>
                  </div>

                  <VRow>
                    <VCol cols="12" md="4">
                      <VCard variant="outlined">
                        <VCardText>
                          <AppTextField
                            v-model="form.price_ne"
                            label="Price NE"
                            type="number"
                            step="0.01"
                            min="0"
                            :rules="[rules.number, rules.positiveNumber]"
                            variant="solo-filled"
                            placeholder="0.00"
                            @input="markFormTouched"
                          />
                        </VCardText>
                      </VCard>
                    </VCol>

                    <VCol cols="12" md="4">
                      <VCard variant="outlined">
                        <VCardText>
                          <AppTextField
                            v-model="form.price_gp"
                            label="Price GP"
                            type="number"
                            step="0.01"
                            min="0"
                            :rules="[rules.number, rules.positiveNumber]"
                            variant="solo-filled"
                            placeholder="0.00"
                            @input="markFormTouched"
                          />
                        </VCardText>
                      </VCard>
                    </VCol>

                    <VCol cols="12" md="4">
                      <VCard variant="outlined">
                        <VCardText>
                          <AppTextField
                            v-model="form.price"
                            label="Base Price"
                            type="number"
                            step="0.01"
                            min="0"
                            :rules="[rules.number, rules.positiveNumber]"
                            variant="solo-filled"
                            placeholder="0.00"
                            @input="markFormTouched"
                          />
                        </VCardText>
                      </VCard>
                    </VCol>
                  </VRow>

                  <!-- Total Display -->
                  <VSlideYTransition>
                    <VCard
                      v-if="form.total_price > 0"
                      color="error"
                      variant="tonal"
                      class="mt-4"
                    >
                      <VCardText class="text-center pa-4">
                        <p class="text-body-2 mb-2">Total Price</p>
                        <p class="text-h3 font-weight-bold mb-0">{{ formatCurrency(form.total_price) }}</p>
                      </VCardText>
                    </VCard>
                  </VSlideYTransition>
                </div>

                <VDivider class="mb-6" />

                <!-- Additional Details -->
                <div class="additional-details">
                  <p class="text-body-1 font-weight-medium mb-4">Additional Information</p>
                  
                  <VRow>
                    <VCol cols="12">
                      <AppTextField
                        v-model="form.anchor_text"
                        label="Anchor Text"
                        placeholder="Enter the anchor text for the link"
                        :rules="[rules.maxLength(200)]"
                        variant="outlined"
                        hint="The clickable text for the hyperlink"
                        persistent-hint
                        @input="markFormTouched"
                      />
                    </VCol>

                    <VCol cols="12">
                      <AppTextarea
                        v-model="form.special_requirements"
                        label="Special Requirements"
                        placeholder="Enter any special requirements, notes, or instructions..."
                        :rules="[rules.maxLength(500)]"
                        variant="outlined"
                        rows="5"
                        counter
                        hint="Additional instructions or notes for domain setup"
                        persistent-hint
                        @input="markFormTouched"
                      />
                    </VCol>
                  </VRow>
                </div>
              </VCardText>
            </div>
          </VExpandTransition>
        </VCard>

        <!-- Form Actions -->
        <VCard class="action-card">
          <VCardText class="pa-6">
            <div class="d-flex justify-space-between align-center">
              <div>
                <p class="text-body-2 text-medium-emphasis mb-0">
                  <VIcon icon="mdi-information-outline" size="16" class="me-1" />
                  All fields marked with * are required
                </p>
              </div>
              
              <div class="d-flex gap-3">
                <VBtn
                  variant="outlined"
                  color="default"
                  size="large"
                  @click="resetForm"
                  :disabled="state.submitting"
                >
                  Reset Form
                </VBtn>
                
                <!-- <VBtn
                  variant="tonal"
                  color="primary"
                  size="large"
                  @click="saveDraft"
                  :disabled="state.submitting || !state.formTouched"
                >
                  <VIcon icon="mdi-content-save" class="me-2" />
                  Save Draft
                </VBtn> -->

                <VBtn
                  type="submit"
                  color="success"
                  size="large"
                  variant="elevated"
                  :loading="state.submitting"
                  :disabled="state.showSuccessAlert"
                >
                  Create Domain
                </VBtn>
              </div>
            </div>
          </VCardText>
        </VCard>
      </VForm>
  </div>
</template>

<style lang="scss" scoped>
.domain-create-page {
  position: relative;
  min-height: 100vh;
  padding-bottom: 2rem;
  
  .save-indicator {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1000;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }
  
  .page-header {
    background: white;
    padding: 1rem 0;
    border-radius:12px;
    margin-bottom: 2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  }
}

.section-card {
  transition: all 0.3s ease;
  border-radius: 12px !important;
  overflow: hidden;
  
  &:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
  }
  
  .section-header {
    background: linear-gradient(135deg, rgba(var(--v-theme-primary), 0.05) 0%, transparent 100%);
    padding: 1.5rem;
  }
}

.status-selection-card {
  cursor: pointer;
  transition: all 0.3s ease;
  margin-bottom: 1rem;
  
  &:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
  }
  
  &:last-child {
    margin-bottom: 0;
  }
}

.metric-input-card {
  height: 100%;
  transition: all 0.3s ease;
  
  &:hover {
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
  }
  
  .metric-input {
    :deep(.v-field__input) {
      font-size: 2rem;
      font-weight: 600;
      text-align: center;
      padding: 1rem;
    }
  }
}

.url-flow-visualization {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  align-items: center;
  
  .url-box {
    width: 100%;
    display: flex;
    align-items: center;
    padding: 1.25rem;
    border-radius: 8px;
    background: white;
    border: 2px solid rgba(var(--v-theme-primary), 0.1);
    transition: all 0.3s ease;
    
    &:hover {
      border-color: rgba(var(--v-theme-primary), 0.3);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    
    &.source {
      border-left: 4px solid rgb(var(--v-theme-info));
    }
    
    &.target {
      border-left: 4px solid rgb(var(--v-theme-success));
    }
  }
  
  .flow-arrow {
    padding: 0.5rem;
    animation: bounce 2s infinite;
  }
}

.pricing-section {
  .v-card {
    transition: all 0.3s ease;
    
    &:hover {
      transform: scale(1.02);
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
    }
  }
}

.v-card {
   box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;
}


.action-card {
  position: sticky;
  bottom: 0;
  background: white;
  border-radius: 12px !important;
  box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
  z-index: 10;
}

@keyframes bounce {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(5px);
  }
}

// Dark mode adjustments
.v-theme--dark {
  .domain-create-page {
    .page-header {
      background: rgba(0, 0, 0, 0.2);
    }
  }
  
  .section-card {
    .section-header {
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, transparent 100%);
    }
  }
  
  .url-flow-visualization {
    .url-box {
      background: rgba(255, 255, 255, 0.05);
      border-color: rgba(255, 255, 255, 0.1);
      
      &:hover {
        border-color: rgba(255, 255, 255, 0.3);
      }
    }
  }
  
  .action-card {
    background: rgba(0, 0, 0, 0.4);
  }
}

// Responsive adjustments
@media (max-width: 960px) {
  .domain-create-page {
    .page-header {
      .text-h3 {
        font-size: 1.75rem !important;
      }
    }
  }
  
  .section-card {
    .section-header {
      .text-h5 {
        font-size: 1.25rem !important;
      }
    }
  }
  
  .action-card {
    position: relative;
    
    .d-flex {
      flex-direction: column;
      gap: 1rem;
      
      .d-flex.gap-3 {
        width: 100%;
        
        .v-btn {
          flex: 1;
        }
      }
    }
  }
}

.cursor-pointer {
  cursor: pointer;
  
  &:hover {
    opacity: 0.8;
  }
}
</style>