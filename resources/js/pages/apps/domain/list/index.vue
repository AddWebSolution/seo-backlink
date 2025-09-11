<script setup>
import { useDomainDelete, useDomainIndex } from '@/composables/domainApi.js'
import { IconWorldWww } from '@tabler/icons-vue';

const headers = [
  { title: 'ID', key: 'id', width: '80px' },
  { title: 'Client ID', key: 'client_id', width: '100px' },
  { title: 'Title', key: 'title', width: '200px' },
  { title: 'Target URL', key: 'target_url', width: '250px' },
  { title: 'DA', key: 'domain_authority', width: '80px' },
  { title: 'DR', key: 'domain_rating', width: '80px' },
  { title: 'Traffic', key: 'organic_traffic', width: '100px' },
  { title: 'Price', key: 'total_price', width: '100px' },
  { title: 'Turnaround', key: 'turnaround_time', width: '120px' },
  { title: 'Status', key: 'status', width: '120px' },
  { title: 'Approval', key: 'approval_status', width: '120px' },
  { title: 'Country', key: 'country', width: '100px' },
  { title: 'Actions', key: 'actions', sortable: false, width: '100px' },
]

// Filters
const selectedStatus = ref()
const selectedApprovalStatus = ref()
const selectedCountry = ref()
const searchQuery = ref('')
const selectedRows = ref([])
const showAdvancedFilters = ref(false)

// Data table options
const itemsPerPage = ref(10)
const page = ref(1)
const sortBy = ref()
const orderBy = ref()

// Clear all filters
const clearAllFilters = () => {
  selectedStatus.value = null
  selectedApprovalStatus.value = null
  selectedCountry.value = ''
  searchQuery.value = ''
  fetchDomains()
}

// Check if any filters are active
const hasActiveFilters = computed(() => {
  return selectedStatus.value || selectedApprovalStatus.value || selectedCountry.value || searchQuery.value
})

// Update options from table
const updateOptions = options => {
  sortBy.value = options.sortBy[0]?.key
  orderBy.value = options.sortBy[0]?.order
  fetchDomains()
}

// API call
const {
  data: domainsData,
  execute: fetchDomains,
} = await useDomainIndex()

const domains = computed(() => domainsData.value?.data.resource ?? [])

const totalDomains = domains.value?.length ?? 0;

const deleteDomain = async id => {
  await useDomainDelete(id)
  const index = selectedRows.value.findIndex(row => row === id)
  if (index !== -1) selectedRows.value.splice(index, 1)
  fetchDomains()
}

// Status options
const statusOptions = [
  { title: 'Available', value: 1, color: 'success' },
  { title: 'Unavailable', value: 2, color: 'error' }
]

const approvalStatusOptions = [
  { title: 'Pending', value: 1, color: 'warning' },
  { title: 'Rejected', value: 2, color: 'error' },
  { title: 'Approved', value: 3, color: 'success' }
]
</script>

