<template>
  <!-- Related Movies Component - Shows movies related by genre or tags -->
  <section v-if="movies.length > 0" class="related-movies">
    <!-- Section Header -->
    <div class="mb-6 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-white/5">
          <svg class="h-5 w-5 text-cinema-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
          </svg>
        </div>
        <div>
          <h2 class="cinema-heading-display text-xl font-bold tracking-wide text-white">
            {{ title }}
          </h2>
          <p v-if="subtitle" class="mt-0.5 text-sm text-white/50">
            {{ subtitle }}
          </p>
        </div>
      </div>

      <router-link
        v-if="viewAllLink"
        :to="viewAllLink"
        class="group hidden items-center gap-1.5 text-sm font-medium text-cinema-accent transition-colors hover:text-cinema-accent/80 sm:flex"
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

    <!-- Movies Scroll Container -->
    <div class="relative">
      <!-- Scroll Shadows -->
      <div
        v-show="canScrollLeft"
        class="pointer-events-none absolute bottom-0 left-0 top-0 z-10 w-16 bg-gradient-to-r from-cinema-bg to-transparent"
      />
      <div
        v-show="canScrollRight"
        class="pointer-events-none absolute bottom-0 right-0 top-0 z-10 w-16 bg-gradient-to-l from-cinema-bg to-transparent"
      />

      <!-- Scroll Buttons -->
      <button
        v-show="canScrollLeft"
        class="absolute left-2 top-1/2 z-20 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-cinema-bg-elevated/90 text-white shadow-lg backdrop-blur-sm transition-all hover:bg-cinema-accent"
        @click="scrollLeft"
      >
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M15 18l-6-6 6-6" />
        </svg>
      </button>
      <button
        v-show="canScrollRight"
        class="absolute right-2 top-1/2 z-20 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-cinema-bg-elevated/90 text-white shadow-lg backdrop-blur-sm transition-all hover:bg-cinema-accent"
        @click="scrollRight"
      >
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M9 18l6-6-6-6" />
        </svg>
      </button>

      <!-- Movies Container -->
      <div
        ref="scrollContainer"
        class="related-scroll flex gap-4 overflow-x-auto pb-4 scrollbar-hide"
        @scroll="updateScrollState"
      >
        <router-link
          v-for="(movie, index) in movies"
          :key="movie.id"
          :to="{ name: 'movie-details', params: { id: movie.id } }"
          class="related-movie-card group relative shrink-0 overflow-hidden rounded-lg transition-all duration-300 hover:-translate-y-1"
          :style="{ animationDelay: `${index * 75}ms` }"
          :class="[
            cardSize === 'sm' ? 'w-32 md:w-40' : 'w-40 md:w-48',
            animated ? 'cinema-animate-fade-in opacity-0' : ''
          ]"
        >
          <!-- Poster -->
          <div class="relative aspect-[2/3] overflow-hidden bg-cinema-bg-card">
            <img
              :src="movie.image"
              :alt="movie.name"
              class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
              loading="lazy"
            />

            <!-- Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 transition-opacity group-hover:opacity-100" />

            <!-- Play Icon -->
            <div class="absolute inset-0 flex items-center justify-center opacity-0 transition-opacity group-hover:opacity-100">
              <div class="flex h-12 w-12 items-center justify-center rounded-full bg-cinema-accent/90 shadow-lg">
                <svg class="ml-0.5 h-5 w-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M8 5v14l11-7z" />
                </svg>
              </div>
            </div>

            <!-- Rating Badge -->
            <div class="absolute bottom-2 left-2 flex items-center gap-1 rounded bg-black/60 px-2 py-0.5 text-xs backdrop-blur-sm opacity-0 transition-opacity group-hover:opacity-100">
              <svg class="h-3 w-3 text-cinema-secondary" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
              </svg>
              <span class="font-medium text-white">{{ movie.rating }}</span>
            </div>
          </div>

          <!-- Info -->
          <div class="p-2">
            <h4 class="truncate text-sm font-medium text-white transition-colors group-hover:text-cinema-accent">
              {{ movie.name }}
            </h4>
            <p class="mt-0.5 text-xs text-white/50">
              {{ movie.genre.name }} • {{ movie.release }}
            </p>
          </div>
        </router-link>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import type { Movie } from '@/data/mockMovies';

// Props
interface Props {
  movies: Movie[];
  title?: string;
  subtitle?: string;
  viewAllLink?: string;
  cardSize?: 'sm' | 'md';
  animated?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Related Movies',
  subtitle: '',
  viewAllLink: '',
  cardSize: 'md',
  animated: true,
});

// Refs
const scrollContainer = ref<HTMLElement | null>(null);

// State
const canScrollLeft = ref(false);
const canScrollRight = ref(false);

// Methods
const updateScrollState = () => {
  if (!scrollContainer.value) return;
  
  const { scrollLeft, scrollWidth, clientWidth } = scrollContainer.value;
  canScrollLeft.value = scrollLeft > 10;
  canScrollRight.value = scrollLeft < scrollWidth - clientWidth - 10;
};

const scrollLeft = () => {
  if (!scrollContainer.value) return;
  const cardWidth = props.cardSize === 'sm' ? 160 : 192;
  scrollContainer.value.scrollBy({ left: -cardWidth * 2, behavior: 'smooth' });
};

const scrollRight = () => {
  if (!scrollContainer.value) return;
  const cardWidth = props.cardSize === 'sm' ? 160 : 192;
  scrollContainer.value.scrollBy({ left: cardWidth * 2, behavior: 'smooth' });
};

// Lifecycle
onMounted(() => {
  updateScrollState();
  window.addEventListener('resize', updateScrollState);
});

onUnmounted(() => {
  window.removeEventListener('resize', updateScrollState);
});
</script>

<style scoped>
.related-movies {
  --cinema-bg: #0a0a0b;
  --cinema-bg-elevated: #1c1c1f;
  --cinema-bg-card: #161618;
  --cinema-accent: #e50914;
  --cinema-secondary: #f5c518;
}

.related-scroll {
  scrollbar-width: none;
  -ms-overflow-style: none;
}

.related-scroll::-webkit-scrollbar {
  display: none;
}

/* Focus state for accessibility */
.related-movie-card:focus-visible {
  outline: 2px solid var(--cinema-accent);
  outline-offset: 2px;
}
</style>
