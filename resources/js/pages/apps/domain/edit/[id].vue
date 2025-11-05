<script setup>
import { useDomainApi } from '@/composables/domainApi'
import DomainEditDrawer from '@/views/apps/domain/DomainEditDrawer.vue'
import { onMounted, ref, computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const domainData = ref(null)
const isEditDrawerActive = ref(false)

const { 
  currentDomain, 
  loading, 
  error, 
  updateDomain, 
  showAlert 
} = useDomainApi()

const fetchDomainById = async (id) => {
  loading.value = true
  error.value = null
  
  try {
    const result = await useApi(`api/clientdomain/get/${id}`, { 
      method: 'POST' 
    })
    
    if (result?.data?.resource) {
      domainData.value = result.data.resource
      currentDomain.value = result.data.resource
    } else {
      showAlert('Domain not found', 'error')
    }
    
    return result
  } catch (err) {
    error.value = err
    showAlert('Failed to load domain', 'error')
    throw err
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  if (route.params.id) {
    await fetchDomainById(route.params.id)
  }
})

const saveDomain = async (updatedDomain) => {
  if (!updatedDomain?.id) {
    showAlert('Invalid domain data', 'error')
    return
  }

  try {
    await updateDomain(updatedDomain.id, updatedDomain)
    domainData.value = { ...updatedDomain }
    isEditDrawerActive.value = false
    
    await fetchDomainById(updatedDomain.id)
  } catch (error) {
    console.error('Save failed:', error)
  }
}

const addSubItem = (value) => {
  if (!domainData.value) return
  if (!domainData.value.subItems) domainData.value.subItems = []
  domainData.value.subItems.push(value)
}

const removeSubItem = (index) => {
  if (!domainData.value?.subItems) return
  domainData.value.subItems.splice(index, 1)
}

const domain = computed(() => domainData.value || currentDomain.value)
const isLoading = computed(() => loading.value)
const hasError = computed(() => error.value)
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
          <VBtn block color="secondary" prepend-icon="tabler-arrow-back" :to="{ name: 'domain-list' }">
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
