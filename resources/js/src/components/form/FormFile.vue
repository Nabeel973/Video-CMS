<template>
    <div class="mb-5">
        <label
            :for="field.name"
            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
        >
            {{ field.label }}
            <span v-if="field.required" class="text-red-500">*</span>
        </label>

        <!-- Image Preview (when image exists) -->
        <div v-if="previewUrl || (defaultPreviewUrl && isEdit)" class="mb-3">
            <div class="relative inline-block">
                <img
                    :src="previewUrl || defaultPreviewUrl"
                    alt="Preview"
                    :class="[
                        'object-cover rounded-lg border',
                        field.compact ? 'w-20 h-20' : 'w-32 h-32'
                    ]"
                />
                <button
                    v-if="previewUrl"
                    type="button"
                    @click="clearImage"
                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center hover:bg-red-600 transition-colors text-xs"
                    :class="field.compact ? 'w-4 h-4 text-xs' : 'w-6 h-6'"
                >
                    ×
                </button>
            </div>
            <p v-if="fileName" class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                {{ fileName }}
            </p>
        </div>

        <!-- File Upload Area -->
        <div class="flex items-center justify-center w-full">
            <label
                :for="field.name"
                    :class="[
                        'flex flex-col items-center justify-center w-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600',
                        field.compact ? 'h-16 py-1.5' : 'h-24',
                        { 'border-red-500': error }
                    ]"
            >
                <div
                    :class="[
                        'flex flex-col items-center justify-center',
                        field.compact ? 'py-1' : 'pt-5 pb-6'
                    ]"
                >
                    <svg
                        :class="[
                            'text-gray-500 dark:text-gray-400',
                            field.compact ? 'w-4 h-4 mb-0.5' : 'w-6 h-6 mb-2'
                        ]"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 20 16"
                    >
                        <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"
                        />
                    </svg>
                    <p :class="[
                        'text-gray-500 dark:text-gray-400',
                        field.compact ? 'text-[10px] mb-0' : 'text-xs mb-1'
                    ]">
                        <span class="font-semibold">{{
                            previewUrl
                                ? "Click to change"
                                : "Click to upload"
                        }}</span>
                        <span v-if="!field.compact"> or drag and drop</span>
                    </p>
                    <p v-if="!field.compact" class="text-xs text-gray-500 dark:text-gray-400">
                        PNG, JPG, GIF up to 2MB
                    </p>
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

        <div v-if="error" class="text-danger text-xs mt-1">{{ error }}</div>
    </div>
</template>

<script setup>
import { onMounted, ref, watch } from "vue";

const props = defineProps({
    field: {
        type: Object,
        required: true,
    },
    modelValue: {
        type: [File, String, null],
        default: null,
    },
    error: {
        type: String,
        default: "",
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    isEdit: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:modelValue"]);

const fileName = ref("");
const previewUrl = ref("");
const defaultPreviewUrl = ref("");

// Set default preview based on file type
const getDefaultPreview = () => {
    if (!props.field.accept) return null;
    
    if (props.field.accept.includes('image')) {
        // Default image placeholder
        return '/assets/images/default-movie-poster.svg';
    } else if (props.field.accept.includes('video')) {
        // Default video placeholder
        return '/assets/images/default-video-thumbnail.svg';
    }
    return null;
};

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        fileName.value = file.name;
        emit("update:modelValue", file);

        // Create preview URL for images and videos
        if (file.type.startsWith("image/") || file.type.startsWith("video/")) {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewUrl.value = e.target.result;
                defaultPreviewUrl.value = null; // Hide default when we have a real preview
            };
            reader.readAsDataURL(file);
        }
    } else {
        clearImage();
    }
};

const clearImage = () => {
    fileName.value = "";
    previewUrl.value = "";
    // Only show default in edit mode
    defaultPreviewUrl.value = props.isEdit ? getDefaultPreview() : null;
    emit("update:modelValue", null);

    // Clear the file input
    const fileInput = document.getElementById(props.field.name);
    if (fileInput) {
        fileInput.value = "";
    }
};

// Watch for external changes to modelValue
watch(
    () => props.modelValue,
    (newValue) => {
        if (!newValue) {
            fileName.value = "";
            previewUrl.value = "";
            // Only show default placeholder in edit mode when preview is not available
            defaultPreviewUrl.value = props.isEdit ? getDefaultPreview() : null;
        } else if (typeof newValue === "string") {
            // Handle existing image URL - construct full URL if needed
            let imageUrl;
            if (newValue.startsWith("http") || newValue.startsWith("/")) {
                imageUrl = newValue;
            } else {
                imageUrl = `/storage/${newValue}`;
            }
            console.log('Setting image preview:', { original: newValue, processed: imageUrl });
            previewUrl.value = imageUrl;
            fileName.value = "Current image";
            defaultPreviewUrl.value = null; // Hide default when we have a real preview
        } else if (newValue instanceof File) {
            // Handle new file upload
            fileName.value = newValue.name;
            if (newValue.type.startsWith("image/") || newValue.type.startsWith("video/")) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewUrl.value = e.target.result;
                    defaultPreviewUrl.value = null; // Hide default when we have a real preview
                };
                reader.readAsDataURL(newValue);
            }
        }
    },
    { immediate: true }
);

// Initialize default preview on mount (only in edit mode)
onMounted(() => {
    if (!previewUrl.value && props.isEdit) {
        defaultPreviewUrl.value = getDefaultPreview();
    }
});
</script>
