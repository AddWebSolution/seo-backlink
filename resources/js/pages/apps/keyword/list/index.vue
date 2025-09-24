<script setup>
import { onMounted, ref, computed } from "vue";
import { useKeywordApi } from "@/composables/KeywordApi";
import { IconWorldWww } from "@tabler/icons-vue";

const headers = [
  { title: "ID", key: "id", align: "start", width: "60px" },
  {
    title: "Domain",
    key: "domain.title",
    align: "center",
    width: "80px",
  },
  { title: "Keyword", key: "keyword", align: "center", width: "100px" },
  { title: "Status", key: "status", align: "center", width: "20px" },
  {
    title: "Processed At",
    key: "processed_at",
    align: "center",
    width: "50px",
  },
  {
    title: "Actions",
    key: "actions",
    sortable: false,
    align: "start",
    width: "10px",
  },
];

const {
  keywords,
  pagination,
  loading,
  error,
  fetchKeywords,
  deleteKeyword,
  updateKeyword,
  downloadTemplate,
  importKeywords,
  showAlert,
} = useKeywordApi();

// Filters
const selectedStatus = ref();
const selectedCountry = ref();
const searchQuery = ref("");
const selectedRows = ref([]);
const showAdvancedFilters = ref(false);

const keywordEditDialog = ref(false);
const selectedkeyword = ref(null);
const formValid = ref(false);



// Excel Import Dialog State
const importDialog = ref(false);
const selectedFile = ref(null);
const fileError = ref("");
const importing = ref(false);
const isDragOver = ref(false);

// Build filters object
const buildFilters = () => {
  const filters = {};

  if (selectedStatus.value) filters.status = selectedStatus.value;
  if (searchQuery.value) filters.searchTerm = searchQuery.value;
  if (sortBy.value) filters.sortField = sortBy.value;
  if (orderBy.value) filters.sortOrder = orderBy.value;

  filters.pageNumber = pagination.value.page;
  filters.perPage = pagination.value.itemsPerPage;

  return filters;
};

const loadKeywords = async () => {
  const filters = buildFilters();
  console.log('run', 'run')
  await fetchKeywords(filters, pagination.value.page);
};

const clearAllFilters = async () => {
  selectedStatus.value = null;
  selectedCountry.value = "";
  searchQuery.value = "";
  pagination.value.page = 1;
  await loadKeywords();
  showAlert("Custom message here!", "info");
};

const hasActiveFilters = computed(() => {
  return (
    selectedStatus.value ||
    selectedCountry.value ||
    searchQuery.value
  );
});


// Excel Import Dialog Functions
const validateFile = (file) => {
  const allowedTypes = [
    "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", // .xlsx
    "application/vnd.ms-excel", // .xls
  ];

  const maxSize = 10 * 1024 * 1024; // 10MB

  if (!allowedTypes.includes(file.type)) {
    return "Please select a valid Excel file (.xlsx or .xls)";
  }

  if (file.size > maxSize) {
    return "File size must be less than 10MB";
  }

  return null;
};


const handleFileChange = (event) => {
  const files = event.target?.files || event;
  if (files && files.length > 0) {
    const file = files[0];
    const error = validateFile(file);

    if (error) {
      fileError.value = error;
      selectedFile.value = null;
    } else {
      fileError.value = "";
      selectedFile.value = file;
    }
  }
};

const clearFile = () => {
  selectedFile.value = null;
  fileError.value = "";
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
  if (!e.currentTarget.contains(e.relatedTarget)) {
    isDragOver.value = false;
  }
};

const handleDrop = (e) => {
  e.preventDefault();
  isDragOver.value = false;

  const files = e.dataTransfer.files;
  if (files.length > 0) {
    handleFileChange(files);
  }
};