<template>
  <!-- Header Section -->
  <VCard class="mb-6 pa-6 overflow-hidden" elevation="0">
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
                <h1 class="text-h3 font-weight-bold mb-1">Domain Management</h1>
                <p class="text-body-1 text-medium-emphasis mb-0">
                   Manage and monitor your domain portfolio
                </p>
              </div>
          </div>
        </VCol>
        <VCol cols="12" md="4" class="text-md-end">
          <VBtn 
            variant="outlined" 
            size="large"
            class="text-primary font-weight-medium"
            :to="{ name: 'apps-domain-add' }"
          >
            <VIcon icon="tabler-plus" class="me-2" />
            Add Domain
          </VBtn>
        </VCol>
      </VRow>
    </VContainer>
  </VCard>

  <VContainer fluid>
    <!-- Enhanced Search & Filter Section -->
    <VCard class="mb-6" elevation="1">
      <VCardTitle class="d-flex align-center justify-space-between pa-6 pb-4">
        <div class="d-flex align-center">
          <VIcon icon="tabler-filter" class="me-2 text-primary" />
          <span class="text-h6 font-weight-medium">Search & Filters</span>
          <VBadge 
            v-if="hasActiveFilters" 
            :content="1" 
            color="primary" 
            class="ml-2"
          />
        </div>
        <div class="d-flex align-center gap-2">
          <VBtn
            v-if="hasActiveFilters"
            variant="text"
            size="small"
            color="error"
            @click="clearAllFilters"
          >
            <VIcon icon="tabler-x" class="me-1" />
            Clear All
          </VBtn>
          <VBtn
            variant="text"
            size="small"
            @click="showAdvancedFilters = !showAdvancedFilters"
          >
            <VIcon :icon="showAdvancedFilters ? 'tabler-chevron-up' : 'tabler-chevron-down'" class="me-1" />
            {{ showAdvancedFilters ? 'Less' : 'More' }} Filters
          </VBtn>
        </div>
      </VCardTitle>
      
      <VCardText class="pt-0">
        <!-- Primary Search Bar -->
        <VRow class="mb-4">
          <VCol cols="12">
            <AppTextField
              v-model="searchQuery"
              placeholder="Search by title, URL, or any domain details..."
              prepend-inner-icon="tabler-search"
              variant="outlined"
              hide-details
              clearable
              class="search-field"
            />
          </VCol>
        </VRow>

        <!-- Quick Filters -->
        <VRow class="mb-4">
          <VCol cols="12" sm="6" md="3">
            <AppSelect
              v-model="selectedStatus"
              label="Status"
              :items="statusOptions"
              variant="outlined"
              clearable
              hide-details
              prepend-inner-icon="tabler-circle-dot"
            />
          </VCol>
          <VCol cols="12" sm="6" md="3">
            <AppSelect
              v-model="selectedApprovalStatus"
              label="Approval Status"
              :items="approvalStatusOptions"
              variant="outlined"
              clearable
              hide-details
              prepend-inner-icon="tabler-check"
            />
          </VCol>
          <VCol cols="12" sm="6" md="3">
            <AppTextField
              v-model="selectedCountry"
              label="Country"
              placeholder="Enter country"
              variant="outlined"
              clearable
              hide-details
              prepend-inner-icon="tabler-world"
            />
          </VCol>
          <VCol cols="12" sm="6" md="3" class="d-flex align-end">
            <VBtn
              color="primary"
              variant="flat"
              block
              @click="fetchDomains"
            >
              <VIcon icon="tabler-search" class="me-2" />
              Search
            </VBtn>
          </VCol>
        </VRow>

        <!-- Advanced Filters -->
        <VExpandTransition>
          <div v-show="showAdvancedFilters">
            <VDivider class="mb-4" />
            <VRow>
              <VCol cols="12" sm="6" md="3">
                <AppTextField
                  label="Min Domain Authority"
                  placeholder="0-100"
                  variant="outlined"
                  type="number"
                  hide-details
                  prepend-inner-icon="tabler-trending-up"
                />
              </VCol>
              <VCol cols="12" sm="6" md="3">
                <AppTextField
                  label="Max Price"
                  placeholder="Enter max price"
                  variant="outlined"
                  type="number"
                  hide-details
                  prepend-inner-icon="tabler-currency-dollar"
                />
              </VCol>
              <VCol cols="12" sm="6" md="3">
                <AppTextField
                  label="Min Traffic"
                  placeholder="Monthly visits"
                  variant="outlined"
                  type="number"
                  hide-details
                  prepend-inner-icon="tabler-eye"
                />
              </VCol>
              <VCol cols="12" sm="6" md="3">
                <AppTextField
                  label="Turnaround Time"
                  placeholder="Max days"
                  variant="outlined"
                  type="number"
                  hide-details
                  prepend-inner-icon="tabler-clock"
                />
              </VCol>
            </VRow>
          </div>
        </VExpandTransition>
      </VCardText>
    </VCard>

    <!-- Results Summary -->
    <div class="d-flex justify-space-between align-center mb-4">
      <div class="text-body-1">
        <span class="font-weight-medium">{{ totalDomains }}</span> domains found
        <VChip v-if="selectedRows.length" color="primary" size="small" class="ml-2">
          {{ selectedRows.length }} selected
        </VChip>
      </div>
      <div class="d-flex align-center gap-2">
        <VBtn
          v-if="selectedRows.length"
          variant="text"
          size="small"
          color="error"
        >
          <VIcon icon="tabler-trash" class="me-1" />
          Delete Selected
        </VBtn>
        <VBtn
          variant="text"
          size="small"
          prepend-icon="tabler-download"
        >
          Export
        </VBtn>
      </div>
    </div>

    <!-- Enhanced Data Table -->
    <VCard elevation="1">
      <VDataTableServer 
        v-model:items-per-page="itemsPerPage" 
        v-model:model-value="selectedRows" 
        v-model:page="page"
        :headers="headers" 
        show-select 
        :items="domains" 
        :items-length="totalDomains" 
        class="domain-table"
        hover
        @update:options="updateOptions"
      >
        <!-- Custom Header -->
        <template #header.data-table-select="{ props }">
          <VCheckbox v-bind="props" color="primary" />
        </template>

        <!-- Custom cells -->
        <template #item.data-table-select="{ item, isSelected, toggleSelect }">
          <VCheckbox 
            :model-value="isSelected({ value: item.id })"
            color="primary"
            @update:model-value="toggleSelect({ value: item.id })"
          />
        </template>

        <template #item.title="{ item }">
          <div class="text-truncate" style="max-width: 180px;" :title="item.title">
            <span class="font-weight-medium">{{ item.title }}</span>
          </div>
        </template>

        <template #item.target_url="{ item }">
          <div class="text-truncate" style="max-width: 200px;">
            <VTooltip>
              <template #activator="{ props }">
                <a 
                  v-bind="props"
                  :href="item.target_url" 
                  target="_blank" 
                  class="text-decoration-none text-primary"
                >
                  {{ item.target_url }}
                </a>
              </template>
              <span>{{ item.target_url }}</span>
            </VTooltip>
          </div>
        </template>

        <template #item.domain_authority="{ item }">
          <div class="d-flex align-center">
            <VProgressCircular 
              :model-value="item.domain_authority" 
              size="24" 
              width="3"
              :color="item.domain_authority > 70 ? 'success' : item.domain_authority > 40 ? 'warning' : 'error'"
            />
            <span class="ml-2 font-weight-medium">{{ item.domain_authority }}</span>
          </div>
        </template>

        <template #item.domain_rating="{ item }">
          <div class="d-flex align-center">
            <VProgressCircular 
              :model-value="item.domain_rating" 
              size="24" 
              width="3"
              :color="item.domain_rating > 70 ? 'success' : item.domain_rating > 40 ? 'warning' : 'error'"
            />
            <span class="ml-2 font-weight-medium">{{ item.domain_rating }}</span>
          </div>
        </template>

        <template #item.organic_traffic="{ item }">
          <div class="d-flex align-center">
            <VIcon icon="tabler-eye" size="16" class="me-1 text-medium-emphasis" />
            <span class="font-weight-medium">{{ item.organic_traffic?.toLocaleString() || 'N/A' }}</span>
          </div>
        </template>

        <template #item.total_price="{ item }">
          <div class="d-flex align-center">
            <VIcon icon="tabler-currency-dollar" size="16" class="me-1 text-success" />
            <span class="font-weight-bold text-success">${{ item.total_price }}</span>
          </div>
        </template>

        <template #item.turnaround_time="{ item }">
          <VChip 
            size="small" 
            :color="item.turnaround_time <= 3 ? 'success' : item.turnaround_time <= 7 ? 'warning' : 'error'"
            variant="tonal"
          >
            {{ item.turnaround_time }}d
          </VChip>
        </template>

        <template #item.status="{ item }">
          <VChip 
            :color="item.status === 1 ? 'success' : 'error'" 
            variant="flat"
            size="small"
            class="font-weight-medium"
          >
            {{ item.status === 1 ? 'Available' : 'Unavailable' }}
          </VChip>
        </template>

        <template #item.approval_status="{ item }">
          <VChip 
            :color="item.approval_status === 1
              ? 'warning'
              : item.approval_status === 2
                ? 'error'
                : 'success'" 
            variant="flat"
            size="small"
            class="font-weight-medium"
          >
            {{ item.approval_status === 1 ? 'Pending' : item.approval_status === 2 ? 'Rejected' : 'Approved' }}
          </VChip>
        </template>

        <template #item.country="{ item }">
          <div class="d-flex align-center">
            <VIcon icon="tabler-world" size="16" class="me-1 text-medium-emphasis" />
            <span>{{ item.country }}</span>
          </div>
        </template>

        <template #item.actions="{ item }">
          <div class="d-flex align-center">
            <VTooltip text="View Details">
              <template #activator="{ props }">
                <IconBtn v-bind="props" size="small">
                  <router-link :to="{ name: 'apps-domain-view', params: { id: item.id } }">
                    <VIcon icon="tabler-eye" size="24" />
                  </router-link>
                </IconBtn>
              </template>
            </VTooltip>

            <VTooltip text="More Options">
              <template #activator="{ props }">
                <IconBtn v-bind="props" size="small">
                  <VIcon icon="tabler-dots-vertical" size="18" />
                  <VMenu activator="parent" offset="8">
                    <VList>
                      <VListItem 
                        value="edit" 
                        prepend-icon="tabler-edit"
                        class="text-primary"
                      >
                        Edit
                      </VListItem>
                      <VListItem 
                        value="duplicate" 
                        prepend-icon="tabler-copy"
                        class="text-info"
                      >
                        Duplicate
                      </VListItem>
                      <VDivider />
                      <VListItem 
                        value="delete" 
                        prepend-icon="tabler-trash" 
                        class="text-error"
                        @click="deleteDomain(item.id)"
                      >
                        Delete
                      </VListItem>
                    </VList>
                  </VMenu>
                </IconBtn>
              </template>
            </VTooltip>
          </div>
        </template>

        <!-- Enhanced Pagination -->
        <template #bottom>
          <div class="d-flex justify-space-between align-center pa-4 border-t">
            <div class="text-body-2 text-medium-emphasis">
              Showing {{ ((page - 1) * itemsPerPage) + 1 }} to {{ Math.min(page * itemsPerPage, totalDomains) }} of {{ totalDomains }} entries
            </div>
            <TablePagination 
              v-model:page="page" 
              :items-per-page="itemsPerPage" 
              :total-items="totalDomains" 
            />
          </div>
        </template>

        <!-- Empty State -->
        <template #no-data>
          <div class="text-center pa-8">
            <VIcon icon="tabler-world-off" size="48" class="text-medium-emphasis mb-4" />
            <h3 class="text-h6 mb-2">No domains found</h3>
            <p class="text-body-2 text-medium-emphasis mb-4">
              Try adjusting your search criteria or add a new domain to get started.
            </p>
            <VBtn color="primary" :to="{ name: 'apps-domain-add' }">
              <VIcon icon="tabler-plus" class="me-2" />
              Add First Domain
            </VBtn>
          </div>
        </template>
      </VDataTableServer>
    </VCard>
  </VContainer>
