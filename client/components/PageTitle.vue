<script setup lang="ts">
interface Props {
    title: string
}

defineProps<Props>()
</script>

<template>
    <h1 class="font-semibold text-2xl lg:text-4xl mb-6">{{ title }}</h1>
</template>

<style scoped>

</style>
