<script setup>
import { ref, onMounted } from 'vue'
import { useClientApi } from "@/composables/clientApi"
import { useRouter } from 'vue-router'
import { IconWorldWww } from '@tabler/icons-vue'

const router = useRouter()
const { createClient, fetchClientList, showAlert } = useClientApi()

const form = ref({
  name: '',
  email: '',
  company_name: '',
  website: '',
  industry: '',
  city: '',
  state: '',
  zip_code: '',
  country: '',
  status: 1,
  profile_pic: '',
  phone: '',
})

const submitting = ref(false)
const formRef = ref(null)

// Validation rules
const rules = {
  required: value => !!value || 'This field is required',
  email: value => !value || /^\S+@\S+\.\S+$/.test(value) || 'Enter a valid email',
  website: value => !value || /^(https?:\/\/)?([\w\d-]+\.)+\w{2,}(\/.*)?$/.test(value) || 'Enter a valid URL',
  phone: value => !value || /^\+?[\d\s-]{7,15}$/.test(value) || 'Enter a valid phone number'
}

const handleSubmit = async () => {
  const valid = await formRef.value.validate()
  if (!valid) return

  submitting.value = true
  try {
    await createClient(form.value)
    showAlert('Client created successfully!', 'success')
    router.push({ name: 'apps-client-list' })
  } catch (err) {
    console.error(err)
    showAlert('Failed to create client.', 'error')
  } finally {
    submitting.value = false
  }
}

onMounted(async () => {
  await fetchClientList()
})
</script>

<template>
  <!-- Header -->
  <VCard class="mb-6 pa-6" elevation="2">
    <VContainer fluid>
      <VRow align="center">
        <VCol cols="12" md="8">
          <div class="d-flex align-center">
            <VAvatar size="64" color="primary" variant="elevated" class="me-4">
              <IconWorldWww stroke={2} />
            </VAvatar>
            <div>
              <h1 class="text-h3 font-weight-bold mb-1">Add Client</h1>
              <p class="text-body-1 text-medium-emphasis mb-0">
                Fill in client details to create a new client record.
              </p>
            </div>
          </div>
        </VCol>
      </VRow>
    </VContainer>
  </VCard>

  <!-- Form -->
  <VCard class="pa-6" elevation="2">
    <h2 class="text-h5 font-weight-bold mb-6">Client Details</h2>

    <VForm ref="formRef" @submit.prevent="handleSubmit">
      <VRow dense>
        <VCol cols="12" md="6">
          <AppTextField
            v-model="form.name"
            label="Full Name *"
            :rules="[rules.required]"
            variant="outlined"
          />
        </VCol>

        <VCol cols="12" md="6">
          <AppTextField
            v-model="form.email"
            label="Email *"
            type="email"
            :rules="[rules.required, rules.email]"
            variant="outlined"
          />
        </VCol>

        <VCol cols="12" md="6">
          <AppTextField
            v-model="form.company_name"
            label="Company Name *"
            :rules="[rules.required]"
            variant="outlined"
          />
        </VCol>

        <VCol cols="12" md="6">
          <AppTextField
            v-model="form.website"
            label="Website"
            placeholder="https://example.com"
            :rules="[rules.website]"
            variant="outlined"
          />
        </VCol>

        <VCol cols="12" md="6">
          <AppTextField
            v-model="form.phone"
            label="Phone Number *"
            :rules="[rules.required,rules.phone]"
            variant="outlined"
          />
        </VCol>

        <VCol cols="12" md="6">
          <AppTextField v-model="form.industry" label="Industry" variant="outlined" />
        </VCol>

        <VCol cols="12" md="4">
          <AppTextField v-model="form.city" label="City" variant="outlined" />
        </VCol>

        <VCol cols="12" md="4">
          <AppTextField v-model="form.state" label="State" variant="outlined" />
        </VCol>

        <VCol cols="12" md="4">
          <AppTextField v-model="form.zip_code" label="Zip Code" variant="outlined" />
        </VCol>

        <VCol cols="12" md="6">
          <AppTextField v-model="form.country" label="Country" variant="outlined" />
        </VCol>

        <VCol cols="12" md="6">
          <AppSelect
            v-model="form.status"
            :items="[
              { title: 'Active', value: 1 },
              { title: 'Inactive', value: 2 }
            ]"
            label="Status"
            variant="outlined"
          />
        </VCol>

        <VCol cols="12" class="mt-4 d-flex">
          <VBtn :loading="submitting" color="primary" type="submit" class="me-3">
            Create Client
          </VBtn>
          <VBtn variant="outlined" @click="router.back()">Cancel</VBtn>
        </VCol>
      </VRow>
    </VForm>
  </VCard>
</template>

<style scoped>
.pa-6 {
  padding: 24px;
}
.text-medium-emphasis {
  color: rgba(0, 0, 0, 0.6);
}
</style>
