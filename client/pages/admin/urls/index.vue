<script setup lang="ts">
import type {BreadcrumbItem, DropdownMenuItem, TableColumn, TableRow} from "@nuxt/ui";
import type {Column} from "@tanstack/table-core";
import CopyToClipboardButton from "~/components/CopyToClipboardButton.vue";

definePageMeta({
    middleware: ['sanctum:auth', 'admin-only'],
})

const route = useRoute()
const client = useSanctumClient()
const { errorToast, successToast } = useBasicToast()
const adminPath = useAdminPath()

const UButton = resolveComponent('UButton')

const breadcrumbItems = ref<BreadcrumbItem[]>([
    {
        label: 'Dashboard',
        icon: 'i-lucide-layout-dashboard',
        to: adminPath.get('dashboard')
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

const { data, status, refresh } = await useApiFetchCached<Collection<Url>>('/urls', {
    query: filters,
    watch: [filters]
})

const columns: TableColumn<Url>[] = [
    {
        accessorKey: 'created_at',
        header: ({ column }) => getHeader(column, 'created_at', 'Created At'),
        cell: ({ row }) => {
            return formatDateTime(row.getValue('created_at'))
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

const actionItems = (row: Url): DropdownMenuItem[][] => {
    const isActive = row.status === 'active'

    return [
        [{
            label: 'View',
            icon: 'i-lucide-eye',
            to: adminPath.get(`urls/${row.id}`)
        },
        {
            label: isActive ? 'Deactivate' : 'Activate',
            icon: isActive ? 'i-lucide-unlink' : 'i-lucide-link',
            color: isActive ? 'error' : 'info',
            onSelect: () => onToggleLink(row.id)
        }],
        [{
            label: 'Delete',
            icon: 'i-lucide-trash',
            color: 'error',
            onSelect: () => onDelete(row.id)
        }]
    ]
}

const onSorting = (column: Column<Url>, key: string) => {
    filters.sort_key = key
    filters.sort_order = column.getIsSorted() === 'desc' ? 'asc' : 'desc'

    column.toggleSorting(column.getIsSorted() === 'asc')
}

const onDelete = async (urlId: number|string) => {
    try {
        await client(`/urls/${urlId}`, {method: 'DELETE'})
        successToast('Successfully Deleted')
        await refresh()
    } catch (err) {
        errorToast(err.data.message)
    }
}

const onToggleLink = async (urlId: number|string) => {
    try {
        await client(`/urls/${urlId}`, {method: 'PATCH'})
        successToast('Successfully Updated')
        await refresh()
    } catch (err) {
        errorToast(err.data.message)
    }
}

const onClearFilters = () => {
    filters.status = undefined
    filters.keyword = undefined
}

const onSelect = async (row: TableRow<Url>) => {
    await navigateTo(adminPath.get(`urls/${row.original.id}`))
}
</script>

<template>
    <UBreadcrumb class="mb-6" :items="breadcrumbItems" />

    <UForm :state="filters" class="sm:flex items-center space-x-2 mb-4">
        <UFormField name="keyword" class="flex-1 max-w-sm mb-2 sm:mb-0">
            <UInput
                v-model="filters.keyword"
                placeholder="Search Destination URL"
                icon="i-lucide-search"
            />
        </UFormField>
        <UFormField name="status">
            <USelect v-model="filters.status" :items="['active', 'deactivated']" class="w-40" placeholder="Select status" />
        </UFormField>
        <UButton
            type="button"
            label="Clear"
            variant="outline"
            @click="onClearFilters"
        />
    </UForm>

    <UCard>
        <UTable
            ref="table"
            :loading="status === 'pending'"
            :columns="columns"
            :data="data?.data ?? []"
            sticky
            @select="onSelect"
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
                    <UDropdownMenu
                        :items="actionItems(row.original)"
                        :content="{
                          align: 'end',
                          side: 'bottom',
                          sideOffset: 8
                        }"
                        :ui="{
                          content: 'w-40'
                        }"
                    >
                        <UButton icon="i-lucide-ellipsis-vertical" color="neutral" variant="link" />
                    </UDropdownMenu>
                </div>
            </template>
        </UTable>
    </UCard>
    <div class="mt-4 mb-16 flex flex-col sm:flex-row items-center justify-between space-x-4 space-y-4">
        <UFormField name="per_page">
            <USelect v-model="filters.per_page" :items="[10, 20, 30, 40, 50, 100]" class="w-16" />
            <span class="ml-2 whitespace-nowrap">items per page</span>
        </UFormField>
        <UPagination
            v-model:page="filters.page"
            :items-per-page="data?.meta.per_page"
            :total="data?.meta.total"
        />
    </div>
</template>

<style scoped>

</style>
