<script setup>
import { onMounted, ref, computed, unref } from "vue";
import { useDomainApi } from "@/composables/domainApi.js";
import { useClientApi } from "@/composables/clientApi";
import { IconWorldWww } from "@tabler/icons-vue";
import { VBtn } from "vuetify/components";
import { useRoute, useRouter } from 'vue-router'
import useAuthStore from "@/router/store/auth";
import { useAbility } from "@casl/vue";
import UserAssignDialog from "@/components/dialogs/UserAssignDialog.vue";
import RivalBacklinksDialog from "@/components/dialogs/RivalBacklinksDialog.vue";
import { useUserApi} from "@/composables/userApi.js";

const { assignableUsers, assignedUserIds, fetchAssignableUsers } = useUserApi();
const showAssignDialog = ref(false);
const showRivalBacklinks = ref(false);
const currentDomainId = ref(null);

const openDialog = (domainId) => {
  currentDomainId.value = domainId;
  showAssignDialog.value = true;
  fetchAssignableUsers(domainId);
};

const headers = [
  { title: "Title", key: "title", align: "center", width: "130px" },
  { title: "Target URL", key: "target_url", align: "center", width: "140px" },
  { title: "Price", key: "total_price", align: "start", width: "100px" },
  {
    title: "Turnaround",
    key: "turnaround_time",
    align: "center",
    width: "40px",
  },
  { title: "Status", key: "status", align: "center", width: "120px" },
  {
    title: "Approval",
    key: "approval_status",
    align: "center",
    width: "120px",
  },
  {
    title: "Rival Domains",
    key: "manage_domains",
    sortable: false,
    width: "120px",
  },
  {
    title: "Rivel Referring Domains",
    key: "rival_backlinks",
    sortable: false,
    width: "120px",
  },
  {
    title: "Actions",
    key: "actions",
    sortable: false,
    width: "120px",
  },
];


const { ClientList, fetchClientList } = useClientApi()
const authStore = useAuthStore()
const ability = useAbility()

const {
  domains,
  rivalBacklinks,
  pagination,
  loading,
  error,
  fetchClientDomains,
  fetchRivalBacklinks,
  downloadTemplate,
  importDomains,
  deleteDomain,
  assignUsersToDomain,
  showAlert,
} = useDomainApi();

const openRivalBacklinksDialog = (domainId) => {
  currentDomainId.value = domainId;
  showRivalBacklinks.value = true;
  fetchRivalBacklinks(domainId);
};

const onUsersAssigned = async (selectedUserIds) => {
  await assignUsersToDomain(currentDomainId.value, selectedUserIds);
  showAssignDialog.value = false;
};


// Filters
const selectedStatus = ref();
const selectedClient = ref();
const selectedApprovalStatus = ref();
const selectedCountry = ref();
const searchQuery = ref("");
const selectedRows = ref([]);
const showAdvancedFilters = ref(false);


const route = useRoute()
const router = useRouter()

const clientId = computed(() =>
 route.params.id
);

const showImportResult = ref(false)
const importResult = ref({
  data: {},
  message: '',
  success: false,
})

// Excel Import Dialog State
const importDialog = ref(false);
const selectedFile = ref(null);
const fileError = ref("");
const importing = ref(false);
const isDragOver = ref(false);

// Data table options
const sortBy = ref();
const orderBy = ref();

// Build filters object
const buildFilters = () => {
  const filters = {};

  if (selectedStatus.value) filters.status = selectedStatus.value;

  const params = {
    pageNumber: pagination.value.page,
    perPage: pagination.value.itemsPerPage,
  };

  if (searchQuery.value) params.searchTerm = searchQuery.value;

  if (sortBy.value) params.sortField = sortBy.value;
  if (orderBy.value) params.sortOrder = orderBy.value;

  if (Object.keys(filters).length > 0) {
    params.filters = filters;
  }

  return params;
};


const loadDomains = async (id = clientId.value) => {
  const filters = buildFilters();
  await fetchClientDomains(id, filters, pagination.value.page);
};

