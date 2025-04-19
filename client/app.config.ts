export default defineAppConfig({
    ui: {
        input: {
            slots: {
                root: 'w-full'
            },
        },
        button: {
            slots: {
                base: 'hover:cursor-pointer'
            }
        },
        avatar: {
            slots: {
                root: 'bg-(--ui-primary)/30 stroke-(--ui-primary)',
                fallback: 'text-(--ui-neutral)'
            }
        },
        popover: {
            slots: {
                content: 'bg-(--ui-bg-elevated)'
            }
        },
    }
})
