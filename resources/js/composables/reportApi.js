import { ref, readonly } from "vue";
import { useApi } from "./useApi";
import { useAlert } from "./useAlert";

export function useReportApi() {
  const { showAlert } = useAlert();

  const reports = ref([]);
  const pagination = ref({
    total: 0,
    currentPage: 1,
    perPage: 10,
    lastPage: 1,
    itemsPerPage: 10,
    page: 1,
  });
  const currentReport = ref(null);
  const loading = ref(false);
  const error = ref(null);

  // get report backlinks by id
  const fetchReportBacklinks = async (id, body = {}) => {
    loading.value = true;
    error.value = null;
    try {
      const result = await useApi(`api/report/backlinks/${id}`, {
        method: "POST",
        body,
      });
      currentReport.value = result.data.value;
      return result;
    } catch (err) {
      error.value = err;
      currentReport.value = null;
      reportBacklinks.value = [];
      showAlert("Failed to load report", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // get all reports
  const fetchReports = async (filters = {}, page = null) => {
    loading.value = true;
    error.value = null;

    try {
      const body = {
        ...filters
      };
      const result = await useApi(createUrl("api/report/get",{query : body}), {
        method: "POST",
      });
      
      reports.value = result.data.value.data.resource;
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
      showAlert("Failed to load reports", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // delete report
  const deleteReport = async (id) => {
    loading.value = true;
    error.value = null;

    try {
      const result = await useApi(`api/report/delete/${id}`, {
        method: "POST",
      });

      showAlert("Report deleted successfully!", "success");
      await fetchReports();
      return result;
    } catch (err) {
      error.value = err;
      showAlert("Failed to delete report", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // export reports
  const exportReports = async (requestBody) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await fetch("/api/report/export", {
        method: "POST",
        headers: {
          Accept:
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
          "Content-Type": "application/json",
          Authorization: `Bearer ${useCookie("accessToken").value}`,
        },
        body: JSON.stringify(requestBody),
      });
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
      }
      const blob = await response.blob();
      showAlert("Reports exported successfully!", "success");
      return blob;
    } catch (err) {
      error.value = err;
      showAlert("Failed to export reports", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return {
    reports: readonly(reports),
    pagination: pagination,
    currentReport: readonly(currentReport),
    loading: readonly(loading),
    error: readonly(error),

    // Methods
    fetchReports,
    fetchReportBacklinks,
    deleteReport,
    exportReports,

    showAlert,
  };
}
