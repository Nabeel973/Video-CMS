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
                    <form @submit.prevent="handleSubmit">
                        <!-- Name Field -->
                        <div class="mb-5">
                            <label for="name">Name</label>
                            <input 
                                id="name" 
                                type="text" 
                                v-model="formData.name" 
                                class="form-input" 
                                :placeholder="namePlaceholder" 
                                :class="{'border-red-500': errors.name}"
                                :disabled="isOwnRole"
                            />
                            <span class="text-red-500 text-sm" v-if="errors.name">{{ errors.name[0] }}</span>
                            <span class="text-yellow-600 text-sm" v-if="isOwnRole">You cannot modify your own role name</span>
                        </div>

                        <!-- Email Field (for users) -->
                        <div class="mb-5" v-if="isUserForm">
                            <label for="email">Email</label>
                            <input 
                                id="email" 
                                type="email" 
                                v-model="formData.email" 
                                class="form-input" 
                                placeholder="Enter email address"
                                :class="{'border-red-500': errors.email}"
                            />
                            <span class="text-red-500 text-sm" v-if="errors.email">{{ errors.email[0] }}</span>
                        </div>

                        <!-- Password Field (for users) -->
                        <div class="mb-5" v-if="isUserForm">
                            <label for="password">Password {{ isEdit ? '(Leave blank to keep current)' : '' }}</label>
                            <input 
                                id="password" 
                                type="password" 
                                v-model="formData.password" 
                                class="form-input" 
                                :placeholder="isEdit ? 'Leave blank to keep current password' : 'Enter password'"
                                :class="{'border-red-500': errors.password}"
                            />
                            <span class="text-red-500 text-sm" v-if="errors.password">{{ errors.password[0] }}</span>
                        </div>

                        <!-- Password Confirmation Field (for users) -->
                        <div class="mb-5" v-if="isUserForm">
                            <label for="password_confirmation">Confirm Password</label>
                            <input 
                                id="password_confirmation" 
                                type="password" 
                                v-model="formData.password_confirmation" 
                                class="form-input" 
                                :placeholder="isEdit ? 'Leave blank to keep current password' : 'Confirm password'"
                                :class="{'border-red-500': errors.password_confirmation}"
                            />
                            <span class="text-red-500 text-sm" v-if="errors.password_confirmation">{{ errors.password_confirmation[0] }}</span>
                        </div>

                        <!-- Role Field (for users) -->
                        <div class="mb-5" v-if="isUserForm">
                            <label for="role_id">Role</label>
                            <select 
                                id="role_id" 
                                v-model="formData.role_id" 
                                class="form-select" 
                                :class="{'border-red-500': errors.role_id}"
                            >
                                <option value="">Select Role</option>
                                <option 
                                    v-for="role in availableRoles" 
                                    :key="role.id" 
                                    :value="role.id"
                                    v-show="role.status === 'active'"
                                >
                                    {{ role.name }}
                                </option>
                            </select>
                            <span class="text-red-500 text-sm" v-if="errors.role_id">{{ errors.role_id[0] }}</span>
                        </div>

                        <!-- Status Field -->
                        <div class="mb-5">
                            <label for="status">Status</label>
                            <select 
                                id="status" 
                                v-model="formData.status" 
                                class="form-select" 
                                :class="{'border-red-500': errors.status}"
                                :disabled="isOwnRole"
                            >
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <span class="text-red-500 text-sm" v-if="errors.status">{{ errors.status[0] }}</span>
                            <span class="text-yellow-600 text-sm" v-if="isOwnRole">You cannot modify your own status</span>
                        </div>

                        <div class="flex justify-end items-center mt-8">
                            <button type="button" class="btn btn-outline-danger ltr:mr-3 rtl:ml-3" @click="closeModal">Cancel</button>
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
import { computed, watch, ref, onMounted } from 'vue';
import axios from 'axios';

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
        default: 'Item' // Provide default value
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
    }
});

const emit = defineEmits(['close', 'submit']);

// State
const availableRoles = ref([]);

// Computed properties
const modalTitle = computed(() => {
    return props.isEdit ? `Edit ${props.singularTitle}` : `Add ${props.singularTitle}`;
});

const namePlaceholder = computed(() => {
    const title = props.singularTitle || 'item';
    return `Enter ${title.toLowerCase()} name`;
});

const isUserForm = computed(() => {
    return props.endpoint === 'users';
});

// Methods
const closeModal = () => {
    emit('close');
};

const handleSubmit = () => {
    emit('submit');
};

const fetchRoles = async () => {
    if (!isUserForm.value) return;
    
    try {
        const response = await axios.get('/users/roles/available');
        if (response.data && response.data.data) {
            availableRoles.value = response.data.data;
        }
    } catch (error) {
        console.error('Error fetching roles:', error);
    }
};

// Watch for form data changes and emit them back to parent
watch(() => props.formData, (newData) => {
    // This ensures reactivity is maintained
}, { deep: true });

// Watch for modal open to fetch roles
watch(() => props.isOpen, (isOpen) => {
    if (isOpen && isUserForm.value) {
        fetchRoles();
    }
});

onMounted(() => {
    if (props.isOpen && isUserForm.value) {
        fetchRoles();
    }
});
</script>