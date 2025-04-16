export default function useBasicToast() {
  const toast = useToast()

  function errorToast(title: string, description: string = 'Please try again') {
    toast.add({
      id: 'error_notification',
      title,
      description,
      icon: 'i-heroicons-x-circle-solid',
      color: 'error',
    })
  }

  function successToast(title: string, description?: string) {
    toast.add({
      id: 'success_notification',
      title,
      description,
      icon: 'i-heroicons-check-badge',
      color: 'success',
    })
  }

  return {
    errorToast,
    successToast,
  }
}
