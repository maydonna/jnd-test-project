<script setup lang="ts">
definePageMeta({
    middleware: ['sanctum:guest'],
})

const {successToast, errorToast} = useBasicToast()
const client = useSanctumClient()

const state = reactive({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})
const form = ref()
const showPassword = ref(false)

const isAtLeast8 = computed(() => state.password.length >= 8)

const handleRegister = async () => {
    form.value.clear()
    try {
        await client('register', {
            method: 'POST',
            body: state,
        })

        successToast('Register Successfully', 'Please Login with your registered email and password')
        await navigateTo('/login')
    } catch (err) {
        const error = handleApiError(err)

        if (error.isValidationError) {
            form.value.setErrors(formatAPIErrors(error.payload.errors))
        }

        errorToast(err.data.message)
    }
}
</script>

<template>
    <section class="w-full mt-16">
        <h1 class="text-xl lg:2xl text-primary font-semibold text-center mb-8">Register</h1>

        <UCard class="w-full max-w-xl mx-auto rounded-xl">
            <UForm ref="form" :state="state" class="space-y-4" @submit="handleRegister" @keydown.enter="handleRegister">
                <UFormField label="Your Name" name="name" required>
                    <UInput
                        v-model="state.name"
                        placeholder="Enter your name"
                    />
                </UFormField>
                <UFormField label="Email" name="email" required>
                    <UInput
                        v-model="state.email"
                        type="email"
                        autocomplete="email"
                        placeholder="Enter your email"
                    />
                </UFormField>
                <UFormField label="Password" name="password" required>
                    <UInput
                        v-model="state.password"
                        placeholder="Enter your password"
                        :type="showPassword ? 'text' : 'password'"
                        :ui="{ trailing: 'pe-1' }"
                        :aria-invalid="!isAtLeast8"
                        :color="!isAtLeast8 ? 'error' : 'success'"
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
                    <template #help>
                        <span
                            class="flex items-center gap-0.5 text-xs font-light"
                            :class="isAtLeast8 ? 'text-(--ui-success)' : 'text-(--ui-text-muted)'"
                        >
                            <UIcon
                                :name="isAtLeast8 ? 'i-lucide-circle-check' : 'i-lucide-circle-x'"
                                class="size-4 shrink-0"
                            />
                            At least 8 characters
                            <span class="sr-only">
                                {{ isAtLeast8 ? ' - Requirement met' : ' - Requirement not met' }}
                            </span>
                        </span>
                    </template>
                </UFormField>
                <UFormField label="Confirm Password" name="password_confirmation" required>
                    <UInput
                        v-model="state.password_confirmation"
                        type="password"
                        placeholder="Confirm your password"
                    />
                </UFormField>

                <div class="flex justify-between items-center space-x-4">
                    <UButton type="submit" size="md" loading-auto>
                        Register
                    </UButton>
                    <div class="inline-flex items-center">
                        <span>Have account already?</span>
                        <UButton color="primary" variant="link" to="/login">Login</UButton>
                    </div>
                </div>
            </UForm>
        </UCard>
    </section>
</template>

<style scoped>

</style>
