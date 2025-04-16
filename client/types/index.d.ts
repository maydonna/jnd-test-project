import type {TestimonialType, EducationLevel, EducationStatus, EmploymentType, MemberStatus, MemberRejectType, EmploymentWageUnit, JobPosition, JobStatus} from '~/types/enum'


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
}
