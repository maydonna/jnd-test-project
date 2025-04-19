export default function (date: string) {
    return new Date(date).toLocaleString('en-EN', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    })
}
