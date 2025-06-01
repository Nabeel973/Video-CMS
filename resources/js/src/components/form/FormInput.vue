<template>
    <div class="mb-5">
        <label :for="field.name" class="block text-sm font-medium mb-2">
            {{ field.label }}
            <span v-if="field.required" class="text-red-500">*</span>
            <span v-if="field.hint" class="text-gray-500 text-xs">({{ field.hint }})</span>
        </label>
        <input 
            :id="field.name"
            :type="field.type || 'text'"
            :value="modelValue"
            :placeholder="field.placeholder"
            :disabled="disabled"
            :class="[
                'form-input',
                { 'border-red-500': error },
                { 'bg-gray-100 cursor-not-allowed': disabled }
            ]"
            @input="$emit('update:modelValue', $event.target.value)"
        />
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
    }
});

defineEmits(['update:modelValue']);
</script>