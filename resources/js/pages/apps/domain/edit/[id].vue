<script setup>
import { useApi } from '@/composables/api.js'
import { useDomainShow } from '@/composables/domainApi.js'
import DomainEditDrawer from '@/views/apps/domain/DomainEditDrawer.vue'
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const domainData = ref(null)
const isEditDrawerActive = ref(false)

// Fetch single domain by ID
const { data, execute: fetchDomain } = useDomainShow(route.params.id)

onMounted(async () => {
  const response = await fetchDomain()
  if (response?.data?.resource) {
    domainData.value = response.data.resource
  }
})

// Save domain changes
const saveDomain = async (updatedDomain) => {
  if (!updatedDomain?.id) return

  await useApi(`api/clientdomain/${updatedDomain.id}`, {
    method: 'PUT',
    body: updatedDomain,
  })()
  domainData.value = { ...updatedDomain }
  isEditDrawerActive.value = false
}

// Example sub-item functions
const addSubItem = (value) => {
  if (!domainData.value.subItems) domainData.value.subItems = []
  domainData.value.subItems.push(value)
}

const removeSubItem = (index) => {
  domainData.value?.subItems?.splice(index, 1)
}
</script>

<template>
  <VRow v-if="domainData">
    <!-- Left Column: Editable fields -->
    <VCol cols="12" md="9">
      <DomainEditDrawer v-model:is-drawer-open="isEditDrawerActive" :domain="domainData" @save="saveDomain" />
    </VCol>

    <!-- Right Column: Actions -->
    <VCol cols="12" md="3">
      <VCard class="mb-8">
        <VCardText>
          <VBtn block color="success" prepend-icon="tabler-save" @click="saveDomain(domainData)">
            Save Changes
          </VBtn>
          <VBtn block color="secondary" prepend-icon="tabler-arrow-back" :to="{ name: 'apps-domain-list' }">
            Back to List
          </VBtn>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>

  <section v-else>
    <VAlert type="error" variant="tonal">
      Domain with ID {{ route.params.id }} not found!
    </VAlert>
  </section>
</template>
