<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useClientApi } from '@/composables/clientApi'

const route = useRoute()
const router = useRouter()
const { getClientById, updateClient, showAlert } = useClientApi()

const clientData = ref(null)
const submitting = ref(false)
const formRef = ref(null)

// Validation rules
const rules = {
  required: value => !!value || 'This field is required',
  email: value => !value || /^\S+@\S+\.\S+$/.test(value) || 'Enter a valid email',
  website: value => !value || /^(https?:\/\/)?([\w\d-]+\.)+\w{2,}(\/.*)?$/.test(value) || 'Enter a valid URL',
  phone: value => !value || /^\+?[\d\s-]{7,15}$/.test(value) || 'Enter a valid phone number'
}

// Fetch client on mount
onMounted(async () => {s
  if (route.params.id) {
    try {
      const res = await getClientById(route.params.id)
      if (res?.data?.resource) {
        clientData.value = { ...res.data.resource }
      } else {
        showAlert('Client not found', 'error')
        router.push({ name: 'apps-client-list' })
      }
    } catch (err) {
      console.error(err)
      showAlert('Failed to load client data', 'error')
      router.push({ name: 'apps-client-list' })
    }
  }
})

// Save client
const saveClient = async () => {
  const valid = await formRef.value.validate()
  if (!valid) return

  submitting.value = true
  try {
    await updateClient(clientData.value.id, clientData.value)
    showAlert('Client updated successfully!', 'success')
    router.push({ name: 'apps-client-list' })
  } catch (err) {
    console.error(err)
    showAlert('Failed to save client', 'error')
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <VCard class="pa-6">
    <h1 class="text-h4 font-weight-bold mb-4">Edit Client</h1>

    <VForm ref="formRef" @submit.prevent="saveClient">
      <VRow dense>
        <VCol cols="12" md="6">
          <AppTextField v-model="clientData.name" label="Full Name" :rules="[rules.required]" />
        </VCol>
        <VCol cols="12" md="6">
          <AppTextField v-model="clientData.email" label="Email" :rules="[rules.required, rules.email]" />
        </VCol>
        <VCol cols="12" md="6">
          <AppTextField v-model="clientData.company_name" label="Company Name" :rules="[rules.required]" />
        </VCol>
        <VCol cols="12" md="6">
          <AppTextField v-model="clientData.website" label="Website" :rules="[rules.website]" />
        </VCol>
        <VCol cols="12" md="6">
          <AppTextField v-model="clientData.phone" label="Phone Number" :rules="[rules.phone]" />
        </VCol>
        <VCol cols="12" md="6">
          <AppTextField v-model="clientData.industry" label="Industry" />
        </VCol>
        <VCol cols="12" md="4">
          <AppTextField v-model="clientData.city" label="City" />
        </VCol>
        <VCol cols="12" md="4">
          <AppTextField v-model="clientData.state" label="State" />
        </VCol>
        <VCol cols="12" md="4">
          <AppTextField v-model="clientData.zip_code" label="Zip Code" />
        </VCol>
        <VCol cols="12" md="6">
          <AppTextField v-model="clientData.country" label="Country" />
        </VCol>
        <VCol cols="12" md="6">
          <AppSelect
            v-model="clientData.status"
            :items="[
              { title: 'Active', value: 1 },
              { title: 'Inactive', value: 2 }
            ]"
            label="Status"
          />
        </VCol>
      </VRow>

      <VCardActions class="mt-4">
        <VBtn color="primary" :loading="submitting" @click="saveClient">Save Changes</VBtn>
        <VBtn variant="text" @click="router.push({ name: 'apps-client-list' })">Cancel</VBtn>
      </VCardActions>
    </VForm>
  </VCard>
</template>

<style scoped>
.pa-6 {
  padding: 24px;
}
</style>