const currentClient = computed(() => {
  if (!ClientList.value?.length) return null
  if (!clientId.value) return null
  return ClientList.value.find(client => client.id == clientId.value)
})


const clearAllFilters = async () => {
  selectedStatus.value = null;
  selectedClient.value = null;
  selectedApprovalStatus.value = null;
  sortBy.value = null;
  orderBy.value = null;
  selectedCountry.value = "";
  searchQuery.value = "";
  pagination.value.page = 1;
  await loadDomains(clientId.value);
  showAlert("Filters  Cleared !", "info");
};

const hasActiveFilters = computed(() => {
  return (
    selectedStatus.value ||
    selectedClient.value ||
    selectedApprovalStatus.value ||
    selectedCountry.value ||
    searchQuery.value
  );
});

const getStatusConfig = (status) => {
  if (status == 1)
    return {
      color: "success",
      icon: "tabler-progress-check",
      text: "Active",
    };
  if (status == 2)
    return { color: "error", icon: "tabler-progress-x", text: "Inactive" };
};

const applyFilters = async () => {
  page.value = 1;
  await loadDomains(clientId.value);
};

const handleDeleteDomain = async (id) => {
  try {
    await deleteDomain(id);
    await loadDomains(clientId.value);
  } catch (error) {
    console.error("Delete failed:", error);
  }
};


const handleDeleteDomainBatch = async (ids) => {
  loading.value = true;
  try {
    await Promise.all(ids.map(id => deleteDomain(id)));
    selectedRows.value = [];
    await loadDomains(clientId.value);
  } catch (error) {
    ss
    console.error("Delete failed:", error);
  }
};



const handleExportReports = () => {
  showAlert("Export functionality is not implemented yet.", "info");
};

// import dialog function 
const handleImportDomains = async () => {
  if (!selectedFile.value) {
    showAlert("Please select a file first", "error");
    return;
  }

  importing.value = true;

  try {
    const res = await importDomains(selectedFile.value)
    importResult.value = res
    closeImportDialog();
    if (res.success) {
      showImportResult.value = true;
      importing.value = false;
      selectedFile.value = null;
    }
    await loadDomains(clientId.value);
  } catch (err) {
    showAlert("Import failed", "error");
  } finally {
    importing.value = false;
  }
};

const closeImportDialog = () => {
  importDialog.value = false;
  importing.value = false;
};

const formatFileSize = (bytes) => {
  if (bytes === 0) return "0 Bytes";
  const k = 1024;
  const sizes = ["Bytes", "KB", "MB", "GB"];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
};

const templateDownload = async () => {
  try {
    const blob = await downloadTemplate();

    const url = window.URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = url;
    link.setAttribute("download", "domains_import_template.xlsx");
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);

    showAlert("Template downloaded successfully!", "success");
  } catch (err) {
    console.error(err);
  }
};

// Computed total domains count
const totalDomains = computed(() => pagination.value.total ?? 0);

// Status options
const statusOptions = [
  { title: "Available", value: 1, color: "success" },
  { title: "Unavailable", value: 2, color: "error" },
];

const approvalStatusOptions = [
  { title: "Pending", value: 1, color: "warning" },
  { title: "Rejected", value: 2, color: "error" },
  { title: "Approved", value: 3, color: "success" },
];

// Get status color
const getStatusColor = (status) => {
  const option = statusOptions.find((opt) => opt.value === status);
  return option?.color || "default";
};

const getApprovalStatusColor = (status) => {
  const option = approvalStatusOptions.find((opt) => opt.value === status);
  return option?.color || "default";
};

const itemsPerPage = computed({
  get: () => pagination.value.itemsPerPage,
  set: (val) => {
    pagination.value.itemsPerPage = val;
    pagination.value.page = 1;
  },
});


const updateOptions = async (options) => {

  pagination.value.itemsPerPage = options.itemsPerPage
  pagination.value.page = options.page

  if (options.sortBy && options.sortBy.length > 0) {
    sortBy.value = options.sortBy[0].key
    orderBy.value = options.sortBy[0].order
  } else {
    sortBy.value = null
    orderBy.value = null
  }

  await loadDomains(clientId.value);
}

