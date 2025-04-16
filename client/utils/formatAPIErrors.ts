import type { FormError } from '@nuxt/ui'
export default function (errors): FormError[] {
  const formatErrors = []
  Object.keys(errors).forEach((key) => {
    formatErrors.push({
        message: errors[key][0],
        name: key,
    })
  })
  return formatErrors ?? []
}
