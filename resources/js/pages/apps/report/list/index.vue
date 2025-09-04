<script setup>

const headers = [
  { title: 'ID', key: 'id' },
  { title: 'Run ID', key: 'run_id' },
  { title: 'Run At', key: 'run_at' },
  { title: 'Domain Count', key: 'domain_count' },
  { title: 'Total Backlinks', key: 'total_backlink' },
  { title: 'Accepted Backlinks', key: 'accepted_backlinks' },
  { title: 'Rejected Backlinks', key: 'rejected_backlinks' },
  { title: 'Actions', key: 'actions', sortable: false },
]

// Filters (example)
const selectedStatus = ref()
const searchQuery = ref('')
const selectedRows = ref([])

// Data table options
const itemsPerPage = ref(10)
const page = ref(1)
const sortBy = ref()
const orderBy = ref()

const {
  data: reportData,
  execute: fetchReports,
} = await useReportIndex()

// const updateOptions = options => {
//   sortBy.value = options.sortBy[0]?.key
//   orderBy.value = options.sortBy[0]?.order
//   fetchReports()
// }

const reports = computed(() => reportData.value?.data.resource ?? [])
const totalReports = computed(() => reportData.value?.total ?? 0)

const deleteReport = async id => {
  await useReportDelete(id)
  const index = selectedRows.value.findIndex(row => row == id)
  if (index !== -1) selectedRows.value.splice(index, 1)
  fetchReports()
}
</script>

<template>
  <VCard title="Reports" class="mb-6">
    <VDivider class="mb-4" />

    <!-- Filters -->
    <VCardText>
      <VRow>
        <VCol cols="12" sm="4">
          <AppSelect v-model="selectedStatus" placeholder="Status" :items="[
            { title: 'Accepted', value: 'accepted' },
            { title: 'Rejected', value: 'rejected' }
          ]" clearable clear-icon="tabler-x" />
        </VCol>

        <VCol cols="12" sm="4">
          <AppTextField v-model="searchQuery" placeholder="Search Reports" clearable />
        </VCol>
      </VRow>
    </VCardText>

    <VDivider class="mt-4" />

    <!-- Data Table -->
    <VDataTableServer v-model:items-per-page="itemsPerPage" v-model:model-value="selectedRows" v-model:page="page"
      :headers="headers" show-select :items="reports" :items-length="totalReports" class="text-no-wrap"
      @update:options="updateOptions">
      <!-- Custom cells -->
      <template #item.status="{ item }">
        <VChip :color="item.status === 'accepted' ? 'success' : 'error'" label size="small">
          {{ item.status }}
        </VChip>
      </template>

      <template #item.key_highlights="{ item }">
        <div class="d-flex flex-column ga-1">
          <span v-for="(highlight, idx) in (Array.isArray(item.key_highlights) ? item.key_highlights : [])" :key="idx">
            - {{ highlight }}
          </span>
        </div>
      </template>

      <template #item.actions="{ item }">
        <router-link :to="{ name: 'apps-report-view', params: { id: item.id } }">
          <IconBtn>
            <VIcon icon="tabler-eye" />
          </IconBtn>
        </router-link>
      </template>

      <!-- Pagination -->
      <template #bottom>
        <TablePagination v-model:page="page" :items-per-page="itemsPerPage" :total-items="totalReports" />
      </template>
    </VDataTableServer>
  </VCard>
</template>
