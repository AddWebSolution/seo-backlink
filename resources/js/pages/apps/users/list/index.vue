<script setup>
import { onMounted, ref, computed, unref } from "vue";
import { useUserApi } from "@/composables/userApi";
import { IconWorldWww } from "@tabler/icons-vue";
import { useAbility } from "@casl/vue";

const headers = [
  { title: "Name", key: "name", align: "center", width: "80px" },
  { title: "E-Mail", key: "email", align: "center", width: "80px" },
  { title: "role", key: "role", align: "center", width: "60px" },
  { title: "Phone", key: "phone", align: "center", width: "80px" },
  {
    title: "Status",
    key: "status",
    align: "center",
    width: "110px",
  },
  {
    title: "Actions",
    key: "actions",
    align  : "center",
    sortable: false,
    width: "120px",
  },
];

const {
  Users,
  pagination,
  loading,
  error,
  fetchUsers,
  downloadTemplate,
  updateUser,
  importUsers,
  deleteUser,
  showAlert,
} = useUserApi();
const ability = useAbility();

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
  name: "",
  email: "",
  company_name: "",
  role: "",
  phone: "",
  designation: "",
  status: 1,
});
const submitting = ref(false);
const formRef = ref(null);

// Form validation rules
const rules = {
  required: (value) => !!value || "This field is required",
  email: (value) => {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return pattern.test(value) || "Please enter a valid email address";
  },
  website: (value) => {
    if (!value) return true; // Optional field
    const pattern = /^https?:\/\/.+/;
    return (
      pattern.test(value) ||
      "Please enter a valid URL (starting with http:// or https://)"
    );
  },
  phone: (value) => {
    if (!value) return true; // Optional field
    const pattern = /^[\+]?[0-9\-\(\)\s]+$/;
    return pattern.test(value) || "Please enter a valid phone number";
  },
};

const showImportResult = ref(false);
const importResult = ref({
  data: {},
  message: "",
  success: false,
});

const importDialog = ref(false);
const selectedFile = ref(null);
const fileError = ref("");
const importing = ref(false);
const isDragOver = ref(false);

const page = ref(1);
const sortBy = ref();
const orderBy = ref();

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

const loadUsers = async () => {
  const filters = buildFilters();
  // await fetchclients(filters, pagination.value.page);
  await fetchUsers(filters, pagination.value.page);
};

// Open dialog for a client
const openEditDialog = (client) => {
  selectedClient.value = { ...client };
  clientData.value = {
    id: client.id,
    name: client.name || "",
    email: client.email || "",
    company_name: client.company_name || "",
    role: client.role || "",
    phone: client.phone || "",
    designation: client.designation || "",
    status: client.status || 1,
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
    await loadUsers();
  } catch (error) {
    console.error("Update failed:", error);
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
  await loadUsers();
  showAlert("Filters  Cleared !", "info");
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
      text: "Active",
    };
  if (status == 2)
    return { color: "error", icon: "tabler-progress-x", text: "InActive" };
};

const getRoleConfig = (status) => {
  if (status == "2")
    return {
      color: "info",
      icon: "tabler-user",
      text: "Client",
    };
  if (status == "1")
    return { color: "error", icon: "tabler-user", text: "SuperAdmin" };
};

const applyFilters = async () => {
  pagination.value.page = 1;
  await loadUsers();
};

const handleDeleteClient = async (id) => {
  try {
    await deleteUser(id);
    const index = selectedRows.value.findIndex((row) => row === id);
    if (index !== -1) selectedRows.value.splice(index, 1);
    await loadUsers(); // Refresh the list
    // showAlert("Client deleted successfully!", "success");
  } catch (error) {
    console.error("Delete failed:", error);
    // showAlert("Failed to delete client", "error");
  }
};

