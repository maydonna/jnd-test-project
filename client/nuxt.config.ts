export default defineNuxtConfig({
    compatibilityDate: '2024-11-01',
    devtools: { enabled: true },
    vite: {
      server: {
          allowedHosts: [process.env.ALLOWED_HOSTS || 'localhost:3000']
      }
    }
})
