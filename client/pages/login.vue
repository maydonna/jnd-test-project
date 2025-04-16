<script setup lang="ts">
definePageMeta({
    middleware: ['sanctum:guest'],
})

const { login, refreshIdentity } = useSanctumAuth()
const { errorToast } = useBasicToast()

const user = useSanctumUser()

const state = reactive({
    email: '',
    password: '',
    remember: false,
})
const form = ref()
const isLoggingIn = ref(false)
const showPassword = ref(false)

const handleLogin = async () => {
    form.value.clear()
    isLoggingIn.value = true

    try {
        await login(state)
        await navigateTo('/')
    }
    catch (err) {
        const error = handleApiError(err)
        if (error.isValidationError) {
            const errors = formatAPIErrors(err.data.errors)
            errors.push({
                message: err.data.errors['email'][0],
                name: 'password',
            })
            form.value.setErrors(errors)
        }

        errorToast(err.data.message)
        isLoggingIn.value = false
    }
}

onMounted(async () => {
    try {
        await refreshIdentity()
        if (user.value) {
            await navigateTo('/')
        }
    } catch (err) {
        console.error(err)
    }
})
</script>

<template>
    <section class="w-full mt-16">
        <h1 class="text-xl lg:2xl text-primary font-semibold text-center mb-8">Login</h1>

        <UCard class="w-full max-w-xl mx-auto rounded-xl">
            <UForm ref="form" :state="state" class="space-y-4" @submit="handleLogin" @keydown.enter="handleLogin">
                <UFormField label="Email" name="email">
                    <UInput
                        v-model="state.email"
                        type="email"
                        autocomplete="email"
                        placeholder="Enter your email"
                        icon="i-lucide-at-sign"
                        :ui="{root: 'w-full'}"
                    />
                </UFormField>

                <UFormField label="Password" name="password">
                    <UInput
                        v-model="state.password"
                        :type="showPassword ? 'text' : 'password'"
                        autocomplete="current-password"
                        placeholder="Enter your password"
                        icon="i-lucide-key-round"
                        :ui="{root: 'w-full', trailing: 'pe-1'}"
                    >
                        <template #trailing>
                            <UButton
                                color="neutral"
                                variant="link"
                                size="sm"
                                :icon="showPassword ? 'i-lucide-eye-off' : 'i-lucide-eye'"
                                :aria-label="showPassword ? 'Hide password' : 'Show password'"
                                :aria-pressed="showPassword"
                                aria-controls="password"
                                @click="showPassword = !showPassword"
                            />
                        </template>
                    </UInput>
                </UFormField>

                <div class="flex justify-between items-center space-x-4">
                    <UButton type="submit" size="md" :loading="isLoggingIn">
                        Login
                    </UButton>
                    <div class="inline-flex items-center">
                        <span>Don't have account?</span>
                        <UButton color="primary" variant="link" to="/register">Register</UButton>
                    </div>
                </div>
            </UForm>
        </UCard>
    </section>
</template>

<style scoped>

</style>
