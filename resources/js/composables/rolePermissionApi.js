// @/composables/useRolePermissions.js
import { ref, readonly } from "vue";
import { useApi } from "@/composables/useApi";
import { useAlert } from "@/composables/useAlert";

export function useRolePermissions() {
    const { showAlert } = useAlert();

    const roles = ref([]);
    const permissions = ref([]);
    const loading = ref(false);
    const error = ref(null);

    // Fetch all roles
    const fetchRoles = async () => {
        loading.value = true;
        error.value = null;
        try {
            const result = await useApi("/api/user/role/all", { method: "GET" });
            roles.value = result?.data?.value?.data || [];
            return result;
        } catch (err) {
            error.value = err;
            roles.value = [];
            showAlert("Failed to fetch roles", "error");
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // Fetch permissions list
    const fetchPermissions = async () => {
        loading.value = true;
        error.value = null;

        try {
            const result = await useApi("/api/user/role/permissions", { method: "GET" });

            const data = result?.data?.value?.data || [];

            if (Array.isArray(data) && data.length > 0) {
                const grouped = {};

                data.forEach(item => {
                    const [action, module] = item.name.split(" ");
                    if (!module) return;

                    const formattedModule =
                        module.charAt(0).toUpperCase() +
                        module.slice(1).replace(/([a-z])([A-Z])/g, "$1 $2");

                    if (!grouped[formattedModule]) grouped[formattedModule] = [];

                    grouped[formattedModule].push({
                        key: item.name,
                        label: action.charAt(0).toUpperCase() + action.slice(1),
                    });
                });

                permissions.value = Object.keys(grouped).map(moduleName => ({
                    module: moduleName,
                    permissions: grouped[moduleName],
                }));
            } else {
                permissions.value = [];
            }

            return result;
        } catch (err) {
            error.value = err;
            permissions.value = [];
            showAlert("Failed to fetch permissions", "error");
            throw err;
        } finally {
            loading.value = false;
        }
    };


    // Create role
    const createRole = async (payload) => {
        loading.value = true;
        error.value = null;
        try {
            const result = await useApi("/api/user/role/create", {
                method: "POST",
                body: payload,
            });
            showAlert("Role created successfully!", "success");
            await fetchRoles();
            return result;
        } catch (err) {
            error.value = err;
            showAlert("Failed to create role", "error");
            throw err;
        } finally {
            loading.value = false;
        }
    };

    return {
        roles: readonly(roles),
        permissions: readonly(permissions),
        loading: readonly(loading),
        error: readonly(error),
        fetchRoles,
        fetchPermissions,
        createRole,
    };
}