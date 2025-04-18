<script setup lang="ts">
import HeaderNavBar from "~/components/HeaderNavBar.vue";
</script>

<template>
    <HeaderNavBar />
    <UContainer class="mt-8">
        <slot></slot>
    </UContainer>
</template>

<style scoped>

</style>
