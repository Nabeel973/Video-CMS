<template>
  <!-- Movie Grid Component - Displays movies in a responsive grid layout -->
  <section class="movie-grid-section">
    <!-- Section Header -->
    <div v-if="title || $slots.header" class="mb-6 flex items-center justify-between">
      <slot name="header">
        <div class="flex items-center gap-3">
          <div v-if="icon" class="flex h-10 w-10 items-center justify-center rounded-lg bg-cinema-accent/10 text-cinema-accent">
            <component :is="icon" class="h-5 w-5" />
          </div>
          <div>
            <h2 class="cinema-heading-display text-xl font-bold tracking-wide text-white md:text-2xl">
              {{ title }}
            </h2>
            <p v-if="subtitle" class="mt-0.5 text-sm text-white/50">
              {{ subtitle }}
            </p>
          </div>
        </div>
      </slot>

      <!-- View All Link -->
      <router-link
        v-if="viewAllLink"
        :to="viewAllLink"
        class="group flex items-center gap-1.5 text-sm font-medium text-cinema-accent transition-colors hover:text-cinema-accent/80"
      >
        View All
        <svg
          class="h-4 w-4 transition-transform group-hover:translate-x-1"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <path d="M9 18l6-6-6-6" />
        </svg>
      </router-link>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="cinema-grid-movies">
      <div
        v-for="n in skeletonCount"
        :key="n"
        class="movie-skeleton"
      >
        <div class="aspect-[2/3] w-full cinema-skeleton rounded-lg" />
        <div class="mt-3 h-4 w-3/4 cinema-skeleton rounded" />
        <div class="mt-2 h-3 w-1/2 cinema-skeleton rounded" />
      </div>
    </div>

    <!-- Empty State -->
    <div
      v-else-if="movies.length === 0"
      class="flex flex-col items-center justify-center py-16 text-center"
    >
      <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-white/5">
        <svg class="h-10 w-10 text-white/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
        </svg>
      </div>
      <h3 class="text-lg font-semibold text-white">No movies found</h3>
      <p class="mt-1 text-sm text-white/50">{{ emptyMessage }}</p>
    </div>

    <!-- Movie Grid -->
    <div
      v-else
      :class="[
        scrollable ? 'cinema-scroll-x' : 'cinema-grid-movies',
        gridClass
      ]"
    >
      <MovieCard
        v-for="(movie, index) in displayedMovies"
        :key="movie.id"
        :movie="movie"
        :style="{ animationDelay: `${index * 50}ms` }"
        class="cinema-animate-fade-in opacity-0"
        :class="{ 'min-w-[160px] md:min-w-[200px]': scrollable }"
      />
    </div>

    <!-- Load More Button -->
    <div v-if="showLoadMore && hasMore" class="mt-8 text-center">
      <button
        class="cinema-btn cinema-btn-secondary"
        @click="loadMore"
      >
        <span>Load More</span>
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M19 9l-7 7-7-7" />
        </svg>
      </button>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed, type Component } from 'vue';
import type { Movie } from '@/data/mockMovies';
import MovieCard from './MovieCard.vue';

// Props
interface Props {
  movies: Movie[];
  title?: string;
  subtitle?: string;
  icon?: Component;
  loading?: boolean;
  scrollable?: boolean;
  viewAllLink?: string;
  limit?: number;
  showLoadMore?: boolean;
  emptyMessage?: string;
  gridClass?: string;
  skeletonCount?: number;
}

const props = withDefaults(defineProps<Props>(), {
  title: '',
  subtitle: '',
  loading: false,
  scrollable: false,
  viewAllLink: '',
  limit: 0,
  showLoadMore: false,
  emptyMessage: 'Try adjusting your search or filters.',
  gridClass: '',
  skeletonCount: 6,
});

// State
const displayLimit = ref(props.limit || props.movies.length);

// Computed
const displayedMovies = computed(() => {
  if (props.limit && !props.showLoadMore) {
    return props.movies.slice(0, props.limit);
  }
  return props.movies.slice(0, displayLimit.value);
});

const hasMore = computed(() => {
  return displayLimit.value < props.movies.length;
});

// Methods
const loadMore = () => {
  displayLimit.value += props.limit || 6;
};
</script>

<style scoped>
.movie-skeleton {
  animation: skeleton-pulse 1.5s ease-in-out infinite;
}

@keyframes skeleton-pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}
</style>
