<script setup>
import { ref, onMounted } from 'vue'
import { useKeywordApi } from '@/composables/KeywordApi'
import { useDomainApi } from '@/composables/domainApi'
import { useRouter } from 'vue-router'
import { IconWorldWww } from '@tabler/icons-vue'

const router = useRouter()

const { createKeyword, showAlert } = useKeywordApi()
const { fetchDomainList, domainList, loading } = useDomainApi()

const form = ref({
  client_domain_id: null,
  keyword: '',
  status: 1,
})

const resetForm = () => {
  form.value = {
    client_domain_id: null,
    keyword: '',
    status: 1,
  }
}

const submitting = ref(false)

// Fetch domain list on mount
onMounted(async () => {
  await fetchDomainList()
})

const handleSubmit = async () => {
  if (!form.value.client_domain_id || !form.value.keyword.trim()) {
    showAlert('Please select a domain and enter a keyword.', 'error')
    return
  }

  submitting.value = true
  try {
    await createKeyword(form.value)
    showAlert('Keyword added successfully!', 'success')
    // router.push({ name: 'apps-keyword-list' })
    resetForm()
  } catch (err) {
    console.error('Keyword creation failed:', err)
    showAlert('Failed to add keyword.', 'error')
  } finally {
    submitting.value = false
  }
}
</script>

<template>

   <!-- Header Section -->
  <VCard class="mb-6 pa-6 overflow-hidden" elevation="0">
    <VContainer fluid>
      <VRow align="center">
        <VCol cols="12" md="8">
          <div class="d-flex align-center">
            <VAvatar size="64" color="primary" variant="elevated" class="me-4">
              <IconWorldWww stroke={2} />
            </VAvatar>
            <div>
              <h1 class="text-h3 font-weight-bold mb-1">Add Keyword</h1>
              <p class="text-body-1 text-medium-emphasis mb-0">
              </p>
            </div>
          </div>
        </VCol>
        <!-- <VCol cols="12" md="4" class="text-md-end">
          <VBtn variant="outlined" size="large" class="text-primary font-weight-medium"
            :to="{ name: 'apps-domain-add' }">
            <VIcon icon="tabler-plus" class="me-2" />
            Add Domain
          </VBtn>
        </VCol> -->
      </VRow>
    </VContainer>
  </VCard>
  <VCard class="pa-6">
    <h2 class="text-h5 font-weight-bold mb-6">Add New Keyword</h2>

    <VForm @submit.prevent="handleSubmit">
      <VRow>
        <VCol cols="12" md="6">
          <AppSelect
            v-model="form.client_domain_id"
            :items="domainList"
            item-title="title"
            item-value="id"
            label="Select Domain"
            variant="outlined"
            hide-details
            clearable
            :loading="loading"
            required
          />
        </VCol>

        <VCol cols="12" md="6">
          <AppTextField
            v-model="form.keyword"
            label="Keyword"
            placeholder="Enter SEO keyword"
            variant="outlined"
            hide-details
            required
          />
        </VCol>

        <VCol cols="12" md="6">
          <AppSelect
            v-model="form.status"
            :items="[
              { title: 'Active', value: 1 },
              { title: 'Deactivated', value: 2 }
            ]"
            label="Status"
            variant="outlined"
            hide-details
          />
        </VCol>

        <VCol cols="12" class="mt-4">
          <VBtn :loading="submitting" color="primary" type="submit" class="mr-3">
            Add Keyword
          </VBtn>
          <VBtn variant="text" @click="$router.back()">Cancel</VBtn>
        </VCol>
      </VRow>
    </VForm>
  </VCard>
</template>

<style scoped>
.pa-6 {
  padding: 24px;
}
</style>