const handleDeleteClientBatch = async (ids) => {
  loading.value = true;
  try {
    await Promise.all(ids.map((id) => deleteUser(id)));
    selectedRows.value = [];
    await loadDomains(clientId.value);
  } catch (error) {
    ss;
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
    "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
    "application/vnd.ms-excel",
  ];

  if (!allowedTypes.includes(file.type)) {
    fileError.value = "Please select a valid Excel file (.xlsx or .xls)";
    return;
  }

  if (file.size > 10 * 1024 * 1024) {
    // 10MB
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

// const summaryStats = computed(() => {
//   const totalKeywords = clients.value.reduce(
//     (sum, report) => sum + (report.length || 0),
//     0
//   );
//   const totalAccepted = clients.value.reduce(
//     (sum, report) => sum + (report.accepted_keywords || 0),
//     0
//   );
//   const totalRejected = clients.value.reduce(
//     (sum, report) => sum + (report.rejected_keywords || 0),
//     0
//   );
//   const overallSuccessRate =
//     totalKeywords > 0 ? Math.round((totalAccepted / totalKeywords) * 100) : 0;

//   return {
//     totalReports: clients.value.length,
//     totalKeywords,
//     totalAccepted,
//     totalRejected,
//     overallSuccessRate,
//   };
// });

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
      await loadUsers();
    }
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
    showAlert("Export functionality to be implemented", "info");
  } catch (err) {
    console.error(err);
    showAlert("Export failed", "error");
  }
};

const totalDomains = computed(() => pagination.value.total ?? 0);

const statusOptions = [
  { title: "Active", value: 1, color: "success" },
  { title: "Inactive", value: 2, color: "error" },
];

const approvalStatusOptions = [
  { title: "Pending", value: 1, color: "warning" },
  { title: "Rejected", value: 2, color: "error" },
  { title: "Approved", value: 3, color: "success" },
];

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

  if (options.sortBy && options.sortBy.length > 0) {
    sortBy.value = options.sortBy[0].key
    orderBy.value = options.sortBy[0].order
  } else {
    sortBy.value = null
    orderBy.value = null
  }

  await loadUsers();
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
              <VIcon icon="tabler-user"></VIcon>
            </VAvatar>
            <div>
              <h1 class="text-h3 font-weight-bold mb-1">User Management</h1>
              <p class="text-body-1 text-medium-emphasis mb-0">
                Manage and monitor your user portfolio
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
      </div>
    </VCardTitle>

    <VCardText class="pt-0">
      <VRow align="end" justify="space-between">
        <VCol cols="12" md="6">
          <AppTextField v-model="searchQuery" placeholder="Search by name, email, company, or client details..."
            prepend-inner-icon="tabler-search" variant="outlined" hide-details clearable class="search-field" />
        </VCol>

        <VCol cols="12" sm="6" md="3">
          <AppSelect v-model="selectedStatus" label="Status" :items="statusOptions" variant="outlined" hide-details
            clearable prepend-inner-icon="tabler-circle-dot" />
        </VCol>

        <VCol cols="12" sm="6" md="3" class="d-flex justify-end">
          <VBtn color="primary" variant="flat" block @click="loadUsers">
            <VIcon icon="tabler-search" class="me-2" />
            Search
          </VBtn>
        </VCol>
      </VRow>
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
        <div>
          <h4 class="text-subtitle-1 font-weight-bold text-success mb-2">
            Successful Imports ({{ importResult.data?.imported?.length || 0 }})
          </h4>
          <div style="max-height: 150px; overflow-y: auto; scroll-behavior: smooth" class="pr-2">
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
          <div style="max-height: 150px; overflow-y: auto; scroll-behavior: smooth" class="pr-2">
            <VList density="compact">
              <VListItem v-for="(f, idx) in importResult.data?.failed || []" :key="'f-' + idx">
                <VListItemTitle>
                  <span class="text-primary">{{
                    f.name || f.email || "N/A"
                    }}</span>
                  — {{ f.reason }}
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
        <VBtn v-if="selectedRows.length" variant="text" @click="handleDeleteClientBatch(selectedRows)" size="small"
          color="error">
          <VIcon icon="tabler-trash" class="me-1" />
          Delete Selected
        </VBtn>
      </div>
