<template>
  <!-- Cinema Navbar Component - Sticky navigation with search -->
  <header
    class="cinema-navbar fixed left-0 right-0 top-0 z-50 transition-all duration-300"
    :class="[
      isScrolled ? 'bg-cinema-bg/95 backdrop-blur-md shadow-lg' : 'bg-gradient-to-b from-black/80 to-transparent',
    ]"
  >
    <div class="cinema-container">
      <nav class="flex h-16 items-center justify-between md:h-20">
        <!-- Logo -->
        <router-link
          to="/movies"
          class="flex items-center gap-2 text-xl font-bold tracking-tight text-white transition-opacity hover:opacity-80"
        >
          <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-cinema-accent">
            <svg class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="currentColor">
              <path d="M18 4l2 4h-3l-2-4h-2l2 4h-3l-2-4H8l2 4H7L5 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4h-4z" />
            </svg>
          </div>
          <span class="hidden sm:block">
            <span class="text-cinema-accent">Vistro</span>Video
          </span>
        </router-link>

        <!-- Desktop Navigation -->
        <div class="hidden items-center gap-8 lg:flex">
          <router-link
            v-for="link in navLinks"
            :key="link.path"
            :to="link.path"
            class="nav-link text-sm font-medium text-white/70 transition-colors hover:text-white"
            :class="{ 'text-cinema-accent!': isActiveRoute(link.path) }"
          >
            {{ link.label }}
          </router-link>
        </div>

        <!-- Search Bar -->
        <div class="hidden flex-1 items-center justify-center px-8 md:flex">
          <div class="relative w-full max-w-md">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search movies, series, genres..."
              class="search-input w-full rounded-full bg-white/10 py-2.5 pl-11 pr-4 text-sm text-white placeholder-white/40 outline-none ring-1 ring-white/10 transition-all focus:bg-white/15 focus:ring-cinema-accent/50"
              @focus="isSearchFocused = true"
              @blur="handleSearchBlur"
              @input="handleSearch"
              @keydown.enter="submitSearch"
            />
            <svg
              class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-white/40"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <circle cx="11" cy="11" r="8" />
              <path d="M21 21l-4.35-4.35" />
            </svg>

            <!-- Search Results Dropdown -->
            <div
              v-show="isSearchFocused && searchQuery && searchResults.length > 0"
              class="absolute left-0 right-0 top-full mt-2 max-h-80 overflow-y-auto rounded-xl bg-cinema-bg-elevated/95 p-2 shadow-xl backdrop-blur-md ring-1 ring-white/10"
            >
              <router-link
                v-for="movie in searchResults"
                :key="movie.id"
                :to="{ name: 'movie-details', params: { id: movie.id } }"
                class="flex items-center gap-3 rounded-lg p-2 transition-colors hover:bg-white/5"
                @click="clearSearch"
              >
                <img
                  :src="movie.image"
                  :alt="movie.name"
                  class="h-12 w-8 rounded object-cover"
                />
                <div class="flex-1 overflow-hidden">
                  <p class="truncate text-sm font-medium text-white">{{ movie.name }}</p>
                  <p class="text-xs text-white/50">{{ movie.genre.name }} • {{ movie.release }}</p>
                </div>
                <div class="flex items-center gap-1 text-cinema-secondary">
                  <svg class="h-3 w-3" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                  </svg>
                  <span class="text-xs">{{ movie.rating }}</span>
                </div>
              </router-link>
            </div>
          </div>
        </div>

        <!-- Right Section -->
        <div class="flex items-center gap-3">
          <!-- Mobile Search Toggle -->
          <button
            class="flex h-10 w-10 items-center justify-center rounded-full text-white/70 transition-colors hover:bg-white/10 hover:text-white md:hidden"
            @click="toggleMobileSearch"
          >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8" />
              <path d="M21 21l-4.35-4.35" />
            </svg>
          </button>

          <!-- Categories Dropdown -->
          <div class="relative hidden md:block">
            <button
              class="flex items-center gap-1.5 text-sm font-medium text-white/70 transition-colors hover:text-white"
              @click="showCategoriesMenu = !showCategoriesMenu"
            >
              Browse
              <svg
                class="h-4 w-4 transition-transform"
                :class="{ 'rotate-180': showCategoriesMenu }"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <!-- Categories Dropdown Menu -->
            <div
              v-show="showCategoriesMenu"
              class="absolute right-0 top-full mt-2 w-48 rounded-xl bg-cinema-bg-elevated/95 p-2 shadow-xl backdrop-blur-md ring-1 ring-white/10"
            >
              <router-link
                v-for="genre in genres"
                :key="genre.id"
                :to="{ name: 'movies', query: { genre: genre.id } }"
                class="block rounded-lg px-4 py-2 text-sm text-white/70 transition-colors hover:bg-white/5 hover:text-white"
                @click="showCategoriesMenu = false"
              >
                {{ genre.name }}
              </router-link>
            </div>
          </div>

          <!-- User Menu / Login Button -->
          <router-link
            v-if="!isLoggedIn"
            to="/auth/boxed-signin"
            class="cinema-btn cinema-btn-primary hidden py-2 text-xs sm:flex"
          >
            Sign In
          </router-link>

          <div v-else class="relative">
            <button
              class="flex h-10 w-10 items-center justify-center rounded-full bg-cinema-accent text-white transition-opacity hover:opacity-80"
              @click="showUserMenu = !showUserMenu"
            >
              <span class="text-sm font-semibold">{{ userInitials }}</span>
            </button>

            <!-- User Dropdown Menu -->
            <div
              v-show="showUserMenu"
              class="absolute right-0 top-full mt-2 w-48 rounded-xl bg-cinema-bg-elevated/95 p-2 shadow-xl backdrop-blur-md ring-1 ring-white/10"
            >
              <router-link
                to="/dashboard"
                class="flex items-center gap-3 rounded-lg px-4 py-2 text-sm text-white/70 transition-colors hover:bg-white/5 hover:text-white"
                @click="showUserMenu = false"
              >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="3" width="7" height="7" />
                  <rect x="14" y="3" width="7" height="7" />
                  <rect x="3" y="14" width="7" height="7" />
                  <rect x="14" y="14" width="7" height="7" />
                </svg>
                Dashboard
              </router-link>
              <button
                class="flex w-full items-center gap-3 rounded-lg px-4 py-2 text-sm text-white/70 transition-colors hover:bg-white/5 hover:text-white"
                @click="handleLogout"
              >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9" />
                </svg>
                Sign Out
              </button>
            </div>
          </div>

          <!-- Mobile Menu Toggle -->
          <button
            class="flex h-10 w-10 items-center justify-center rounded-full text-white/70 transition-colors hover:bg-white/10 hover:text-white lg:hidden"
            @click="showMobileMenu = !showMobileMenu"
          >
            <svg v-if="!showMobileMenu" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg v-else class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 6L6 18M6 6l12 12" />
            </svg>
          </button>
        </div>
      </nav>

      <!-- Mobile Search Bar -->
      <div
        v-show="showMobileSearch"
        class="border-t border-white/10 py-3 md:hidden"
      >
        <div class="relative">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search movies..."
            class="w-full rounded-lg bg-white/10 py-2.5 pl-10 pr-4 text-sm text-white placeholder-white/40 outline-none focus:ring-1 focus:ring-cinema-accent/50"
            @input="handleSearch"
            @keydown.enter="submitSearch"
          />
          <svg
            class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-white/40"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <circle cx="11" cy="11" r="8" />
            <path d="M21 21l-4.35-4.35" />
          </svg>
        </div>
      </div>
    </div>

    <!-- Mobile Menu -->
    <div
      v-show="showMobileMenu"
      class="border-t border-white/10 bg-cinema-bg/95 backdrop-blur-md lg:hidden"
    >
      <div class="cinema-container py-4">
        <router-link
          v-for="link in navLinks"
          :key="link.path"
          :to="link.path"
          class="block py-3 text-sm font-medium text-white/70 transition-colors hover:text-white"
          :class="{ 'text-cinema-accent!': isActiveRoute(link.path) }"
          @click="showMobileMenu = false"
        >
          {{ link.label }}
        </router-link>
        
        <div class="mt-4 border-t border-white/10 pt-4">
          <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-white/40">Genres</p>
          <div class="flex flex-wrap gap-2">
            <router-link
              v-for="genre in genres"
              :key="genre.id"
              :to="{ name: 'movies', query: { genre: genre.id } }"
              class="rounded-full bg-white/5 px-3 py-1.5 text-xs text-white/70 transition-colors hover:bg-white/10"
              @click="showMobileMenu = false"
            >
              {{ genre.name }}
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useMovieStore } from '@/stores/movies';
import { useAuthStore } from '@/stores/auth';
import type { Movie } from '@/data/mockMovies';