const handleImportKeywords = async () => {
  if (!selectedFile.value) {
    showAlert("Please select a file first", "error");
    return;
  }

  importing.value = true;

  try {
    const result = await importKeywords(selectedFile.value);

    if (result.success) {
      closeImportDialog();
      selectedFile.value = null;
    }
  } catch (err) {
    showAlert("Import failed", "error");
  } finally {
    importing.value = false;
  }
};

const closeImportDialog = () => {
  importDialog.value = false;
  clearFile();
  importing.value = false;
};

const openEditKeywordDialog = (keyword) => {
  selectedkeyword.value = { ...keyword };
  keywordEditDialog.value = true;
};

const saveKeyword = async () => {
  if (!formValid.value) return;

  try {
    await updateKeyword(selectedkeyword.value.id, {
      keyword: selectedkeyword.value.keyword,
      status: selectedkeyword.value.status,
    });

    keywordEditDialog.value = false;
  } catch (err) {
    console.error("Failed to save keyword:", err);
  }
};


const formatFileSize = (bytes) => {
  if (bytes === 0) return "0 Bytes";
  const k = 1024;
  const sizes = ["Bytes", "KB", "MB", "GB"];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
};

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
  page.value = 1;
  // await loadKeywords();
};

const handleDeleteKeyword = async (id) => {
  try {
    await deleteKeyword(id);
    const index = selectedRows.value.findIndex((row) => row === id);
    if (index !== -1) selectedRows.value.splice(index, 1);
  } catch (error) {
    console.error("Delete failed:", error);
  }
};

// Computed total domains count
const totalKeywords = computed(() => pagination.value.total ?? 0);

// Status options
const statusOptions = [
  { title: "Active", value: 1, color: "success" },
  { title: "Deactivate", value: 2, color: "error" },
];

const sortBy = ref("");
const orderBy = ref("");

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

  // Sorting
  if (options.sortBy && options.sortBy.length > 0) {
    sortBy.value = options.sortBy[0];       
    orderBy.value = options.sortDesc[0] ? "desc" : "asc";
  } else {
    sortBy.value = null;
    orderBy.value = null;
  }
  await loadKeywords();
};

const statusMap = {
  1: 'active',
  2: 'inactive',
  3: 'pending',
};

const getStatusIcon = (status) => {
  const s = statusMap[status] || 'unknown';
  const icons = {
    active: 'tabler-circle-check',
    inactive: 'tabler-circle-x',
    pending: 'tabler-clock',
    unknown: 'tabler-circle',
  };
  return icons[s];
};

const getStatusColor = (status) => {
  const s = statusMap[status] || 'unknown';
  const colors = {
    active: 'success',
    inactive: 'error',
    pending: 'warning',
    unknown: 'default',
  };
  return colors[s];
};



const templateDownload = async () => {
  try {
    const blob = await downloadTemplate();

    const url = window.URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = url;
    link.setAttribute("download", "keyword_import_template.xlsx");
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);

    showAlert("Template downloaded successfully!", "success");
  } catch (err) {
    console.error(err);
  }
};

const handleExportReports = () => {
  showAlert("Export functionality coming soon!", "info");
};

onMounted(() => {
  const preventDefaults = (e) => {
    e.preventDefault();
    e.stopPropagation();
  };
  document.addEventListener("dragover", preventDefaults);
  document.addEventListener("drop", preventDefaults);
});
</script>

