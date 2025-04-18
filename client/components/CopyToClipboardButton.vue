<script setup lang="ts">
import {useClipboard} from "@vueuse/core";

const text = defineModel<string>()

const { copy, isSupported, copied } = useClipboard()
const { successToast } = useBasicToast()

const onCopy = (text: string|undefined) => {
    copy(text ?? '')
    successToast('Copied!')
}
</script>

<template>
    <client-only>
        <UButton
            v-if="isSupported"
            :icon="copied ? 'i-lucide-copy-check' : 'i-lucide-copy'"
            variant="link"
            :color="copied ? 'success' : 'neutral'"
            size="sm"
            aria-label="Copy URL"
            @click.prevent="onCopy(text)"
        />
    </client-only>
</template>

<style scoped>

</style>
