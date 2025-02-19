<script setup>
import VerticalNavGroup from '@layouts/components/VerticalNavGroup.vue';
import VerticalNavLink from '@layouts/components/VerticalNavLink.vue';
</script>

<template>
  <!-- 👉 Dashboards -->
  <VerticalNavGroup
    :item="{
      title: 'Dashboards',
      icon: 'ri-home-smile-line',
    }"
  >
    <VerticalNavLink
      :item="{
        title: 'Analytics',
        to: '/dashboard',
      }"
    />
    <VerticalNavLink
      :item="{
        title: 'CRM',
        to: '/api/leads',  // Corrected path
      }"
    />
  </VerticalNavGroup>

  <VerticalNavLink
    :item="{
      title: 'Action Plan',
      icon: 'ri-git-commit-line',
      href: '',
    }"
  />
  
  <VerticalNavLink
    :item="{
      title: 'Settings',
      icon: 'ri-user-settings-line',
      to: '/account-settings',
    }"
  />

  <!-- 👉 Front Pages -->
  <VerticalNavGroup
    :item="{
      title: 'Listings',
      icon: 'ri-file-copy-line',
    }"
  >
    <VerticalNavLink
      :item="{
        title: 'Property Listings',
        href: 'https://demos.themeselection.com/materio-vuetify-vuejs-laravel-admin-template/demo-1/front-pages/landing-page',
        target: '_blank',
      }"
    />
    <VerticalNavLink
      :item="{
        title: 'Pre-Construction Listings',
        href: 'https://demos.themeselection.com/materio-vuetify-vuejs-laravel-admin-template/demo-1/front-pages/pricing',
        target: '_blank',
      }"
    />
  </VerticalNavGroup>

  <VerticalNavLink
    :item="{
      title: 'My Team',
      icon: 'ri-checkbox-multiple-line',
      href: '',
    }"
  />  

  <VerticalNavGroup
    :item="{
      title: 'Reports',
      icon: 'ri-home-smile-line',
    }"
  >
    <VerticalNavLink
      :item="{
        title: 'Email Reports',
        to: '/dashboard',
      }"
    />
    <VerticalNavLink
      :item="{
        title: 'SMS Reports',
        to: '/dashboard',  // Ensure this is the correct path
      }"
    />
  </VerticalNavGroup> 

  <VerticalNavLink
    :item="{
      title: 'Integration',
      icon: 'ri-drag-drop-line',
      to: '/integration',
    }"
  />

  <VerticalNavLink
    :item="{
      title: 'Manage Website',
      icon: 'ri-remixicon-line',
      to: '/icons',
    }"
  />

  <!-- 👉 Settings -->
  <VerticalNavGroup
    :item="{
      title: 'Settings',
      icon: 'ri-home-smile-line',
    }"
  >
    <VerticalNavLink
      :item="{
        title: 'Tags | Stage | Source',
        to: '/settings/tags-stages-sources',
      }"
    />
    <VerticalNavLink
      :item="{
        title: 'Signature',
        to: { name: 'signature' },
      }"
    />
    <VerticalNavLink
      :item="{
        title: 'SMS Templates',
        to: { name: 'sms-templates' },
      }"
    />
    <VerticalNavLink
      :item="{
        title: 'Email Templates',
        to: { name: 'email-templates' },
      }"
    />
    <VerticalNavLink
      :item="{
        title: 'Birthday Wishes',
        to: { name: 'birthday-wishes' },
      }"
    />

    <VerticalNavLink
      :item="{
        title: 'Listing Settings',
        to: '/api/leads',  // Corrected path
      }"
    />
  </VerticalNavGroup>

  <VerticalNavLink
    :item="{
      title: 'Tables',
      icon: 'ri-table-alt-line',
      to: '/tables',
    }"
  />
</template>
