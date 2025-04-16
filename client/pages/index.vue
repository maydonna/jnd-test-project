<script setup lang="ts">
const user = useSanctumUser<Item<User>>()
</script>

<template>
    <h1 class="text-3xl font-bold">
        Hello world!, {{ user?.data.name }}
    </h1>
</template>

<style scoped>

</style>
