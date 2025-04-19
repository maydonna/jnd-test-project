<script setup lang="ts">
import HeaderNavBar from "~/components/HeaderNavBar.vue";

const route = useRoute()
const adminPath = useAdminPath()

const sidebarNavigation = ref<NavigationItem[]>([
    {
        name: 'Dashboard',
        href: adminPath.get('dashboard'),
        icon: 'i-lucide-layout-dashboard',
        current: ['admin-dashboard'],
    },
    {
        name: 'URL management',
        href: adminPath.get('urls'),
        icon: 'i-lucide-link',
        current: ['admin-urls', 'admin-urls-[uid]'],
    },
    {
        name: 'User management',
        href: adminPath.get('users'),
        icon: 'i-lucide-users-round',
        current: ['admin-users', 'admin-users-[uid]', 'admin-users-create', 'admin-users-[uid]-edit'],
    },
])

const sidebarOpen = ref(false)
const selectedCurrent = ref(route?.name as string ?? '')

const checkPath = () => {
    selectedCurrent.value = route?.name as string ?? ''
}

const handlingToggleSideMenu = (toggle: boolean) => {
    sidebarOpen.value = toggle
}

watch(() => route.path, () => {
    checkPath()
})

onBeforeMount(() => {
    checkPath()
})
</script>

<template>
    <div>
        <div class="ease-in-out duration-300 transform transition-all min-h-screen flex flex-col lg:pl-64">
            <HeaderNavBar
                :is-dashboard="true"
                :sidebar-navigation="sidebarNavigation"
                :selected-current="selectedCurrent"
            />

            <div class="flex-grow flex flex-col h-full">
                <slot name="error" />
                <main class="flex-grow h-full py-10 mx-6 lg:mx-10">
                    <slot></slot>
                </main>
            </div>

        </div>

        <div class="hidden lg:w-64 lg:fixed lg:inset-y-0 lg:left-0 lg:z-50 lg:block h-screen ease-in-out duration-300 transform transition-all border-r border-gray-700 bg-(--ui-bg-elevated)">
            <div class="flex flex-col h-full">
                <div class="sticky top-0 px-4 py-6">
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
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
