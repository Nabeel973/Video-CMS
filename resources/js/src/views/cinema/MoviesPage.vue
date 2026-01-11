<template>
  <!-- Movies Page - Main landing page for movie browsing -->
  <div class="movies-page cinema-app">
    <!-- Hero Section -->
    <HeroSection
      v-if="!isLoading && featuredMovie"
      :featured-movie="featuredMovie"
    />
    <SkeletonLoader v-else type="hero" />

    <!-- Main Content -->
    <main class="cinema-container relative -mt-16 pb-12 md:-mt-24">
      <!-- Categories Navigation -->
      <section class="mb-8">
        <CategoryList
          :items="genres"
          title="Browse by Genre"
          item-label="Genres"
          v-model="selectedGenreId"
          @select="handleGenreSelect"
        />
      </section>

      <!-- Search Results (when searching) -->
      <section v-if="searchQuery" class="mb-12">
        <MovieGrid
          :movies="filteredMovies"
          :title="`Search results for '${searchQuery}'`"
          :subtitle="`${filteredMovies.length} movies found`"
          :loading="isLoading"
          empty-message="No movies match your search. Try different keywords."
        />
        <div v-if="filteredMovies.length > 0" class="mt-4">
          <button
            class="text-sm text-white/50 hover:text-white transition-colors"
            @click="clearSearch"
          >
            Clear search
          </button>
        </div>
      </section>

      <!-- Filtered by Genre (when genre selected) -->
      <section v-else-if="selectedGenreId" class="mb-12">
        <MovieGrid
          :movies="genreFilteredMovies"
          :title="selectedGenreName"
          :subtitle="`${genreFilteredMovies.length} movies in this genre`"
          :loading="isLoading"
          show-load-more
          :limit="12"
        />
        <div class="mt-4">
          <button
            class="text-sm text-white/50 hover:text-white transition-colors"
            @click="clearGenreFilter"
          >
            Show all movies
          </button>
        </div>
      </section>

      <!-- Default Content (no filters) -->
      <template v-else>
        <!-- Trending Movies -->
        <section class="mb-12">
          <MovieGrid
            :movies="trendingMovies"
            title="Trending Now"
            subtitle="Most popular this week"
            :loading="isLoading"
            scrollable
            view-all-link="/movies?filter=trending"
          />
        </section>

        <!-- Advertisement Banner -->
        <section v-if="bannerAd" class="mb-12">
          <AdvertisementBanner
            :advertisement="bannerAd"
            variant="large"
            overlay-position="left"
            cta-text="Subscribe Now"
          />
        </section>

        <!-- New Releases -->
        <section class="mb-12">
          <MovieGrid
            :movies="newReleases"
            title="New Releases"
            subtitle="Fresh content added recently"
            :loading="isLoading"
            scrollable
            view-all-link="/movies?filter=new"
          />
        </section>

        <!-- Browse by Category -->
        <section class="mb-12">
          <div class="mb-6">
            <h2 class="cinema-heading-display text-xl font-bold tracking-wide text-white md:text-2xl">
              Browse by Category
            </h2>
          </div>
          <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <router-link
              v-for="category in categories"
              :key="category.id"
              :to="{ name: 'movies', query: { category: category.id } }"
              class="category-card group relative overflow-hidden rounded-xl bg-gradient-to-br from-cinema-bg-card to-cinema-bg-elevated p-6 transition-all hover:-translate-y-1 hover:shadow-lg"
            >
              <div class="category-icon mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-cinema-accent/10 text-cinema-accent transition-all group-hover:bg-cinema-accent group-hover:text-white">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-white">{{ category.name }}</h3>
              <p class="mt-1 text-sm text-white/50">{{ getCategoryMovieCount(category.id) }} titles</p>
              
              <!-- Hover arrow -->
              <div class="absolute bottom-6 right-6 opacity-0 transition-all group-hover:opacity-100 group-hover:translate-x-1">
                <svg class="h-5 w-5 text-cinema-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12h14M12 5l7 7-7 7" />
                </svg>
              </div>
            </router-link>
          </div>
        </section>

        <!-- Action Movies -->
        <section v-if="actionMovies.length > 0" class="mb-12">
          <MovieGrid
            :movies="actionMovies"
            title="Action & Adventure"
            subtitle="Thrilling experiences await"
            :loading="isLoading"
            :limit="6"
          />
        </section>

        <!-- Second Advertisement -->
        <section v-if="secondBannerAd" class="mb-12">
          <AdvertisementBanner
            :advertisement="secondBannerAd"
            overlay-position="right"
            cta-text="Watch Now"
          />
        </section>

        <!-- Drama & Romance -->
        <section v-if="dramaMovies.length > 0" class="mb-12">
          <MovieGrid
            :movies="dramaMovies"
            title="Drama & Romance"
            subtitle="Stories that touch the heart"
            :loading="isLoading"
            :limit="6"
          />
        </section>

        <!-- Sci-Fi & Fantasy -->
        <section v-if="scifiMovies.length > 0" class="mb-12">
          <MovieGrid
            :movies="scifiMovies"
            title="Sci-Fi & Fantasy"
            subtitle="Explore new worlds"
            :loading="isLoading"
            :limit="6"
          />
        </section>

        <!-- All Movies -->
        <section class="mb-12">
          <MovieGrid
            :movies="allMovies"
            title="All Movies"
            subtitle="Complete collection"
            :loading="isLoading"
            show-load-more
            :limit="12"
            empty-message="No movies available at the moment."
          />
        </section>
      </template>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useMovieStore } from '@/stores/movies';
