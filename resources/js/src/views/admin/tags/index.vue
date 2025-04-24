<template>
    <DataTableCrud
        title="Tags"
        singularTitle="Tag"
        endpoint="tags"
        tableId="tagTable"
    >
    <template #isActive="data">
        <span class="capitalize" :class="data.value.isActive ? 'text-success' : 'text-danger'">{{ data.value.isActive }}</span>
    </template>
    </DataTableCrud>
</template>


<script setup>
import DataTableCrud from '@/components/DataTableCrud.vue';
</script> 