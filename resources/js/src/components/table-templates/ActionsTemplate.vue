<template>
    <div class="flex items-center gap-2">
        <!-- Edit Button -->
        <button 
            v-if="canEditRole(data.value)"
            class="btn btn-sm btn-outline-primary" 
            @click="$emit('edit', data.value)"
        >
            <svg class="w-4 h-4" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" fill="none">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-1 1 1-4 9.5-9.5z"></path>
            </svg>
        </button>
        
        <!-- Permissions Button -->
        <router-link 
            v-if="endpoint === 'roles' && canManagePermissions(data.value)" 
            :to="`/roles/${typeof data.value === 'object' ? data.value.id : data.value}/permissions`" 
            class="btn btn-sm btn-outline-info"
        >
            <svg class="w-4 h-4" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" fill="none">
                <path d="M12 15v3m-3-3h6M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <path d="M9 10a3 3 0 1 0 6 0 3 3 0 0 0-6 0"></path>
            </svg>
        </router-link>
        
        <!-- Delete Button -->
        <button 
            v-if="canDeleteRole(data.value)"
            class="btn btn-sm btn-outline-danger" 
            @click="$emit('delete', data.value)"
        >
            <svg class="w-4 h-4" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" fill="none">
                <path d="M3 6h18"></path>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            </svg>
        </button>
    </div>
</template>

<script setup>
const props = defineProps({
    data: {
        type: Object,
        required: true
    },
    endpoint: {
        type: String,
        required: true
    },
    currentUser: {
        type: Object,
        default: () => ({})
    },
    singularTitle: {
        type: String,
        default: ''
    }
});

defineEmits(['edit', 'delete']);

const canEditRole = (roleData) => {
    if (props.endpoint !== 'roles') return true;
    
    const roleName = typeof roleData === 'object' ? roleData.name : 
                    props.data.value.name;
    
    if (!props.currentUser.roles || !roleName) return true;
    
    return !props.currentUser.roles.some(role => role.name === roleName);
};

const canDeleteRole = (roleData) => {
    if (props.endpoint !== 'roles') return true;
    
    const roleName = typeof roleData === 'object' ? roleData.name : 
                    props.data.value.name;
    
    if (!props.currentUser.roles || !roleName) return true;
    
    return !props.currentUser.roles.some(role => role.name === roleName);
};

const canManagePermissions = (roleData) => {
    if (props.endpoint !== 'roles') return false;
    
    const roleName = typeof roleData === 'object' ? roleData.name : 
                    props.data.value.name;
    
    if (!props.currentUser.roles || !roleName) return true;
    
    return !props.currentUser.roles.some(role => role.name === roleName);
};
</script>