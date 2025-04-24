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
                <div class="dropdown">
                    <Popper :placement="store.rtlClass === 'rtl' ? 'bottom-end' : 'bottom-start'" offsetDistance="0" class="align-middle">
                        <button
                            type="button"
                            class="btn btn-outline-primary"
                        >
                            <span class="flex items-center">
                                <svg class="w-4 h-4 ltr:mr-2 rtl:ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" fill="none">
                                    <path d="M19 9L12 15L5 9"></path>
                                </svg>
                                Export
                            </span>
                        </button>
                        <template #content>
                            <ul class="whitespace-nowrap">
                                <li>
                                    <button type="button" class="flex w-full items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600" @click="exportToCSV">
                                        <svg class="w-4 h-4 ltr:mr-2 rtl:ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" fill="none">
                                            <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                            <path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2z"></path>
                                            <line x1="12" y1="11" x2="12" y2="17"></line>
                                            <line x1="9" y1="14" x2="15" y2="14"></line>
                                        </svg>
                                        CSV
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="flex w-full items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600" @click="exportToExcel">
                                        <svg class="w-4 h-4 ltr:mr-2 rtl:ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" fill="none">
                                            <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                            <path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2z"></path>
                                            <path d="M8 11h8"></path>
                                            <path d="M8 15h8"></path>
                                            <path d="M8 19h8"></path>
                                        </svg>
                                        Excel
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="flex w-full items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600" @click="exportToPDF">
                                        <svg class="w-4 h-4 ltr:mr-2 rtl:ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" fill="none">
                                            <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                            <path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2z"></path>
                                            <path d="M5 12v7"></path>
                                            <path d="M12 12v7"></path>
                                            <path d="M19 12v7"></path>
                                        </svg>
                                        PDF
                                    </button>
                                </li>
                            </ul>
                        </template>
                    </Popper>
                </div>
            </div>
        </div>

        <!-- Search -->
        <div class="flex md:items-center md:flex-row flex-col mb-5 gap-5">
            <div class="flex items-center gap-5">
                <div>
                    <input v-model="search" type="text" class="form-input" placeholder="Search..." />
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
                :search="search"
                skin="whitespace-nowrap bh-table-hover"
                firstArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M13 19L7 12L13 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path opacity="0.5" d="M16.9998 19L10.9998 12L16.9998 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>'
                lastArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M11 19L17 12L11 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path opacity="0.5" d="M6.99976 19L12.9998 12L6.99976 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>'
                previousArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M15 5L9 12L15 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>'
                nextArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>'
            >
                <template #status="data">
                    <span class="badge" :class="data.value.status === 'active' ? 'badge badge-outline-success' : 'badge badge-outline-danger'">
                        {{ data.value.status.toUpperCase() }}
                    </span>
                </template>
                <template #created_at="data">
                    {{ formatDate(data.value.created_at) }}
                </template>
                <template #actions="data">
                    <div class="flex items-center gap-2">
                        <button class="btn btn-sm btn-outline-primary" @click="editItem(data.value)">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" fill="none">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" @click="deleteItem(data.value)">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" fill="none">
                                <path d="M3 6h18"></path>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                        </button>
                    </div>
                </template>
            </vue3-datatable>
        </div>

        <!-- Modal -->
        <div class="fixed inset-0 bg-black/60 z-[999] hidden" :class="modal && '!block'">
            <div class="flex items-center justify-center min-h-screen px-4" @click.self="closeModal">
                <div class="panel border-0 p-0 rounded-lg overflow-hidden w-full max-w-lg">
                    <div class="flex bg-[#fbfbfb] dark:bg-[#121c2c] items-center justify-between px-5 py-3">
                        <h5 class="font-bold text-lg">{{ isEdit ? `Edit ${singularTitle}` : `Add ${singularTitle}` }}</h5>
                        <button type="button" class="hover:opacity-80" @click="closeModal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    <div class="p-5">
                        <form @submit.prevent="handleSubmit">
                            <div class="mb-5">
                                <label for="name">Name</label>
                                <input id="name" type="text" v-model="form.name" class="form-input" :placeholder="`Enter ${singularTitle.toLowerCase()} name`" :class="{'border-red-500': errors.name}" />
                                <span class="text-red-500 text-sm" v-if="errors.name">{{ errors.name[0] }}</span>
                            </div>

                            <div class="mb-5">
                                <label for="status">Status</label>
                                <select id="status" v-model="form.status" class="form-select" :class="{'border-red-500': errors.status}">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <span class="text-red-500 text-sm" v-if="errors.status">{{ errors.status[0] }}</span>
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
    </div>
</template>

<script setup>
import { useAppStore } from '@/stores/index';
import Vue3Datatable from '@bhplugin/vue3-datatable';
import axios from 'axios';
import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';
import Swal from 'sweetalert2';
import { onMounted, reactive, ref } from 'vue';
import * as XLSX from 'xlsx';

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

// State
const modal = ref(false);
const isEdit = ref(false);
const loading = ref(false);
const errors = ref({});
const search = ref('');
const rows = ref([]);
const totalRows = ref(0);

