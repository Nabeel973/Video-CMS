<template>
    <div class="panel">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-5">
            <h5 class="font-semibold text-lg dark:text-white-light">{{ title }}</h5>
            <button type="button" class="btn btn-primary" @click="openModal()">
                <span class="flex items-center">
                    <svg class="w-5 h-5 ltr:mr-2 rtl:ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" fill="none">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Add New
                </span>
            </button>
        </div>

        <!-- Datatable -->
        <div class="datatables">
            <table :id="tableId" class="whitespace-nowrap"></table>
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
import axios from 'axios';
import { DataTable } from 'simple-datatables';
import Swal from 'sweetalert2';
import { onMounted, reactive, ref } from 'vue';

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
const dataTable = ref(null);
const form = reactive({
    id: null,
    name: '',
    status: 'active'
});

// Initialize DataTable
const initDataTable = () => {
    const data = {
        headings: ['ID', 'Name', 'Status', 'Created By', 'Updated By', 'Created At', 'Actions'],
        data: []
    };

    const config = {
        searchable: true,
        sortable: true,
        perPage: 10,
        perPageSelect: [10, 20, 30, 50],
        columns: [
            { select: 0, sort: 'asc' },
            { select: 6, sortable: false }
        ],
        labels: {
            searchTitle: `Search ${props.title}`,
            placeholder: 'Search...',
            perPage: `${props.title} per page`,
            noRows: `No ${props.title.toLowerCase()} found`,
            info: `Showing {start} to {end} of {rows} ${props.title.toLowerCase()}`
        },
        exportable: {
            type: ['csv', 'txt', 'json'],
            download: true,
            position: 'top'
        }
    };

    dataTable.value = new DataTable(`#${props.tableId}`, { ...config, data });
    fetchData();
};

// Fetch Data
const fetchData = async () => {
    try {
        const response = await axios.get(`/${props.endpoint}`);
        
        // Check if response has data property
        if (!response.data || !response.data.data) {
            throw new Error('Invalid response format');
        }

        const formattedData = response.data.data.map(item => [
            item.id,
            item.name,
            `<span class="badge ${item.status === 'active' ? 'badge-outline-success' : 'badge-outline-danger'}">${item.status.charAt(0).toUpperCase() + item.status.slice(1)}</span>`,
            item.created_by?.name || 'N/A',
            item.updated_by?.name || 'N/A',
            new Date(item.created_at).toLocaleDateString(),
            `<div class="flex items-center gap-2">
                <button class="btn btn-sm btn-outline-primary" onclick="editItem('${props.endpoint}', ${item.id})">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" fill="none">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="deleteItem('${props.endpoint}', ${item.id})">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" fill="none">
                        <path d="M3 6h18"></path>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                </button>
            </div>`
        ]);

        dataTable.value.insert({ data: formattedData });
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
        
        await axios[method](url, form);
        closeModal();
        fetchData();
        
        // Show success message
        Swal.fire({
            title: 'Success!',
            text: `${props.singularTitle} ${form.id ? 'updated' : 'created'} successfully.`,
            icon: 'success',
            confirmButtonText: 'OK'
        });
    } catch (error) {
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors;
        } else {
            Swal.fire({
                title: 'Error!',
                text: `Failed to ${form.id ? 'update' : 'create'} ${props.singularTitle.toLowerCase()}. Please try again.`,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    } finally {
        loading.value = false;
    }
};

// Delete Handler
const deleteItem = async (endpoint, id) => {
    // Show confirmation dialog
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
            await axios.delete(`/${endpoint}/${id}`);
            fetchData();
            
            // Show success message
            Swal.fire({
                title: 'Deleted!',
                text: `${props.singularTitle} has been deleted.`,
                icon: 'success',
                confirmButtonText: 'OK'
            });
        } catch (error) {
            console.error(`Error deleting ${props.singularTitle.toLowerCase()}:`, error);
            Swal.fire({
                title: 'Error!',
                text: `Failed to delete ${props.singularTitle.toLowerCase()}. Please try again.`,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    }
};

// Initialize on mount
onMounted(() => {
    initDataTable();
    
    // Make functions available globally for the inline button handlers
    window.editItem = (endpoint, id) => {
        axios.get(`/${endpoint}/${id}`).then(response => {
            openModal(response.data.data);
        }).catch(error => {
            Swal.fire({
                title: 'Error!',
                text: `Failed to fetch ${props.singularTitle.toLowerCase()} details. Please try again.`,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
    };
    
    window.deleteItem = deleteItem;
});
</script>

<style lang="scss">
.datatables {
    .simple-datatables {
        background-color: white;
        border-radius: 6px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        
        .dataTable-wrapper {
            .dataTable-top {
                padding: 1rem;
                border-bottom: 1px solid #e5e7eb;
            }
            
            .dataTable-container {
                padding: 1rem;
            }
            
            .dataTable-bottom {
                padding: 1rem;
                border-top: 1px solid #e5e7eb;
            }
        }
        
        .dataTable-table {
            width: 100%;
            border-collapse: collapse;
            
            th {
                padding: 0.75rem;
                text-align: left;
                font-weight: 600;
                background-color: #f9fafb;
                border-bottom: 1px solid #e5e7eb;
            }
            
            td {
                padding: 0.75rem;
                border-bottom: 1px solid #e5e7eb;
            }
        }
        
        .dataTable-pagination {
            a {
                padding: 0.5rem 0.75rem;
                margin: 0 0.25rem;
                border-radius: 0.375rem;
                color: #374151;
                
                &:hover {
                    background-color: #f3f4f6;
                }
                
                &.active {
                    background-color: #4f46e5;
                    color: white;
                }
            }
        }
    }
}

.dark {
    .datatables {
        .simple-datatables {
            background-color: #1e293b;
            
            .dataTable-wrapper {
                .dataTable-top,
                .dataTable-bottom {
                    border-color: #374151;
                }
            }
            
            .dataTable-table {
                th {
                    background-color: #1e293b;
                    border-color: #374151;
                    color: #e5e7eb;
                }
                
                td {
                    border-color: #374151;
                    color: #e5e7eb;
                }
            }
            
            .dataTable-pagination {
                a {
                    color: #e5e7eb;
                    
                    &:hover {
                        background-color: #374151;
                    }
                }
            }
        }
    }
}
</style> 