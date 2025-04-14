export default function (errors): FormError[] {
  const formatErrors = []
  Object.keys(errors).forEach((key) => {
    formatErrors.push({
      message: errors[key][0],
      path: key,
    })
  })
  return formatErrors ?? []
}
