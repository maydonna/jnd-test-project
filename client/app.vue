<template>
  <UApp
    :toaster="{
      position: 'top-right'
    }"
  >
      <NuxtLoadingIndicator />
      <NuxtLayout>
          <NuxtPage />
      </NuxtLayout>
  </UApp>
</template>
<script setup lang="ts">
</script>
