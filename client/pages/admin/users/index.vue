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
        label: 'User management',
    },
])

const filters = reactive({
    page: parseInt(route.query?.page ?? 1),
    per_page: parseInt(route.query?.per_page ?? 10),
    sort_key: route.query?.sort_key || undefined,
    sort_order: route.query?.sort_order || undefined,
    keyword: route.query?.keyword || undefined,
})

const { data, status, refresh } = await useApiFetchCached<Collection<User>>('/users', {
    query: filters,
    watch: [filters]
})

const columns: TableColumn<User>[] = [
    {
        accessorKey: 'created_at',
        header: ({ column }) => getHeader(column, 'created_at', 'Created At'),
        cell: ({ row }) => {
            return formatDateTime(row.getValue('created_at'))
        },
    },
    {
        accessorKey: 'name',
        header: ({ column }) => getHeader(column, 'name', 'Name'),
    },
    {
        accessorKey: 'email',
        header: ({ column }) => getHeader(column, 'email', 'Email'),
    },
    {
        id: 'action',
        header: () => h('div', { class: 'text-center' }, '#'),
    }
]

const getHeader = (column: Column<User>, key: string, name: string) => {
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

const actionItems = (row: User): DropdownMenuItem[] => {
    return [
        {
            label: 'Delete',
            icon: 'i-lucide-trash',
            color: 'error',
            onSelect: () => onDelete(row.id)
        }
    ]
}

const onSorting = (column: Column<User>, key: string) => {
    filters.sort_key = key
    filters.sort_order = column.getIsSorted() === 'desc' ? 'asc' : 'desc'

    column.toggleSorting(column.getIsSorted() === 'asc')
}

const onDelete = async (userId: number|string) => {
    try {
        await client(`/users/${userId}`, {method: 'DELETE'})
        successToast('Successfully Deleted')
        await refresh()
    } catch (err) {
        errorToast(err.data.message)
    }
}

const onSelect = async (row: TableRow<User>) => {
    await navigateTo(adminPath.get(`users/${row.original.id}`))
}
</script>

<template>
    <UBreadcrumb class="mb-6" :items="breadcrumbItems" />

    <UForm :state="filters" class="sm:flex items-center space-x-2 mb-4">
        <UFormField name="keyword" class="flex-1 max-w-sm mb-2 sm:mb-0">
            <UInput
                v-model="filters.keyword"
                placeholder="Search Name or Email"
                icon="i-lucide-search"
            />
        </UFormField>
        <UButton
            type="button"
            label="Clear"
            variant="outline"
            @click="filters.keyword = undefined"
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