const columns = ref([
    { field: 'id', title: 'ID', isUnique: true, hide: false },
    { field: 'name', title: 'Name', hide: false },
    { field: 'status', title: 'Status', hide: false },
    { field: 'created_by', title: 'Created By', hide: false },
    { field: 'updated_by', title: 'Updated By', hide: false },
    { field: 'created_at', title: 'Created At', hide: false },
    { field: 'actions', title: 'Actions', sortable: false, hide: false }
]);

const form = reactive({
    id: null,
    name: '',
    status: 'active'
});

// Fetch Data
const fetchData = async () => {
    try {
        const response = await axios.get(`/${props.endpoint}`);
        
        if (!response.data || !response.data.data) {
            throw new Error('Invalid response format');
        }

        rows.value = response.data.data.map(item => ({
            id: item.id,
            name: item.name,
            status: item.status || 'inactive',
            created_by: item.created_by?.name || 'N/A',
            updated_by: item.updated_by?.name || 'N/A',
            created_at: item.created_at,
            actions: item.id
        }));
        totalRows.value = rows.value.length;
    } catch (error) {
        console.error(`Error fetching ${props.title.toLowerCase()}:`, error);
        Swal.fire({
            title: 'Error!',
            text: `Failed to fetch ${props.title.toLowerCase()}. Please try again.`,
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
};

// Modal Handlers
const openModal = (itemData = null) => {
    if (itemData) {
        isEdit.value = true;
        form.id = itemData.id;
        form.name = itemData.name;
        form.status = itemData.status;
    } else {
        isEdit.value = false;
        form.id = null;
        form.name = '';
        form.status = 'active';
    }
    modal.value = true;
};

const closeModal = () => {
    modal.value = false;
    errors.value = {};
    form.id = null;
    form.name = '';
    form.status = 'active';
};

// Form Submission
const handleSubmit = async () => {
    loading.value = true;
    errors.value = {};

    try {
        const url = form.id ? `/${props.endpoint}/${form.id}` : `/${props.endpoint}`;
        const method = form.id ? 'put' : 'post';
        
        const response = await axios[method](url, form);
        
        if (response.data && response.data.message) {
            closeModal();
            fetchData();
            
            Swal.fire({
                title: 'Success!',
                text: response.data.message,
                icon: 'success',
                confirmButtonText: 'OK'
            });
        }
    } catch (error) {
        if (error.response?.data?.errors) {
            // Handle Laravel validation errors
            errors.value = error.response.data.errors;
        } else if (error.response?.data?.error) {
            // Handle single error message
            errors.value = {
                name: [error.response.data.error]
            };
        } else {
            // Handle other errors
            Swal.fire({
                title: 'Error!',
                text: error.response?.data?.message || `Failed to ${form.id ? 'update' : 'create'} ${props.singularTitle.toLowerCase()}. Please try again.`,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    } finally {
        loading.value = false;
    }
};

// Delete Handler
const deleteItem = async (id) => {
    // Extract the numeric ID if it's an object
    const itemId = typeof id === 'object' ? id.id : id;

    const result = await Swal.fire({
        title: 'Are you sure?',
        text: `This ${props.singularTitle.toLowerCase()} will be permanently deleted!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    });

    if (result.isConfirmed) {
        try {
            const response = await axios.delete(`/${props.endpoint}/${itemId}`);
            
            if (response.data && response.data.message) {
                await fetchData();
                
                Swal.fire({
                    title: 'Deleted!',
                    text: response.data.message || `${props.singularTitle} has been deleted.`,
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            } else {
                throw new Error('Invalid response format');
            }
        } catch (error) {
            console.error(`Error deleting ${props.singularTitle.toLowerCase()}:`, error);
            Swal.fire({
                title: 'Error!',
                text: error.response?.data?.message || `Failed to delete ${props.singularTitle.toLowerCase()}. Please try again.`,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    }
};

// Edit Handler
const editItem = async (id) => {
    try {
        // Extract the numeric ID if it's an object
        const itemId = typeof id === 'object' ? id.id : id;
        
        const response = await axios.get(`/${props.endpoint}/${itemId}`);
        if (response.data && response.data.data) {
            openModal(response.data.data);
        } else {
            throw new Error('Invalid response format');
        }
    } catch (error) {
        console.error(`Error fetching ${props.singularTitle.toLowerCase()}:`, error);
        Swal.fire({
            title: 'Error!',
            text: `Failed to fetch ${props.singularTitle.toLowerCase()} details. Please try again.`,
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
};

// Add formatDate function
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return 'N/A';
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch (error) {
        return 'N/A';
    }
};

// Export functions
const exportToCSV = () => {
    const exportColumns = columns.value.filter(col => !col.hide && col.field !== 'actions');
    const csvContent = "data:text/csv;charset=utf-8," 
        + exportColumns.map(col => col.title).join(",") + "\n"
        + rows.value.map(row => 
            exportColumns
                .map(col => {
                    let value = row[col.field];
                    if (col.field === 'created_at') {
                        value = formatDate(value);
                    } else if (col.field === 'status') {
                        value = String(value).toUpperCase();
                    }
                    return `"${value}"`;
                })
                .join(",")
        ).join("\n");
    
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", `${props.title.toLowerCase()}_${new Date().toISOString().split('T')[0]}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};

const exportToExcel = () => {
    const exportColumns = columns.value.filter(col => !col.hide && col.field !== 'actions');
    const worksheet = XLSX.utils.json_to_sheet(
        rows.value.map(row => {
            const newRow = {};
            exportColumns.forEach(col => {
                let value = row[col.field];
                if (col.field === 'created_at') {
                    value = formatDate(value);
                } else if (col.field === 'status') {
                    value = String(value).toUpperCase();
                }
                newRow[col.title] = value;
            });
            return newRow;
        })
    );
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");
    XLSX.writeFile(workbook, `${props.title.toLowerCase()}_${new Date().toISOString().split('T')[0]}.xlsx`);
};

const exportToPDF = async () => {
    try {
        // Get the original table
        const originalTable = document.querySelector('.datatable table');
        if (!originalTable) {
            throw new Error('Table not found');
        }

        // Create a new div to hold our temporary table
        const container = document.createElement('div');
        container.style.position = 'absolute';
        container.style.left = '-9999px';
        container.style.top = '-9999px';
        document.body.appendChild(container);

        // Clone the table and remove action column
        const tempTable = originalTable.cloneNode(true);
        const actionColumnIndex = columns.value.findIndex(col => col.field === 'actions');

        if (actionColumnIndex !== -1) {
            const rows = tempTable.querySelectorAll('tr');
            rows.forEach(row => {
                const cells = row.querySelectorAll('th, td');
                if (cells[actionColumnIndex]) {
                    cells[actionColumnIndex].remove();
                }
            });
        }

        // Add the temporary table to the container
        container.appendChild(tempTable);

        // Generate PDF
        const doc = new jsPDF('l', 'mm', 'a4'); // 'l' for landscape
        const canvas = await html2canvas(tempTable, {
            scale: 2,
            logging: false,
            useCORS: true,
            allowTaint: true
        });

        // Calculate dimensions
        const imgData = canvas.toDataURL('image/png');
        const pageWidth = doc.internal.pageSize.getWidth();
        const pageHeight = doc.internal.pageSize.getHeight();
        const imgWidth = pageWidth - 20; // 10mm margin on each side
        const imgHeight = (canvas.height * imgWidth) / canvas.width;

        // Add image to PDF
        doc.addImage(imgData, 'PNG', 10, 10, imgWidth, imgHeight);

        // Save PDF
        doc.save(`${props.title.toLowerCase()}_${new Date().toISOString().split('T')[0]}.pdf`);

        // Clean up
        document.body.removeChild(container);
    } catch (error) {
        console.error('PDF Export Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to export PDF. Please try again.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
};

// Initialize on mount
onMounted(() => {
    fetchData();
});
</script>

<style lang="scss">
.datatable {
    .bh-table-responsive {
        @apply rounded-none;
    }

    .bh-table-responsive table thead tr th {
        @apply font-bold;
    }

    .bh-pagination {
        @apply font-medium;
    }

    .bh-pagination .bh-page-item {
        @apply w-9 h-9 bg-white-light text-dark border-white-light dark:border-[#191e3a] dark:bg-[#191e3a] dark:text-white-light hover:bg-primary hover:text-white dark:hover:bg-primary dark:hover:text-white;
    }

    .bh-pagination .bh-page-item.bh-active {
        @apply bg-primary text-white dark:bg-primary dark:text-white;
    }

    .bh-pagination select {
        @apply rounded-md border border-[#e0e6ed] bg-white pl-2 pr-4 py-1.5 text-sm font-semibold text-black focus:border-primary focus:ring-transparent dark:border-[#17263c] dark:bg-[#121e32] dark:text-white-dark dark:focus:border-primary;
    }

    .bh-pagination .bh-pagination-number {
        @apply rtl:!ml-0 rtl:sm:mr-auto rtl:space-x-reverse;
    }

    .bh-pagination .bh-pagination-info > span {
        @apply rtl:mr-0 rtl:ml-2;
    }

    .bh-filter div button {
        @apply block;
    }

    .bh-sort svg polygon {
        @apply dark:text-dark;
    }

    .bh-filter .bh-form-control {
        @apply dark:!border-[#17263c] dark:!bg-[#121e32] dark:!text-white-dark dark:focus:!ring-transparent;
    }

    .bh-filter > button {
        @apply dark:border-dark dark:bg-dark dark:text-white-dark dark:hover:text-white-light;
    }

    .bh-filter-menu button {
        @apply dark:bg-[#1b2e4b] dark:hover:bg-[#181f32] dark:hover:text-white-dark;
    }

    .bh-filter-menu button.active {
        @apply dark:bg-[#181f32];
    }
}
</style> 