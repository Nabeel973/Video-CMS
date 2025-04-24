<template>
    <DataTableCrud
        title="Releases"
        singularTitle="Release"
        endpoint="releases"
        tableId="releaseTable"
    />
</template>

<script setup>
import DataTableCrud from '@/components/DataTableCrud.vue';
</script> 