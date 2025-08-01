<style>
.v-table .v-table__wrapper .v-data-table__tr td {
  padding: 25px;
}

.v-table .v-table__wrapper table thead tr th.v-data-table__td {
  padding: 25px;
  background-color: #fff !important;
  border-block-end: 1px solid #e6e5e7 !important;
}
.layout-nav-type-vertical .layout-vertical-nav .nav-item-title { color: #000; }

header.layout-navbar {
  background: #fff;
  border-block-end: 1px solid #eee;
}

.layout-nav-type-vertical .layout-vertical-nav {
  background-color: #fff !important;
  box-shadow: 0 0 15px 2px #d7d7d78f;
}

.services-card .v-row .service-cards {
  border-radius: 10px;
  box-shadow: 0 1px 5px 3px #c3c3c324;
  padding-block: 30px;
  padding-inline: 25px;
}

.services-card .v-row .v-col-sm-6:first-child .service-cards {
  background-image: linear-gradient(to left, #7fd5ff 0%, #f9e4db 100%);
}

.services-card .service-cards .v-avatar {
  background-color: transparent !important;
  box-shadow: none !important;
}
.services-card .service-cards .v-avatar i { color: #000; }

.services-card .v-row .v-col-sm-6:nth-child(2) .service-cards {
  background-image: linear-gradient(to left, #99a4f9 0%, #f0e3f7 100%);
}

.services-card .v-row .v-col-sm-6:nth-child(3) .service-cards {
  background-image: linear-gradient(135deg, #fff6b7 10%, #f6416c 100%);
}
.services-card .v-row .v-col-sm-6:last-child .service-cards { background-image: linear-gradient(-225deg, #dfffcd 0%, #39f3bb 100%); }
</style>
 
<script setup>
const statistics = [
  {
    title: 'Leads',
    stats: '245k',
    icon: 'ri-pie-chart-2-line',
    color: 'primary',
  },
  {
    title: 'Action plan',
    stats: 'Active',
    icon: 'ri-group-line',
    color: 'success',
  },
  {
    title: 'Inbox',
    stats: '1.54k',
    icon: 'ri-macbook-line',
    color: 'warning',
  },
  {
    title: 'Todays Events',
    stats: '$88k',
    icon: 'ri-money-dollar-circle-line',
    color: 'info',
  },
]
 
const moreList = [
  {
    title: 'Share',
    value: 'Share',
  },
  {
    title: 'Refresh',
    value: 'Refresh',
  },
  {
    title: 'Update',
    value: 'Update',
  },
]
</script>
 
<template>
<VCard title="Dashboard">
<template #subtitle>
</template>
 
    <template #append>
<MoreBtn :menu-list="moreList" />
</template>
 
    <VCardText class="services-card">
<VRow>
<VCol
          v-for="item in statistics"
          :key="item.title"
          cols="12"
          sm="6"
          md="3"
>
<div class="d-flex align-center gap-x-3 service-cards">
<VAvatar
              :color="item.color"
              rounded
              size="40"
              class="elevation-2"
>
<VIcon
                size="24"
                :icon="item.icon"
              />
</VAvatar>
 
            <div class="d-flex flex-column">
<div class="text-body-1">
                {{ item.title }}
</div>
<h5 class="text-h5">
                {{ item.stats }}
</h5>
</div>
</div>
</VCol>
</VRow>
</VCardText>
</VCard>
</template>
