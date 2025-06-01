<template>
    <div class="dropdown">
        <Popper :placement="store.rtlClass === 'rtl' ? 'bottom-end' : 'bottom-start'" offsetDistance="0" class="align-middle">
            <button type="button" class="btn btn-outline-primary">
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
                        <button type="button" class="flex w-full items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600" @click="$emit('export-csv')">
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
                        <button type="button" class="flex w-full items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600" @click="$emit('export-excel')">
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
                        <button type="button" class="flex w-full items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600" @click="$emit('export-pdf')">
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
</template>

<script setup>
import { useAppStore } from '@/stores/index';

const store = useAppStore();

defineEmits(['export-csv', 'export-excel', 'export-pdf']);
</script>