</template>

<style lang="scss" scoped>
// Enhanced search field
.search-field {
  .v-input__control {
    border-radius: 12px;
  }
}

// Domain table improvements
.domain-table {
  .v-data-table__wrapper {
    overflow-x: auto;
    border-radius: 8px;
    
    table {
      min-width: 1200px;
      
      th {
        background-color: rgb(var(--v-theme-surface));
        font-weight: 600;
        font-size: 0.875rem;
        border-bottom: 2px solid rgb(var(--v-theme-primary)) !important;
      }
      
      tbody tr {
        transition: all 0.2s ease;
        
        &:hover {
          background-color: rgba(var(--v-theme-primary), 0.04) !important;
          transform: translateY(-1px);
          box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
      }
      
      td {
        padding: 16px 12px;
        vertical-align: middle;
        border-bottom: 1px solid rgba(var(--v-theme-on-surface), 0.12);
      }
    }
  }
}

// Responsive improvements
@media (max-width: 1024px) {
  .domain-table table {
    min-width: 1000px;
  }
}

@media (max-width: 768px) {
  .domain-table {
    .v-data-table__wrapper table {
      min-width: 800px;
      
      th, td {
        padding: 12px 8px;
        font-size: 0.8rem;
      }
    }
  }
  
  .v-card {
    margin: 0.5rem;
  }
}

// Custom scrollbar
.v-data-table__wrapper::-webkit-scrollbar {
  height: 6px;
}

.v-data-table__wrapper::-webkit-scrollbar-track {
  background: rgb(var(--v-theme-surface-variant));
  border-radius: 3px;
}

.v-data-table__wrapper::-webkit-scrollbar-thumb {
  background: rgb(var(--v-theme-primary));
  border-radius: 3px;
}

// Progress circles
.v-progress-circular {
  margin: 0 !important;
}

// Chip improvements
.v-chip {
  font-size: 0.75rem;
  height: 24px;
}
</style>