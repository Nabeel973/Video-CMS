<template>
    <span class="text-sm">{{ data.value.email }}</span>
</template>

<script setup>
defineProps({
    data: {
        type: Object,
        required: true
    }
});
</script>