<template>
    <div class="mb-5">
        <label class="block text-sm font-medium mb-2">
            {{ field.label }}
            <span v-if="field.required" class="text-red-500">*</span>
        </label>
        
        <div class="border border-gray-300 dark:border-gray-600 rounded-md p-4">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-2 px-3">Name</th>
                            <th class="text-left py-2 px-3">Image</th>
                            <th class="text-center py-2 px-3 w-20">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr 
                            v-for="(cast, index) in castList" 
                            :key="index"
                            class="border-b border-gray-100 dark:border-gray-800"
                        >
                            <td class="py-2 px-3">
                                <input 
                                    type="text"
                                    v-model="cast.name"
                                    placeholder="Enter cast name"
                                    class="form-input w-full"
                                    @input="updateCast"
                                />
                            </td>
                            <td class="py-2 px-3">
                                <div class="flex items-center gap-2">
                                    <input 
                                        type="file"
                                        :ref="el => fileInputs[index] = el"
                                        accept="image/*"
                                        class="hidden"
                                        @change="handleImageUpload(index, $event)"
                                    />
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        @click="triggerFileInput(index)"
                                    >
                                        Choose Image
                                    </button>
                                    <span v-if="cast.image" class="text-sm text-gray-600">
                                        {{ getImageName(cast.image) }}
                                    </span>
                                    <img 
                                        v-if="cast.imagePreview" 
                                        :src="cast.imagePreview" 
                                        alt="Cast preview"
                                        class="w-10 h-10 object-cover rounded"
                                    />
                                </div>
                            </td>
                            <td class="py-2 px-3 text-center">
                                <button 
                                    type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    @click="removeCast(index)"
                                >
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="castList.length === 0">
                            <td colspan="3" class="py-4 text-center text-gray-500">
                                No cast members added yet
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <button 
                type="button"
                class="btn btn-primary btn-sm mt-3"
                @click="addCast"
            >
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Add Cast Member
            </button>
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
    }
});

const emit = defineEmits(['update:modelValue']);

const fileInputs = ref([]);

const castList = ref([]);

// Initialize cast list from modelValue
const initializeCastList = () => {
    if (Array.isArray(props.modelValue) && props.modelValue.length > 0) {
        castList.value = props.modelValue.map(cast => ({
            name: cast.name || '',
            image: cast.image || null,
            imagePreview: cast.imagePreview || null
        }));
    } else if (typeof props.modelValue === 'string' && props.modelValue) {
        try {
            const parsed = JSON.parse(props.modelValue);
            castList.value = Array.isArray(parsed) ? parsed : [];
        } catch {
            castList.value = [];
        }
    } else {
        castList.value = [];
    }
};

watch(() => props.modelValue, () => {
    initializeCastList();
}, { immediate: true, deep: true });

const addCast = () => {
    castList.value.push({
        name: '',
        image: null,
        imagePreview: null
    });
    updateCast();
};

const removeCast = (index) => {
    castList.value.splice(index, 1);
    updateCast();
};

const triggerFileInput = (index) => {
    if (fileInputs.value[index]) {
        fileInputs.value[index].click();
    }
};

const handleImageUpload = (index, event) => {
    const file = event.target.files[0];
    if (file) {
        castList.value[index].image = file;
        
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            castList.value[index].imagePreview = e.target.result;
            updateCast();
        };
        reader.readAsDataURL(file);
    }
};

const getImageName = (image) => {
    if (image instanceof File) {
        return image.name;
    }
    if (typeof image === 'string') {
        return image.split('/').pop();
    }
    return '';
};

const updateCast = () => {
    emit('update:modelValue', castList.value);
};

initializeCastList();
</script>
