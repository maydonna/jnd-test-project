<script setup lang="ts">
const user = useSanctumUser<Item<User>>()
const {logout, refreshIdentity} = useSanctumAuth()
const route = useRoute()

const openPopover = ref(false)
const openSlideMenu = ref(false)

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
    <header class="bg-(--ui-bg)">
        <nav class="mx-auto flex max-w-7xl items-center justify-between p-6 lg:px-8" aria-label="Global">
            <h1 class="text-xl lg:2xl text-primary font-semibold">
                <a href="/" class="-m-1.5 p-1.5">JND Test Project</a>
            </h1>
            <div v-if="['login', 'register'].indexOf(route?.name) === -1">
                <USlideover
                    v-model:open="openSlideMenu"
                    class="flex lg:hidden"
                    title="JND Test Project"
                >
                    <UButton label="Open" color="neutral" variant="subtle">
                        <span class="sr-only">Open main menu</span>
                        <UIcon name="i-lucide-align-justify" class="size-5" />
                    </UButton>

                    <template #body>
                        <div class="flex flex-col space-y-4">
                            <UButton to="/" variant="link" color="neutral" @click="closeSlideMenu">Home</UButton>
                            <UButton v-if="user?.data.id && user?.data.role === 'admin'" to="/dashboard" variant="link" color="neutral" @click="closeSlideMenu">Dashboard</UButton>
                            <UButton v-if="user?.data.id" variant="link" color="neutral" trailing-icon="i-lucide-arrow-right" @click="handlingLogout">Sign out</UButton>
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
                        </UButton>

                        <template #content>
                            <div class="flex flex-col space-y-4 min-w-3xs p-4">
                                <UButton v-if="user?.data.role === 'admin'" to="/dashboard" variant="link" color="neutral" @click="closePopover">Dashboard</UButton>
                                <UButton variant="link" color="neutral" trailing-icon="i-lucide-arrow-right" @click="handlingLogout">Sign out</UButton>
                            </div>
                        </template>
                    </UPopover>
                </div>
                <UButton v-else-if="['login', 'register'].indexOf(route?.name) === -1" to="/login" trailing-icon="i-lucide-arrow-right">Sign in</UButton>
            </div>
        </nav>
    </header>
    <UContainer class="mt-8">
        <slot></slot>
    </UContainer>
</template>

<style scoped>

</style>
