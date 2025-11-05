<script setup>
import { ref, onMounted } from 'vue'
import ScrollToTop from '@core/components/ScrollToTop.vue'
import initCore from '@core/initCore'
import {
  initConfigStore,
  useConfigStore,
} from '@core/stores/config'
import { hexToRgb } from '@core/utils/colorConverter'
import { useTheme } from 'vuetify'
import GlobalDialog from "@/components/dialogs/GlobalDialog.vue";

const { global } = useTheme()

// ℹ️ Sync current theme with initial loader theme
initCore()
initConfigStore()

const confirmDialogRef = ref(null)
const { setDialogRef } = useConfirmDialog()

const configStore = useConfigStore()

onMounted(() => {
  setDialogRef(confirmDialogRef.value)
})
</script>

<template>
  <VLocaleProvider :rtl="configStore.isAppRTL">
    <!-- ℹ️ This is required to set the background color of active nav link based on currently active global theme's primary -->
    <VApp :style="`--v-global-theme-primary: ${hexToRgb(global.current.value.colors.primary)}`">
      <RouterView />
      <ScrollToTop />
      <GlobalDialog ref="confirmDialogRef" />
    </VApp>
  </VLocaleProvider>
</template>
