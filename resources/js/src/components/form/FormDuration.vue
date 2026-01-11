<template>
    <div class="mb-5">
        <label :for="field.name" class="block text-sm font-medium mb-2">
            {{ field.label }}
            <span v-if="field.required" class="text-red-500">*</span>
        </label>
        <div class="flex items-end gap-2">
            <!-- Hours Input -->
            <div class="flex-1">
                <input
                    id="hours"
                    type="number"
                    min="0"
                    max="24"
                    :value="hours"
                    :disabled="disabled"
                    :class="[
                        'form-input',
                        { 'border-red-500': error },
                        { 'bg-gray-100 cursor-not-allowed': disabled }
                    ]"
                    placeholder="0"
                    @input="updateHours($event.target.value)"
                />
            </div>
            
            <span class="text-gray-500 dark:text-gray-400 mb-1">h</span>
            
            <!-- Minutes Input -->
            <div class="flex-1">
                <input
                    id="minutes"
                    type="number"
                    min="0"
                    max="59"
                    :value="minutes"
                    :disabled="disabled"
                    :class="[
                        'form-input',
                        { 'border-red-500': error },
                        { 'bg-gray-100 cursor-not-allowed': disabled }
                    ]"
                    placeholder="0"
                    @input="updateMinutes($event.target.value)"
                />
            </div>
            
            <span class="text-gray-500 dark:text-gray-400 mb-1">m</span>
        </div>
        <span v-if="error" class="text-red-500 text-sm">{{ error[0] }}</span>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format: Xh Ym (e.g., 2h 30m)</p>
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
        type: String,
        default: ''
    },
    error: {
        type: Array,
        default: null
    },
    disabled: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:modelValue']);

const hours = ref(0);
const minutes = ref(0);

// Parse duration string (e.g., "2h 30m") into hours and minutes
const parseDuration = (duration) => {
    if (!duration) {
        hours.value = 0;
        minutes.value = 0;
        return;
    }
    
    const match = duration.match(/(\d+)h\s*(\d+)m/);
    if (match) {
        hours.value = parseInt(match[1]) || 0;
        minutes.value = parseInt(match[2]) || 0;
    } else {
        hours.value = 0;
        minutes.value = 0;
    }
};

// Update hours and emit formatted duration
const updateHours = (value) => {
    hours.value = Math.max(0, Math.min(24, parseInt(value) || 0));
    emitDuration();
};

// Update minutes and emit formatted duration
const updateMinutes = (value) => {
    minutes.value = Math.max(0, Math.min(59, parseInt(value) || 0));
    emitDuration();
};

// Emit duration in format "Xh Ym"
const emitDuration = () => {
    // Only emit if at least one value is greater than 0
    if (hours.value > 0 || minutes.value > 0) {
        const formatted = `${hours.value}h ${minutes.value}m`;
        emit('update:modelValue', formatted);
    } else {
        // Emit empty string if both are 0 (for validation)
        emit('update:modelValue', '');
    }
};

// Watch for external changes to modelValue
watch(
    () => props.modelValue,
    (newValue) => {
        const currentFormatted = `${hours.value}h ${minutes.value}m`;
        if (!newValue || newValue === '') {
            // If empty, reset to 0
            hours.value = 0;
            minutes.value = 0;
        } else if (newValue !== currentFormatted) {
            parseDuration(newValue);
        }
    },
    { immediate: true }
);
</script>
