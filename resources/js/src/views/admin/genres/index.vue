<template>
    <DataTableCrud
        title="Genres"
        singularTitle="Genre"
        endpoint="genres"
        tableId="genreTable"
    />
</template>

<script setup>
import DataTableCrud from '@/components/DataTableCrud.vue';
</script> 