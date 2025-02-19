<script setup>
import AnalyticsTransactions from '@/views/dashboard/AnalyticsTransactions.vue';
import AnalyticsUserTable from '@/views/dashboard/AnalyticsUserTable.vue';


const totalProfit = {
  title: 'Total Profit',
  color: 'secondary',
  icon: 'ri-pie-chart-2-line',
  stats: '$25.6k',
  change: 42,
  subtitle: 'Weekly Project',
}

const newProject = {
  title: 'New Project',
  color: 'primary',
  icon: 'ri-file-word-2-line',
  stats: '862',
  change: -18,
  subtitle: 'Yearly Project',
}
</script>

<template>
  <VRow class="match-height">

    <VCol
      cols="12"
      md="12"
    >
      <AnalyticsTransactions />
    </VCol>
    <VCol cols="12">
      <AnalyticsUserTable />
    </VCol>
    <VCol cols="12">
      <AnalyticsUserTabl />
    </VCol>
  </VRow>
</template>
