<template>
    <span class="text-sm">{{ data.value.roles || 'No Role' }}</span>
</template>

<script setup>
defineProps({
    data: {
        type: Object,
        required: true
    }
});
</script>