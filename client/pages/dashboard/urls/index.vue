<script setup lang="ts">
import type {BreadcrumbItem, TableColumn} from "@nuxt/ui";
import type {Column} from "@tanstack/table-core";
import CopyToClipboardButton from "~/components/CopyToClipboardButton.vue";

definePageMeta({
    middleware: ['sanctum:auth', 'admin-only'],
})

const route = useRoute()
const client = useSanctumClient()
const { errorToast, successToast } = useBasicToast()
const UButton = resolveComponent('UButton')

const breadcrumbItems = ref<BreadcrumbItem[]>([
    {
        label: 'Dashboard',
        icon: 'i-lucide-layout-dashboard',
        to: '/dashboard'
    },
    {
        label: 'URL management',
    },
])

const filters = reactive({
    page: parseInt(route.query?.page ?? 1),
    per_page: parseInt(route.query?.per_page ?? 10),
    sort_key: route.query?.sort_key || undefined,
    sort_order: route.query?.sort_order || undefined,
    keyword: route.query?.keyword || undefined,
    status: route.query?.status || undefined
})

// const sorting: ColumnSort[] = computed(() => [{id: filters.sort_key ?? 'created_at', desc: filters.sort_order === 'desc'}])

const { data, status, refresh } = await useApiFetchCached<Collection<Url>>('/urls', {
    query: filters,
    watch: [filters]
})

const getHeader = (column: Column<Url>, key: string, name: string) => {
    const isSorted = column.getIsSorted()

    return h(UButton, {
        color: 'neutral',
        variant: 'ghost',
        label: name,
        icon: isSorted
            ? isSorted === 'asc'
                ? 'i-lucide-arrow-up-narrow-wide'
                : 'i-lucide-arrow-down-wide-narrow'
            : 'i-lucide-arrow-up-down',
        class: '-mx-2.5',
        onClick: () => {
            onSorting(column, key)
        }
    })
}

const columns: TableColumn<Url>[] = [
    {
        accessorKey: 'created_at',
        header: ({ column }) => getHeader(column, 'created_at', 'Created At'),
        cell: ({ row }) => {
            return new Date(row.getValue('created_at')).toLocaleString('en-EN', {
                day: 'numeric',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            })
        },
    },
    {
        accessorKey: 'default_short_url',
        header: ({ column }) => getHeader(column, 'default_short_url', 'Short URL'),
    },
    {
        accessorKey: 'destination_url',
        header: ({ column }) => getHeader(column, 'destination_url', 'Destination URL'),
    },
    {
        accessorKey: 'visitors_count',
        header: ({ column }) => h('div', { class: 'text-right' }, getHeader(column, 'visitors_count', 'Visitors')),
        cell: ({ row }) => {
            return h('div', { class: 'text-right'}, row.getValue('visitors_count'))
        },
    },
    {
        id: 'status',
        header: () => h('div', { class: 'text-center' }, 'Status'),
    },
    {
        id: 'action',
        header: () => h('div', { class: 'text-center' }, '#'),
    }
]

const onSorting = (column: Column<Url>, key: string) => {
    filters.sort_key = key
    filters.sort_order = column.getIsSorted() === 'desc' ? 'asc' : 'desc'

    column.toggleSorting(column.getIsSorted() === 'asc')
}

const onDelete = async (urlId: number) => {
    try {
        await client(`/urls/${urlId}`, {method: 'DELETE'})
        successToast('Successfully Deleted')
        await refresh()
    } catch (err) {
        errorToast(err.data.message)
    }
}
</script>

<template>
    <UBreadcrumb class="mb-4" :items="breadcrumbItems" />

    <UTable
        ref="table"
        :loading="status === 'pending'"
        :columns="columns"
        :data="data?.data ?? []"
        sticky
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
            <div class="text-left max-w-3xs truncate">
                <UTooltip :text="row.original.destination_url">
                    <a :href="row.original.destination_url" target="_blank">
                        {{ row.original.destination_url }}
                    </a>
                </UTooltip>
            </div>
        </template>
        <template #status-cell="{ row }">
            <div class="text-center">
                <UBadge variant="subtle" :color="row.original.status === 'active' ? 'success' : 'error'">{{ row.original.status }}</UBadge>
            </div>
        </template>
        <template #action-cell="{ row }">
            <div class="text-center">
                <UPopover
                    :content="{
                          align: 'end',
                          side: 'bottom',
                          sideOffset: 8
                        }"
                    arrow
                >
                    <UButton icon="i-lucide-ellipsis-vertical" color="neutral" variant="link" />

                    <template #content>
                        <div class="flex flex-col space-y-2 min-w-3xs p-2">
                            <UButton
                                variant="link"
                                color="neutral"
                                icon="i-lucide-eye"
                                :to="`/dashboard/urls/${row.original.id}`"
                            >
                                View
                            </UButton>
                            <UButton
                                variant="link"
                                color="neutral"
                                icon="i-lucide-pencil"
                                :to="`/dashboard/urls/${row.original.id}`"
                            >
                                Edit
                            </UButton>
                            <UButton
                                variant="link"
                                color="error"
                                icon="i-lucide-trash"
                                @click="onDelete(row.original.id)"
                            >
                                Delete
                            </UButton>
                        </div>
                    </template>
                </UPopover>
            </div>
        </template>
    </UTable>
    <div class="mt-4 mb-16 flex justify-end">
        <UPagination
            v-model:page="filters.page"
            :items-per-page="data?.meta.per_page"
            :total="data?.meta.total"
        />
    </div>
</template>

<style scoped>

</style>
