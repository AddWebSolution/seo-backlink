<script setup>
import { onMounted, ref, computed, unref } from "vue";
import { useClientApi } from "@/composables/clientApi";
import { IconWorldWww } from "@tabler/icons-vue";
import { flat } from "@/views/demos/components/button/demoCodeButton";

const headers = [
  { title: "ID", key: "id", align: "start", width: "60px" },
  { title: "Name", key: "name", align: "center", width: "70px" },
  { title: "E-Mail", key: "email", align: "center", width: "40px" },
  { title: "Company", key: "company_name", align: "center", width: "80px" },
  { title: "Website", key: "website", align: "center", width: "80px" },
  { title: "City", key: "city", align: "center", width: "80px" },
  { title: "Phone", key: "phone", align: "center", width: "100px" },
  { title: "Country", key: "country", align: "center", width: "100px" },
  {
    title: "Status",
    key: "status",
    align: "center",
    width: "30px",
  },
  {
    title: "Actions",
    key: "actions",
    sortable: false,
    width: "40px",
  },
];

const {
  clients,
  pagination,
  loading,
  error,
  fetchclients,
  downloadTemplate,
  updateClient,
  importclients,
  deleteClient,
  showAlert,
} = useClientApi();

// Filters
const selectedStatus = ref();
const selectedApprovalStatus = ref();
const selectedCountry = ref();
const searchQuery = ref("");
const selectedRows = ref([]);
const showAdvancedFilters = ref(false);

// Edit Dialog State
const isEditDialogActive = ref(false);
const selectedClient = ref(null);
const clientData = ref({
  id: null,
  name: '',
  email: '',
  company_name: '',
  website: '',
  phone: '',
  industry: '',
  city: '',
  state: '',
  zip_code: '',
  country: '',
  status: 1
});
const submitting = ref(false);
const formRef = ref(null);

// Form validation rules
const rules = {
  required: value => !!value || 'This field is required',
  email: value => {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return pattern.test(value) || 'Please enter a valid email address';
  },
  website: value => {
    if (!value) return true; // Optional field
    const pattern = /^https?:\/\/.+/;
    return pattern.test(value) || 'Please enter a valid URL (starting with http:// or https://)';
  },
  phone: value => {
    if (!value) return true; // Optional field
    const pattern = /^[\+]?[0-9\-\(\)\s]+$/;
    return pattern.test(value) || 'Please enter a valid phone number';
  }
};

const showImportResult = ref(false);
const importResult = ref({
  data: {},
  message: '',
  success: false,
});

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
  if (selectedApprovalStatus.value)
    filters.approval_status = selectedApprovalStatus.value;
  if (selectedCountry.value) filters.country = selectedCountry.value;
  if (searchQuery.value) filters.searchTerm = searchQuery.value;
  if (sortBy.value) filters.sortField = sortBy.value;
  if (orderBy.value) filters.sortOrder = orderBy.value;

  filters.pageNumber = pagination.value.page;
  filters.perPage = pagination.value.itemsPerPage;

  return filters;
};

const loadClients = async () => {
  const filters = buildFilters();
  await fetchclients(filters, pagination.value.page);
};

// Open dialog for a client
const openEditDialog = (client) => {
  selectedClient.value = { ...client };
  clientData.value = {
    id: client.id,
    name: client.name || '',
    email: client.email || '',
    company_name: client.company_name || '',
    website: client.website || '',
    phone: client.phone || '',
    industry: client.industry || '',
    city: client.city || '',
    state: client.state || '',
    zip_code: client.zip_code || '',
    country: client.country || '',
    status: client.status || 1
  };
  isEditDialogActive.value = true;
};

// Save client function
const saveClient = async () => {
  if (!formRef.value) return;

  const { valid } = await formRef.value.validate();
  if (!valid) return;

  submitting.value = true;
  try {
    await updateClient(clientData.value.id, clientData.value);
    showAlert("Client updated successfully!", "success");
    isEditDialogActive.value = false;
    await loadClients(); // Refresh the list
  } catch (error) {
    console.error('Update failed:', error);
    showAlert("Failed to update client", "error");
  } finally {
    submitting.value = false;
  }
};

const clearAllFilters = async () => {
  selectedStatus.value = null;
  selectedApprovalStatus.value = null;
  selectedCountry.value = "";
  searchQuery.value = "";
  pagination.value.page = 1;
  await loadClients();
  showAlert("Custom message here!", "info");
};

