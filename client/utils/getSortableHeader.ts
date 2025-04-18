// import {Column} from "@tanstack/table-core";

export default function <T>(name: string, column: Column<T>): {} {
    const isSorted = column.getIsSorted()

    return {
        color: 'neutral',
        variant: 'ghost',
        label: name,
        icon: isSorted
            ? isSorted === 'asc'
                ? 'i-lucide-arrow-up-narrow-wide'
                : 'i-lucide-arrow-down-wide-narrow'
            : 'i-lucide-arrow-up-down',
        class: '-mx-2.5',
    }
}
