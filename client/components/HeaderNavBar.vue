<script setup lang="ts">
interface Props {
    isDashboard?: boolean
    sidebarNavigation?: NavigationItem[]
    selectedCurrent?: string
}

withDefaults(defineProps<Props>(), {
    isDashboard: false,
    sidebarNavigation: [],
    selectedCurrent: ''
})

const route = useRoute()
const user = useSanctumUser<Item<User>>()
const {logout, refreshIdentity} = useSanctumAuth()

const openPopover = ref(false)
const openSlideMenu = ref(false)

const isGuestOnly = computed(() => ['login', 'register'].indexOf(route?.name) === -1)
const isAdmin = computed(() => user.value?.data.role === 'admin')
const isLoggedIn = computed(() => !!user.value)

const closePopover = () => {
    openPopover.value = false
}

const closeSlideMenu = () => {
    openSlideMenu.value = false
}

const handlingLogout = async () => {
    closePopover()
    closeSlideMenu()
    await logout()
    await refreshIdentity()
}
</script>

<template>
    <header class="bg-(--ui-bg)" :class="isDashboard ? 'border-b border-gray-700 shadow-md' : ''">
        <nav class="mx-auto flex max-w-7xl items-center justify-between lg:px-8" :class="isDashboard ? 'p-2' : 'p-6'">
            <h1 v-if="!isDashboard" class="text-xl lg:2xl text-primary font-semibold">
                <NuxtLink to="/" class="-m-1.5 p-1.5">
                    JND Test Project
                </NuxtLink>
            </h1>
            <div v-else class="lg:hidden flex items-center space-x-4">
                <USlideover class="flex lg:hidden" title="JND Test Project" side="left">
                    <UButton
                        color="neutral"
                        variant="subtle"
                        icon="i-lucide-align-justify"
                        aria-label="Toggle side menu"
                        @click=""
                    />

                    <template #body>
                        <nav class="flex-1 overflow-y-auto pt-5">
                            <ul
                                class="flex flex-col justify-center items-center gap-y-4 transition-all"
                                role="list"
                            >
                                <li
                                    v-for="item in sidebarNavigation"
                                    :key="item.name"
                                    class="w-full transition-all duration-300 no-scrollbar"
                                >
                                    <UButton variant="link" :color="item.current.includes(selectedCurrent) ? 'primary' : 'neutral'" :to="item.href">
                                    <span class="flex grow min-w-1 items-center gap-x-3 whitespace-nowrap">
                                      <UIcon
                                          :name="item.icon"
                                          class="h-6 w-6 shrink-0"
                                          aria-hidden="true"
                                      />
                                      <span class="truncate">{{ item.name }}</span>
                                    </span>
                                    </UButton>
                                </li>
                            </ul>
                        </nav>
                    </template>
                </USlideover>
            </div>
            <div v-if="isGuestOnly">
                <USlideover
                    v-model:open="openSlideMenu"
                    class="flex lg:hidden"
                    title="JND Test Project"
                >
                    <UButton color="neutral" variant="link">
                        <UAvatar :alt="user?.data.name" size="xl" />
                        <span class="ml-2">{{ user?.data.name }}</span>
                        <UIcon class="hidden lg:flex" name="i-lucide-chevron-down" />
                        <UIcon class="lg:hidden" name="i-lucide-chevron-left" />
                    </UButton>

                    <template #body>
                        <div class="flex flex-col space-y-4">
                            <UButton to="/" variant="link" color="neutral" @click="closeSlideMenu">Home</UButton>
                            <UButton v-if="isLoggedIn && isAdmin" to="/dashboard" variant="link" color="neutral" @click="closeSlideMenu">Dashboard</UButton>
                            <UButton v-if="isLoggedIn" variant="link" color="neutral" trailing-icon="i-lucide-arrow-right" @click="handlingLogout">Sign out</UButton>
                            <UButton v-else to="/login" trailing-icon="i-lucide-arrow-right" variant="link" @click="closeSlideMenu">Sign in</UButton>
                        </div>
                    </template>
                </USlideover>
            </div>

            <div class="hidden lg:flex lg:gap-x-12">
                <div v-if="user">
                    <UPopover
                        v-model:open="openPopover"
                        :content="{
                          align: 'end',
                          side: 'bottom',
                          sideOffset: 8
                        }"
                        arrow
                    >
                        <UButton color="neutral" variant="link">
                            <UAvatar :alt="user?.data.name" size="xl" />
                            <span class="ml-2">{{ user?.data.name }}</span>
                            <UIcon name="i-lucide-chevron-down" />
                        </UButton>

                        <template #content>
                            <div class="flex flex-col space-y-4 min-w-3xs p-4">
                                <UButton v-if="isDashboard" to="/" variant="link" color="neutral" @click="closePopover">Home</UButton>
                                <UButton v-if="isAdmin" to="/dashboard" variant="link" color="neutral" @click="closePopover">Dashboard</UButton>
                                <UButton variant="link" color="neutral" trailing-icon="i-lucide-arrow-right" @click="handlingLogout">Sign out</UButton>
                            </div>
                        </template>
                    </UPopover>
                </div>
                <UButton v-else-if="isGuestOnly" to="/login" trailing-icon="i-lucide-arrow-right">Sign in</UButton>
            </div>
        </nav>
    </header>
</template>

<style scoped>

</style>
