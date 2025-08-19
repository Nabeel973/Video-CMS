<template>
    <!-- {{ data.value.created_at }} -->
    <span class="text-sm">{{ data.value.created_at }}</span>
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