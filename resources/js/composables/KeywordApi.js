// @/composables/useKeywordApi.js
import { ref, readonly } from "vue";
import { useApi } from "./useApi";
import { useAlert } from "./useAlert";

export function useKeywordApi() {
  const { showAlert } = useAlert();

  const Keywords = ref([]);
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
      showAlert("Failed to load domain", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // get all keywords
  const fetchKeywords = async (filters = {}) => {
    loading.value = true;
    error.value = null;

    try {
      const result = await useApi("api/keyword/get", {
        method: "POST",
        body: filters,
      });
      Keywords.value = result.data.value.data.resource;
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
  const createKeyword = async (payload) => {
    loading.value = true;
    error.value = null;

    try {
      const result = await useApi("api/keyword/store", {
        method: "POST",
        body: payload,
      });

      showAlert("Domain created successfully!", "success");
      await fetchKeywords();
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
  const updateKeyword = async (id, payload) => {
    loading.value = true;
    error.value = null;

    try {
      const result = await useApi(`api/keyword/update/${id}`, {
        method: "POST",
        body: payload,
      });

      showAlert("Domain updated successfully!", "success");
      await fetchKeywords();
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
  const deleteKeyword = async (id) => {
    loading.value = true;
    error.value = null;

    try {
      const result = await useApi(`api/keyword/delete/${id}`, {
        method: "POST",
      });

      showAlert("Domain deleted successfully!", "success");
      await fetchKeywords();
      return result;
    } catch (err) {
      error.value = err;
      showAlert("Failed to delete domain", "error");
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
      return;
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

      showAlert(
        `Imported ${result.data.value} keywords successfully!`,
        "success"
      );
      await fetchKeywords();
    return result.data.value; 
    } catch (err) {
      error.value = err;
      showAlert("Failed to import keywords", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return {
    keywords: readonly(Keywords),
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
