<script setup lang="ts">
definePageMeta({
    middleware: ['sanctum:auth'],
});
</script>

<template>
    <h1 class="text-3xl font-bold underline">
        Hello world!
    </h1>
</template>

<style scoped>

</style>
