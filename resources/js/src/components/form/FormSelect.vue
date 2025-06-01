<template>
    <div class="mb-5">
        <label :for="field.name" class="block text-sm font-medium mb-2">
            {{ field.label }}
            <span v-if="field.required" class="text-red-500">*</span>
        </label>
        <select 
            :id="field.name"
            :value="modelValue"
            :disabled="disabled"
            :class="[
                'form-select',
                { 'border-red-500': error },
                { 'bg-gray-100 cursor-not-allowed': disabled }
            ]"
            @change="$emit('update:modelValue', $event.target.value)"
        >
            <option value="">{{ field.placeholder || `Select ${field.label}` }}</option>
            <option 
                v-for="option in options" 
                :key="option.value || option.id" 
                :value="option.value || option.id"
            >
                {{ option.label || option.name }}
            </option>
        </select>
        <span v-if="error" class="text-red-500 text-sm">{{ error[0] }}</span>
        <span v-if="disabled && field.disabledMessage" class="text-yellow-600 text-sm">
            {{ field.disabledMessage }}
        </span>
    </div>
</template>

<script setup>
defineProps({
    field: {
        type: Object,
        required: true
    },
    modelValue: {
        type: [String, Number],
        default: ''
    },
    error: {
        type: Array,
        default: null
    },
    disabled: {
        type: Boolean,
        default: false
    },
    options: {
        type: Array,
        default: () => []
    }
});

defineEmits(['update:modelValue']);

const handleDeleteItem = async (id) => {
    // singularTitle will be auto-determined from endpoint
    await deleteItem(id, null, () => loadData(search.value));
};
</script>