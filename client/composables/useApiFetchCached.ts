import type { UseFetchOptions } from 'nuxt/app'

export function useApiFetchCached<T>(
  url: string | (() => string),
  options?: UseFetchOptions<T>,
) {
  return useFetch(url, {
    ...options,
    $fetch: useSanctumClient(),
    /**
     * Get data from cached
     * But not working when manual trigger refresh or watch reactive url change
     * see https://github.com/nuxt/nuxt/issues/24332 for more information
     */
    getCachedData(key, nuxtApp) {
      // console.log(key)
      const data = nuxtApp.payload.data[key] || nuxtApp.static.data[key]
      // If data is not fetched yet
      if (!data) {
        // Fetch the first time
        return
      }
      // Return the cached data
      return data
    },
  })
}
