// @/composables/useDomainApi.js
import { ref, readonly } from "vue";
import { useApi } from "./useApi";
import { useAlert } from "./useAlert";

export function useDomainApi() {
  const { showAlert } = useAlert();

  const domains = ref([]);
  const backlinkhistory = ref([]);
  const rivalBacklinks = ref([]);
  const pagination = ref({
    total: 0,
    currentPage: 1,
    perPage: 10,
    lastPage: 1,
    itemsPerPage: 10,
    page: 1,
  });
  const domainList = ref([]);
  const currentDomain = ref(null);
  const loading = ref(false);
  const error = ref(null);

  // get domains by id
  const fetchDomain = async (id) => {
    loading.value = true;
    error.value = null;
    try {
      const result = await useApi(`api/clientdomain/get/${id}`, {
        method: "POST",
      });
      currentDomain.value = result.data.value.data;
      return result;
    } catch (err) {
      error.value = err;
      currentDomain.value = null;
      showAlert("Failed to load domain", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // get all domains
  const fetchDomains = async (filters = {}, page = null) => {
    loading.value = true;
    error.value = null;

    try {
      const body = {
        ...filters,
      };
      const result = await useApi(
        createUrl("api/clientdomain/get", { query: body }),
        {
          method: "POST",
        }
      );

      domains.value = result.data.value.data.resource;
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
      // showAlert("Failed to load domains", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const fetchClientDomains = async (id,filters = {}, page = null) => {
    loading.value = true;
    error.value = null;

    try {
      const body = {
        ...filters,
      };

      const result = await useApi(
        createUrl(`api/clientdomain/get/domains/${id}`, { query: body }),
        {
          method: "POST",
        }
      );

      domains.value = result.data.value.domains.data;
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
      // showAlert("Failed to load domains", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };


   const fetchClientDomainsHistory = async (id,filters = {}, page = null) => {
    loading.value = true;
    error.value = null;

    try {
      const body = {
        ...filters,
      };

      const result = await useApi(
        createUrl(`api/backlinkhistory/clientdomain/history/compare/${id}`, { query: body }),
        {
          method: "POST",
        }
      );

      backlinkhistory.value = result.data.value;

      return result;
    } catch (err) {
      error.value = err;
      throw err;
    } finally {
      loading.value = false;
    }
  };

    // fetch rival backlinks (unique domains)
    const fetchRivalBacklinks = async (id, filters = {}) => {
        loading.value = true;
        error.value = null;

        try {
            const body = {
                ...filters,
            };

            const result = await useApi(
                createUrl(`api/clientdomain/rival-backlinks/${id}`, { query: body }),
                {
                    method: "POST",
                }
            );

            rivalBacklinks.value = result.data.value.data || [];

            return result;
        } catch (err) {
            error.value = err;
            showAlert("Failed to fetch rival backlinks", "error");
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // import domains

  const importDomains = async (file) => {
    if (!file) {
      showAlert("No file selected", "error");
      return { success: false, message: "No file selected" };
    }

    loading.value = true;
    error.value = null;

    try {
      const formData = new FormData();
      formData.append("file", file);

      const result = await useApi("api/clientdomain/import", {
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
        responseData?.message || "Domains imported successfully!";

      showAlert(successMsg, "success");

      return {
        success: true,
        message: successMsg,
        data: responseData,
      };
    } catch (err) {
      error.value = err;
      const errorMsg = "Failed to import domains";
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
      const response = await fetch(
        "/api/clientdomain/import/template/download",
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
  const createDomain = async (payload) => {
    loading.value = true;
    error.value = null;

    try {
      const result = await useApi("api/clientdomain/store", {
        method: "POST",
        body: payload,
      });

      showAlert("Domain created successfully!", "success");
      await fetchClientDomains(payload.client_id);
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
  const updateDomain = async (id, payload) => {
    loading.value = true;
    error.value = null;

    try {
      const result = await useApi(`api/clientdomain/update/${id}`, {
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
  const deleteDomain = async (id) => {
    loading.value = true;
    error.value = null;

    try {
      const result = await useApi(`api/clientdomain/delete/${id}`, {
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
  const fetchDomainList = async () => {
    loading.value = true;
    error.value = null;

    try {
      const result = await useApi("api/clientdomain/domain/list", {
        method: "POST",
      });
      domainList.value = result.data.value.domains || [];
      return result;
    } catch (err) {
      error.value = err;
      domainList.value = [];
      showAlert("Failed to load domain list", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // assign domains to users
    const assignUsersToDomain = async (domainId, userIds) => {
        loading.value = true;
        error.value = null;

        try {
            const result = await useApi(`api/clientdomain/assign-users/${domainId}`, {
                method: "POST",
                body: { user_ids: userIds },
            });

            showAlert("Users assigned successfully!", "success");
            return result;
        } catch (err) {
            error.value = err;
            showAlert("Failed to assign users", "error");
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const exportReferringDomains = async (domainId) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await fetch(
                `/api/clientdomain/referring-domains/export/${domainId}`,
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
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const blob = await response.blob();

            const url = window.URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            const timestamp = new Date().toISOString().replace(/[:.-]/g, "");
            a.download = `ReferringDomains_${timestamp}.xlsx`;
            a.click();
            window.URL.revokeObjectURL(url);

            showAlert("Export downloaded successfully!", "success");
            return true;
        } catch (err) {
            error.value = err;
            showAlert("Failed to export referring domains", "error");
            throw err;
        } finally {
            loading.value = false;
        }
    };

    return {
    domains: readonly(domains),
    backlinkhistory: readonly(backlinkhistory),
    rivalBacklinks: readonly(rivalBacklinks),
    pagination: pagination,
    domainList: domainList,
    currentDomain: readonly(currentDomain),
    loading: readonly(loading),
    error: readonly(error),

    // Methods
    fetchDomains,
    fetchClientDomains,
    fetchClientDomainsHistory,
    fetchRivalBacklinks,
    fetchDomainList,
    importDomains,
    fetchDomain,
    downloadTemplate,
    createDomain,
    updateDomain,
    deleteDomain,
    exportReferringDomains,

    assignUsersToDomain,
    showAlert,
  };
}
