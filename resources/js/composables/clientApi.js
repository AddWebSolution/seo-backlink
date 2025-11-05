// @/composables/useClientApi.js
import { ref, readonly } from "vue";
import { useApi } from "./useApi";
import { useAlert } from "./useAlert";

export function useClientApi() {
  const { showAlert } = useAlert();

  const clients = ref([]);
  const pagination = ref({
    total: 0,
    currentPage: 1,
    perPage: 10,
    lastPage: 1,
    itemsPerPage: 10,
    page: 1,
  });
  const ClientList = ref();
  const currentClient = ref(null);
  const loading = ref(false);
  const error = ref(null);

  // get clients by id
  const fetchClient = async (id) => {
    loading.value = true;
    error.value = null;
    try {
      const result = await useApi(`api/client/get/${id}`, {
        method: "POST",
      });
      currentClient.value = result.data.value.data;
      return result;
    } catch (err) {
      error.value = err;
      currentClient.value = null;
      showAlert("Failed to load Client", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // get all clients
  const fetchclients = async (filters = {}, page = null) => {
    loading.value = true;
    error.value = null;

    try {
      const body = {
        ...filters
      };
      const result = await useApi(createUrl("api/client/get", { query: body }), {
        method: "POST",
      });
      clients.value = result.data.value.data.resource;
      const apiPagination = result.data.value.data.pagination;

      pagination.value = {
        total: apiPagination.total,
        currentPage: apiPagination.currentPage,
        perPage: apiPagination.perPage,
        lastPage: apiPagination.lastPage,
        itemsPerPage: apiPagination.perPage,
        page: apiPagination.currentPage,
      };

      await fetchClientList();
      return result;
    } catch (err) {
      error.value = err;
      showAlert("Failed to load clients", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // import clients

  const importclients = async (file) => {
    if (!file) {
      showAlert("No file selected", "error");
      return { success: false, message: "No file selected" };
    }

    loading.value = true;
    error.value = null;

    try {
      const formData = new FormData();
      formData.append("file", file);

      const result = await useApi("api/client/import", {
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
        responseData?.message || "clients imported successfully!";

      showAlert(successMsg, "success");
      await fetchclients();

      return {
        success: true,
        message: successMsg,
        data: responseData,
      };
    } catch (err) {
      error.value = err;
      const errorMsg = "Failed to import clients";
      showAlert(errorMsg, "error");
      return { success: false, message: errorMsg };
    } finally {
      loading.value = false;
    }
  };

  // Download Client Import Template
  const downloadTemplate = async () => {
    loading.value = true;
    error.value = null;

    try {
      const response = await fetch("/api/client/import/template/download", {
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

  // create Client
  const createClient = async (payload) => {
    loading.value = true;
    error.value = null;

    try {
      const result = await useApi("api/client/store", {
        method: "POST",
        body: payload,
      });

      showAlert("Client created successfully!", "success");
      await fetchclients();
      return result;
    } catch (err) {
      error.value = err;
      showAlert("Failed to create Client", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };


  // update Client
  const updateClient = async (id, payload) => {
    loading.value = true;

    try {
      const result = await useApi(`api/client/update/${id}`, {
        method: "POST",
        body: payload,
      });

      showAlert("record updated successfully!", "success");
      await fetchclients();
      return result;
    } catch (err) {
      error.value = err;
      showAlert("Failed to update Client", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // delete Client
  const deleteClient = async (id) => {
    loading.value = true;
    error.value = null;

    try {
      const result = await useApi(`api/client/delete/${id}`, {
        method: "POST",
      });

      showAlert("Client deleted successfully!", "success");
      await fetchclients();
      return result;
    } catch (err) {
      error.value = err;
      showAlert("Failed to delete Client", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // get Client list
  const fetchClientList = async () => {
    loading.value = true;
    error.value = null;

    try {
      const result = await useApi("api/client/name/list", {
        method: "POST",
      });
      ClientList.value = result?.data?.value?.clients;
      return result;
    } catch (err) {
      error.value = err;
      ClientList.value = [];
      showAlert("Failed to load Client list", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return {
    clients: readonly(clients),
    pagination: pagination,
    ClientList: ClientList,
    currentClient: readonly(currentClient),
    loading: readonly(loading),
    error: readonly(error),

    // Methods
    fetchclients,
    fetchClientList,
    importclients,
    fetchClient,
    downloadTemplate,
    createClient,
    updateClient,
    deleteClient,

    showAlert,
  };
}
