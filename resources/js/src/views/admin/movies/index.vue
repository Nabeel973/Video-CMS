<template>
    <DataTableCrud
        title="Movies"
        singularTitle="Movies"
        endpoint="movies"
        tableId="moviesTable"
    />
</template>

<script setup>
import DataTableCrud from '@/components/DataTableCrud.vue';
</script> 