// Stores
const movieStore = useMovieStore();
const authStore = useAuthStore();
const route = useRoute();
const router = useRouter();

// Navigation links
const navLinks = [
  { path: '/movies', label: 'Home' },
  { path: '/movies?filter=trending', label: 'Trending' },
  { path: '/movies?filter=new', label: 'New Releases' },
  { path: '/movies?category=2', label: 'TV Series' },
];

// State
const isScrolled = ref(false);
const searchQuery = ref('');
const isSearchFocused = ref(false);
const searchResults = ref<Movie[]>([]);
const showMobileSearch = ref(false);
const showMobileMenu = ref(false);
const showCategoriesMenu = ref(false);
const showUserMenu = ref(false);

// Computed
const genres = computed(() => movieStore.activeGenres);
const isLoggedIn = computed(() => authStore.isLoggedIn);
const userInitials = computed(() => {
  const user = authStore.user;
  if (!user?.name) return 'U';
  return user.name.split(' ').map((n: string) => n[0]).join('').toUpperCase().slice(0, 2);
});

// Methods
const isActiveRoute = (path: string) => {
  return route.path === path || route.fullPath.startsWith(path + '?');
};

const handleScroll = () => {
  isScrolled.value = window.scrollY > 20;
};

const handleSearch = () => {
  if (searchQuery.value.length >= 2) {
    searchResults.value = movieStore.movies.filter(movie =>
      movie.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      movie.genre.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    ).slice(0, 5);
  } else {
    searchResults.value = [];
  }
};

