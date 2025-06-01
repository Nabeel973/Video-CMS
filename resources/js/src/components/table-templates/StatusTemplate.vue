<template>
    <span class="badge" :class="data.value.status === 'active' ? 'badge badge-outline-success' : 'badge badge-outline-danger'">
        {{ data.value.status.toUpperCase() }}
    </span>
</template>

<script setup>
defineProps({
    data: {
        type: Object,
        required: true
    }
});
</script>