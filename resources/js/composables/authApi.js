// @/composables/useAuthApi.js
import { ref, readonly } from "vue";
import { useApi } from "./useApi";
import { useAlert } from "./useAlert";

export function useAuthApi() {
  const { showAlert } = useAlert();

  const user = ref(null);
  const loading = ref(false);
  const error = ref(null);

  // Login
 const login = async (payload) => {

    try {
      const result = await useApi("api/auth/login", {
        method: "POST",
        body: payload,
      });

      if (result?.statusCode.value === 200) {
        user.value = result.data.user;
        showAlert(result.data.message || "Login successful", "success");
      } else {
        showAlert(result?.data?.message || "Login didn’t work. Check your email and password.", "error")
      }

      return result;
    } catch (err) {
      error.value = err;
      showAlert(err.message || "Login error", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Register
  const register = async (payload) => {
    loading.value = true;
    error.value = null;

    try {
      const result = await useApi("api/auth/register", {
        method: "POST",
        body: payload,
      });
      showAlert("Registered successfully!", "success");
      return result;
    } catch (err) {
      error.value = err;
      showAlert("Registration failed", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Logout
  const logout = async () => {
    loading.value = true;
    error.value = null;

    try {
      const result = await useApi("api/auth/logout", {
        method: "POST",
      });
      user.value = null;
      showAlert("Logged out successfully!", "success");
      return result;
    } catch (err) {
      error.value = err;
      showAlert("Logout failed", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Refresh token
  const refresh = async () => {
    loading.value = true;
    error.value = null;

    try {
      const result = await useApi("api/auth/refresh", {
        method: "POST",
      });
      return result;
    } catch (err) {
      error.value = err;
      showAlert("Token refresh failed", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Get current authenticated user
  const fetchUser = async () => {
    loading.value = true;
    error.value = null;

    try {
      const result = await useApi("api/auth/me", {
        method: "POST",
      });
      user.value = result.data?.user || null;
      return result;
    } catch (err) {
      error.value = err;
      user.value = null;
      showAlert("Failed to fetch user", "error");
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return {
    user: readonly(user),
    loading: readonly(loading),
    error: readonly(error),

    login,
    register,
    logout,
    refresh,
    fetchUser,

    showAlert,
  }

}