<template>
  <!-- Header Section -->
  <VCard class="mb-6 pa-6 overflow-hidden" elevation="0">
    <VContainer fluid>
      <VRow align="center">
        <VCol cols="12" md="8">
          <div class="d-flex align-center">
            <VAvatar size="64" color="primary" variant="elevated" class="me-4">
              <VIcon icon="tabler-key" size="26"></VIcon>
            </VAvatar>
            <div>
              <h1 class="text-h3 font-weight-bold mb-1">Keyword Management</h1>
              <p class="text-body-1 text-medium-emphasis mb-0">
                Manage and monitor your domain keywords portfolio
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
      <!-- Primary Search Bar -->
      <VRow class="mb-4">
        <VCol cols="12">
          <AppTextField v-model="searchQuery" placeholder="Search by title, URL, or any domain details..."
            prepend-inner-icon="tabler-search" variant="outlined" hide-details clearable class="search-field" />
        </VCol>
      </VRow>

      <!-- Quick Filters -->
      <VRow class="mb-4">
        <VCol cols="12" sm="6" md="3">
          <AppSelect v-model="selectedStatus" label="Status" :items="statusOptions" variant="outlined" clearable
            hide-details prepend-inner-icon="tabler-circle-dot" />
        </VCol>
        <!-- <VCol cols="12" sm="6" md="3">
          <AppTextField
            v-model="selectedCountry"
            label="Country"
            placeholder="Enter country"
            variant="outlined"
            clearable
            hide-details
            prepend-inner-icon="tabler-world"
          />
        </VCol> -->
        <VCol cols="12" sm="6" md="3" class="d-flex align-end">
          <VBtn color="primary" variant="flat" block @click="fetchKeywords">
            <VIcon icon="tabler-search" class="me-2" />
            Search
          </VBtn>
        </VCol>
      </VRow>
    </VCardText>
  </VCard>

  <!-- Results Summary -->
  <VCard elevation="1">
    <div class="d-flex flex-wrap gap-4 ma-6">
      <div class="d-flex align-center text-body-1 font-weight-regular my-4">
        <span class="font-weight-medium text-h6">
          {{ totalKeywords }}
        </span>
        <span class="ml-2">Keywords found</span>

        <VChip v-if="selectedRows.length" color="primary" size="small" class="ml-4" elevation="2" outlined>
          {{ selectedRows.length }} selected
        </VChip>
      </div>

      <VSpacer />
      <div class="d-flex align-center gap-2">
        <VBtn v-if="selectedRows.length" variant="text" size="small" color="error">
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

        <VBtn variant="tonal" color="secondary" prepend-icon="tabler-upload" @click="handleExportReports">
          Export
        </VBtn>

        <VBtn color="primary" prepend-icon="tabler-plus" @click="$router.push('/apps/keyword/add')">
          Add Keyword
        </VBtn>
      </div>
    </div>

    <VDivider class="mt-5 mb-2" />

    <!-- Enhanced Data Table -->
    <VDataTableServer v-model:page="pagination.page" v-model:items-per-page="pagination.itemsPerPage"
      v-model:model-value="selectedRows" :headers="headers" show-select :items="keywords" :loading="loading"
      :items-length="pagination.total" loading-text="Fetching keywords, please wait..." class="keyword-table" hover
      @update:options="updateOptions">
      <template #item.status="{ item }">
        <VChip :color="item.status == '1' ? 'success' : 'error'" variant="tonal" size="small" class="ma-1">
          {{ item.status == 1 ? "Active" : "Deactivated" }}
        </VChip>
      </template>

      <template #item.processed_at="{ item }">
        {{
        item.processed_at
        ? new Date(item.processed_at).toLocaleString()
        : "Not Processed"
        }}
      </template>

      <template #item.actions="{ item }">
        <div class="d-flex">
          <VTooltip text="View Details">
            <template #activator="{ props }">
              <IconBtn v-bind="props" size="small">
                <router-link :to="{ name: 'apps-keyword-view', params: { id: item.id } }">
                  <VIcon icon="tabler-eye" size="24" />
                </router-link>
              </IconBtn>
            </template>
          </VTooltip>

          <VTooltip text="Edit">
            <template #activator="{ props }">
              <IconBtn v-bind="props" size="small" @click="openEditKeywordDialog(item)">
                <VIcon color="info" icon="tabler-edit" size="24" />
              </IconBtn>
            </template>
          </VTooltip>

          <VTooltip text="Delete">
            <template #activator="{ props }">
              <IconBtn v-bind="props" size="small" @click="handleDeleteKeyword(item.id)">
                <VIcon color="error" icon="tabler-trash" size="24" />
              </IconBtn>
            </template>
          </VTooltip>
        </div>
      </template>

      <template #no-data>
        <div class="text-center pa-8">
          <VIcon icon="tabler-search-off" size="48" class="text-medium-emphasis mb-4" />
          <h3 class="text-h6 mb-2">No keywords found</h3>
          <p class="text-body-2 text-medium-emphasis mb-4">
            Try adjusting your search criteria or add new keywords to get
            started.
          </p>
          <VBtn color="primary" :to="{ name: 'apps-keyword-add' }">
            <VIcon icon="tabler-plus" class="me-2" />
            Add First Keyword
          </VBtn>
        </div>
      </template>
      <template #bottom>
        <TablePagination v-model:page="pagination.page" :items-per-page="pagination.itemsPerPage"
          :total-items="totalKeywords" />
      </template>
    </VDataTableServer>
  </VCard>


  <!-- Edit keyword dialog -->
  <VDialog v-model="keywordEditDialog" max-width="900" persistent scrollable>
    <VCard v-if="selectedkeyword" elevation="0">
      <VCardTitle class="pa-0 position-relative">
        <div class="pa-6">
          <div class="d-flex align-center justify-space-between w-100">
            <div class="d-flex align-center">
              <VAvatar size="56" class="me-4" variant="tonal">
                <VIcon icon="tabler-edit" size="28" />
              </VAvatar>
              <div>
                <h3 class="text-h4 font-weight-bold mb-1">
                  Edit Keyword
                </h3>
                <div class="d-flex align-center">
                  <VChip size="small" variant="tonal" class="me-3">
                    #{{ selectedkeyword.id }}
                  </VChip>
                  <span class="text-caption">
                    Created {{ selectedkeyword.processed_at }}
                  </span>
                </div>
              </div>
            </div>
            <VBtn icon="tabler-x" variant="text" color="white" size="large" class="rounded-circle"
              @click="keywordEditDialog = false" />
          </div>
        </div>
      </VCardTitle>

      <!-- Main Content -->
      <VCardText class="pa-8" style="max-height: 65vh; overflow-y: auto">
        <VForm ref="keywordForm" v-model="formValid">
          <VRow class="mb-4">
            <VCol cols="12" md="6">
              <div class="mb-2">
                <label class="text-subtitle-2 font-weight-medium text-medium-emphasis mb-2 d-block">
                  Keyword *
                </label>
              </div>
              <VTextField v-model="selectedkeyword.keyword" placeholder="Enter keyword"
                :rules="[v => !!v || 'Keyword is required']" variant="outlined" color="primary" required>
                <template v-slot:prepend-inner>
                  <VIcon icon="tabler-hash" size="20" class="text-medium-emphasis" />
                </template>
              </VTextField>
            </VCol>

            <VCol cols="12" md="6">
              <div class="mb-2">
                <label class="text-subtitle-2 font-weight-medium text-medium-emphasis mb-2 d-block">
                  Status *
                </label>
              </div>
              <VSelect  v-model="selectedkeyword.status" :items="statusOptions" item-title="title" item-value="value"
                placeholder="Select status" variant="outlined" color="primary" required>
              <template v-slot:selection="{ item }">
                <div class="d-flex align-center">
                  <VIcon :icon="getStatusIcon(item.value)" :color="getStatusColor(item.value)" size="16" class="me-2" />
                  {{ item.title }}
                </div>
              </template>

              <template v-slot:item="{ props, item }">
                <VListItem v-bind="props">
                  <template v-slot:prepend>
                    <VIcon :icon="getStatusIcon(item.raw.value)" :color="getStatusColor(item.raw.value)" size="16" />
                  </template>
                </VListItem>
              </template>
              </VSelect>
            </VCol>
          </VRow>

          <!-- Additional Information Card -->
          <VCard variant="tonal" color="primary" class="rounded-lg mb-4" elevation="0">
            <VCardText class="pa-4">
              <div class="d-flex align-center mb-2">
                <VIcon icon="tabler-info-circle" size="20" class="me-2" />
                <span class="text-subtitle-2 font-weight-medium">
                  Keyword Information
                </span>
              </div>
              <div class="text-body-2 text-medium-emphasis">
                Last modified: {{ selectedkeyword.updated_at || 'Never' }}
              </div>
            </VCardText>
          </VCard>
        </VForm>
      </VCardText>

      <!-- Actions with better spacing and styling -->
      <VDivider class="mx-6" />

      <VCardActions class="pa-6 d-flex justify-end">
        <VBtn color="default" variant="text" size="large" class="me-2 px-6" @click="keywordEditDialog = false">
          Cancel
        </VBtn>
        <VBtn color="primary" variant="flat" size="large" class="px-8 rounded-lg" :disabled="!formValid"
          @click="saveKeyword">
          <VIcon icon="tabler-check" class="me-1" size="18" />
          Save Changes
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>



  <!-- Excel Import Dialog -->
  <VDialog v-model="importDialog" max-width="600" persistent>
    <VCard>
      <VCardTitle class="pa-6 pb-4">
        <div class="d-flex align-center">
          <VIcon icon="tabler-file-excel" class="me-3 text-success" size="32" />
          <div>
            <h3 class="text-h5 font-weight-bold">Import Keywords</h3>
            <p class="text-body-2 text-medium-emphasis mb-0">
              Upload an Excel file to import multiple keywords
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
                  <li>Required columns: keyword, status, country</li>
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
        <VBtn variant="flat" @click="closeImportDialog" :disabled="importing">
          Cancel
        </VBtn>
        <VBtn color="primary" :loading="importing" :disabled="!selectedFile || !!fileError"
          @click="handleImportKeywords">
          <VIcon icon="tabler-download" class="me-2" />
          Import Keywords
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

