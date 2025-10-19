<template>
    <div class="mb-5">
        <label :for="field.name" class="block text-sm font-medium mb-2">
            {{ field.label }}
            <span v-if="field.required" class="text-red-500">*</span>
        </label>
        <div class="relative">
            <div 
                :class="[
                    'form-input min-h-[42px] cursor-pointer flex flex-wrap gap-2 items-center',
                    { 'border-red-500': error },
                    { 'bg-gray-100 cursor-not-allowed': disabled }
                ]"
                @click="toggleDropdown"
            >
                <template v-if="selectedItems.length > 0">
                    <span 
                        v-for="item in selectedItems" 
                        :key="item.value"
                        class="inline-flex items-center gap-1 px-2 py-1 bg-primary text-white rounded text-sm"
                    >
                        {{ item.label }}
                        <button 
                            type="button"
                            @click.stop="removeItem(item.value)"
                            class="hover:bg-primary-dark rounded"
                        >
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </span>
                </template>
                <span v-else class="text-gray-400">
                    {{ field.placeholder || `Select ${field.label}` }}
                </span>
                <svg class="w-4 h-4 ml-auto" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </div>
            
            <div 
                v-if="isOpen" 
                class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-auto"
            >
                <div 
                    v-for="option in options" 
                    :key="option.value || option.id"
                    :class="[
                        'px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700',
                        { 'bg-primary/10': isSelected(option.value || option.id) }
                    ]"
                    @click="toggleItem(option)"
                >
                    <div class="flex items-center gap-2">
                        <input 
                            type="checkbox" 
                            :checked="isSelected(option.value || option.id)"
                            class="form-checkbox"
                            @click.stop
                        />
                        <span>{{ option.label || option.name }}</span>
                    </div>
                </div>
            </div>
        </div>
        <span v-if="error" class="text-red-500 text-sm">{{ error[0] }}</span>
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';

const props = defineProps({
    field: {
        type: Object,
        required: true
    },
    modelValue: {
        type: [Array, String],
        default: () => []
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

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);

const selectedValues = computed(() => {
    if (Array.isArray(props.modelValue)) {
        return props.modelValue;
    }
    if (typeof props.modelValue === 'string' && props.modelValue) {
        return props.modelValue.split(',');
    }
    return [];
});

const selectedItems = computed(() => {
    return selectedValues.value
        .map(value => props.options.find(opt => (opt.value || opt.id) == value))
        .filter(item => item);
});

const toggleDropdown = () => {
    if (!props.disabled) {
        isOpen.value = !isOpen.value;
    }
};

const isSelected = (value) => {
    return selectedValues.value.includes(String(value));
};

const toggleItem = (option) => {
    const value = String(option.value || option.id);
    let newValues = [...selectedValues.value];
    
    if (isSelected(value)) {
        newValues = newValues.filter(v => v !== value);
    } else {
        newValues.push(value);
    }
    
    emit('update:modelValue', newValues);
};

const removeItem = (value) => {
    const newValues = selectedValues.value.filter(v => v !== String(value));
    emit('update:modelValue', newValues);
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    if (isOpen.value && !event.target.closest('.relative')) {
        isOpen.value = false;
    }
};

watch(isOpen, (newVal) => {
    if (newVal) {
        document.addEventListener('click', handleClickOutside);
    } else {
        document.removeEventListener('click', handleClickOutside);
    }
});
</script>
