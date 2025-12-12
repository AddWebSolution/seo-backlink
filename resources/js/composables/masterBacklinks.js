import { ref, readonly } from "vue";
import { useApi } from "./useApi";
import { useAlert } from "./useAlert";

export function useMasterBacklinksApi() {
    const { showAlert } = useAlert();

    const masterBacklinks = ref([]);
    const loading = ref(false);
    const error = ref(null);
    const pagination = ref({
        total: 0,
        currentPage: 1,
        perPage: 10,
        lastPage: 1,
        itemsPerPage: 10,
        page: 1,
    });

    const importResult = ref(null);
    const importLoader = ref(false);

    // DOWNLOAD TEMPLATE
    const downloadTemplate = async () => {
        loading.value = true;
        error.value = null;

        try {
            const response = await fetch(
                "/api/masterbacklink/import/template/download",
                {
                    method: "GET",
                    headers: {
                        Accept:
                            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                        Authorization: `Bearer ${useCookie("accessToken").value}`,
                    },
                }
            );

            if (!response.ok) {
                showAlert("Failed to download template", "error");
                throw new Error(`HTTP ${response.status}`);
            }

            const blob = await response.blob();
            return blob;

        } catch (err) {
            error.value = err;
            showAlert("Failed to fetch master backlinks template", "error");
            throw err;

        } finally {
            loading.value = false;
        }
    };

    // IMPORT MASTER BACKLINKS
    const importMasterBacklinks = async (file) => {
        if (!file) {
            showAlert("No file selected", "error");
            return { success: false };
        }

        importLoader.value = true;
        error.value = null;

        try {
            const formData = new FormData();
            formData.append("file", file);

            const result = await useApi("api/masterbacklink/import", {
                method: "POST",
                body: formData,
                skipJsonTransform: true,
            });

            if (result.error.value || result.statusCode.value >= 400) {
                const msg =
                    result.data.value?.message ||
                    "Import failed — invalid file or server error";

                showAlert(msg, "error");
                return { success: false };
            }

            const responseData = result.data.value;

            importResult.value = {
                inserted: responseData.inserted_rows,
                duplicates: responseData.duplicate_rows,
                duplicateUrls: responseData.duplicate_urls,
                skippedRows: responseData.skipped_rows,
            };

            showAlert("Master Backlinks Imported Successfully", "success");

            return {
                success: true,
                data: importResult.value,
            };

        } catch (err) {
            error.value = err;
            showAlert("Failed to import backlinks", "error");
            return { success: false };

        } finally {
            importLoader.value = false;
        }
    };

    // GET ALL MASTER BACKLINKS
    const fetchMasterBacklinks = async (filters = {}, page = null) => {
        loading.value = true;
        error.value = null;

        try {
            const body = {
                ...filters,
            };
            const result = await useApi(
                createUrl("api/masterbacklink/get", { query: body }),
                {
                    method: "POST",
                }
            );

            masterBacklinks.value = result.data.value.data.resource;
            const apiPagination = result.data.value.data.pagination;

            pagination.value = {
                total: apiPagination.total,
                currentPage: apiPagination.currentPage,
                perPage: apiPagination.perPage,
                lastPage: apiPagination.lastPage,
                itemsPerPage: apiPagination.perPage,
                page: apiPagination.currentPage,
            };

            return result;
        } catch (err) {
            error.value = err;
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // DELETE MASTER BACKLINK
    const deleteMasterBacklink = async (id) => {
        loading.value = true;
        error.value = null;

        try {
            const result = await useApi(`api/masterbacklink/delete/${id}`, {
                method: "POST",
            });

            showAlert("Master Backlink  deleted successfully!", "success");
            return result;
        } catch (err) {
            error.value = err;
            showAlert("Failed to delete master backlink", "error");
            throw err;
        } finally {
            loading.value = false;
        }
    };

    return {
        masterBacklinks: readonly(masterBacklinks),
        pagination: pagination,
        loading: readonly(loading),
        error: readonly(error),

        importLoader: readonly(importLoader),
        importResult: readonly(importResult),

        downloadTemplate,
        importMasterBacklinks,
        fetchMasterBacklinks,
        deleteMasterBacklink,
        showAlert,
    };
}