const hasActiveFilters = computed(() => {
  return (
    selectedStatus.value ||
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
      text: "Available",
    };
  if (status == 2)
    return { color: "error", icon: "tabler-progress-x", text: "Unavailable" };
};

const applyFilters = async () => {
  pagination.value.page = 1;
  await loadClients();
};

const handleDeleteClient = async (id) => {
  try {
    await deleteClient(id);
    const index = selectedRows.value.findIndex((row) => row === id);
    if (index !== -1) selectedRows.value.splice(index, 1);
    await loadClients(); // Refresh the list
    showAlert("Client deleted successfully!", "success");
  } catch (error) {
    console.error("Delete failed:", error);
    showAlert("Failed to delete client", "error");
  }
};

const handleDeleteClientBatch = async (ids) => {
  loading.value = true;
  try {
    await Promise.all(ids.map(id => deleteClient(id)));
    selectedRows.value = []; 
    await loadDomains(clientId.value);
  } catch (error) {ss
    console.error("Delete failed:", error);
  }
};

// File handling functions for import
const handleFileChange = (files) => {
  if (files && files.length > 0) {
    const file = files[0];
    validateFile(file);
  }
};

const validateFile = (file) => {
  fileError.value = "";
  
  if (!file) return;
  
  const allowedTypes = [
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'application/vnd.ms-excel'
  ];
  
  if (!allowedTypes.includes(file.type)) {
    fileError.value = "Please select a valid Excel file (.xlsx or .xls)";
    return;
  }
  
  if (file.size > 10 * 1024 * 1024) { // 10MB
    fileError.value = "File size must be less than 10MB";
    return;
  }
};

const clearFile = () => {
  selectedFile.value = null;
  fileError.value = "";
};

const handleDrop = (e) => {
  e.preventDefault();
  isDragOver.value = false;
  
  const files = Array.from(e.dataTransfer.files);
  if (files.length > 0) {
    selectedFile.value = [files[0]];
    validateFile(files[0]);
  }
};

const handleDragOver = (e) => {
  e.preventDefault();
  isDragOver.value = true;
};

const handleDragEnter = (e) => {
  e.preventDefault();
  isDragOver.value = true;
};

const handleDragLeave = (e) => {
  e.preventDefault();
  isDragOver.value = false;
};

// Import dialog function 
const handleImportClients = async () => {
  if (!selectedFile.value) {
    showAlert("Please select a file first", "error");
    return;
  }

  importing.value = true;

  try {
    const res = await importclients(selectedFile.value);
    importResult.value = res;
    closeImportDialog();
    if (res.success) {
      showImportResult.value = true;
      selectedFile.value = null;
      await loadClients(); 
    }
    console.log('showImportResult', showImportResult.value);
  } catch (err) {
    showAlert(err, "error");
  } finally {
    importing.value = false;
  }
};

const closeImportDialog = () => {
  importDialog.value = false;
  importing.value = false;
  clearFile();
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
    link.setAttribute("download", "clients_import_template.xlsx");
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);

    showAlert("Template downloaded successfully!", "success");
  } catch (err) {
    console.error(err);
    showAlert("Failed to download template", "error");
  }
};

// Export function
const handleExportReports = async () => {
  try {
    // Implement your export logic here
    showAlert("Export functionality to be implemented", "info");
  } catch (err) {
    console.error(err);
    showAlert("Export failed", "error");
  }
};

// Computed total clients count
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
  pagination.value.itemsPerPage = options.itemsPerPage;
  pagination.value.page = options.page;
  await loadClients();
};
</script>

