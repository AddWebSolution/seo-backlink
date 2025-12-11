<script setup>
import { IconTableImport } from "@tabler/icons-vue";
import { VBtn } from "vuetify/components";
import {computed, ref, onMounted} from "vue";
import { useMasterBacklinksApi } from "@/composables/masterBacklinks.js";
import AppSelect from "@core/components/app-form-elements/AppSelect.vue";
import {useConfirmDialog} from "@/composables/useConfirmDialog.js";
import { getCodes } from 'country-list';
import {CATEGORIES, PLATFORM_TYPES} from "@/utils/backlinkOptions.js";

// Reactive variables
const showImportResult = ref(false);
const importing = ref(false);
const downloading = ref(false);
const selectedFile = ref(null);
const importDialog = ref(false);
const fileError = ref("");
const isDragOver = ref(false);
const selectedRows = ref([]);
const searchQuery = ref("");
const selectedPlateform = ref(null)
const selectedCountry = ref(null)
const selectedCategory = ref(null)
const selectedDoFollow = ref(null)
const selectedIndexed = ref(null)
const { confirm } = useConfirmDialog()
const countryCodes = getCodes();

const {
  masterBacklinks,
  pagination,
  loading,
  error,
  downloadTemplate,
  importMasterBacklinks,
  fetchMasterBacklinks,
  deleteMasterBacklink,
  showAlert,
} = useMasterBacklinksApi();

const closeImportDialog = () => {
  importDialog.value = false;
  importing.value = false;
  downloading.value = false;
  selectedFile.value = null;
  fileError.value = "";
};

const clearFile = () => {
  selectedFile.value = null;
  fileError.value = "";
};

const formatFileSize = (bytes) => {
  if (!bytes) return "0 Bytes";
  const sizes = ["Bytes", "KB", "MB", "GB"];
  const i = Math.floor(Math.log(bytes) / Math.log(1024));
  return (bytes / Math.pow(1024, i)).toFixed(2) + " " + sizes[i];
};

/* --------------------------------------------
   TEMPLATE DOWNLOAD
-------------------------------------------- */
const templateDownload = async () => {
  try {
    downloading.value = true;
    const blob = await downloadTemplate();

    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "master_backlinks_import_template.xlsx";
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url);

    downloading.value = false;
    showAlert("Template downloaded successfully!", "success");
  } catch (err) {
    showAlert("Failed to download template", "error");
  }
};

/* --------------------------------------------
   MAIN LISTING FUNCTION
-------------------------------------------- */

const buildFilters = () => {
  const filters = {};

  if (selectedPlateform.value) filters.platform_type = selectedPlateform.value;
  if (selectedCountry.value) filters.country = selectedCountry.value;
  if (selectedCategory.value) filters.categories = selectedCategory.value;
  if (selectedDoFollow.value !== null) filters.dofollow = selectedDoFollow.value;
  if (selectedIndexed.value !== null) filters.indexed = selectedIndexed.value;

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

const clearAllFilters = async () => {
  selectedPlateform.value = null;
  selectedCountry.value = null;
  selectedCategory.value = null;
  selectedDoFollow.value = null;
  selectedIndexed.value = null;
  sortBy.value = null;
  orderBy.value = null;
  searchQuery.value = "";
  pagination.value.page = 1;
  await loadMasterBacklinks();
  showAlert("Filters  Cleared !", "info");
};

const hasActiveFilters = computed(() => {
  return (
      sortBy.value ||
      orderBy.value ||
      selectedPlateform.value ||
      selectedCountry.value ||
      selectedCategory.value ||
      selectedDoFollow.value !== null ||
      selectedIndexed.value !== null ||
      searchQuery.value
  );
});

const loadMasterBacklinks = async () => {
  const filters = buildFilters();
  await fetchMasterBacklinks(filters, pagination.value.page);
};

/* --------------------------------------------
   DATA TABLE
-------------------------------------------- */
const sortBy = ref("");
const orderBy = ref("");

const headers = [
  { title: "Website", key: "website_name", align: "start", minWidth: "150px" },
  { title: "Domain URL", key: "domain_url", align: "start", minWidth: "180px" },
  { title: "DA", key: "da", align: "center", width: "70px" },
  { title: "Profile", key: "profile_url", align: "start", minWidth: "180px" },
  { title: "Platform", key: "platform_type", align: "center", width: "100px" },
  { title: "Country", key: "country", align: "center", width: "90px" },
  { title: "Categories", key: "categories", align: "start", minWidth: "200px" },
  { title: "Follow", key: "dofollow", align: "center", width: "70px" },
  { title: "Index", key: "indexed", align: "center", width: "70px" },
  { title: "Active", key: "last_active", align: "center", width: "100px" },
  { title: "Traffic", key: "avg_traffic", align: "center", width: "90px" },
  { title: "Age", key: "domain_age", align: "center", width: "80px" },
  { title: "Actions", key: "actions", align: "center", width: "60px", sortable: false },
];

const totalMasterBacklinks = computed(() => pagination.value.total ?? 0);

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
    sortBy.value = options.sortBy[0].key;
    orderBy.value = options.sortBy[0].order;
  } else {
    sortBy.value = null;
    orderBy.value = null;
  }

  await loadMasterBacklinks();
};

