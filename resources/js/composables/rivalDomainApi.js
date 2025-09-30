// @/composables/useDomainApi.js
import { ref, readonly } from "vue";
import { useApi } from "./useApi";
import { useAlert } from "./useAlert";

export function useRivalDomainApi() {
  const { showAlert } = useAlert();

  const rivaldomains = ref([]);
  const pagination = ref({
    total: 0,
    currentPage: 1,
    perPage: 10,
    lastPage: 1,
    itemsPerPage: 10,
    page: 1,
  });
  const rivalDomainList = ref([]);
  const currentRivalDomain = ref(null);
  const loading = ref(false);
  const error = ref(null);

  // get rivaldomains by id
  const fetchRivalDomain = async (id) => {
    loading.value = true;
    error.value = null;
    try {
      const result = await useApi(`api/rivaldomain/get/${id}`, {
        method: "POST",
      });
        currentRivalDomain.value = result.data.value.data;
      return result;
    } catch (err) {
      error.value = err;
        currentRivalDomain.value = null;
      showAlert("Failed to load domain", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // get all rivaldomains
  const fetchRivalDomains = async (filters = {}, page = null) => {
    loading.value = true;
    error.value = null;

    try {
      const body = {
        ...filters
      };
      const result = await useApi(createUrl("api/rivaldomain/get",{query : body}), {
        method: "POST",
      });

      rivaldomains.value = result.data.value.data.resource;
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
      showAlert("Failed to load rivaldomains", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const fetchClientRivalDomains = async (
    id,
    filters = {},
    page = 1,
    perPage = 10
  ) => {
    loading.value = true;
    error.value = null;

    try {
      const query = {
        ...filters,
        page,
        per_page: perPage,
      };

      const result = await useApi(`api/rivaldomain/get/domains/${id}`, {
        method: "POST",
        body : query,
      });
      rivaldomains.value = result.data.value.domains.data;
      const apiPagination  = result.data.value.domains;

      pagination.value = {
        total: apiPagination.total ?? 0,
        currentPage: apiPagination.current_page ?? 1,
        perPage: apiPagination.per_page ?? perPage,
        lastPage: apiPagination.last_page ?? 1,
        itemsPerPage: apiPagination.per_page ?? perPage,
        page: apiPagination.current_page ?? 1,
      };

      return result;
    } catch (err) {
      error.value = err;
      showAlert("Failed to load domains", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // import rivaldomains

  const importRivalDomains = async (file) => {
    if (!file) {
      showAlert("No file selected", "error");
      return { success: false, message: "No file selected" };
    }

    loading.value = true;
    error.value = null;

    try {
      const formData = new FormData();
      formData.append("file", file);

      const result = await useApi("api/rivaldomain/import", {
        method: "POST",
        body: formData,
        skipJsonTransform: true,
      });

      if (result.error.value || result.statusCode.value >= 400) {
        const errorMsg = result.data.value?.message || "Import dsa failed";
        return { success: false, message: errorMsg };
      }

      const responseData = result.data.value;
      const successMsg =
        responseData?.message || "rivaldomains imported successfully!";

      showAlert(successMsg, "success");

      return {
        success: true,
        message: successMsg,
        data: responseData,
      };
    } catch (err) {
      error.value = err;
      const errorMsg = "Failed to import rivaldomains";
      showAlert(errorMsg, "error");
      return { success: false, message: errorMsg };
    } finally {
      loading.value = false;
    }
  };

  // Download Domain Import Template
  const downloadTemplate = async () => {
    loading.value = true;
    error.value = null;

    try {
      const response = await fetch("/api/rivaldomain/import/template/download", {
        method: "GET",
        headers: {
          Accept:
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
          Authorization: `Bearer ${useCookie("accessToken").value}`,
        },
      });

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
      }
      const blob = await response.blob();
      return blob;
    } catch (err) {
      error.value = err;
      showAlert("Failed to fetch keyword template", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // create domain
  const createRivalDomain = async (payload) => {
    loading.value = true;
    error.value = null;

    try {
      const result = await useApi("api/rivaldomain/store", {
        method: "POST",
        body: payload,
      });

      showAlert("Domain created successfully!", "success");
      await fetchRivalDomains();
      return result;
    } catch (err) {
      error.value = err;
      showAlert("Failed to create domain", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // update domain
  const updateRivalDomain = async (id, payload) => {
    loading.value = true;
    error.value = null;

    try {s
      const result = await useApi(`api/rivaldomain/update/${id}`, {
        method: "POST",
        body: payload,
      });

      showAlert("Domain updated successfully!", "success");
      return result;
    } catch (err) {
      error.value = err;
      showAlert("Failed to update domain", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // delete domain
  const deleteRivalDomain = async (id) => {
    loading.value = true;
    error.value = null;

    try {
      const result = await useApi(`api/rivaldomain/delete/${id}`, {
        method: "POST",
      });

      showAlert("Domain deleted successfully!", "success");
      return result;
    } catch (err) {
      error.value = err;
      showAlert("Failed to delete domain", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // get domain list
  const fetchRivalDomainList = async () => {
    loading.value = true;
    error.value = null;

    try {
      const result = await useApi("api/rivaldomain/domain/list", {
        method: "POST",
      });
      console.log("Domain List API Result:", result);
      rivalDomainList.value = result.data.value.rivaldomains || [];
      return result;
    } catch (err) {
      error.value = err;
      rivalDomainList.value = [];
      showAlert("Failed to load domain list", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return {
    rivaldomains: readonly(rivaldomains),
    pagination : pagination,
    rivalDomainList: rivalDomainList,
    currentRivalDomain: readonly(currentRivalDomain),
    loading: readonly(loading),
    error: readonly(error),

    // Methods
    fetchRivalDomains,
    fetchRivalDomainList,
    fetchClientRivalDomains,
    importRivalDomains,
    fetchRivalDomain,
    downloadTemplate,
    createRivalDomain,
    updateRivalDomain,
    deleteRivalDomain,

    showAlert,
  };
}
