// @/composables/useDomainApi.js
import { ref, readonly } from "vue";
import { useApi } from "./useApi";
import { useAlert } from "./useAlert";

export function useDomainApi() {
  const { showAlert } = useAlert();

  const domains = ref([]);
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
        ...filters
      };
      const result = await useApi(createUrl("api/clientdomain/get",{query : body}), {
        method: "POST",
      });

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
      showAlert("Failed to load domains", "error");
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
      await fetchDomains();
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
      await fetchDomains();
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
      await fetchDomains();
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
      console.log("Domain List API Result:", result);
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

  return {
    domains: readonly(domains),
    pagination : pagination,
    domainList: domainList,
    currentDomain: readonly(currentDomain),
    loading: readonly(loading),
    error: readonly(error),

    // Methods
    fetchDomains,
    fetchDomainList,
    fetchDomain,
    createDomain,
    updateDomain,
    deleteDomain,

    showAlert,
  };
}