import { useMeta } from '@/composables/use-meta';
import {
  HeroSection,
  MovieGrid,
  CategoryList,
  AdvertisementBanner,
  SkeletonLoader,
} from '@/components/cinema';

// Meta
useMeta({ title: 'VistroVideo - Stream Movies & TV Shows' });

// Store & Router
const movieStore = useMovieStore();
const route = useRoute();
const router = useRouter();

// State
const isLoading = ref(true);
const selectedGenreId = ref<number | null>(null);

// Computed
const featuredMovie = computed(() => movieStore.featuredMovie);
const genres = computed(() => movieStore.activeGenres);
const categories = computed(() => movieStore.activeCategories);
const allMovies = computed(() => movieStore.movies);
const trendingMovies = computed(() => movieStore.trendingMovies);
const newReleases = computed(() => movieStore.newReleases);
const filteredMovies = computed(() => movieStore.filteredMovies);
const searchQuery = computed(() => movieStore.searchQuery);

const bannerAd = computed(() => movieStore.bannerAds[0] || null);
const secondBannerAd = computed(() => movieStore.bannerAds[1] || null);

const selectedGenreName = computed(() => {
  if (!selectedGenreId.value) return '';
  const genre = genres.value.find(g => g.id === selectedGenreId.value);
  return genre ? `${genre.name} Movies` : '';
});

const genreFilteredMovies = computed(() => {
  if (!selectedGenreId.value) return allMovies.value;
  return movieStore.getMoviesForGenre(selectedGenreId.value);
});

// Genre-specific movie lists
const actionMovies = computed(() => movieStore.getMoviesForGenre(1)); // Action genre ID
const dramaMovies = computed(() => {
  const drama = movieStore.getMoviesForGenre(3); // Drama
  const romance = movieStore.getMoviesForGenre(7); // Romance
  return [...drama, ...romance].slice(0, 6);
});
const scifiMovies = computed(() => {
  const scifi = movieStore.getMoviesForGenre(5); // Sci-Fi
  const fantasy = movieStore.getMoviesForGenre(10); // Fantasy
  return [...scifi, ...fantasy].slice(0, 6);
});

// Methods
const handleGenreSelect = (genreId: number | null) => {
  selectedGenreId.value = genreId;
  if (genreId) {
    router.push({ query: { ...route.query, genre: genreId.toString() } });
  } else {
    const { genre, ...rest } = route.query;
    router.push({ query: rest });
  }
};

const clearGenreFilter = () => {
  selectedGenreId.value = null;
  const { genre, ...rest } = route.query;
  router.push({ query: rest });
};

const clearSearch = () => {
  movieStore.clearFilters();
  router.push({ query: {} });
};

const getCategoryMovieCount = (categoryId: number): number => {
  return movieStore.getMoviesForCategory(categoryId).length;
};

// Watch for route query changes
watch(
  () => route.query,
  (query) => {
    if (query.genre) {
      selectedGenreId.value = parseInt(query.genre as string);
    } else {
      selectedGenreId.value = null;
    }

    if (query.search) {
      movieStore.setSearchQuery(query.search as string);
    } else {
      movieStore.setSearchQuery('');
    }
  },
  { immediate: true }
);

// Lifecycle
onMounted(async () => {
  // Simulate loading for skeleton demo
  await movieStore.simulateLoading(800);
  isLoading.value = false;
});
</script>

<style scoped>
.movies-page {
  --cinema-bg: #0a0a0b;
  --cinema-bg-secondary: #111113;
  --cinema-bg-card: #161618;
  --cinema-bg-elevated: #1c1c1f;
  --cinema-accent: #e50914;
}

.category-card {
  border: 1px solid rgba(255, 255, 255, 0.05);
}

.category-card:hover {
  border-color: rgba(229, 9, 20, 0.3);
}
</style>
