<script setup lang="ts">
interface Props {
    label?: string
    content?: string|number|undefined
}

defineProps<Props>()
</script>

<template>
    <div v-bind="$attrs" class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
        <dt class="text-sm/6 font-medium text-white">
            {{ label }}
            <slot name="label"></slot>
        </dt>
        <dd class="mt-1 text-sm/6 text-gray-400 sm:col-span-2 sm:mt-0">
            {{ content }}
            <slot></slot>
        </dd>
    </div>
</template>

<style scoped>

</style>
