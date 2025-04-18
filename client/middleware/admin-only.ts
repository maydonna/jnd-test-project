export default defineNuxtRouteMiddleware(async () => {
    setPageLayout('admin-dashboard')
    const user = useSanctumUser<Item<User>>()
    return user.value?.data.role === 'admin'
})
