<script setup lang="ts">
import {transformModel} from "@vue/compiler-core";

definePageMeta({
    middleware: ['sanctum:auth', 'admin-only'],
})
interface DashboardData {
  users_count: number
  urls_count: number
}

const { data } = await useApiFetchCached<DashboardData>('/dashboard', { method: 'GET' })
</script>

<template>
<div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
        <NuxtLink to="/dashboard/users">
            <UCard class="data-card">
                <UIcon name="i-lucide-users-round" class="size-10 shrink-0" />
                <h4 class="font-medium mb-6">Users</h4>
                <p class="text-3xl lg:text-5xl text-(--ui-primary) font-semibold">{{ data?.users_count }}</p>
            </UCard>
        </NuxtLink>
        <NuxtLink to="/dashboard/urls">
            <UCard class="data-card">
                <UIcon name="i-lucide-link" class="size-10 shrink-0" />
                <h4 class="font-medium mb-6">URLs</h4>
                <p class="text-3xl lg:text-5xl text-(--ui-primary) font-semibold">{{ data?.urls_count }}</p>
            </UCard>
        </NuxtLink>
    </div>
</div>
</template>

<style scoped>
.data-card {
    @apply text-center bg-(--ui-bg-elevated) hover:scale-105 transition-transform duration-200
}
</style>
