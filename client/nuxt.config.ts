export default defineNuxtConfig({
    compatibilityDate: '2024-11-01',
    devtools: { enabled: true },

    css: ['~/assets/css/main.css'],

    modules: [
        'nuxt-auth-sanctum',
        '@nuxt/ui'
    ],

    sanctum: {
        baseUrl: 'http://localhost:8000', // Laravel API
    },
})
