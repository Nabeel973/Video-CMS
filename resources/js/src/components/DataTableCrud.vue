<template>
    <div class="panel">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-5">
            <h5 class="font-semibold text-lg dark:text-white-light">{{ title }}</h5>
            <div class="flex items-center gap-2">
                <button type="button" class="btn btn-primary" @click="openModal()">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 ltr:mr-2 rtl:ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" fill="none">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Add New
                    </span>
                </button>
                <ExportDropdown 
                    :rows="rows" 
                    :columns="columns" 
                    :filename="title.toLowerCase()"
                    @export-csv="handleExportCSV"
                    @export-excel="handleExportExcel"
                    @export-pdf="handleExportPDF"
                />
            </div>
        </div>

        <!-- Search -->
        <div class="flex md:items-center md:flex-row flex-col mb-5 gap-5">
            <div class="flex items-center gap-5">
                <div>
                    <input 
                        v-model="search" 
                        type="text" 
                        class="form-input" 
                        placeholder="Search..." 
                        @input="handleSearch"
                    />
                </div>
            </div>
            <div class="flex items-center gap-5 ltr:ml-auto rtl:mr-auto">
                <!-- Any other elements that might be on the right side -->
            </div>
        </div>

        <!-- Datatable -->
        <div class="datatable">
            <vue3-datatable
                :rows="rows"
                :columns="columns"
                :totalRows="totalRows"
                :sortable="true"
                :search="false"
                skin="whitespace-nowrap bh-table-hover"
                :firstArrow="paginationIcons.first"
                :lastArrow="paginationIcons.last"
                :previousArrow="paginationIcons.previous"
                :nextArrow="paginationIcons.next"
            >
                <!-- Dynamic Templates -->
                <template v-for="template in dynamicTemplates" :key="template.name" #[template.name]="data">
                    <component 
                        :is="template.component" 
                        :data="data" 
                        :endpoint="endpoint"
                        :current-user="currentUser"
                        :singular-title="singularTitle"
                        @edit="editItem"

                        @delete="handleDeleteItem"
                    />
                </template>
            </vue3-datatable>
        </div>

        <!-- Dynamic Modal -->
        <DynamicModal
            :isOpen="modal"
            :isEdit="isEdit"
            :singularTitle="singularTitle"
            :formData="form"
            :errors="errors"
            :loading="loading"
            :isOwnRole="isOwnRole"
            :endpoint="endpoint"
            :formFields="formFields"
            @close="closeModal"
            @submit="handleModalSubmit"
            @update:formData="updateFormData"
        />
    </div>
</template>

<script setup>
import { useAppStore } from '@/stores/index';
import Vue3Datatable from '@bhplugin/vue3-datatable';
import { computed, onMounted, reactive, ref } from 'vue';
import DynamicModal from './DynamicModal.vue';
import ExportDropdown from './ExportDropdown.vue';
import ActionsTemplate from './table-templates/ActionsTemplate.vue';
import DateTemplate from './table-templates/DateTemplate.vue';
import EmailTemplate from './table-templates/EmailTemplate.vue';
import RolesTemplate from './table-templates/RolesTemplate.vue';
import StatusTemplate from './table-templates/StatusTemplate.vue';

// Composables
import { useCurrentUser } from '@/composables/useCurrentUser';
import { useDataOperations } from '@/composables/useDataOperations';
import { useExport } from '@/composables/useExport';
import { useFormConfig } from '@/composables/useFormConfig';
import { useSearch } from '@/composables/useSearch';
import { useTableConfig } from '@/composables/useTableConfig';

// Styles
import '@/styles/datatable.scss';

const store = useAppStore();

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    singularTitle: {
        type: String,
        required: true
    },
    endpoint: {
        type: String,
        required: true
    },
    tableId: {
        type: String,
        required: true
    }
});

// Composables
const { getColumnsForEndpoint, transformRowData } = useTableConfig();
const { rows, totalRows, fetchData, fetchItem, deleteItem } = useDataOperations(props.endpoint);
const { exportToCSV, exportToExcel, exportToPDF } = useExport();
const { getFormFields, getInitialFormData } = useFormConfig();
const { currentUser, fetchCurrentUser } = useCurrentUser();
const { search, handleSearch } = useSearch((query) => loadData(query));

