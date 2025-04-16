<script setup lang="ts">
definePageMeta({
    middleware: ['sanctum:auth'],
});

const user = useSanctumUser<Item<User>>()
console.log(user)
const { logout, refreshIdentity } = useSanctumAuth()

const isLoggingOut = ref(false)

const handleLogout = async () => {
    await logout()
    await refreshIdentity()
}

watch(() => user.value, async () => {
    if(!user.value?.data.id) {
        await navigateTo('/login')
    }
})
</script>

<template>
    <h1 class="text-3xl font-bold">
        Hello world!, {{ user?.data.name }}
    </h1>

    <UButton size="md" :loading="isLoggingOut" @click="handleLogout">
        Logout
    </UButton>
</template>

<style scoped>

</style>
