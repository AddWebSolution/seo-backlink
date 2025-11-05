// @/composables/useClientApi.js
import { ref, readonly } from "vue";
import { useApi } from "./useApi";
import { useAlert } from "./useAlert";
import {useConfirmDialog} from "@/composables/useConfirmDialog.js";

const { confirm } = useConfirmDialog()

export function useUserApi() {
    const { showAlert } = useAlert();

    const Users = ref([]);
    const pagination = ref({
        total: 0,
        currentPage: 1,
        perPage: 10,
        lastPage: 1,
        itemsPerPage: 10,
        page: 1,
    });
    const UserList = ref();
    const currentUser = ref(null);
    const loading = ref(false);
    const error = ref(null);

    // get users by id
    const fetchUser = async (id) => {
        loading.value = true;
        error.value = null;
        try {
            const result = await useApi(`api/user/get/${id}`, {
                method: "POST",
            });
            currentUser.value = result.data.value.data;
            return result;
        } catch (err) {
            error.value = err;
            currentUser.value = null;
            showAlert("Failed to load Client", "error");
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // get all users
    const fetchUsers = async (filters = {}, page = null) => {
        loading.value = true;
        error.value = null;

        try {
            const body = {
                ...filters
            };
            const result = await useApi(createUrl("api/user/get", { query: body }), {
                method: "POST",
            });
            Users.value = result.data.value.data.resource;
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
            showAlert("Failed to load users", "error");
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // import users

    const importUsers = async (file) => {
        if (!file) {
            showAlert("No file selected", "error");
            return { success: false, message: "No file selected" };
        }

        loading.value = true;
        error.value = null;

        try {
            const formData = new FormData();
            formData.append("file", file);

            const result = await useApi("api/user/import", {
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
                responseData?.message || "users imported successfully!";

            showAlert(successMsg, "success");
            await fetchUsers();

            return {
                success: true,
                message: successMsg,
                data: responseData,
            };
        } catch (err) {
            error.value = err;
            const errorMsg = "Failed to import users";
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
            const response = await fetch("/api/user/import/template/download", {
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

    // create User
    const createUser = async (payload) => {
        loading.value = true;
        error.value = null;

        try {
            const result = await useApi("api/user/store", {
                method: "POST",
                body: payload,
            });

            showAlert("User created successfully!", "success");
            await fetchUsers();
            return result;
        } catch (err) {
            error.value = err;
            showAlert("Failed to create User", "error");
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // update Client
    const updateUser = async (id, payload) => {
        loading.value = true;

        try {
            const result = await useApi(`api/user/update/${id}`, {
                method: "POST",
                body: payload,
            });

            showAlert("record updated successfully!", "success");
            await fetchUsers();
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
    const deleteUser = async (id) => {
        const confirmed = await confirm({
            title: 'Delete User',
            message: 'Are you sure you want to delete this user? This action cannot be undone.',
            confirmText: 'Delete',
            cancelText: 'Cancel',
            confirmColor: 'error',
            type: 'warning'
        })

        if (!confirmed) {
            return
        }
        loading.value = true;
        error.value = null;

        try {
            const result = await useApi(`api/user/delete/${id}`, {
                method: "POST",
            });

            showAlert("Client deleted successfully!", "success");
            await fetchUsers();
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
    const fetchUserList = async () => {
        loading.value = true;
        error.value = null;

        try {
            const result = await useApi("api/client/name/list", {
                method: "POST",
            });
            UserList.value = result?.data?.value?.users;
            return result;
        } catch (err) {
            error.value = err;
            UserList.value = [];
            showAlert("Failed to load Client list", "error");
            throw err;
        } finally {
            loading.value = false;
        }
    };

    return {
        Users: readonly(Users),
        pagination: pagination,
        UserList: UserList,
        currentUser: readonly(currentUser),
        loading: readonly(loading),
        error: readonly(error),

        // Methods
        fetchUsers,
        fetchUserList,
        importUsers,
        fetchUser,
        downloadTemplate,
        createUser,
        updateUser,
        deleteUser,

        showAlert,
    };
}
