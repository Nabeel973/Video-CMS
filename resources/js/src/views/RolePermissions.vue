<template>
    <div>
        <!-- Breadcrumb -->
        <ul class="flex space-x-2 rtl:space-x-reverse mb-4">
            <li>
                <router-link to="/" class="text-primary hover:underline">Home</router-link>
            </li>
            <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2">
                <router-link to="/roles" class="text-primary hover:underline">Roles</router-link>
            </li>
            <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2">
                <span>Permissions</span>
            </li>
        </ul>

        <!-- Page Header -->
        <div class="panel mb-5">
            <div class="flex items-center justify-between">
                <h5 class="font-semibold text-lg dark:text-white-light">Manage Permissions for {{ role.name }}</h5>
                <router-link to="/roles" class="btn btn-outline-primary">
                    <svg class="w-5 h-5 ltr:mr-2 rtl:ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" fill="none">
                        <path d="M9 5L4 9L9 13"></path>
                        <path d="M4 9H20"></path>
                    </svg>
                    Back to Roles
                </router-link>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="panel p-5">
            <div class="flex justify-center items-center py-8">
                <div class="animate-spin border-4 border-primary border-l-transparent rounded-full w-10 h-10"></div>
            </div>
        </div>

        <!-- Content -->
        <div v-else class="panel p-5">
            <!-- Search and Actions -->
            <div class="mb-5 flex flex-col md:flex-row justify-between gap-4">
                <div class="flex gap-2">
                    <button type="button" class="btn btn-primary" @click="selectAllPermissions">Select All</button>
                    <button type="button" class="btn btn-outline-primary" @click="deselectAllPermissions">Deselect All</button>
                </div>
                <div class="md:w-1/3">
                    <input v-model="search" type="text" class="form-input" placeholder="Search permissions..." />
                </div>
            </div>

            <!-- Permissions Groups -->
            <div v-if="Object.keys(groupedPermissions).length === 0" class="text-center py-8">
                <p>No permissions found.</p>
            </div>

            <div v-else>
                <div v-for="(permissions, group) in groupedPermissions" :key="group" class="mb-8">
                    <div class="border-b pb-2 mb-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold">{{ formatGroupName(group) }}</h3>
                            <div class="flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" @click="selectGroupPermissions(group)">Select All</button>
                                <button type="button" class="btn btn-sm btn-outline-danger" @click="deselectGroupPermissions(group)">Deselect All</button>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        <div v-for="permission in filterPermissions(permissions)" :key="permission.id" class="border p-3 rounded-md hover:border-primary transition-all duration-300">
                            <label class="flex items-start cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    :value="permission.id" 
                                    v-model="selectedPermissions" 
                                    class="form-checkbox mt-1"
                                />
                                <span class="ml-2">
                                    <span class="block font-medium">{{ formatPermissionName(permission.name) }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ permission.name }}</span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex justify-end mt-8">
                <button type="button" class="btn btn-primary" :disabled="saving" @click="savePermissions">
                    {{ saving ? 'Saving...' : 'Save Permissions' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';

const route = useRoute();
const router = useRouter();

// State
const role = ref({});
const allPermissions = ref([]);
const selectedPermissions = ref([]);
const loading = ref(true);
const saving = ref(false);
const search = ref('');

// Computed
const groupedPermissions = computed(() => {
    // If permissions are already grouped in the response, use that directly
    if (typeof allPermissions.value === 'object' && !Array.isArray(allPermissions.value) && Object.keys(allPermissions.value).length > 0) {
        return allPermissions.value;
    }
    
    // Otherwise, group them ourselves
    return groupPermissionsByModule(allPermissions.value || []);
});

// Methods
const formatPermissionName = (name) => {
    if (!name) return '';
    
    // Extract the action part (after the last dot)
    const parts = name.split('.');
    const action = parts[parts.length - 1];
    
    // Convert to title case and replace underscores with spaces
    return action
        .replace(/_/g, ' ')
        .replace(/\b\w/g, l => l.toUpperCase());
};

const formatGroupName = (group) => {
    if (!group) return '';
    
    return group
        .replace(/\./g, ' - ')
        .replace(/_/g, ' ')
        .replace(/\b\w/g, l => l.toUpperCase());
};

const filterPermissions = (permissions) => {
    if (!search.value || !Array.isArray(permissions)) return permissions;
    
    return permissions.filter(permission => 
        permission.name && permission.name.toLowerCase().includes(search.value.toLowerCase())
    );
};

const groupPermissionsByModule = (permissions) => {
    const grouped = {};
    
    // Ensure permissions is an array before proceeding
    if (!Array.isArray(permissions)) {
        console.error('Permissions is not an array:', permissions);
        return grouped;
    }
    
    permissions.forEach(permission => {
        if (!permission || !permission.name) {
            console.warn('Invalid permission object:', permission);
            return;
        }
        
        // Extract the module part (before the last dot)
        const parts = permission.name.split('.');
        parts.pop(); // Remove the last part (action)
        const module = parts.join('.') || 'other';
        
        if (!grouped[module]) {
            grouped[module] = [];
        }
        
        grouped[module].push(permission);
    });
    
    return grouped;
};

const selectAllPermissions = () => {
    if (!Array.isArray(allPermissions.value)) return;
    selectedPermissions.value = allPermissions.value.map(p => p.id);
};

const deselectAllPermissions = () => {
    selectedPermissions.value = [];
};

const selectGroupPermissions = (group) => {
    if (!groupedPermissions.value[group]) return;
    
    const groupPermissionIds = groupedPermissions.value[group].map(p => p.id);
    const currentSelected = new Set(selectedPermissions.value);
    
    groupPermissionIds.forEach(id => currentSelected.add(id));
    selectedPermissions.value = Array.from(currentSelected);
};

const deselectGroupPermissions = (group) => {
    if (!groupedPermissions.value[group]) return;
    
    const groupPermissionIds = new Set(groupedPermissions.value[group].map(p => p.id));
    selectedPermissions.value = selectedPermissions.value.filter(id => !groupPermissionIds.has(id));
};

const savePermissions = async () => {
    saving.value = true;
    
    try {
        const response = await axios.post(`/roles/${role.value.id}/permissions`, {
            permissions: selectedPermissions.value
        });
        
        if (response.data) {
            Swal.fire({
                title: 'Success!',
                text: response.data.message || 'Permissions updated successfully',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                router.push('/roles');
            });
        } else {
            throw new Error('Invalid response format');
        }
    } catch (error) {
        console.error('Error saving permissions:', error);
        Swal.fire({
            title: 'Error!',
            text: error.response?.data?.message || 'Failed to update permissions. Please try again.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    } finally {
        saving.value = false;
    }
};

// Fetch data
const fetchData = async () => {
    loading.value = true;
    
    try {
        const roleId = route.params.id;
        
        // Fetch role details
        const roleResponse = await axios.get(`/roles/${roleId}`);
        console.log('Role response:', roleResponse.data);
        
        if (roleResponse.data && roleResponse.data.data) {
            role.value = roleResponse.data.data;
        } else if (roleResponse.data) {
            // If the data is directly in the response without a data property
            role.value = roleResponse.data;
        } else {
            console.error('Invalid role response format:', roleResponse.data);
            throw new Error('Invalid role response format');
        }
        
        // Fetch all permissions
        const permissionsResponse = await axios.get('/permissions');
        console.log('Permissions response:', permissionsResponse.data);
        
        // Handle the case where permissions are already grouped in the response
        if (permissionsResponse.data && permissionsResponse.data.data && typeof permissionsResponse.data.data === 'object' && !Array.isArray(permissionsResponse.data.data)) {
            // Permissions are already grouped by category in the response
            // We need to flatten them into a single array for our component
            const groupedPermissions = permissionsResponse.data.data;
            const flattenedPermissions = [];
            
            // Iterate through each group and add its permissions to our flattened array
            Object.keys(groupedPermissions).forEach(group => {
                if (Array.isArray(groupedPermissions[group])) {
                    flattenedPermissions.push(...groupedPermissions[group]);
                }
            });
            
            allPermissions.value = flattenedPermissions;
            // We'll use the original groupedPermissions directly
            groupedPermissions.value = permissionsResponse.data.data;
        } else if (permissionsResponse.data && Array.isArray(permissionsResponse.data.data)) {
            // If permissions are a flat array in the data property
            allPermissions.value = permissionsResponse.data.data;
        } else if (permissionsResponse.data && Array.isArray(permissionsResponse.data)) {
            // If permissions are directly in the response without a data property
            allPermissions.value = permissionsResponse.data;
        } else {
            console.error('Invalid permissions response format:', permissionsResponse.data);
            allPermissions.value = []; // Set to empty array to avoid errors
        }
        
        // Fetch role permissions
        const rolePermissionsResponse = await axios.get(`/roles/${roleId}/permissions`);
        console.log('Role permissions response:', rolePermissionsResponse.data);
        
        // Handle different possible response formats
        if (rolePermissionsResponse.data) {
            let rolePermissions = [];
            
            if (Array.isArray(rolePermissionsResponse.data)) {
                rolePermissions = rolePermissionsResponse.data;
            } else if (rolePermissionsResponse.data.data && Array.isArray(rolePermissionsResponse.data.data)) {
                rolePermissions = rolePermissionsResponse.data.data;
            } else if (rolePermissionsResponse.data.permissions && Array.isArray(rolePermissionsResponse.data.permissions)) {
                rolePermissions = rolePermissionsResponse.data.permissions;
            } else {
                console.error('Role permissions is not in expected format:', rolePermissionsResponse.data);
                rolePermissions = [];
            }
            
            // Set selected permissions - handle both objects with id property and direct id values
            selectedPermissions.value = rolePermissions.map(p => typeof p === 'object' && p !== null ? p.id : p);
        } else {
            console.error('Invalid role permissions response:', rolePermissionsResponse);
            selectedPermissions.value = []; // Set to empty array to avoid errors
        }
        
    } catch (error) {
        console.error('Error fetching data:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to fetch role permissions. Please try again.',
            icon: 'error',
            confirmButtonText: 'OK'
        }).then(() => {
            router.push('/roles');
        });
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchData();
});
</script>