<template>
  <!-- Header Section -->
  <VCard class="mb-6 pa-6 overflow-hidden" elevation="0">
    <VContainer fluid>
      <VRow align="center">
        <VCol cols="12" md="8">
          <div class="d-flex align-center">
            <VAvatar size="64" color="primary" variant="elevated" class="me-4">
              <IconWorldWww stroke="{2}" />
            </VAvatar>
            <div>
              <h1 class="text-h3 font-weight-bold mb-1">Client Management</h1>
              <p class="text-body-1 text-medium-emphasis mb-0">
                Manage and monitor your client portfolio
              </p>
            </div>
          </div>
        </VCol>
      </VRow>
    </VContainer>
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
        <VBtn variant="text" size="small" @click="showAdvancedFilters = !showAdvancedFilters">
          <VIcon :icon="
              showAdvancedFilters ? 'tabler-chevron-up' : 'tabler-chevron-down'
            " class="me-1" />
          {{ showAdvancedFilters ? "Less" : "More" }} Filters
        </VBtn>
      </div>
    </VCardTitle>

    <VCardText class="pt-0">
      <!-- Primary Search Bar -->
      <VRow class="mb-4">
        <VCol cols="12">
          <AppTextField v-model="searchQuery" placeholder="Search by name, email, company, or any client details..."
            prepend-inner-icon="tabler-search" variant="outlined" hide-details clearable class="search-field" />
        </VCol>
      </VRow>

      <!-- Quick Filters -->
      <VRow class="mb-4">
        <VCol cols="12" sm="6" md="3">
          <AppSelect v-model="selectedStatus" label="Status" :items="statusOptions" variant="outlined" clearable
            hide-details prepend-inner-icon="tabler-circle-dot" />
        </VCol>
        <VCol cols="12" sm="6" md="3">
          <AppSelect v-model="selectedApprovalStatus" label="Approval Status" :items="approvalStatusOptions"
            variant="outlined" clearable hide-details prepend-inner-icon="tabler-check" />
        </VCol>
        <VCol cols="12" sm="6" md="3">
          <AppTextField v-model="selectedCountry" label="Country" placeholder="Enter country" variant="outlined"
            clearable hide-details prepend-inner-icon="tabler-world" />
        </VCol>
        <VCol cols="12" sm="6" md="3" class="d-flex align-end">
          <VBtn color="primary" variant="flat" block @click="loadClients">
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
              <AppTextField label="Industry" placeholder="Enter industry" variant="outlined" 
                hide-details prepend-inner-icon="tabler-building" />
            </VCol>
            <VCol cols="12" sm="6" md="3">
              <AppTextField label="City" placeholder="Enter city" variant="outlined" 
                hide-details prepend-inner-icon="tabler-map-pin" />
            </VCol>
            <VCol cols="12" sm="6" md="3">
              <AppTextField label="State" placeholder="Enter state" variant="outlined" 
                hide-details prepend-inner-icon="tabler-map" />
            </VCol>
            <VCol cols="12" sm="6" md="3">
              <AppTextField label="Zip Code" placeholder="Enter zip code" variant="outlined" 
                hide-details prepend-inner-icon="tabler-mail" />
            </VCol>
          </VRow>
        </div>
      </VExpandTransition>
    </VCardText>
  </VCard>

  <!-- Import Result Card -->
  <VCard v-if="importResult.message" class="mb-6 pa-6" elevation="1">
    <!-- Summary Row -->
    <div class="d-flex align-center justify-space-between">
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
            Successful Imports ({{ importResult.data?.imported?.length || 0 }})
          </h4>
          <div style="max-height: 150px; overflow-y: auto; scroll-behavior: smooth;" class="pr-2">
            <VList density="compact">
              <VListItem v-for="(client, idx) in importResult.data?.imported || []" :key="'s-' + idx">
                <VListItemTitle>{{ client }}</VListItemTitle>
              </VListItem>
              <VListItem v-if="!importResult.data?.imported?.length">
                <VListItemTitle class="text-grey">No clients imported</VListItemTitle>
              </VListItem>
            </VList>
          </div>
        </div>

        <!-- Failed -->
        <div class="mt-4">
          <h4 class="text-subtitle-1 font-weight-bold text-error mb-2">
            Failed Imports ({{ importResult.data?.failed?.length || 0 }})
          </h4>
          <div style="max-height: 150px; overflow-y: auto; scroll-behavior: smooth;" class="pr-2">
            <VList density="compact">
              <VListItem v-for="(f, idx) in importResult.data?.failed || []" :key="'f-' + idx">
                <VListItemTitle>
                  <span class="text-primary">{{ f.name || f.email || 'N/A' }}</span> — {{ f.reason }}
                </VListItemTitle>
              </VListItem>
              <VListItem v-if="!importResult.data?.failed?.length">
                <VListItemTitle class="text-grey">No failed clients</VListItemTitle>
              </VListItem>
            </VList>
          </div>
        </div>
      </VSheet>
    </VExpandTransition>
  </VCard>

  <!-- Results Summary & Actions -->
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
        <VBtn v-if="selectedRows.length" variant="text" @click="handleDeleteClientBatch(selectedRows)" size="small" color="error">
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

        <!-- Export button -->
        <VBtn variant="tonal" color="secondary" prepend-icon="tabler-upload" @click="handleExportReports">
          Export
        </VBtn>
        
        <!-- Add Client button -->
        <VBtn color="primary" prepend-icon="tabler-plus" @click="$router.push('/apps/client/add')">
          Add Client
        </VBtn>
      </div>
    </div>

    <VDivider class="mt-5 mb-2" />

    <!-- Enhanced Data Table -->
    <VDataTableServer 
      :page="pagination.page" 
      :items-per-page="pagination.itemsPerPage"
      v-model:model-value="selectedRows" 
      :headers="headers" 
      show-select 
      :items="clients" 
      :loading="loading"
      :items-length="pagination.total" 
      loading-text="Fetching clients, please wait..." 
      hover
      @update:options="updateOptions">
      
      <template #item.website="{ item }">
        <a v-if="item.website" :href="item.website" target="_blank" class="text-success text-decoration-none text-body-1"
          :title="item.website">
          <span class="text-truncate" style="max-width: 200px">
            {{ item.website }}
          </span>
          <VIcon icon="tabler-external-link" size="12" class="flex-shrink-0 ms-1" />
        </a>
        <span v-else class="text-grey">N/A</span>
      </template>

      <template #item.status="{ item }">
        <VChip :color="getStatusConfig(item.status)?.color || 'default'" variant="tonal" size="small" class="ma-1">
          <VIcon :icon="getStatusConfig(item.status)?.icon || 'tabler-circle'" size="14" class="me-1" />
          {{ getStatusConfig(item.status)?.text || 'Unknown' }}
        </VChip>
      </template>

      <template #item.actions="{ item }">
        <div class="d-flex">
          <VTooltip text="View Details">
            <template #activator="{ props }">
              <IconBtn v-bind="props" size="small">
                <router-link :to="{ name: 'apps-client-view', params: { id: item.id } }">
                  <VIcon icon="tabler-eye" size="20" />
                </router-link>
              </IconBtn>
            </template>
          </VTooltip>

          <VTooltip text="Edit Client">
            <template #activator="{ props }">
              <IconBtn v-bind="props" size="small" @click="openEditDialog(item)">
                <VIcon  color= "info"  icon="tabler-edit" size="20" />
              </IconBtn>
            </template>
          </VTooltip>

          <VTooltip text="View Domains for This Client">
            <template #activator="{ props }">
              <IconBtn v-bind="props" size="small"
                @click="$router.push({ name: 'apps-domain-clientdomain-list', params: { id: item.id } })">
                <VIcon color="success" icon="tabler-world" size="20" />
              </IconBtn>
            </template>
          </VTooltip>

          <VTooltip text="Delete Client">
            <template #activator="{ props }">
              <IconBtn v-bind="props" size="small" @click="handleDeleteClient(item.id)">
                <VIcon icon="tabler-trash" size="20" color="error" />
              </IconBtn>
            </template>
          </VTooltip>
        </div>
      </template>

      <!-- Empty State -->
      <template #no-data>
        <div class="text-center pa-8">
          <VIcon icon="tabler-users-off" size="48" class="text-medium-emphasis mb-4" />
          <h3 class="text-h6 mb-2">No clients found</h3>
          <p class="text-body-2 text-medium-emphasis mb-4">
            Try adjusting your search criteria or add a new client to get started.
          </p>
          <VBtn color="primary" @click="$router.push('/apps/client/add')">
            <VIcon icon="tabler-plus" class="me-2" />
            Add First Client
          </VBtn>
        </div>
      </template>

      <template #bottom>
        <TablePagination v-model:page="pagination.page" :items-per-page="pagination.itemsPerPage"
          :total-items="totalDomains" />
      </template>
    </VDataTableServer>
  </VCard>

  <!-- Edit Client Dialog -->
  <VDialog v-model="isEditDialogActive" max-width="800" persistent>
    <VCard>
      <VCardTitle class="text-h5 font-weight-bold pa-6 pb-4">
        <div class="d-flex align-center">
          <VIcon icon="tabler-edit" class="me-3" />
          {{clientData.name}} #  Edit Client
        </div>
      </VCardTitle>

      <VDivider />

      <VCardText class="pa-6">
        <VForm ref="formRef" @submit.prevent="saveClient">
          <VRow dense>
            <VCol cols="12" md="6">
              <AppTextField 
                v-model="clientData.name" 
                label="Full Name" 
                :rules="[rules.required]" 
                variant="outlined"
              />
            </VCol>

            <VCol cols="12" md="6">
              <AppTextField 
                v-model="clientData.email" 
                label="Email" 
                :rules="[rules.required, rules.email]" 
                variant="outlined"
              />
            </VCol>

            <VCol cols="12" md="6">
              <AppTextField 
                v-model="clientData.company_name" 
                label="Company Name" 
                :rules="[rules.required]" 
                variant="outlined"
              />
            </VCol>

            <VCol cols="12" md="6">
              <AppTextField 
                v-model="clientData.website" 
                label="Website" 
                :rules="[rules.website]"
                placeholder="https://example.com" 
                variant="outlined"
              />
            </VCol>

            <VCol cols="12" md="6">
              <AppTextField 
                v-model="clientData.phone" 
                label="Phone Number" 
                :rules="[rules.phone]" 
                variant="outlined"
              />
            </VCol>

            <VCol cols="12" md="6">
              <AppTextField 
                v-model="clientData.industry" 
                label="Industry" 
                variant="outlined"
              />
            </VCol>

            <VCol cols="12" md="4">
              <AppTextField 
                v-model="clientData.city" 
                label="City" 
                variant="outlined"
              />
            </VCol>

            <VCol cols="12" md="4">
              <AppTextField 
                v-model="clientData.state" 
                label="State" 
                variant="outlined"
              />
            </VCol>

            <VCol cols="12" md="4">
              <AppTextField 
                v-model="clientData.zip_code" 
                label="Zip Code" 
                variant="outlined"
              />
            </VCol>

            <VCol cols="12" md="6">
              <AppTextField 
                v-model="clientData.country" 
                label="Country" 
                variant="outlined"
              />
            </VCol>

            <VCol cols="12" md="6">
              <AppSelect 
                v-model="clientData.status" 
                :items="[
                  { title: 'Active', value: 1 },
                  { title: 'Inactive', value: 2 }
                ]" 
                label="Status" 
                variant="outlined"
              />
            </VCol>
          </VRow>
        </VForm>
      </VCardText>

      <VDivider />

      <VCardActions class="pa-6">
        <VSpacer />
        <VBtn variant="flat" @click="isEditDialogActive = false" :disabled="submitting">
          Cancel
        </VBtn>
        <VBtn variant="flat" :loading="submitting" @click="saveClient">
          <VIcon icon="tabler-device-floppy" class="me-2" />
          Save Changes
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>

  <!-- Import Clients Dialog -->
  <VDialog v-model="importDialog" max-width="600" persistent>
    <VCard>
      <VCardTitle class="pa-6 pb-4">
        <div class="d-flex align-center">
          <VIcon icon="tabler-file-excel" class="me-3 text-success" size="32" />
          <div>
            <h3 class="text-h5 font-weight-bold">Import Clients</h3>
            <p class="text-body-2 text-medium-emphasis mb-0">
              Upload an Excel file to import multiple clients
            </p>
          </div>
        </div>
      </VCardTitle>

      <VDivider />

      <VCardText class="pa-6">
        <!-- File Upload Area -->
        <div class="mb-6">
          <VFileInput 
            v-model="selectedFile" 
            label="Select Excel File" 
            accept=".xlsx,.xls"
            prepend-icon="tabler-paperclip" 
            variant="outlined" 
            show-size 
            counter 
            :error-messages="fileError"
            @change="handleFileChange" 
            @click:clear="clearFile">
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
        <div 
          class="drop-zone" 
          :class="{ 'drop-zone--dragover': isDragOver }" 
          @drop="handleDrop"
          @dragover="handleDragOver" 
          @dragenter="handleDragEnter" 
          @dragleave="handleDragLeave">
          <div class="text-center">
            <VIcon 
              :icon="isDragOver ? 'tabler-file-plus' : 'tabler-cloud-upload'" 
              size="48"
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
        <div v-if="selectedFile && selectedFile.length && !fileError" class="mt-6">
          <VAlert color="info" variant="tonal" class="mb-4">
            <VIcon icon="tabler-info-circle" />
            <VAlertTitle>File Ready for Import</VAlertTitle>
            <div class="mt-2">
              <div class="d-flex align-center justify-space-between">
                <span><strong>File:</strong> {{ selectedFile[0]?.name }}</span>
                <span><strong>Size:</strong>
                  {{ formatFileSize(selectedFile[0]?.size || 0) }}</span>
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
                  <li>Required columns: name, email, company_name</li>
                  <li>Optional columns: website, phone, industry, city, state, zip_code, country</li>
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
        <VBtn variant="outlined" @click="closeImportDialog" :disabled="importing">
          Cancel
        </VBtn>
        <VBtn 
          color="primary" 
          :loading="importing" 
          @click="handleImportClients">
          <VIcon icon="tabler-download" class="me-2" />
          Import Clients
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
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

// Drop zone styling
.drop-zone {
  border: 2px dashed rgba(var(--v-theme-primary), 0.3);
  border-radius: 12px;
  padding: 40px 20px;
  margin: 20px 0;
  transition: all 0.3s ease;
  cursor: pointer;

  &:hover, &--dragover {
    border-color: rgb(var(--v-theme-primary));
    background-color: rgba(var(--v-theme-primary), 0.04);
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