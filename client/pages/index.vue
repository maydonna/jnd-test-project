<script setup lang="ts">
import { useClipboard } from "@vueuse/core";
import type { TableColumn } from '@nuxt/ui'

const user = useSanctumUser<Item<User>>()
const client = useSanctumClient()
const { successToast, errorToast } = useBasicToast()
const { copy, isSupported } = useClipboard()
const route = useRoute()

const state = reactive({
    url: ''
})

const form = ref()
const latestShortUrl = ref<string>('')
const query = reactive({
    user_id: user.value?.data.id ?? 0,
    page: parseInt(route.query?.page ?? 1)
})
const { data, refresh, status } = await useApiFetchCached<Collection<Url>>('/urls', {
    query: query,
    watch: [query]
})

const userUrlList = computed(() => data.value?.data ?? [])

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
    },
    {
        id: 'action',
        header: () => h('div', { class: 'text-center' }, '#'),
    }
]

const onSubmit = async () => {
    //check if logged in
    if(user.value) {
        form.value.clear()
        latestShortUrl.value = ''
        try {
            const response = await client('urls', {
                method: 'POST',
                body: state,
            })
            latestShortUrl.value = response.short_url
            successToast('Successfully shorten your URL!')
            await refresh()
        } catch (err) {
            const error = handleApiError(err)

            if (error.isValidationError) {
                form.value.setErrors(formatAPIErrors(error.payload.errors))
            }

            errorToast(err.data.message)
        }
    } else {
        await navigateTo('/login')
    }
}

const onCopy = (url: string) => {
    copy(url)
    successToast('Copied!')
}

const onDelete = async (urlId: string) => {
    try {
        await client(`/urls/${urlId}`, {method: 'DELETE'})
        await refresh()
    } catch (err) {
        errorToast(err.data.message)
    }
}
</script>

<template>
    <section class="mx-auto max-w-5xl sm:px-6 lg:px-8">
        <div class="relative isolate overflow-hidden px-6 py-10 sm:px-24 xl:py-14">
            <h1 class="mx-auto max-w-3xl text-center text-4xl font-semibold tracking-tight text-white sm:text-5xl">Shorten a long link</h1>
            <p class="mx-auto mt-6 max-w-lg text-center text-lg text-gray-300">Get your link shorten for free</p>

            <UForm ref="form" :state="state" class="space-y-4 mt-12" @submit="onSubmit" @keydown.enter="onSubmit">
                <UFormField name="url" size="xl">
                    <UInput
                        v-model="state.url"
                        placeholder="https://example.com/your-long-destination-url"
                        icon="i-lucide-link"
                        :ui="{ trailing: 'pe-1' }"
                    >
                        <template v-if="state.url?.length" #trailing>
                            <UButton
                                color="neutral"
                                variant="link"
                                size="sm"
                                icon="i-lucide-x"
                                aria-label="Clear input"
                                @click="state.url = ''"
                            />
                        </template>
                    </UInput>
                </UFormField>
                <div class="flex justify-center">
                    <UButton type="submit" size="xl" class="px-10" loading-auto>
                        Submit
                    </UButton>
                </div>
            </UForm>
        </div>

        <div v-if="user && userUrlList.length > 0">
            <UCard>
                <h2 class="flex justify-center text-2xl font-semibold mb-4 text-(--ui-primary)">Your Short Urls</h2>
                <UTable
                    ref="table"
                    :data="userUrlList"
                    :columns="columns"
                    :loading="status === 'pending'"
                >
                    <template #default_short_url-cell="{ row }">
                        <div class="inline-flex items-center">
                            <a :href="row.original.default_short_url" target="_blank">
                                {{ row.original.default_short_url }}
                            </a>
                            <UButton
                                v-if="isSupported"
                                icon="i-lucide-copy"
                                variant="link"
                                color="neutral"
                                size="sm"
                                aria-label="Copy URL"
                                @click.prevent="onCopy(row.original.default_short_url)"
                            />
                        </div>
                    </template>
                    <template #destination_url-cell="{ row }">
                        <div class="max-w-xs truncate">
                            <UTooltip :text="row.original.destination_url">
                                <a :href="row.original.destination_url" target="_blank">
                                    {{ row.original.destination_url }}
                                </a>
                            </UTooltip>
                        </div>
                    </template>
                    <template #action-cell="{ row }">
                        <UButton
                            icon="i-lucide-x"
                            variant="subtle"
                            color="error"
                            size="md"
                            aria-label="Delete URL"
                            @click.prevent="onDelete(row.original.id)"
                        />
                    </template>
                </UTable>
            </UCard>
            <div class="mt-4 mb-16 flex justify-center">
                <UPagination
                    v-model:page="query.page"
                    :items-per-page="data?.meta.per_page"
                    :total="data?.meta.total"
                />
            </div>
        </div>

    </section>
</template>

<style scoped>

</style>
