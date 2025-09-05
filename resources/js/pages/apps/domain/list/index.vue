<script setup>
import { useDomainDelete, useDomainIndex } from '@/composables/domainApi.js'

const headers = [
  { title: 'ID', key: 'id' },
  { title: 'Client ID', key: 'client_id' },
  { title: 'Source URL', key: 'source_url' },
  { title: 'Title', key: 'title' },
  { title: 'Target URL', key: 'target_url' },
  { title: 'Domain Authority', key: 'domain_authority' },
  { title: 'Domain Rating', key: 'domain_rating' },
  { title: 'Organic Traffic', key: 'organic_traffic' },
  { title: 'Price NE', key: 'price_ne' },
  { title: 'Price GP', key: 'price_gp' },
  { title: 'Total Price', key: 'total_price' },
  { title: 'Turnaround Time', key: 'turnaround_time' },
  { title: 'Status', key: 'status' },
  { title: 'Approval Status', key: 'approval_status' },
  { title: 'Country', key: 'country' },
  { title: 'Anchor Text', key: 'anchor_text' },
  { title: 'Special Requirements', key: 'special_requirements' },
  { title: 'Price', key: 'price' },
  { title: 'Actions', key: 'actions', sortable: false },
]

// Filters
const selectedStatus = ref()
const selectedCountry = ref()
const searchQuery = ref('')
const selectedRows = ref([])

// Data table options
const itemsPerPage = ref(10)
const page = ref(1)
const sortBy = ref()
const orderBy = ref()


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
const totalDomains = computed(() => domainsData.value?.total ?? 0)

const deleteDomain = async id => {
  await useDomainDelete(id)
  const index = selectedRows.value.findIndex(row => row === id)
  if (index !== -1) selectedRows.value.splice(index, 1)
  fetchDomains()
}
</script>

<template>
  <VCard title="Domains" class="mb-6">
    <VDivider class="mb-4" />

    <!-- Filters -->
    <VCardText>
      <VRow>
        <VCol cols="12" md="3">
          <AppSelect v-model="selectedStatus" placeholder="Status" :items="[
            { title: 'Available', value: 1 },
            { title: 'Unavailable', value: 2 }
          ]" clearable clear-icon="tabler-x" />
        </VCol>

        <VCol cols="12" md="3">
          <AppTextField v-model="selectedCountry" placeholder="Country" clearable />
        </VCol>

        <VCol cols="12" md="3">
          <AppTextField v-model="searchQuery" placeholder="Search Domains" style="inline-size: 200px;" />
        </VCol>

        <VCol cols="12" md="3" class="d-flex justify-end">
               <VBtn color="primary" variant="outlined" class="text-primary"
              :to="{ name: 'apps-domain-add' }">
              <VIcon icon="tabler-arrow-right" class="me-2" />
              Create Domain
            </VBtn>
          </VCol>
      </VRow>
    </VCardText>

    <VDivider class="mt-4" />

    <!-- Data Table -->
    <VDataTableServer v-model:items-per-page="itemsPerPage" v-model:model-value="selectedRows" v-model:page="page"
      :headers="headers" show-select :items="domains" :items-length="totalDomains" class="text-no-wrap"
      @update:options="updateOptions">
      <!-- Custom cells -->
      <template #item.status="{ item }">
        <VChip :color="item.status === 1 ? 'success' : 'error'" label size="small">
          {{ item.status === 1 ? 'Available' : 'Unavailable' }}
        </VChip>
      </template>

      <template #item.approval_status="{ item }">
        <VChip :color="item.approval_status === 1
          ? 'warning'
          : item.approval_status === 2
            ? 'error'
            : 'success'" label size="small">
          {{ item.approval_status === 1 ? 'Pending' : item.approval_status === 2 ? 'Rejected' : 'Approved' }}
        </VChip>
      </template>

      <template #item.actions="{ item }">
        <router-link :to="{ name: 'apps-domain-view', params: { id: item.id } }">
          <IconBtn>
            <VIcon icon="tabler-eye" />
          </IconBtn>
        </router-link>

        <IconBtn>
          <VIcon icon="tabler-dots-vertical" />
          <VMenu activator="parent">
            <VList>
              <VListItem value="delete" prepend-icon="tabler-trash" @click="deleteDomain(item.id)">
                Delete
              </VListItem>
            </VList>
          </VMenu>
        </IconBtn>
      </template>

      <!-- Pagination -->
      <template #bottom>
        <TablePagination v-model:page="page" :items-per-page="itemsPerPage" :total-items="totalDomains" />
      </template>
    </VDataTableServer>
  </VCard>
</template>
