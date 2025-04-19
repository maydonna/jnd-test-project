export default defineNuxtConfig({
    compatibilityDate: '2024-11-01',
    devtools: { enabled: true },

    css: ['~/assets/css/main.css'],

    modules: ['nuxt-auth-sanctum', '@nuxt/ui', 'nuxt-echarts'],

    sanctum: {
        baseUrl: 'http://localhost:8000/api', // Laravel API
        endpoints: {
            user: '/user/me'
        }
    },

    echarts: {
        charts: ['PieChart'],
        components: ['TooltipComponent', 'LegendComponent']
    }
})
