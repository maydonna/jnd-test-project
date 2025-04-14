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

const handleLogin = async () => {
    form.value.clear()
    isLoggingIn.value = true

    try {
        await login(state)
        // await navigateTo(localePath('/'))
    }
    catch (err) {
        const error = handleApiError(err)
        if (error.isValidationError) {
            const errors = formatAPIErrors(err.data.errors)
            errors.push({
                message: err.data.errors['email'][0],
                path: 'password',
            })
            form.value.setErrors(errors)
        }

        errorToast(err.data.message)
        isLoggingIn.value = false
    }
}

const onSubmit = () => {
    console.log('login')
}
</script>

<template>
    <section class="w-full mt-16">
        <h1 class="text-xl lg:2xl text-primary font-semibold text-center mb-8">Login</h1>

        <UCard class="w-full max-w-xl mx-auto rounded-xl">
            <UForm :state="state" class="space-y-4" @submit="onSubmit">
                <UFormField label="Email" name="email">
                    <UInput
                        v-model="state.email"
                        placeholder="Enter your email"
                        icon="i-lucide-at-sign"
                        :ui="{root: 'w-full'}"
                    />
                </UFormField>

                <UFormField label="Password" name="password">
                    <UInput
                        v-model="state.password"
                        type="password"
                        placeholder="Enter your password"
                        icon="i-lucide-key-round"
                        :ui="{root: 'w-full'}"
                    />
                </UFormField>

                <div class="flex justify-between items-center space-x-4">
                    <UButton type="submit" size="md">
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
