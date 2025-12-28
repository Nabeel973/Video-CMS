<template>
    <!-- <div>
        <ul class="flex space-x-2 rtl:space-x-reverse">
            <li>
                <a href="javascript:;" class="text-primary hover:underline">Dashboard</a>
            </li>
        </ul>
    </div> -->
    <div class="pt-5">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-6 text-white">
            <!-- Total Movies -->
            <div class="panel bg-gradient-to-r from-cyan-500 to-cyan-400">
                <div class="flex justify-between">
                    <div class="ltr:mr-1 rtl:ml-1 text-md font-semibold">Total Movies</div>
                    <router-link to="/admin/movies" class="text-white hover:opacity-80">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </router-link>
                </div>
                <div class="flex items-center mt-5">
                    <div class="text-3xl font-bold ltr:mr-3 rtl:ml-3">{{ stats.total_movies || 0 }}</div>
                </div>
                <div class="flex items-center mt-2 text-sm opacity-90">
                    <span class="ltr:mr-2 rtl:ml-2">{{ stats.active_movies || 0 }} Active</span>
                    <span class="ltr:ml-2 rtl:mr-2">{{ stats.inactive_movies || 0 }} Inactive</span>
                </div>
            </div>

            <!-- Total Users -->
            <div class="panel bg-gradient-to-r from-violet-500 to-violet-400">
                <div class="flex justify-between">
                    <div class="ltr:mr-1 rtl:ml-1 text-md font-semibold">Total Users</div>
                    <router-link to="/user-management" class="text-white hover:opacity-80">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </router-link>
                </div>
                <div class="flex items-center mt-5">
                    <div class="text-3xl font-bold ltr:mr-3 rtl:ml-3">{{ stats.total_users || 0 }}</div>
                </div>
            </div>

            <!-- Total Genres -->
            <div class="panel bg-gradient-to-r from-blue-500 to-blue-400">
                <div class="flex justify-between">
                    <div class="ltr:mr-1 rtl:ml-1 text-md font-semibold">Total Genres</div>
                    <router-link to="/admin/genres" class="text-white hover:opacity-80">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </router-link>
                </div>
                <div class="flex items-center mt-5">
                    <div class="text-3xl font-bold ltr:mr-3 rtl:ml-3">{{ stats.total_genres || 0 }}</div>
                </div>
            </div>

            <!-- Total Categories -->
            <div class="panel bg-gradient-to-r from-fuchsia-500 to-fuchsia-400">
                <div class="flex justify-between">
                    <div class="ltr:mr-1 rtl:ml-1 text-md font-semibold">Total Categories</div>
                    <router-link to="/admin/categories" class="text-white hover:opacity-80">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </router-link>
                </div>
                <div class="flex items-center mt-5">
                    <div class="text-3xl font-bold ltr:mr-3 rtl:ml-3">{{ stats.total_categories || 0 }}</div>
                </div>
            </div>
        </div>

        <!-- Additional Stats Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
            <!-- Tags -->
            <div class="panel">
                <div class="flex items-center justify-between mb-3">
                    <h5 class="font-semibold text-base">Total Tags</h5>
                    <router-link to="/admin/tags" class="text-primary hover:underline text-sm">View All</router-link>
                </div>
                <div class="text-2xl font-bold">{{ stats.total_tags || 0 }}</div>
            </div>

            <!-- Advertisements -->
            <div class="panel">
                <div class="flex items-center justify-between mb-3">
                    <h5 class="font-semibold text-base">Advertisements</h5>
                    <router-link to="/admin/advertisements" class="text-primary hover:underline text-sm">View All</router-link>
                </div>
                <div class="text-2xl font-bold">{{ stats.total_advertisements || 0 }}</div>
            </div>

            <!-- Recent Movies (Last 7 Days) -->
            <div class="panel">
                <div class="flex items-center justify-between mb-3">
                    <h5 class="font-semibold text-base">New This Week</h5>
                </div>
                <div class="text-2xl font-bold text-success">{{ stats.recent_movies_count || 0 }}</div>
                <p class="text-sm text-gray-500 mt-1">Movies added in last 7 days</p>
            </div>

            <!-- Movies Status -->
            <div class="panel">
                <div class="flex items-center justify-between mb-3">
                    <h5 class="font-semibold text-base">Movies Status</h5>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm">Active</span>
                        <span class="font-semibold text-success">{{ stats.movies_by_status?.active || 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm">Inactive</span>
                        <span class="font-semibold text-danger">{{ stats.movies_by_status?.inactive || 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Recent Movies -->
            <div class="panel lg:col-span-2">
                <div class="flex items-center justify-between mb-5">
                    <h5 class="font-semibold text-lg dark:text-white-light">Recent Movies</h5>
                    <router-link to="/admin/movies" class="text-primary hover:underline text-sm">View All</router-link>
                </div>
                <div class="space-y-4">
                    <div v-if="loading" class="text-center py-8">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                    </div>
                    <div v-else-if="recentMovies.length === 0" class="text-center py-8 text-gray-500">
                        No movies found
                    </div>
                    <div v-else v-for="movie in recentMovies" :key="movie.id" class="flex items-center gap-4 p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <div v-if="movie.image" class="w-16 h-16 rounded-lg overflow-hidden flex-shrink-0">
                            <img :src="`/storage/${movie.image}`" :alt="movie.name" class="w-full h-full object-cover">
                        </div>
                        <div v-else class="w-16 h-16 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h6 class="font-semibold text-base truncate">{{ movie.name }}</h6>
                            <div class="flex items-center gap-2 mt-1 text-sm text-gray-500 dark:text-gray-400">
                                <span v-if="movie.genre">{{ movie.genre }}</span>
                                <span v-if="movie.category">• {{ movie.category }}</span>
                                <span v-if="movie.release">• {{ movie.release }}</span>
                            </div>
                            <div v-if="movie.tags && movie.tags.length > 0" class="flex flex-wrap gap-1 mt-2">
                                <span v-for="tag in movie.tags.slice(0, 2)" :key="tag" class="text-xs px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded">{{ tag }}</span>
                            </div>
                        </div>
                        <span :class="[
                            'badge',
                            movie.status === 'active' ? 'bg-success' : 'bg-danger'
                        ]">
                            {{ movie.status }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="panel">
                <div class="flex items-center justify-between mb-5">
                    <h5 class="font-semibold text-lg dark:text-white-light">Quick Actions</h5>
                </div>
                <div class="space-y-3">
                    <router-link to="/admin/movies" class="flex items-center gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <span>Manage Movies</span>
                    </router-link>
                    <router-link to="/admin/genres" class="flex items-center gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <span>Manage Genres</span>
                    </router-link>
                    <router-link to="/admin/categories" class="flex items-center gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span>Manage Categories</span>
                    </router-link>
                    <router-link to="/admin/tags" class="flex items-center gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <span>Manage Tags</span>
                    </router-link>
                    <router-link to="/user-management" class="flex items-center gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Manage Users</span>
                    </router-link>
                </div>
            </div>
        </div>

        <!-- Distribution Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top Categories -->
            <div class="panel">
                <div class="flex items-center justify-between mb-5">
                    <h5 class="font-semibold text-lg dark:text-white-light">Movies by Category</h5>
                </div>
                <div v-if="stats.top_categories && stats.top_categories.length > 0" class="space-y-3">
                    <div v-for="category in stats.top_categories" :key="category.name" class="flex items-center justify-between">
                        <span class="text-sm font-medium">{{ category.name }}</span>
                        <div class="flex items-center gap-3">
                            <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div 
                                    class="bg-primary h-2 rounded-full" 
                                    :style="{ width: `${(category.count / (stats.total_movies || 1)) * 100}%` }"
                                ></div>
                            </div>
                            <span class="text-sm font-semibold w-8 text-right">{{ category.count }}</span>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center py-8 text-gray-500">No data available</div>
            </div>

            <!-- Top Genres -->
            <div class="panel">
                <div class="flex items-center justify-between mb-5">
                    <h5 class="font-semibold text-lg dark:text-white-light">Movies by Genre</h5>
                </div>
                <div v-if="stats.top_genres && stats.top_genres.length > 0" class="space-y-3">
                    <div v-for="genre in stats.top_genres" :key="genre.name" class="flex items-center justify-between">
                        <span class="text-sm font-medium">{{ genre.name }}</span>
                        <div class="flex items-center gap-3">
                            <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div 
                                    class="bg-success h-2 rounded-full" 
                                    :style="{ width: `${(genre.count / (stats.total_movies || 1)) * 100}%` }"
                                ></div>
                            </div>
                            <span class="text-sm font-semibold w-8 text-right">{{ genre.count }}</span>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center py-8 text-gray-500">No data available</div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { useMeta } from '@/composables/use-meta';
import { useAppStore } from '@/stores/index';
import axios from 'axios';
import { onMounted, ref } from 'vue';

useMeta({ title: 'Dashboard' });

const store = useAppStore();

const loading = ref(true);
const stats = ref({
    total_movies: 0,
    total_users: 0,
    total_genres: 0,
    total_categories: 0,
    total_tags: 0,
    total_advertisements: 0,
    active_movies: 0,
    inactive_movies: 0,
    recent_movies_count: 0,
    movies_by_status: {
        active: 0,
        inactive: 0
    },
    top_categories: [],
    top_genres: []
});

const recentMovies = ref([]);

const fetchDashboardStats = async () => {
    try {
        loading.value = true;
        const response = await axios.get('/dashboard/stats');
        
        if (response.data && response.data.success && response.data.data) {
            stats.value = response.data.data;
            recentMovies.value = response.data.data.recent_movies || [];
        }
    } catch (error) {
        console.error('Error fetching dashboard stats:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchDashboardStats();
});
</script>

