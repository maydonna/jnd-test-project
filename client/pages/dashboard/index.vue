<script setup lang="ts">
definePageMeta({
    middleware: ['sanctum:auth', 'admin-only'],
})
</script>

<template>
<h1>Dashboard</h1>
</template>

<style scoped>

</style>
