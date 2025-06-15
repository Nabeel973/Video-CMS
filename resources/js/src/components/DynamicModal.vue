<template>
    <div class="fixed inset-0 bg-black/60 z-[999] hidden" :class="isOpen && '!block'">
        <div class="flex items-center justify-center min-h-screen px-4" @click.self="closeModal">
            <div class="panel border-0 p-0 rounded-lg overflow-hidden w-full max-w-lg">
                <div class="flex bg-[#fbfbfb] dark:bg-[#121c2c] items-center justify-between px-5 py-3">
                    <h5 class="font-bold text-lg">{{ modalTitle }}</h5>
                    <button type="button" class="hover:opacity-80" @click="closeModal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>

                <div class="p-5">
                    <form @submit.prevent="handleSubmit" enctype="multipart/form-data">
                        <!-- Dynamic Form Fields -->
                        <template v-for="field in visibleFields" :key="field.name">
                            <component 
                                :is="getFieldComponent(field.type)"
                                :field="field"
                                :modelValue="localFormData[field.name]"
                                :error="errors[field.name]"
                                :disabled="isFieldDisabled(field)"
                                :options="getFieldOptions(field)"
                                @update:modelValue="updateField(field.name, $event)"
                            />
                        </template>

                        <!-- Action Buttons -->
                        <div class="flex justify-end items-center mt-8">
                            <button type="button" class="btn btn-outline-danger ltr:mr-3 rtl:ml-3" @click="closeModal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary" :disabled="loading">
                                {{ loading ? 'Saving...' : 'Save' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, watch, ref, onMounted, reactive } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import FormInput from './form/FormInput.vue';
import FormSelect from './form/FormSelect.vue';
import FormTextarea from './form/FormTextarea.vue';
import FormSwitch from './form/FormSwitch.vue';
import FormFile from './form/FormFile.vue';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false
    },
    isEdit: {
        type: Boolean,
        default: false
    },
    singularTitle: {
        type: String,
        default: 'Item'
    },
    formData: {
        type: Object,
        required: true
    },
    errors: {
        type: Object,
        default: () => ({})
    },
    loading: {
        type: Boolean,
        default: false
    },
    isOwnRole: {
        type: Boolean,
        default: false
    },
    endpoint: {
        type: String,
        default: ''
    },
    formFields: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['close', 'submit', 'update:formData']);

// State
const availableRoles = ref([]);
const localFormData = reactive({});

// Computed properties
const modalTitle = computed(() => {
    return props.isEdit ? `Edit ${props.singularTitle}` : `Add ${props.singularTitle}`;
});

const isUserForm = computed(() => {
    return props.endpoint === 'users';
});

const visibleFields = computed(() => {
    return props.formFields.filter(field => {
        if (!field.conditional) return true;
        
        const conditionField = field.conditional.field;
        const conditionValue = field.conditional.value;
        const currentValue = localFormData[conditionField];
        
        return currentValue === conditionValue;
    });
});

// Methods
const closeModal = () => {
    emit('close');
};

const handleSubmit = async () => {
    try {
        const url = localFormData.id ? `/${props.endpoint}/${localFormData.id}` : `/${props.endpoint}`;
        const method = localFormData.id ? 'put' : 'post';
        
        // Create FormData for file uploads
        const formData = new FormData();
        
        Object.keys(localFormData).forEach(key => {
            if (localFormData[key] !== null && localFormData[key] !== undefined) {
                if (localFormData[key] instanceof File) {
                    formData.append(key, localFormData[key]);
                } else {
                    formData.append(key, localFormData[key]);
                }
            }
        });
        
        // Add _method for PUT requests when using FormData
        if (method === 'put') {
            formData.append('_method', 'PUT');
        }
        
        console.log('Submitting form data:', Object.fromEntries(formData));
        
        const config = {
            headers: {
                'Content-Type': 'multipart/form-data',
            }
        };
        
        const response = await axios.post(url, formData, config);
        
        if (response.data && response.data.message) {
            emit('submit', response.data);
            
            Swal.fire({
                title: 'Success!',
                text: response.data.message,
                icon: 'success',
                confirmButtonText: 'OK'
            });
        }
    } catch (error) {
        console.error('Form submission error:', error.response?.data);
        // Let parent handle errors by emitting them
        emit('submit', { error: error.response?.data || error });
    }
};

const updateField = (fieldName, value) => {
    localFormData[fieldName] = value;
    emit('update:formData', { ...localFormData });
};

const getFieldComponent = (type) => {
    const componentMap = {
        'text': FormInput,
        'email': FormInput,
        'password': FormInput,
        'select': FormSelect,
        'textarea': FormTextarea,
        'switch': FormSwitch,
        'file': FormFile,
    };
    return componentMap[type] || FormInput;
};

const isFieldDisabled = (field) => {
    if (props.isOwnRole && (field.name === 'name' || field.name === 'status')) {
        return true;
    }
    return field.disabled || false;
};

const getFieldOptions = (field) => {
    if (field.name === 'role_id') {
        return availableRoles.value.filter(role => role.status === 'active');
    }
    return field.options || [];
};

const fetchRoles = async () => {
    if (!isUserForm.value) return;
    
    try {
        const response = await axios.get('/users/roles/available');
        if (response.data && response.data.data) {
            availableRoles.value = response.data.data;
            console.log('Available roles:', availableRoles.value);
        }
    } catch (error) {
        console.error('Error fetching roles:', error);
    }
};

// Initialize local form data
const initializeFormData = () => {
    // Clear existing data first
    Object.keys(localFormData).forEach(key => {
        delete localFormData[key];
    });
    
    // Copy all form data properties
    Object.keys(props.formData).forEach(key => {
        localFormData[key] = props.formData[key];
    });
    
    console.log('Initialized form data:', localFormData);
};

// Watch for form data changes
watch(() => props.formData, (newFormData) => {
    console.log('Form data changed:', newFormData);
    initializeFormData();
}, { deep: true, immediate: true });

// Watch for modal open to fetch roles
watch(() => props.isOpen, (isOpen) => {
    if (isOpen && isUserForm.value) {
        fetchRoles();
    }
});

onMounted(() => {
    initializeFormData();
    if (props.isOpen && isUserForm.value) {
        fetchRoles();
    }
});
</script>