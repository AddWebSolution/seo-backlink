// @/composables/useKeywordApi.js
import { ref, readonly } from "vue";
import { useApi } from "./useApi";
import { useAlert } from "./useAlert";

export function useKeywordApi() {
  const { showAlert } = useAlert();

  const Keywords = ref([]);
  const Pagination = ref({
    total: 0,
    currentPage: 1,
    perPage: 10,
    lastPage: 1,
    itemsPerPage: 10, 
    page: 1
  })
  const KeywordList = ref([]);
  const currentKeyword = ref(null);
  const loading = ref(false);
  const error = ref(null);

  // get domains by id
  const fetchKeyword = async (id) => {
    loading.value = true;
    error.value = null;
    try {
      const result = await useApi(`api/keyword/get/${id}`, {
        method: "POST",
      });
      currentKeyword.value = result.data.value.data;
      return result;
    } catch (err) {
      error.value = err;
      currentKeyword.value = null;
      showAlert("Failed to load keyword", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // get all keywords
  const fetchKeywords = async (filters = {}, page = null) => {
    loading.value = true;
    error.value = null;
    try {
      const body = { 
        ...filters
      };
      const result = await useApi(
        createUrl("api/keyword/get", { query: body}),
        {
          method: "POST",
        }
      );
      Keywords.value = result.data.value.data.resource;
      const apiPagination = result.data.value.data.pagination;

      Pagination.value = {
        total: apiPagination.total,
        currentPage: apiPagination.currentPage,
        perPage: apiPagination.perPage,
        lastPage: apiPagination.lastPage,
        itemsPerPage: apiPagination.perPage,
        page: apiPagination.currentPage
      };

      return result;
    } catch (err) {
      error.value = err;
      showAlert("Failed to load keywords", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };


  // create keyword
  const createKeyword = async (payload) => {
    loading.value = true;
    error.value = null;

    try {
      const result = await useApi("api/keyword/store", {
        method: "POST",
        body: payload,
      });

      showAlert("Keyword created successfully!", "success");
      await fetchKeywords();
      return result;
    } catch (err) {
      error.value = err;
      showAlert("Failed to create keyword", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // update keyword
  const updateKeyword = async (id, payload) => {
    loading.value = true;
    error.value = null;

    try {
      const result = await useApi(`api/keyword/update/${id}`, {
        method: "POST",
        body: payload,
      });

      showAlert("Keyword updated successfully!", "success");
      await fetchKeywords();
      return result;
    } catch (err) {
      error.value = err;
      showAlert("Failed to update keyword", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // delete keyword
  const deleteKeyword = async (id) => {
    loading.value = true;
    error.value = null;

    try {
      const result = await useApi(`api/keyword/delete/${id}`, {
        method: "POST",
      });

      showAlert("Keyword deleted successfully!", "success");
      await fetchKeywords();
      return result;
    } catch (err) {
      error.value = err;
      showAlert("Failed to delete keyword", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Download Keyword Import Template
  const downloadTemplate = async () => {
    loading.value = true;
    error.value = null;

    try {
      const response = await fetch("/api/keyword/import/template/download", {
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

  // Import keywords from file
  const importKeywords = async (file) => {
    if (!file) {
      showAlert("No file selected", "error");
      return { success: false, message: "No file selected" };
    }

    loading.value = true;
    error.value = null;

    try {
      const formData = new FormData();
      formData.append("file", file);

      const result = await useApi("api/keyword/import", {
        method: "POST",
        body: formData,
        skipJsonTransform: true,
      });

      if (result.error.value || result.statusCode.value >= 400) {
        const errorMsg = result.data.value?.message || "Import failed";
        showAlert(errorMsg, "error");
        return { success: false, message: errorMsg };
      }

      const responseData = result.data.value;
      const successMsg = responseData?.message || "Keywords imported successfully!";
      
      showAlert(successMsg, "success");
      await fetchKeywords();
      
      return { 
        success: true, 
        message: successMsg,
        data: responseData 
      };

    } catch (err) {
      error.value = err;
      const errorMsg = "Failed to import keywords";
      showAlert(errorMsg, "error");
      return { success: false, message: errorMsg };
    } finally {
      loading.value = false;
    }
  };

  return {
    keywords: readonly(Keywords),
    pagination : Pagination,
    KeywordList: readonly(KeywordList),
    currentKeyword: readonly(currentKeyword),
    loading: readonly(loading),
    error: readonly(error),

    // Methods
    fetchKeywords,
    fetchKeyword,
    createKeyword,
    updateKeyword,
    deleteKeyword,
    downloadTemplate,
    importKeywords,

    showAlert,
  };
}