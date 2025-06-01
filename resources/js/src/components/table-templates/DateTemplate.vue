<template>
    <span class="text-sm">{{ formatDate(data.value.created_at) }}</span>
</template>

<script setup>
import { useExport } from '@/composables/useExport';

const { formatDate } = useExport();

defineProps({
    data: {
        type: Object,
        required: true
    }
});
</script>