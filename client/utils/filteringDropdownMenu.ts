import type {DropdownMenuItem} from "@nuxt/ui";

export default function (items: DropdownMenuItem[][]) {
    const filteredItems = items.map(group => group.filter((item: FilterDropdownMenuItem) => !Object.hasOwn(item, 'visible') || item.visible))
    return filteredItems.filter(group => group.length > 0)
}
