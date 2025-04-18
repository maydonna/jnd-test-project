<script setup lang="ts">
interface Props {
    data: DashboardData|null
    loading: boolean
}

const props = defineProps<Props>()

const chart = ref()

const option = computed(() => ({
    tooltip: {
        show: true,
        trigger: 'item',
        formatter: '{b}: {c}',
    },
    legend: {
        show: false,
    },
    series: [
        {
            name: 'Total',
            type: 'pie',
            radius: ['100%'],
            data: [
                {
                    value: props.data?.users_count ?? 0,
                    name: 'Users',
                    itemStyle: { color: 'rgb(59 130 246)' },
                    emphasis: {
                        itemStyle: { color: 'rgb(59 130 246)' }
                    },
                },
                {
                    value: props.data?.urls_count ?? 0,
                    name: 'URLs',
                    itemStyle: { color: 'oklch(87.9% 0.169 91.605)' },
                    emphasis: {
                        itemStyle: { color: 'oklch(87.9% 0.169 91.605)' }
                    },
                },
            ],
        },
    ],
}))
</script>

<template>
    <client-only>
        <VChart
            ref="chart"
            :option="option"
            style="width: 100%;height: 300px;"
            :loading="loading"
            :autoresize="true"
        />
    </client-only>
</template>

<style scoped>

</style>
