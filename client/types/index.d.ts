import type {DropdownMenuItem} from "@nuxt/ui";

declare global {
  interface PaginationType {
    current_page: number
    from: number
    last_page: number
    path: string
    per_page: number
    to: number
    total: number
    links: {
      url?: string
      label?: string
      active: boolean
    }[]
  }

  // Collection with pagination
  interface Collection<T> {
    data: T[]
    meta: PaginationType
    links: {
      first?: string
      last?: string
      prev?: string
      next?: string
    }
  }

  // Collection without pagination (eg. countries)
  interface CollectionAll<T> {
    data: T[]
  }

  // Data for models
  interface Item<T> {
    data: T
  }

  interface User {
    readonly id: string
    name: string
    email: string
    role?: string | null
    created_at: string
    updated_at: string
  }

  interface Url {
      readonly id: string|number
      destination_url: string
      url_key: string
      default_short_url: string
      visitors_count?: number
      status: string
      user?: User
      created_at: string
      updated_at: string
  }

  interface Visitor {
      readonly id: string|number
      readonly short_url_id: string|number
      readonly browser: string
      readonly device_type: string
      readonly ip_address: string
      readonly operating_system: string
      readonly visited_at: string
      readonly created_at: string
      readonly updated_at: string
  }

  interface NavigationItem {
      name: string
      href: string
      icon: string
      current: string[]
  }

  interface DashboardData {
      users_count: number
      urls_count: number
      latest_urls: Url[]
      latest_updated_at: string
  }

  interface FilterDropdownMenuItem extends DropdownMenuItem {
      visible?: boolean
  }
}