/* --------------------------------------------
  DELETE BACKLINKS
-------------------------------------------- */
const handleDeleteBacklinks = async (id) => {
  const confirmed = await confirm({
    title: 'Delete Master Backlink',
    message: 'Are you sure you want to delete this master backlink? This action cannot be undone.',
    confirmText: 'Delete',
    cancelText: 'Cancel',
    confirmColor: 'error',
    type: 'error'
  })

  if (!confirmed) {
    return
  }
  try {
    await deleteMasterBacklink(id);
    const index = selectedRows.value.findIndex((row) => row === id);
    if (index !== -1) selectedRows.value.splice(index, 1);
    await loadMasterBacklinks();
  } catch (error) {
    console.error("Delete failed:", error);
  }
};

const handleDeleteBacklinkBatch = async (ids) => {
  const confirmed = await confirm({
    title: 'Delete Master Backlink',
    message: `Are you sure you want to delete ${ids.length} backlinks?`,
    confirmText: 'Delete',
    cancelText: 'Cancel',
    confirmColor: 'error',
    type: 'error'
  })

  if (!confirmed) {
    return
  }
  loading.value = true;
  console.log(ids)
  try {
    await Promise.all(ids.map((id) => deleteMasterBacklink(id)));
    selectedRows.value = [];
    await loadMasterBacklinks();
  } catch (error) {
    console.error("Delete failed:", error);
  }
};

/* --------------------------------------------
   VALIDATE FILE INPUT
-------------------------------------------- */
const validateFile = (file) => {
  fileError.value = "";

  if (!file) {
    fileError.value = "No file selected.";
    return false;
  }

  const validExtensions = ["xlsx", "xls"];
  const ext = file.name.split(".").pop().toLowerCase();

  if (!validExtensions.includes(ext)) {
    fileError.value =
        "Invalid file format. Upload an Excel file (.xlsx or .xls).";
    return false;
  }

  // Maximum file size 10MB
  if (file.size > 10 * 1024 * 1024) {
    fileError.value = "File size must be less than 10MB";
    return false;
  }

  return true;
};

/* --------------------------------------------
   FILE CHANGE HANDLER
-------------------------------------------- */
const handleFileChange = (files) => {
  const file = Array.isArray(files) ? files[0] : files;

  if (!validateFile(file)) {
    selectedFile.value = null;
    return;
  }

  selectedFile.value = file;
};

const DOFOLLOW_OPTIONS = [
  { text: 'Yes', value: 1 },
  { text: 'No', value: 0 },
]

const INDEXED_OPTIONS = [
  { text: 'Yes', value: 1 },
  { text: 'No', value: 0 },
]


/* --------------------------------------------
   DRAG & DROP FUNCTIONS
-------------------------------------------- */
const handleDragOver = (e) => {
  e.preventDefault();
  isDragOver.value = true;
};

const handleDragEnter = (e) => {
  e.preventDefault();
  isDragOver.value = true;
};

const handleDragLeave = () => {
  isDragOver.value = false;
};

const handleDrop = (e) => {
  e.preventDefault();
  isDragOver.value = false;

  const file = e.dataTransfer.files[0];
  handleFileChange(file);
};

/* --------------------------------------------
   IMPORT BACKLINKS
-------------------------------------------- */
const importResult = ref({
  message: "",
  success: false,
  inserted: 0,
  duplicates: 0,
});

