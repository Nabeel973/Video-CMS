<template>
  <!-- Movie Card Component with hover effects and animations -->
  <router-link
    :to="{ name: 'movie-details', params: { id: movie.id } }"
    class="movie-card group relative block overflow-hidden rounded-lg bg-cinema-card transition-all duration-300"
    :class="{ 'movie-card--featured': featured }"
  >
    <!-- Poster Image Container -->
    <div class="movie-card__poster relative aspect-[2/3] overflow-hidden">
      <!-- Skeleton while loading -->
      <div
        v-if="!imageLoaded"
        class="absolute inset-0 cinema-skeleton"
      />
      
      <!-- Movie Poster -->
      <img
        :src="movie.image"
        :alt="movie.name"
        class="movie-card__image h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
        :class="{ 'opacity-0': !imageLoaded }"
        loading="lazy"
        @load="imageLoaded = true"
        @error="handleImageError"
      />

      <!-- Gradient Overlay -->
      <div class="movie-card__overlay absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent opacity-60 transition-opacity duration-300 group-hover:opacity-80" />

      <!-- Play Button Overlay -->
      <div class="movie-card__play absolute inset-0 flex items-center justify-center opacity-0 transition-all duration-300 group-hover:opacity-100">
        <div class="play-button flex h-14 w-14 items-center justify-center rounded-full bg-cinema-accent shadow-lg shadow-cinema-accent/50 transition-transform duration-300 group-hover:scale-110">
          <svg class="ml-1 h-6 w-6 text-white" viewBox="0 0 24 24" fill="currentColor">
            <path d="M8 5v14l11-7z" />
          </svg>
        </div>
      </div>

      <!-- Top Badges -->
      <div class="absolute left-2 right-2 top-2 flex flex-wrap gap-1.5">
        <span
          v-if="isNewRelease"
          class="rounded bg-cinema-accent px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-white"
        >
          New
        </span>
        <span
          v-if="isTrending"
          class="rounded bg-cinema-secondary px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-black"
        >
          Trending
        </span>
      </div>

      <!-- Bottom Info (shows on hover) -->
      <div class="movie-card__info absolute bottom-0 left-0 right-0 translate-y-full p-3 transition-transform duration-300 group-hover:translate-y-0">
        <!-- Rating & Duration -->
        <div class="mb-2 flex items-center gap-3 text-xs">
          <div class="flex items-center gap-1 text-cinema-secondary">
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
            </svg>
            <span class="font-semibold">{{ movie.rating }}</span>
          </div>
          <span class="text-white/60">{{ movie.duration }}</span>
        </div>

        <!-- Genre Tags -->
        <div class="flex flex-wrap gap-1">
          <span class="rounded-full bg-white/10 px-2 py-0.5 text-[10px] text-white/80">
            {{ movie.genre.name }}
          </span>
        </div>
      </div>
    </div>

    <!-- Card Footer -->
    <div class="movie-card__footer p-3">
      <!-- Title -->
      <h3 class="movie-card__title truncate text-sm font-semibold text-white transition-colors group-hover:text-cinema-accent">
        {{ movie.name }}
      </h3>
      
      <!-- Year & Category -->
      <div class="mt-1 flex items-center gap-2 text-xs text-white/50">
        <span>{{ movie.release }}</span>
        <span class="h-1 w-1 rounded-full bg-white/30" />
        <span>{{ movie.category.name }}</span>
      </div>
    </div>

    <!-- Hover Border Effect -->
    <div class="movie-card__border pointer-events-none absolute inset-0 rounded-lg border border-transparent transition-colors duration-300 group-hover:border-cinema-accent/50" />
  </router-link>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import type { Movie } from '@/data/mockMovies';

// Props
interface Props {
  movie: Movie;
  featured?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  featured: false,
});

// State
const imageLoaded = ref(false);

// Computed
const isNewRelease = computed(() => 
  props.movie.tags.some(tag => tag.name === 'New Release')
);

const isTrending = computed(() => 
  props.movie.tags.some(tag => tag.name === 'Trending')
);

// Methods
const handleImageError = (e: Event) => {
  const target = e.target as HTMLImageElement;
  target.src = 'https://via.placeholder.com/300x450?text=No+Image';
  imageLoaded.value = true;
};
</script>

<style scoped>
.movie-card {
  --cinema-card: #161618;
  --cinema-accent: #e50914;
  --cinema-secondary: #f5c518;
}

.movie-card__poster {
  background: linear-gradient(135deg, #1c1c1f 0%, #0f0f10 100%);
}

.movie-card:hover .movie-card__image {
  filter: brightness(0.8);
}

.play-button {
  animation: pulse-glow 2s ease-in-out infinite;
}

@keyframes pulse-glow {
  0%, 100% {
    box-shadow: 0 0 20px rgba(229, 9, 20, 0.4);
  }
  50% {
    box-shadow: 0 0 35px rgba(229, 9, 20, 0.6);
  }
}

/* Featured card variant */
.movie-card--featured {
  grid-column: span 2;
}

@media (max-width: 640px) {
  .movie-card--featured {
    grid-column: span 1;
  }
}

/* Focus state */
.movie-card:focus-visible {
  outline: 2px solid var(--cinema-accent);
  outline-offset: 2px;
}
</style>
