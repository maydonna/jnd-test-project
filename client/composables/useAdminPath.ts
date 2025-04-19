export default function useAdminPath() {

    function get(path: string): string {
        return `/admin/${path}`
    }

    return { get }
}
