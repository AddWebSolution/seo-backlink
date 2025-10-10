// @/composables/useDashboardApi.js
import { ref, readonly } from "vue";
import { useApi } from "./useApi";
import { useAlert } from "./useAlert";

export function useDashboardApi() {
  const { showAlert } = useAlert();

  const dashboard = ref(null);
  const monthlyStats = ref(null);
  const loading = ref(false);
  const error = ref(null);


  // get stats
  const fetchStats = async () => {
    loading.value = true;
    error.value = null;
    try {
      const result = await useApi(`api/dashboard/stats`, {
        method: "POST",
      });
      dashboard.value = result.data;
      return result;
    } catch (err) {
      error.value = err;
      showAlert("Failed to load stats", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

    // get monthly stats
  const fetchMonthlyStats = async () => {
    loading.value = true;
    error.value = null;
    try {
      const result = await useApi(`api/dashboard/monthly/stats`, {
        method: "POST",
      });
      monthlyStats.value = result.data?.value;
      return result;
    } catch (err) {
      error.value = err;
      showAlert("Failed to load stats", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return {
    dashboard: readonly(dashboard),
    monthlyStats: readonly(monthlyStats),
    loading: readonly(loading),
    error: readonly(error),

    fetchStats,
    fetchMonthlyStats,
    showAlert,
  };
}