<script setup lang="ts">
import type {BreadcrumbItem, TableColumn} from "@nuxt/ui";
import type {Column} from "@tanstack/table-core";
import {capitalize} from "vue";

definePageMeta({
    middleware: ['sanctum:auth', 'admin-only'],
})

const route = useRoute()
const client = useSanctumClient()
const { errorToast, successToast } = useBasicToast()
const adminPath = useAdminPath()

const UButton = resolveComponent('UButton')
const isUpdating = ref(false)

const breadcrumbItems = ref<BreadcrumbItem[]>([
    {
        label: 'Dashboard',
        icon: 'i-lucide-layout-dashboard',
        to: adminPath.get('dashboard')
    },
    {
        label: 'URL management',
        to: adminPath.get('urls')
    },
    {
        label: 'Edit URL',
    },
])

const filters = reactive({
    page: 1,
    per_page: 10,
    sort_key: 'visited_at',
    sort_order: 'desc',
    keyword: undefined,
})

const columns: TableColumn<Visitor>[] = [
    {
        accessorKey: 'visited_at',
        header: ({ column }) => getHeader(column, 'visited_at', 'Visited At'),
        cell: ({ row }) => {
            return formatDateTime(row.getValue('visited_at'))
        },
    },
    {
        accessorKey: 'ip_address',
        header: 'IP Address',
    },
    {
        accessorKey: 'browser',
        header: 'Browser',
    },
    {
        accessorKey: 'browser',
        header: 'Browser',
    },
    {
        accessorKey: 'operating_system',
        header: 'Operating System',
    },
    {
        accessorKey: 'device_type',
        header: 'Device Type',
    },
]

const { data, refresh } = await useApiFetchCached<Item<Url>>(`/urls/${route.params.uid}`)
const { data: visitors, status: visitorStatus } = await useApiFetchCached<Collection<Visitor>>(`/urls/${route.params.uid}/visitors`, {
    query: filters,
    watch: [filters]
})

const urlData = computed(() => data.value?.data)
const shortURL = computed(() => urlData.value?.default_short_url ?? '')
const urlActive = computed(() => urlData.value?.status === 'active')

const getHeader = (column: Column<Visitor>, key: string, name: string) => {
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

const onSorting = (column: Column<Visitor>, key: string) => {
    filters.sort_key = key
    filters.sort_order = column.getIsSorted() === 'desc' ? 'asc' : 'desc'

    column.toggleSorting(column.getIsSorted() === 'asc')
}

const onToggleLink = async () => {
    try {
        isUpdating.value = true
        await client(`/urls/${route.params.uid}`, {method: 'PATCH'})
        successToast('Successfully Updated')
        await refresh()
    } catch (err) {
        errorToast(err.data.message)
    } finally {
        isUpdating.value = false
    }
}

const onDelete = async () => {
    try {
        await client(`/urls/${route.params.uid}`, {method: 'DELETE'})
        await navigateTo('/admin/urls')
    } catch (err) {
        errorToast(err.data.message)
    }
}
</script>

<template>
    <div class="sm:flex justify-between items-center sm:space-x-8 mb-6">
        <UBreadcrumb :items="breadcrumbItems" />
        <div class="flex justify-end items-center space-x-4 mt-4 sm:mt-0">
            <UButton
                label="Delete"
                icon="i-lucide-trash"
                color="error"
                variant="subtle"
                @click="onDelete"
            />
            <UBadge :label="capitalize(urlData?.status ?? '')" :color="urlActive ? 'success' : 'error'" size="xl" variant="soft" />
            <USwitch
                :model-value="urlActive"
                size="lg"
                :loading="isUpdating"
                @change="onToggleLink"
            />
        </div>
    </div>

    <UCard>
        <DescriptionListWrapper class="grid grid-cols-1 md:grid-cols-2 gap-x-8">
            <DescriptionListItem label="Short URL">
                <div class="inline-flex space-x-2">
                    <a :href="urlData?.default_short_url" target="_blank">
                        {{ urlData?.default_short_url }}
                    </a>
                    <CopyToClipboardButton v-model="shortURL" />
                </div>
            </DescriptionListItem>
            <DescriptionListItem label="Destination URL">
                <UTooltip :text="urlData?.destination_url">
                    <div class="truncate">
                        <a :href="urlData?.destination_url" target="_blank">
                            {{ urlData?.destination_url }}
                        </a>
                    </div>
                </UTooltip>
            </DescriptionListItem>
            <DescriptionListItem label="URL Key" :content="urlData?.url_key" />
            <DescriptionListItem label="Created By">
                <NuxtLink :to="`/admin/users/${urlData?.user?.id}`" class="hover:underline inline-flex items-center">
                    <span>{{ `${urlData?.user?.name} (${urlData?.user?.email})` }}</span>
                    <UIcon name="i-lucide-external-link" class="size-sm shrink-0 ml-2" />
                </NuxtLink>
            </DescriptionListItem>
            <DescriptionListItem label="Created At" :content="formatDateTime(urlData?.created_at)" />
            <DescriptionListItem label="Updated At" :content="formatDateTime(urlData?.updated_at)" />
        </DescriptionListWrapper>
    </UCard>
    <UCard class="mt-6">
        <div class="flex justify-between items-baseline space-x-8">
            <h4 class="font-medium">Visitors</h4>
            <span class="text-(--ui-text-muted)">{{ `Total visitors: ${visitors?.meta.total}` }}</span>
        </div>

        <UForm :state="filters" class="sm:flex items-center space-x-2 mb-4 mt-6">
            <UFormField name="keyword" class="flex-1 mb-2 sm:mb-0">
                <UInput
                    v-model="filters.keyword"
                    placeholder="Search IP Address, Browser, Operating System, or Device Type ..."
                    icon="i-lucide-search"
                    :ui="{ trailing: 'pe-1' }"
                >
                    <template v-if="filters.keyword?.length" #trailing>
                        <UButton
                            color="neutral"
                            variant="link"
                            size="sm"
                            icon="i-lucide-x"
                            aria-label="Clear input"
                            @click="filters.keyword = undefined"
                        />
                    </template>
                </UInput>
            </UFormField>
        </UForm>
        <UTable
            ref="table"
            :loading="visitorStatus === 'pending'"
            :columns="columns"
            :data="visitors?.data ?? []"
            sticky
        />
        <div class="mt-4 flex justify-end">
            <UPagination
                v-model:page="filters.page"
                :items-per-page="visitors?.meta.per_page"
                :total="visitors?.meta.total"
            />
        </div>
    </UCard>
</template>

<style scoped>

</style>