<!--      <div v-if="ability.can('update', 'client')" class="d-flex gap-4 flex-wrap align-center">-->
      <div class="d-flex gap-4 flex-wrap align-center">
        <AppSelect v-model="itemsPerPage" :items="[5, 10, 20, 25, 50]" />

        <!-- Excel Import Dialog Button -->
        <VBtn v-if="ability.can('import','user')" variant="tonal" color="secondary" prepend-icon="tabler-download" @click="importDialog = true">
          Import
        </VBtn>

        <!-- Export button -->
        <VBtn v-if="ability.can('export','user')" variant="tonal" color="secondary" prepend-icon="tabler-upload" @click="handleExportReports">
          Export
        </VBtn>

        <!-- Add Client button -->
        <VBtn color="primary" prepend-icon="tabler-plus" @click="$router.push('/users/add')" v-if="ability.can('create', 'user')">
          Add User
        </VBtn>
      </div>
    </div>

    <VDivider class="mt-5 mb-2" />

    <!-- Enhanced Data Table -->
    <VDataTableServer :page="pagination.page" :items-per-page="pagination.itemsPerPage"
      v-model:model-value="selectedRows" :headers="headers" show-select :items="Users" :loading="loading"
      :items-length="pagination.total" loading-text="Fetching clients, please wait..." hover
      @update:options="updateOptions">
      <template #item.website="{ item }">
        <a v-if="item.website" :href="item.website" target="_blank"
          class="text-success text-decoration-none text-body-1" :title="item.website">
          <span class="text-truncate" style="max-width: 200px">
            {{ item.website }}
          </span>
          <VIcon icon="tabler-external-link" size="12" class="flex-shrink-0 ms-1" />
        </a>
        <span v-else class="text-grey">N/A</span>
      </template>

      <template #item.role="{ item }">
        <VChip :color="getRoleConfig(item.role.name)?.color || 'default'" variant="tonal" size="small" class="ma-1">
          <VIcon :icon="getRoleConfig(item.role.name)?.icon || 'tabler-circle'" size="14" class="me-1" />
<!--          {{ getRoleConfig(item.role)?.text || "Unknown" }}-->
          {{ item.role.name }}
        </VChip>
      </template>

      <template #item.status="{ item }">
        <VChip :color="getStatusConfig(item.status)?.color || 'default'" variant="tonal" size="small" class="ma-1">
          <VIcon :icon="getStatusConfig(item.status)?.icon || 'tabler-circle'" size="14" class="me-1" />
          {{ getStatusConfig(item.status)?.text || "Unknown" }}
        </VChip>
      </template>


      <template #item.actions="{ item }">
          <VTooltip v-if="ability.can('view', 'user')" text="View Details">
            <template #activator="{ props }">
              <IconBtn v-bind="props" size="small">
                <router-link :to="{ name: 'users-view', params: { id: item.id } }">
                  <VIcon icon="tabler-eye" size="20" />
                </router-link>
              </IconBtn>
            </template>
          </VTooltip>

          <VTooltip v-if="ability.can('update', 'user')" text="Edit User">
            <template #activator="{ props }">
              <IconBtn v-bind="props" size="small" :to="{ name: 'users-edit', params: { id: item.id } }">
                <VIcon color="info" icon="tabler-edit" size="20" />
              </IconBtn>
            </template>
          </VTooltip>

          <!-- <VTooltip v-if="ability.can('view','rivaldomain')" text="View Domains for This Client">
              <template #activator="{ props }">
                <IconBtn v-bind="props" size="small"
                  @click="$router.push({ name: 'domain-clientdomain-list', params: { id: item.id } })">
                  <VIcon color="success" icon="tabler-world" size="20" />
                </IconBtn>
              </template>
            </VTooltip> -->

          <VTooltip v-if="ability.can('delete', 'user')" text="Delete User">
            <template #activator="{ props }">
              <IconBtn v-bind="props" size="small" @click="handleDeleteClient(item.id)">
                <VIcon icon="tabler-trash" size="20" color="error" />
              </IconBtn>
            </template>
          </VTooltip>
      </template>

      <!-- Empty State -->
      <template #no-data>
        <div class="text-center pa-8">
          <VIcon icon="tabler-user-off" size="48" class="text-medium-emphasis mb-4" />
          <h3 class="text-h6 mb-2">No user found</h3>
          <p class="text-body-2 text-medium-emphasis mb-4" v-if="ability.can('create', 'user')">
            Try adjusting your search criteria or add a new user to get
            started.
          </p>
          <VBtn color="primary" @click="$router.push('/users/add')" v-if="ability.can('create', 'user')">
            <VIcon icon="tabler-plus" class="me-2" />
            Add First User
          </VBtn>
        </div>
      </template>

      <template #bottom>
        <TablePagination v-model:page="pagination.page" :items-per-page="pagination.itemsPerPage"
          :total-items="totalDomains" />
      </template>
    </VDataTableServer>
  </VCard>

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
                  <li>
                    Optional columns: website, phone, industry, city, state,
                    zip_code, country
                  </li>
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
        <VBtn color="primary" :loading="importing" variant="flat" @click="handleImportClients">
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

  &:hover,
  &--dragover {
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
