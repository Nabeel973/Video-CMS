<template>
    <div class="mb-5">
        <label :for="field.name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ field.label }}
            <span v-if="field.required" class="text-red-500">*</span>
        </label>
        
        <div class="flex items-center justify-center w-full">
            <label 
                :for="field.name" 
                class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600"
                :class="{ 'border-red-500': error }"
            >
                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                    <svg v-if="!previewUrl" class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                    </svg>
                    <img v-else :src="previewUrl" alt="Preview" class="w-20 h-20 object-cover rounded mb-2">
                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                        <span class="font-semibold">Click to upload</span> or drag and drop
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF up to 2MB</p>
                </div>
                <input 
                    :id="field.name"
                    type="file" 
                    class="hidden" 
                    :accept="field.accept"
                    :disabled="disabled"
                    @change="handleFileChange"
                />
            </label>
        </div>
        
        <div v-if="fileName" class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Selected: {{ fileName }}
        </div>
        
        <div v-if="error" class="text-danger text-xs mt-1">{{ error }}</div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    field: {
        type: Object,
        required: true
    },
    modelValue: {
        type: [File, String, null],
        default: null
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

const fileName = ref('');
const previewUrl = ref('');

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        fileName.value = file.name;
        emit('update:modelValue', file);
        
        // Create preview URL for images
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewUrl.value = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    } else {
        fileName.value = '';
        previewUrl.value = '';
        emit('update:modelValue', null);
    }
};

// Watch for external changes to modelValue
watch(() => props.modelValue, (newValue) => {
    if (!newValue) {
        fileName.value = '';
        previewUrl.value = '';
    } else if (typeof newValue === 'string') {
        // Handle existing image URL
        previewUrl.value = newValue;
        fileName.value = 'Current image';
    }
});
</script>