.drop-zone {
  border: 2px dashed rgb(var(--v-theme-primary));
  border-radius: 12px;
  padding: 3rem 2rem;
  transition: all 0.3s ease;
  background-color: rgba(var(--v-theme-primary), 0.02);
  cursor: pointer;

  &:hover {
    background-color: rgba(var(--v-theme-primary), 0.04);
    border-color: rgb(var(--v-theme-primary));
  }

  &--dragover {
    background-color: rgba(var(--v-theme-primary), 0.08);
    border-color: rgb(var(--v-theme-primary));
    border-style: solid;
    transform: scale(1.02);
  }
}

// Domain table improvements
.keyword-table {
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

.v-file-input {
  :deep(.v-field__input) {
    font-size: 0.875rem;
  }
}

.v-expansion-panel {
  :deep(.v-expansion-panel-title) {
    font-size: 0.875rem;
    font-weight: 500;
  }
}

// Animation for dialog
.v-dialog {
  :deep(.v-overlay__content) {
    animation: dialogFadeIn 0.3s ease-out;
  }
}

@keyframes dialogFadeIn {
  from {
    opacity: 0;
    transform: translateY(-20px) scale(0.95);
  }

  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

// Responsive improvements
@media (max-width: 1024px) {
  .keyword-table table {
    min-width: 1000px;
  }
}

@media (max-width: 768px) {
  .keyword-table {
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

  .drop-zone {
    padding: 2rem 1rem;
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

.bg-gradient-primary {
  background: linear-gradient(135deg, rgb(var(--v-theme-primary)) 0%, rgba(var(--v-theme-primary), 0.8) 100%);
}

.rounded-xl {
  border-radius: 16px !important;
}

.rounded-lg {
  border-radius: 12px !important;
}

.text-white-80 {
  color: rgba(255, 255, 255, 0.8) !important;
}
</style>