// State
const modal = ref(false);
const isEdit = ref(false);
const loading = ref(false);
const errors = ref({});

// Dynamic configuration
const columns = ref([]);
const formFields = ref([]);
const form = reactive({});

// Pagination icons
const paginationIcons = {
    first: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M13 19L7 12L13 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path opacity="0.5" d="M16.9998 19L10.9998 12L16.9998 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
    last: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M11 19L17 12L11 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path opacity="0.5" d="M6.99976 19L12.9998 12L6.99976 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
    previous: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M15 5L9 12L15 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
    next: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>'
};

// Dynamic templates configuration
const dynamicTemplates = computed(() => {
    const templates = [
        { name: 'status', component: StatusTemplate },
        { name: 'created_at', component: DateTemplate },
        { name: 'actions', component: ActionsTemplate }
    ];

    if (props.endpoint === 'users') {
        templates.push(
            { name: 'email', component: EmailTemplate },
            { name: 'roles', component: RolesTemplate }
        );
    }

    return templates;
});

// Computed properties
const isOwnRole = computed(() => {
    if (props.endpoint !== 'roles' || !currentUser.value.roles || !form.name) {
        return false;
    }
    return currentUser.value.roles.some(role => role.name === form.name);
});

// Methods
const initializeConfiguration = () => {
    columns.value = getColumnsForEndpoint(props.endpoint);
    formFields.value = getFormFields(props.endpoint, isEdit.value);
    
    const initialData = getInitialFormData(props.endpoint);
    Object.keys(initialData).forEach(key => {
        form[key] = initialData[key];
    });
};

const loadData = async (searchQuery = '') => {
    await fetchData(searchQuery, transformRowData);
};

const openModal = async (itemData = null) => {
    isEdit.value = !!itemData;
    formFields.value = getFormFields(props.endpoint, isEdit.value);
    
    if (itemData) {
        const data = typeof itemData === 'object' ? itemData : await fetchItem(itemData);
        if (data) {
            Object.keys(form).forEach(key => {
                if (key === 'role_id' && props.endpoint === 'users') {
                    form[key] = data.role_ids ? data.role_ids[0] : '';
                } else if (key === 'password' || key === 'password_confirmation') {
                    form[key] = '';
                } 
                else if (key === 'type' && props.endpoint === 'advertisements') {
                    const typeValue = data[key];
                    form[key] = typeValue ? typeValue.toLowerCase() : 'text';
                }
                else {
                    form[key] = data[key] || form[key];
                }
            });
        }
    } else {
        const initialData = getInitialFormData(props.endpoint);
        Object.keys(form).forEach(key => {
            form[key] = initialData[key];
        });
    }
    
    modal.value = true;
};

const closeModal = () => {
    modal.value = false;
    isEdit.value = false;
    errors.value = {};
    
    const initialData = getInitialFormData(props.endpoint);
    Object.keys(form).forEach(key => {
        form[key] = initialData[key];
    });
};

const editItem = async (id) => {
    await openModal(id);
};

const handleDeleteItem = async (id) => {
    await deleteItem(id, props.singularTitle, () => loadData(search.value));
};

const handleModalSubmit = async (result) => {
    if (result.error) {
        if (result.error.errors) {
            errors.value = result.error.errors;
        } else if (result.error.error) {
            errors.value = { general: [result.error.error] };
        }
    } else {
        closeModal();
        await loadData(search.value);
    }
};

const updateFormData = (newData) => {
    Object.keys(newData).forEach(key => {
        if (form.hasOwnProperty(key)) {
            form[key] = newData[key];
        }
    });
};

// Export handlers
const handleExportCSV = () => {
    exportToCSV(rows.value, columns.value, props.title.toLowerCase());
};

const handleExportExcel = () => {
    exportToExcel(rows.value, columns.value, props.title.toLowerCase());
};

const handleExportPDF = async () => {
    try {
        await exportToPDF(columns.value, props.title.toLowerCase());
    } catch (error) {
        console.error('PDF Export failed:', error);
    }
};

// Initialize on mount
onMounted(async () => {
    initializeConfiguration();
    await fetchCurrentUser();
    await loadData();
});
</script>