onMounted(async () => {
  await fetchClientList()
})

</script>

<template>
  <!-- Header Section -->
  <VCard class="mb-6 pa-6 overflow-hidden" elevation="0">
    <VRow align="center" justify="space-between">
      <VCol cols="12" md="8">
        <div class="d-flex align-center">
          <VAvatar size="64" color="primary" variant="elevated" class="me-4">
            <IconWorldWww stroke="{2}" />
          </VAvatar>
          <div>
            <h1 class="text-h3 font-weight-bold mb-1">Client Domain Management</h1>
            <p class="text-body-1 text-medium-emphasis mb-0">
              Manage and monitor your client domain portfolio
            </p>
          </div>
        </div>
      </VCol>

      <VCol v-if="ability.can('view', 'client')" cols="12" md="4" class="mt-8 text-md-end">
        <VBtn color="primary" variant="flat" :to="{ name: 'client-list' }">
          <VIcon icon="tabler-arrow-autofit-left" size= "xl-large" class="me-1" />
          Back
        </VBtn>
      </VCol>

      <VCol cols="12">
        <div v-if="currentClient" class="d-flex align-center">
          <VChip color="primary" class="ms-3 pa-4" variant="flat" size="large" elevation="2" outlined>
            <VIcon icon="tabler-user" class="me-2" />
            Client Name : {{ currentClient.name }}
          </VChip>
        </div>
      </VCol>
    </VRow>
  </VCard>


  <!-- Enhanced Search & Filter Section -->
  <VCard class="mb-6" elevation="1">
    <VCardTitle class="d-flex align-center justify-space-between pa-6 pb-4">
      <div class="d-flex align-center">
        <VIcon icon="tabler-filter" class="me-2 text-primary" />
        <span class="text-h6 font-weight-medium">Search & Filters</span>
        <VBadge v-if="hasActiveFilters" :content="1" color="primary" class="ml-5" />
      </div>
      <div class="d-flex align-center gap-2">
        <VBtn v-if="hasActiveFilters" variant="text" size="small" color="error" @click="clearAllFilters">
          <VIcon icon="tabler-x" class="me-1" />
          Clear All
        </VBtn>
        <!-- <VBtn variant="text" size="small" @click="showAdvancedFilters = !showAdvancedFilters">
          <VIcon :icon="showAdvancedFilters ? 'tabler-chevron-up' : 'tabler-chevron-down'
            " class="me-1" />
          {{ showAdvancedFilters ? "Less" : "More" }} Filters
        </VBtn> -->
      </div>
    </VCardTitle>

    <VCardText class="pt-0">
      <!-- Primary Search Bar -->
      <VRow align="end" justify="space-between">
        <VCol cols="12" md="6">
          <AppTextField v-model="searchQuery" placeholder="Search by title, URL, or any domain details..."
            prepend-inner-icon="tabler-search" variant="outlined" hide-details clearable class="search-field" />
        </VCol>
        <VCol cols="12" sm="6" md="3">
          <AppSelect v-model="selectedStatus" label="Status" :items="statusOptions" variant="outlined" clearable
            hide-details prepend-inner-icon="tabler-circle-dot" />
        </VCol>
        <VCol cols="12" sm="6" md="3" class="d-flex align-end">
          <VBtn color="primary" variant="flat" block @click="loadDomains(clientId.value)">
            <VIcon icon="tabler-search" class="me-2" />
            Search
          </VBtn>
        </VCol>
      </VRow>

      <!-- Advanced Filters -->
      <!-- <VExpandTransition>
        <div v-show="showAdvancedFilters">
          <VDivider class="mb-4" />
          <VRow>
            <VCol cols="12" sm="6" md="3">
              <AppTextField label="Min Domain Authority" placeholder="0-100" variant="outlined" type="number"
                hide-details prepend-inner-icon="tabler-trending-up" />
            </VCol>
            <VCol cols="12" sm="6" md="3">
              <AppTextField label="Max Price" placeholder="Enter max price" variant="outlined" type="number"
                hide-details prepend-inner-icon="tabler-currency-dollar" />
            </VCol>
            <VCol cols="12" sm="6" md="3">
              <AppTextField label="Min Traffic" placeholder="Monthly visits" variant="outlined" type="number"
                hide-details prepend-inner-icon="tabler-eye" />
            </VCol>
            <VCol cols="12" sm="6" md="3">
              <AppTextField label="Turnaround Time" placeholder="Max days" variant="outlined" type="number" hide-details
                prepend-inner-icon="tabler-clock" />
            </VCol>
          </VRow>
        </div>
      </VExpandTransition> -->
    </VCardText>
  </VCard>

  <VCard v-if="showImportResult" class="mb-6 pa-6" elevation="1">
    <!-- Summary Row -->
    <div v-if="importResult.message" class="d-flex align-center justify-space-between">
      <div class="d-flex align-center gap-2">
        <VIcon color="primary" icon="tabler-file-import" />
        <span class="font-weight-medium">{{ importResult.message }}</span>
      </div>

      <VBtn size="small" variant="text" color="info"
        :icon="showImportResult ? 'tabler-chevron-up' : 'tabler-info-circle'"
        @click="showImportResult = !showImportResult" />
    </div>

    <!-- Expandable Panel -->
    <VExpandTransition>
      <VSheet v-if="showImportResult" class="mt-4 pa-4 border rounded bg-grey-lighten-4">
        <!-- Successful -->
        <div>
          <h4 class="text-subtitle-1 font-weight-bold text-success mb-2">
            Successful Imports ({{ importResult.data.imported.length }})
          </h4>
          <div style="max-height: 150px; overflow-y: auto; scroll-behavior: smooth;" class="pr-2">
            <VList density="compact">
              <VListItem v-for="(domain, idx) in importResult.data.imported" :key="'s-' + idx">
                <VListItemTitle>{{ domain }}</VListItemTitle>
              </VListItem>
              <VListItem v-if="importResult.data.imported.length === 0">
                <VListItemTitle class="text-grey">No domains imported</VListItemTitle>
              </VListItem>
            </VList>
          </div>
        </div>

        <!-- Failed -->
        <div class="mt-4">
          <h4 class="text-subtitle-1 font-weight-bold text-error mb-2">
            Failed Imports ({{ importResult.data.failed.length }})
          </h4>
          <div style="max-height: 150px; overflow-y: auto; scroll-behavior: smooth;" class="pr-2">
            <VList density="compact">
              <VListItem v-for="(f, idx) in importResult.data.failed" :key="'f-' + idx">
                <VListItemTitle>
                  <span class="text-primary">{{ f.url || 'N/A' }}</span> — {{ f.reason }}
                </VListItemTitle>
              </VListItem>
              <VListItem v-if="importResult.data.failed.length === 0">
                <VListItemTitle class="text-grey">No failed domains</VListItemTitle>
              </VListItem>
            </VList>
          </div>
        </div>
      </VSheet>
    </VExpandTransition>
  </VCard>

  <!-- Results Summary -->
  <VCard elevation="1">
    <div class="d-flex flex-wrap gap-4 ma-6">
      <div class="d-flex align-center text-body-1 font-weight-regular my-4">
        <span class="font-weight-medium text-h6">
          {{ totalDomains }}
        </span>
        <span class="ml-2">Record Found</span>

        <VChip v-if="selectedRows.length" color="primary" size="small" class="ml-4" elevation="2" outlined>
          {{ selectedRows.length }} selected
        </VChip>
      </div>

      <VSpacer />
      <div class="d-flex align-center gap-2">
        <VBtn v-if="selectedRows.length" variant="text" size="small" color="error"
          @click="handleDeleteDomainBatch(selectedRows)">
          <VIcon icon="tabler-trash" class="me-1" />
          Delete Selected
        </VBtn>
      </div>
      <div class="d-flex gap-4 flex-wrap align-center">
        <AppSelect v-model="itemsPerPage" :items="[5, 10, 20, 25, 50]" />

        <!-- Excel Import Dialog Button -->
        <VBtn variant="tonal" color="secondary" prepend-icon="tabler-download" @click="importDialog = true">
          Import
        </VBtn>

        <!-- <VBtn variant="tonal" color="secondary" prepend-icon="tabler-download" @click="templateDownload">
          Download Template
        </VBtn> -->
        <!-- 👉 Export button -->
        <VBtn variant="tonal" color="secondary" prepend-icon="tabler-upload" @click="handleExportReports">
          Export
        </VBtn>
        <!-- create domain-->
        <VBtn color="primary" prepend-icon="tabler-plus"
          @click="$router.push({ name: 'domain-clientdomain-add', params: { id: clientId } })">
          Add Client Domain
        </VBtn>
      </div>
    </div>

    <VDivider class="mt-5 mb-2" />

    <!-- Enhanced Data Table -->
    <VDataTableServer :page="pagination.page" :items-per-page="pagination.itemsPerPage"
      v-model:model-value="selectedRows" :headers="headers" show-select :items="domains" :loading="loading"
      :items-length="pagination.total" loading-text="Fetching domains, please wait..." class="domain-table" hover
      @update:options="updateOptions">
      <template #item.target_url="{ item }">
        <a :href="item.target_url" target="_blank" class="text-success text-decoration-none text-body-1"
          :title="item.target_url">
          <span class="text-truncate" style="max-width: 200px">
            {{ item.target_url }}
          </span>
          <VIcon icon="tabler-external-link" size="12" class="flex-shrink-0 ms-1" />
        </a>
      </template>

      <template #item.domain_authority="{ item }">
        <VProgressCircular :model-value="item.domain_authority" size="30" width="4" :color="item.domain_authority > 70
            ? 'success'
            : item.domain_authority > 40
              ? 'warning'
              : 'error'
          ">
          <small class="font-weight-medium">{{
            item.domain_authority ?? 0
            }}</small>
        </VProgressCircular>
      </template>

      <template #item.domain_rating="{ item }">
        <VProgressCircular :model-value="item.domain_rating" size="30" width="4" :color="item.domain_rating > 70
            ? 'success'
            : item.domain_rating > 40
              ? 'warning'
              : 'error'
          ">
          <small class="font-weight-medium">{{
            item.domain_rating ?? 0
            }}</small>
        </VProgressCircular>
      </template>

      <template #item.organic_traffic="{ item }">
        <div class="d-flex align-center">
          <VIcon icon="tabler-eye" size="16" class="me-1 text-medium-emphasis" />
          <span class="font-weight-medium">{{
            item.organic_traffic?.toLocaleString() || "N/A"
            }}</span>
        </div>
      </template>

      <template #item.total_price="{ item }">
        <div class="d-flex align-center">
          <span class="font-weight-bold text-dark">{{ item.total_price ? `$${item.total_price}` : '-' }}</span>
        </div>
      </template>

      <template #item.turnaround_time="{ item }">
        <template v-if="item.turnaround_time && item.turnaround_time > 0">
          <VChip
              size="small"
              :color="
        item.turnaround_time <= 3
          ? 'success'
          : item.turnaround_time <= 7
          ? 'warning'
          : 'error'
      "
              variant="tonal"
          >
            {{ item.turnaround_time }}d
          </VChip>
        </template>

        <template v-else>-</template>
      </template>

      <template #item.status="{ item }">
        <VChip :color="getStatusConfig(item.status).color" variant="tonal" size="small" class="ma-1">
          <VIcon :icon="getStatusConfig(item.status).icon" size="14" class="me-1" />
          {{ getStatusConfig(item.status).text }}
        </VChip>
      </template>

      <template #item.approval_status="{ item }">
        <VChip :color="item.approval_status === 1
            ? 'warning'
            : item.approval_status === 2
              ? 'error'
              : 'success'
          " variant="tonal" size="small" class="font-weight-medium">
          {{
          item.approval_status === 1
          ? "Pending"
          : item.approval_status === 2
          ? "Rejected"
          : "Approved"
          }}
        </VChip>
      </template>

      <template #item.country="{ item }">
        <VIcon icon="tabler-world" size="16" class="me-1 text-medium-emphasis" />
        <span>{{ item.country }}</span>
      </template>

      <template #item.manage_domains="{ item }">
        <div class="d-flex align-center gap-1">
          
          <VTooltip text="View Rival Domains">
            <template #activator="{ props }">
              <IconBtn v-bind="props" size="small" @click="$router.push({
                name: 'domain-clientdomain-rivaldomain-list',
                params: { clientId: item.client_id, domainId: item.id }
              })">
              <VChip color="info" variant="tonal" size="small">
               <VIcon color="success" icon="tabler-external-link" size="20" class="me-1" />
                {{ item.rival_domains_count }}
              </VChip>
              </IconBtn>
            </template>
          </VTooltip>
        </div>
      </template>

      <template #item.rival_backlinks="{ item }">
        <div class="d-flex align-center gap-1">
          <VTooltip text="View Rivel Referring Domains">
        <template #activator="{ props }">
          <IconBtn v-bind="props" size="small" @click="openRivalBacklinksDialog(item.id)">
            <VChip color="warning" variant="tonal" size="small">
            <VIcon color="warning" icon="tabler-link" size="20" />
            </VChip>
          </IconBtn>
        </template>
          </VTooltip>
        </div>
      </template>

      <template #item.actions="{ item }">
        <div class="d-flex">
          <VTooltip text="View Details">
            <template #activator="{ props }">
              <IconBtn v-bind="props" size="small">
                <router-link
                  :to="{ name: 'domain-clientdomain-view', params: { clientId: item.client_id, domainId: item.id } }">
                  <VIcon icon="tabler-eye" size="24" />
                </router-link>
              </IconBtn>
            </template>
          </VTooltip>

          <VTooltip v-if="ability.can('assign','clientdomain')" text="User Assignment">
            <template #activator="{ props }">
              <IconBtn v-bind="props" size="small" @click="openDialog(item.id)">
                <VIcon color="secondary" icon="tabler-users-plus" size="20" />
              </IconBtn>
            </template>
          </VTooltip>

          <VTooltip text="View history">
            <template #activator="{ props }">
              <IconBtn v-bind="props" size="small">
                <router-link :to="{
                  name: 'domain-clientdomain-history',
                  params: {  view : 'client', id: item.id },
                }">
                  <VIcon color="info" icon="tabler-chart-bar-popular" size="20" />
                </router-link>
              </IconBtn>
            </template>
          </VTooltip>

          <VTooltip text="Delete">
            <template #activator="{ props }">
              <IconBtn v-bind="props" color="error" icon="tabler-trash" @click="handleDeleteDomain(item.id)"
                size="small" />
            </template>
          </VTooltip>


        </div>
      </template>

      <!-- Empty State -->
      <template #no-data>
        <div class="text-center pa-8">
          <VIcon icon="tabler-world-off" size="48" class="text-medium-emphasis mb-4" />
          <h3 class="text-h6 mb-2">No domains found</h3>
          <p class="text-body-2 text-medium-emphasis mb-4">
            Try adjusting your search criteria or add a new domain to get
            started.
          </p>
          <VBtn color="primary"
          @click="$router.push({ name: 'domain-clientdomain-add', params: { id: clientId } })">
            <VIcon icon="tabler-plus" class="me-2" />
            Add First Domain
          </VBtn>
        </div>
      </template>

      <template #bottom>
        <TablePagination v-model:page="pagination.page" :items-per-page="pagination.itemsPerPage"
          :total-items="totalDomains" />
      </template>
    </VDataTableServer>
  </VCard>


  <!-- import domains dialog-->

  <VDialog v-model="importDialog" max-width="600" persistent>
    <VCard>
      <VCardTitle class="pa-6 pb-4">
        <div class="d-flex align-center">
          <VIcon icon="tabler-file-excel" class="me-3 text-success" size="32" />
          <div>
            <h3 class="text-h5 font-weight-bold">Import Domains</h3>
            <p class="text-body-2 text-medium-emphasis mb-0">
              Upload an Excel file to import multiple domains
            </p>
          </div>
        </div>
      </VCardTitle>

      <VDivider />

      <VCardText class="pa-6">
        <!-- File Upload Area -->
        <div class="mb-6">
          <VFileInput v-model="selectedFile" label="Select Excel File" accept=".xlsx,.xls"
            prepend-icon="tabler-paperclip" variant="outlined" show-size counter :error-messages="fileError"
            @change="handleFileChange" @click:clear="clearFile">
            <template #selection="{ fileNames }">
              <template v-for="fileName in fileNames" :key="fileName">
                <VChip color="success" size="small" label class="me-2">
                  <VIcon icon="tabler-file-excel" start />
                  {{ fileName }}
                </VChip>
              </template>
            </template>
          </VFileInput>
        </div>

        <!-- Drag & Drop Area -->
        <div class="drop-zone" :class="{ 'drop-zone--dragover': isDragOver }" @drop="handleDrop"
          @dragover="handleDragOver" @dragenter="handleDragEnter" @dragleave="handleDragLeave">
          <div class="text-center">
            <VIcon :icon="isDragOver ? 'tabler-file-plus' : 'tabler-cloud-upload'" size="48"
              class="text-primary mb-4" />
            <h4 class="text-h6 mb-2">
              {{
              isDragOver
              ? "Drop your Excel file here"
              : "Drag & drop your Excel file here"
              }}
            </h4>
            <p class="text-body-2 text-medium-emphasis mb-4">
              or click "Select Excel File" above
            </p>
            <div class="d-flex justify-center gap-2 flex-wrap">
              <VChip size="small" color="success" variant="tonal">
                <VIcon icon="tabler-check" start />
                .xlsx
              </VChip>
              <VChip size="small" color="success" variant="tonal">
                <VIcon icon="tabler-check" start />
                .xls
              </VChip>
            </div>
          </div>
        </div>

        <!-- File Preview -->
        <div v-if="selectedFile && !fileError" class="mt-6">
          <VAlert color="info" variant="tonal" class="mb-4">
            <VIcon icon="tabler-info-circle" />
            <VAlertTitle>File Ready for Import</VAlertTitle>
            <div class="mt-2">
              <div class="d-flex align-center justify-space-between">
                <span><strong>File:</strong> {{ selectedFile.name }}</span>
                <span><strong>Size:</strong>
                  {{ formatFileSize(selectedFile.size) }}</span>
              </div>
            </div>
          </VAlert>
        </div>

        <!-- Import Instructions -->
        <VExpansionPanels class="mt-6" variant="accordion">
          <VExpansionPanel>
            <VExpansionPanelTitle>
              <VIcon icon="tabler-help-circle" class="me-2" />
              Import Instructions
            </VExpansionPanelTitle>
            <VExpansionPanelText>
              <div class="text-body-2">
                <h5 class="text-subtitle-1 mb-3">Excel File Requirements:</h5>
                <ul class="ml-4 mb-4">
                  <li>File must be in Excel format (.xlsx or .xls)</li>
                  <li>First row should contain column headers</li>
                  <li>Required columns: title and target_url</li>
                  <li>Maximum file size: 10MB</li>
                  <li>Maximum 1000 rows per import</li>
                </ul>

                <VBtn variant="outlined" size="small" prepend-icon="tabler-download" @click="templateDownload">
                  Download Template
                </VBtn>
              </div>
            </VExpansionPanelText>
          </VExpansionPanel>
        </VExpansionPanels>
      </VCardText>

      <VDivider />

      <VCardActions class="pa-6">
        <VSpacer />
        <VBtn variant="flat" color="error" @click="closeImportDialog" :disabled="importing">
          Cancel
        </VBtn>
        <VBtn color="primary" :loading="importing" variant="flat" :disabled="!selectedFile || !!fileError"
          @click="handleImportDomains">
          <VIcon icon="tabler-download" class="me-2" />
          Import Domains
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>

  <UserAssignDialog
      v-model="showAssignDialog"
      :users="assignableUsers"
      :assigned="assignedUserIds"
      @assign="onUsersAssigned"
  />

  <RivalBacklinksDialog
      v-model="showRivalBacklinks"
      :rivalBacklinks="rivalBacklinks"
  />

</template>

<style lang="scss" scoped>
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
          box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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

.v-card {
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;
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

      th,
      td {
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
