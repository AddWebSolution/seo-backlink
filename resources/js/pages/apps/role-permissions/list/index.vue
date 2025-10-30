
<script setup>
import { IconLock, IconChartLine } from "@tabler/icons-vue";
import RoleDialog from "@/pages/apps/role-permissions/RoleDialog.vue";
import {useRolePermissions} from "@/composables/rolePermissionApi.js";

const {
  roles,
  permissions,
  fetchRoles,
  // fetchPermissions,
  createRole,
  loading,
} = useRolePermissions();

const roleId = ref('')
const roleName = ref('')

const isAddRoleDialogVisible = ref(false)
const selectedPermission = ref([])

const editRole = (value, permissions, name) => {
  roleName.value = name
  roleId.value = value
  // selectedPermission.value = permissions
  selectedPermission.value = permissions.map(p => p.name || p);
  isAddRoleDialogVisible.value = true
}

const refreshData = async () => {
  // getRoles()
  await fetchRoles();
}

const addRole = () => {
  roleId.value = 0
  roleName.value = ''
  selectedPermission.value = []
  isAddRoleDialogVisible.value = true
}

onMounted(async () => {
  await fetchRoles();
  // await fetchPermissions();
});
</script>

<template>
  <!-- Header Section -->
  <VCard class="mb-6 pa-6 overflow-hidden" elevation="2">
    <VContainer fluid>
      <VRow align="center">
        <VCol cols="12" md="8">
          <div class="d-flex align-center">
            <VAvatar size="64" color="primary" variant="elevated" class="me-4">
              <IconLock stroke="{2}" />
            </VAvatar>
            <div>
              <h1 class="text-h3 font-weight-bold mb-1">Role & Permissions</h1>
              <p class="text-body-1 text-medium-emphasis mb-0">
                Manage Access Control and User Privileges
              </p>
            </div>
          </div>
        </VCol>
      </VRow>
    </VContainer>
  </VCard>
  <VRow>
    <template v-if="loading">
      <VCol
          cols="12"
          sm="6"
          lg="4"
      >
        <VCard
            class="h-100"
            :ripple="false"
        >
          <VRow
              no-gutters
              class="h-100"
          >
            <VCol cols="12">
              <VCardText class="d-flex flex-column align-center justify-center gap-4">
                <VProgressCircular
                    :size="60"
                    color="primary"
                    indeterminate
                />
                <div class="text-end">
                  Loading Data
                </div>
              </VCardText>
            </VCol>
          </VRow>
        </VCard>
      </VCol>
    </template>
    <template v-else>
      <VCol
          v-for="item in roles"
          :key="item.id"
          cols="12"
          sm="6"
          lg="4"
      >
        <VCard>
          <VCardText>
            <div class="d-flex justify-space-between align-center">
              <div>
                <h5 class="text-h5">
                  {{ item.name }}
                </h5>
                <div class="d-flex align-center">
                  <a
                      href="javascript:void(0)"
                      @click="editRole(item.id, item.permissions ?? [], item.name)"
                  >
                    Edit Role
                  </a>
                </div>
              </div>
            </div>
          </VCardText>
          <VCardText class="d-flex align-center pb-4">
            <div class="text-body-1">
              Total {{ item.permissions?.length || 0 }} Permissions
            </div>

            <VSpacer />
          </VCardText>
        </VCard>
      </VCol>
    </template>

    <!-- 👉 Roles Cards -->
    <VCol
        cols="12"
        sm="6"
        lg="4"
    >
      <VCard
          class="h-100"
          :ripple="false"
      >
        <VRow
            no-gutters
            class="h-100"
        >
          <VCol cols="12">
            <VCardText class="d-flex flex-column align-center justify-center gap-4">
              <div class="text-end">
                Add new role, if it doesn't exist.
              </div>
              <VBtn
                  @click="addRole"
              >
                Add New Role
              </VBtn>
            </VCardText>
          </VCol>
        </VRow>
      </VCard>
    </VCol>
  </VRow>

  <RoleDialog
      v-model:isDialogVisible="isAddRoleDialogVisible"
      v-model:roleName="roleName"
      v-model:roleId="roleId"
      v-model:permissions="selectedPermission"
      @update:refresh="refreshData"
  />
</template>
