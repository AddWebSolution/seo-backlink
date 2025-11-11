<script setup>
import EcommerceCongratulationsUser from '@/views/dashboards/ecommerce/EcommerceCongratulationsUser.vue'
import EcommerceStatistics from '@/views/dashboards/ecommerce/EcommerceStatistics.vue'
import CrmActivityTimeline from '@/views/dashboards/crm/CrmActivityTimeline.vue'
import EcommerceGeneratedLeads from '@/views/dashboards/ecommerce/EcommerceGeneratedLeads.vue'
import EcommerceExpensesRadialBarCharts from '@/views/dashboards/ecommerce/EcommerceExpensesRadialBarCharts.vue'
import CrmEarningReportsYearlyOverview from '@/views/dashboards/crm/CrmEarningReportsYearlyOverview.vue'
import EcommerceTotalProfitLineCharts from '@/views/dashboards/ecommerce/EcommerceTotalProfitLineCharts.vue'
</script>

<template>
  <VRow class="match-height">

    <!-- Left Column: Cards & Charts -->
    <VCol cols="12" md="8" lg="4">
      <VRow >
        <VCol cols="12" sm="6" md="12" lg="12">
          <EcommerceCongratulationsUser />
        </VCol>

        <VCol cols="12" sm="6" md="12" lg="12">
          <EcommerceTotalProfitLineCharts />
        </VCol>

        <VCol cols="12" sm="6" md="12" lg="12">
          <EcommerceExpensesRadialBarCharts />
        </VCol>

        <!-- Uncomment if needed -->
        <!-- <VCol cols="12" md="12">
          <EcommerceGeneratedLeads class="h-300" />
        </VCol> -->
      </VRow>
    </VCol>

    <!-- Right Column: Main Stats -->
    <VCol cols="12" md="12" lg="8">
      <EcommerceStatistics class="h-100" />
    </VCol>

    <!-- Example of additional component stacked responsively -->
    <!-- <VCol cols="12" sm="12" md="6" lg="6">
      <CrmActivityTimeline />
    </VCol> -->

    <!-- All other components can follow the same pattern -->
  </VRow>
</template>


<style lang="scss">
@use "@core-scss/template/libs/apex-chart.scss";
</style>
