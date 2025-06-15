<template>
    <div class="mb-5">
        <label class="inline-flex items-center cursor-pointer">
            <span class="text-white-dark mr-3">{{ field.label }}</span>
            <input 
                type="checkbox" 
                class="sr-only peer"
                :checked="isImageType"
                @change="handleToggle"
                :disabled="disabled"
            >
            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">
                {{ isImageType ? 'Image' : 'Text' }}
            </span>
        </label>
        <div v-if="error" class="text-danger text-xs mt-1">{{ error }}</div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    field: {
        type: Object,
        required: true
    },
    modelValue: {
        type: String,
        default: 'text'
    },
    error: {
        type: String,
        default: ''
    },
    disabled: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:modelValue']);

const isImageType = computed(() => props.modelValue === 'image');

const handleToggle = (event) => {
    const newValue = event.target.checked ? 'image' : 'text';
    emit('update:modelValue', newValue);
};
</script>