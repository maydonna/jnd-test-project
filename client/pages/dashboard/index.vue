<script setup lang="ts">
import type {TableColumn} from "@nuxt/ui";
import CopyToClipboardButton from "~/components/CopyToClipboardButton.vue";

definePageMeta({
    middleware: ['sanctum:auth', 'admin-only'],
})

const { data, status } = await useApiFetchCached<DashboardData>('/dashboard', { method: 'GET' })

const columns: TableColumn<Url>[] = [
    {
        accessorKey: 'created_at',
        header: 'Created At',
        cell: ({ row }) => {
            return new Date(row.getValue('created_at')).toLocaleString('en-EN', {
                day: 'numeric',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            })
        }
    },
    {
        accessorKey: 'default_short_url',
        header: 'Short URL',
    },
    {
        accessorKey: 'destination_url',
        header: 'Destination URL',
    },
    {
        accessorKey: 'visitors_count',
        header: 'Visitors',
        cell: ({ row }) => {
            return h('div', { class: 'text-right'}, row.getValue('visitors_count'))
        }
    }
]
</script>

<template>
<div>
    <PageTitle title="Dashboard" />
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
        <NuxtLink to="/dashboard/users">
            <UCard class="data-card interactive">
                <UIcon name="i-lucide-users-round" class="size-10 shrink-0" />
                <h4 class="font-medium mb-6">Users</h4>
                <p class="text-3xl lg:text-5xl text-(--ui-primary) font-semibold">{{ data?.users_count }}</p>
            </UCard>
        </NuxtLink>
        <NuxtLink to="/dashboard/urls">
            <UCard class="data-card interactive">
                <UIcon name="i-lucide-link" class="size-10 shrink-0" />
                <h4 class="font-medium mb-6">URLs</h4>
                <p class="text-3xl lg:text-5xl text-(--ui-primary) font-semibold">{{ data?.urls_count }}</p>
            </UCard>
        </NuxtLink>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 mt-8">
        <UCard class="lg:col-span-2 data-card">
            <h4 class="font-medium mb-6">Users : URLs</h4>
            <StatPieChart :data="data" :loading="status === 'pending'" />
        </UCard>
        <UCard class="lg:col-span-3 data-card">
            <h4 class="font-medium mb-6">Latest URLs</h4>
            <UTable
                ref="table"
                :data="data?.latest_urls"
                :columns="columns"
                :loading="status === 'pending'"
            >
                <template #default_short_url-cell="{ row }">
                    <div class="inline-flex items-center">
                        <a :href="row.original.default_short_url" target="_blank">
                            {{ row.original.default_short_url }}
                        </a>
                        <CopyToClipboardButton v-model="row.original.default_short_url" />
                    </div>
                </template>
                <template #destination_url-cell="{ row }">
                    <div class="text-left max-w-xs truncate">
                        <UTooltip :text="row.original.destination_url">
                            <a :href="row.original.destination_url" target="_blank">
                                {{ row.original.destination_url }}
                            </a>
                        </UTooltip>
                    </div>
                </template>
            </UTable>
        </UCard>
    </div>
</div>
</template>

<style scoped>
.data-card {
    @apply text-center bg-(--ui-bg-elevated)
}

.interactive {
    @apply hover:scale-105 transition-transform duration-200
}
</style>
