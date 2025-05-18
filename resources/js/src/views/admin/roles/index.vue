<template>
    <DataTableCrud
        title="Roles"
        singularTitle="Role"
        endpoint="roles"
        tableId="roleTable"
    />
</template>

<script setup>
import DataTableCrud from '@/components/DataTableCrud.vue';
</script>