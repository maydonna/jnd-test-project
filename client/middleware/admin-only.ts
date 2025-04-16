export default defineNuxtRouteMiddleware(async () => {
  const user = useSanctumUser<Item<User>>()
    return user.value?.data.role === 'admin';
})