const handleImportMasterBacklinks = async () => {
  if (!selectedFile.value) {
    showAlert("Please select a file first", "error");
    return;
  }

  importing.value = true;

  try {
    const res = await importMasterBacklinks(selectedFile.value);

    if (res.success) {
      importResult.value = {
        message: res.data?.message ?? "Import Completed",
        success: true,
        skippedRows: res.data?.skippedRows ?? 0,
        inserted: res.data?.inserted ?? 0,
        duplicates: res.data?.duplicates ?? 0,
        duplicateUrls: res.data?.duplicateUrls ?? [],
      };

      showImportResult.value = true;
      selectedFile.value = null;
      fileError.value = "";
    } else {
      showAlert(res.message || "Import failed", "error");
    }

    importDialog.value = false;
    await loadMasterBacklinks();
  } catch (err) {
    showAlert("Import failed", "error");
  } finally {
    importing.value = false;
  }
};

/* --------------------------------------------
   CATEGORIES DISPLAY LOGIC
-------------------------------------------- */
const dropdownOpen = ref(null);
const toggleDropdown = (id) => {
  dropdownOpen.value = dropdownOpen.value === id ? null : id;
};

onMounted(() => {
  loadMasterBacklinks();
  document.addEventListener("click", () => {
    dropdownOpen.value = null;
  });
});
</script>
<template>
  <!-- Header Section -->
  <VCard class="mb-6 pa-6 overflow-hidden" elevation="2">
    <VContainer fluid>
      <VRow align="center">
        <VCol cols="12" md="8">
          <div class="d-flex align-center">
            <VAvatar size="64" color="primary" variant="elevated" class="me-4">
              <IconTableImport :stroke="2" />
            </VAvatar>
            <div>
              <h1 class="text-h3 font-weight-bold mb-1">Master Backlinks</h1>
              <p class="text-body-1 text-medium-emphasis mb-0">
                Track backlink performance and campaign analytics
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
      <VRow class="mb-4 g-3">
        <VCol cols="12" sm="6"  md="12" class="d-flex align-center">
          <AppTextField v-model="searchQuery" placeholder="Search by Website, Domain URL"
                        prepend-inner-icon="tabler-search" variant="outlined" hide-details clearable class="search-field" />
        </VCol>

        <VCol cols="12" sm="6" md="2" class="d-flex align-center">
          <AppSelect v-model="selectedPlateform" :items="PLATFORM_TYPES" item-value="value" placeholder="Plateform" variant="outlined"
                     clearable prepend-inner-icon="tabler-brand-monday" class="flex-grow-1" />
        </VCol>

        <VCol cols="12" sm="6" md="2" class="d-flex align-center">
          <AppSelect v-model="selectedCountry" :items="countryCodes" item-value="value" placeholder="Country" variant="outlined"
                     clearable prepend-inner-icon="tabler-map-pin" class="flex-grow-1" />
        </VCol>

        <VCol cols="12" sm="6" md="2" class="d-flex align-center">
          <AppSelect v-model="selectedCategory" :items="CATEGORIES" item-value="value" placeholder="Category" variant="outlined"
                     clearable prepend-inner-icon="tabler-category-plus" class="flex-grow-1" />
        </VCol>

        <VCol cols="12" sm="6" md="2" class="d-flex align-center">
          <AppSelect v-model="selectedDoFollow" :items="DOFOLLOW_OPTIONS"
                     item-title="text" item-value="value" placeholder="Do Follow" variant="outlined"
                     clearable prepend-inner-icon="tabler-arrow-bar-both" class="flex-grow-1" />
        </VCol>

        <VCol cols="12" sm="6" md="2" class="d-flex align-center">
          <AppSelect v-model="selectedIndexed" :items="INDEXED_OPTIONS" item-title="text" item-value="value" placeholder="Indexed" variant="outlined"
                     clearable prepend-inner-icon="tabler-sitemap" class="flex-grow-1" />
        </VCol>

        <VCol cols="12" sm="6" md="2" class="d-flex">
          <VBtn color="primary" variant="flat" block @click="loadMasterBacklinks">
            Search & Filter
          </VBtn>
        </VCol>
      </VRow>
    </VCardText>
  </VCard>

  <VCard v-if="showImportResult" class="mb-6 pa-6" elevation="1">
    <div
        v-if="importResult.message"
        class="d-flex align-center justify-space-between"
    >
      <div class="d-flex align-center gap-2">
        <VIcon color="primary" icon="tabler-file-import" />
        <span class="font-weight-medium">{{ importResult.message }}</span>
      </div>

      <VBtn
          size="small"
          variant="text"
          color="info"
          :icon="showImportResult ? 'tabler-chevron-up' : 'tabler-chevron-down'"
          @click="showImportResult = !showImportResult"
      />
    </div>

    <VExpandTransition>
      <VSheet
          v-if="showImportResult"
          class="mt-4 pa-4 border rounded bg-grey-lighten-4"
      >
        <!-- Skip Count -->
        <div class="my-2">
          <h4 class="text-subtitle-1 font-weight-bold text-primary mb-1">
            Skipped Rows :
            <span class="text-h6 font-weight-bold">
              {{ importResult.skippedRows }}
            </span>
          </h4>
        </div>

        <!-- Successful Count -->
        <div class="my-2">
          <h4 class="text-subtitle-1 font-weight-bold text-success mb-1">
            Successful Imports :
            <span class="text-h6 font-weight-bold">
            {{ importResult.inserted }}
          </span>
          </h4>
        </div>

        <!-- Duplicate Count -->
        <div class="my-2">
          <h4 class="text-subtitle-1 font-weight-bold text-warning mb-1">
            Duplicate Rows :
            <span class="text-h6 font-weight-bold">
            {{ importResult.duplicates }}
          </span>
          </h4>
        </div>

        <!-- Duplicate Urls -->
        <div class="my-2">
          <h4 class="text-subtitle-1 font-weight-bold text-info mb-1">
            Duplicate Urls :
          </h4>
          <div v-if="importResult.duplicateUrls.length" class="text-subtitle-2 font-weight-bold" style="color: black">
            <pre>{{ importResult.duplicateUrls.join('\n') }}</pre>
          </div>

          <div v-else class="text-body-1">
            No duplicate URLs
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
          {{ totalMasterBacklinks }}
        </span>
        <span class="ml-2">Record Found</span>

        <VChip v-if="selectedRows.length" color="primary" size="small" class="ml-4" elevation="2" outlined>
          {{ selectedRows.length }} selected
        </VChip>
      </div>
      <VSpacer />
      <div class="d-flex align-center gap-2">
        <VBtn v-if="selectedRows.length" variant="text" size="small" color="error"
              @click="handleDeleteBacklinkBatch(selectedRows)">
          <VIcon icon="tabler-trash" class="me-1" />
          Delete Selected
        </VBtn>
      </div>
      <div class="d-flex gap-4 flex-wrap align-center">
        <AppSelect v-model="itemsPerPage" :items="[5, 10, 20, 25, 50]" />
        <VBtn
            variant="tonal"
            color="secondary"
            prepend-icon="tabler-download"
            @click="importDialog = true"
        >
          Import
        </VBtn>
      </div>
    </div>

    <VDivider class="mt-5 mb-2" />

    <!-- Enhanced Data Table -->
    <VDataTableServer
        v-model="selectedRows"
        :page="pagination.page"
        :items-per-page="pagination.itemsPerPage"
        :headers="headers"
        :items="masterBacklinks"
        :loading="loading"
        :items-length="pagination.total"
        show-select
        density="compact"
        hover
        loading-text="Loading backlinks..."
        class="compact-domain-table"
        @update:options="updateOptions"
    >
      <!-- Website Name -->
      <template #item.website_name="{ item }">
        <div class="text-truncate font-weight-medium" style="max-width: 150px">
          {{ item.website_name || '-' }}
        </div>
      </template>

      <!-- Domain URL -->
      <template #item.domain_url="{ item }">
        <a
            :href="item.domain_url"
            target="_blank"
            class="text-primary text-decoration-none d-inline-flex align-center gap-1"
            :title="item.domain_url"
        >
      <span class="text-truncate" style="max-width: 160px">
        {{ item.domain_url }}
      </span>
          <VIcon icon="tabler-external-link" size="14" />
        </a>
      </template>

      <!-- DA Score -->
      <template #item.da="{ item }">
        <VProgressCircular
            :model-value="item.da || 0"
            size="36"
            width="3"
            :color="item.da > 70 ? 'success' : item.da > 40 ? 'warning' : 'error'"
        >
          <span class="text-caption font-weight-bold">{{ item.da || 0 }}</span>
        </VProgressCircular>
      </template>

      <!-- Profile URL -->
      <template #item.profile_url="{ item }">
        <a
            v-if="item.profile_url"
            :href="item.profile_url"
            target="_blank"
            class="text-primary text-decoration-none d-inline-flex align-center gap-1"
            :title="item.profile_url"
        >
      <span class="text-truncate" style="max-width: 160px">
        {{ item.profile_url }}
      </span>
          <VIcon icon="tabler-external-link" size="14" />
        </a>
        <span v-else class="text-disabled">-</span>
      </template>

      <!-- Platform Type -->
      <template #item.platform_type="{ item }">
        <VChip
            v-if="item.platform_type"
            size="small"
            variant="tonal"
            color="primary"
            label
        >
          {{ item.platform_type }}
        </VChip>
        <span v-else class="text-disabled">-</span>
      </template>

      <!-- Country -->
      <template #item.country="{ item }">
        <VChip
            v-if="item.country"
            size="small"
            variant="outlined"
            prepend-icon="tabler-map-pin"
        >
          {{ item.country }}
        </VChip>
        <span v-else class="text-disabled">-</span>
      </template>

      <!-- Categories -->
      <template #item.categories="{ item }">
        <div class="d-flex flex-wrap gap-1 align-center">
          <VChip
              v-for="cat in (item.categories || []).slice(0, 2)"
              :key="cat"
              size="x-small"
              color="info"
              variant="flat"
              label
          >
            {{ cat }}
          </VChip>

          <VMenu v-if="item.categories && item.categories.length > 2" location="bottom">
            <template #activator="{ props: menuProps }">
              <VChip
                  v-bind="menuProps"
                  size="x-small"
                  variant="tonal"
                  color="secondary"
                  label
              >
                +{{ item.categories.length - 2 }}
              </VChip>
            </template>

            <VList density="compact" max-width="250">
              <VListSubheader class="text-caption">
                All Categories ({{ item.categories.length }})
              </VListSubheader>
              <VDivider />
              <VListItem v-for="cat in item.categories" :key="cat" class="px-3 py-1">
                <VChip size="small" color="info" variant="tonal" label>
                  {{ cat }}
                </VChip>
              </VListItem>
            </VList>
          </VMenu>

          <span v-if="!item.categories || !item.categories.length" class="text-disabled">-</span>
        </div>
      </template>

      <!-- Do Follow -->
      <template #item.dofollow="{ item }">
        <VIcon
            v-if="item.dofollow === true"
            icon="tabler-circle-check-filled"
            color="success"
            size="20"
        />
        <VIcon
            v-else-if="item.dofollow === false"
            icon="tabler-circle-x-filled"
            color="error"
            size="20"
        />
        <span v-else class="text-disabled">-</span>
      </template>

      <!-- Indexed -->
      <template #item.indexed="{ item }">
        <VIcon
            v-if="item.indexed === true"
            icon="tabler-circle-check-filled"
            color="success"
            size="20"
        />
        <VIcon
            v-else-if="item.indexed === false"
            icon="tabler-circle-x-filled"
            color="error"
            size="20"
        />
        <span v-else class="text-disabled">-</span>
      </template>

      <!-- Last Active -->
      <template #item.last_active="{ item }">
    <span v-if="item.last_active" class="text-body-2">
      {{ item.last_active }}
    </span>
        <span v-else class="text-disabled">-</span>
      </template>

      <!-- Average Traffic -->
      <template #item.avg_traffic="{ item }">
    <span v-if="item.avg_traffic" class="text-body-2 font-weight-medium">
      {{ item.avg_traffic.toLocaleString() }}
    </span>
        <span v-else class="text-disabled">-</span>
      </template>

      <!-- Domain Age -->
      <template #item.domain_age="{ item }">
    <span v-if="item.domain_age" class="text-body-2">
      {{ item.domain_age }}
    </span>
        <span v-else class="text-disabled">-</span>
      </template>

      <!-- Actions -->
      <template #item.actions="{ item }">
        <VTooltip text="Delete" location="top">
          <template #activator="{ props }">
            <VBtn
                v-bind="props"
                icon="tabler-trash"
                variant="text"
                color="error"
                size="x-small"
                @click="handleDeleteBacklinks(item.id)"
            />
          </template>
        </VTooltip>
      </template>

      <!-- Empty State -->
      <template #no-data>
        <div class="text-center py-12">
          <VIcon icon="tabler-database-off" size="64" class="text-disabled mb-4" />
          <div class="text-h6 text-medium-emphasis mb-2">No Backlinks Found</div>
          <div class="text-body-2 text-disabled">
            Try adjusting your search filters
          </div>
        </div>
      </template>

      <!-- Pagination -->
      <template #bottom>
        <TablePagination
            v-model:page="pagination.page"
            :items-per-page="pagination.itemsPerPage"
            :total-items="totalMasterBacklinks"
        />
      </template>
    </VDataTableServer>
  </VCard>

  <!-- Import Backlinks Dialog -->
  <VDialog v-model="importDialog" max-width="600" persistent>
    <VCard>
      <VCardTitle class="pa-6 pb-4">
        <div class="d-flex align-center">
          <VIcon icon="tabler-file-excel" class="me-3 text-success" size="32" />
          <div>
            <h3 class="text-h5 font-weight-bold">Import Master Backlinks</h3>
            <p class="text-body-2 text-medium-emphasis mb-0">
              Upload an Excel file to import multiple backlinks
            </p>
          </div>
        </div>
      </VCardTitle>

      <VDivider />

      <VCardText class="pa-6">
        <!-- File Input -->
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
              @click:clear="clearFile"
          >
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

        <!-- Drag Zone -->
        <div
            class="drop-zone"
            :class="{ 'drop-zone--dragover': isDragOver }"
            @drop="handleDrop"
            @dragover="handleDragOver"
            @dragenter="handleDragEnter"
            @dragleave="handleDragLeave"
        >
          <div class="text-center">
            <VIcon
                :icon="isDragOver ? 'tabler-file-plus' : 'tabler-cloud-upload'"
                size="48"
                class="text-primary mb-4"
            />
            <h4 class="text-h6 mb-2">
              {{ isDragOver ? "Drop your Excel file here" : "Drag & drop your Excel file here" }}
            </h4>
            <p class="text-body-2 text-medium-emphasis mb-4">
              or click "Select Excel File" above
            </p>
          </div>
        </div>

        <!-- File Preview -->
        <div v-if="selectedFile && !fileError" class="mt-6">
          <VAlert color="info" variant="tonal" class="mb-4">
            <VIcon icon="tabler-info-circle" />
            <VAlertTitle>File Ready for Import</VAlertTitle>
            <div class="mt-2 d-flex align-center justify-space-between">
              <span><strong>File:</strong> {{ selectedFile.name }}</span>
              <span><strong>Size:</strong> {{ formatFileSize(selectedFile.size) }}</span>
            </div>
          </VAlert>
        </div>

        <!-- Instructions -->
        <VExpansionPanels class="mt-6" variant="accordion">
          <VExpansionPanel>
            <VExpansionPanelTitle>
              <VIcon icon="tabler-help-circle" class="me-2" />
              Import Instructions
            </VExpansionPanelTitle>
            <VExpansionPanelText>
              <h5 class="text-subtitle-1 mb-3">Excel File Requirements:</h5>
              <ul class="ml-4 mb-4">
                <li>File must be in Excel format (.xlsx or .xls)</li>
                <li>First row should contain column headers</li>
                <li>Required columns: Website Name, Domain URL, DA, Profile URL</li>
                <li>Maximum file size: 10MB</li>
                <li>Maximum 1000 rows per import</li>
              </ul>

              <VBtn
                  variant="outlined"
                  size="small"
                  prepend-icon="tabler-download"
                  :loading="downloading"
                  @click="templateDownload"
              >
                Download Template
              </VBtn>
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
        <VBtn
            color="primary"
            :loading="importing"
            variant="flat"
            :disabled="!selectedFile || !!fileError"
            @click="handleImportMasterBacklinks"
        >
          <VIcon icon="tabler-download" class="me-2" />
          Import Master Backlinks
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>
<style scoped>
.compact-domain-table :deep(.v-data-table__td) {
  padding-block: 8px !important;
  padding-inline: 12px !important;
}

.compact-domain-table :deep(.v-data-table__th) {
  font-weight: 600 !important;
  font-size: 0.8125rem !important;
  padding-block: 12px !important;
  padding-inline: 12px !important;
}

.compact-domain-table :deep(.v-table__wrapper) {
  border-radius: 8px;
}
</style>