const submitSearch = () => {
  if (searchQuery.value) {
    router.push({ name: 'movies', query: { search: searchQuery.value } });
    clearSearch();
  }
};

const handleSearchBlur = () => {
  setTimeout(() => {
    isSearchFocused.value = false;
  }, 200);
};

const clearSearch = () => {
  searchQuery.value = '';
  searchResults.value = [];
  isSearchFocused.value = false;
};

const toggleMobileSearch = () => {
  showMobileSearch.value = !showMobileSearch.value;
};

const handleLogout = async () => {
  await authStore.logout();
  showUserMenu.value = false;
  router.push('/auth/boxed-signin');
};

// Close dropdowns when clicking outside
const handleClickOutside = (e: MouseEvent) => {
  const target = e.target as HTMLElement;
  if (!target.closest('.relative')) {
    showCategoriesMenu.value = false;
    showUserMenu.value = false;
  }
};

// Lifecycle
onMounted(() => {
  window.addEventListener('scroll', handleScroll);
  document.addEventListener('click', handleClickOutside);
  handleScroll();
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
  document.removeEventListener('click', handleClickOutside);
});
</script>

<style scoped>
.cinema-navbar {
  --cinema-bg: #0a0a0b;
  --cinema-bg-elevated: #1c1c1f;
  --cinema-accent: #e50914;
  --cinema-secondary: #f5c518;
}

.nav-link {
  position: relative;
}

.nav-link::after {
  content: '';
  position: absolute;
  bottom: -4px;
  left: 0;
  width: 0;
  height: 2px;
  background: var(--cinema-accent);
  transition: width 0.3s ease;
}

.nav-link:hover::after,
.nav-link.router-link-active::after {
  width: 100%;
}

.search-input::placeholder {
  transition: opacity 0.2s ease;
}

.search-input:focus::placeholder {
  opacity: 0.6;
}
</style>
