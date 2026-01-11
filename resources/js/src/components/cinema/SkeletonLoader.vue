<template>
  <!-- Skeleton Loader Component - Animated loading placeholders -->
  <div class="skeleton-loader">
    <!-- Movie Card Skeleton -->
    <template v-if="type === 'movie-card'">
      <div class="movie-card-skeleton overflow-hidden rounded-lg bg-cinema-bg-card">
        <div class="skeleton-shimmer aspect-[2/3]" />
        <div class="p-3">
          <div class="skeleton-shimmer h-4 w-3/4 rounded" />
          <div class="mt-2 skeleton-shimmer h-3 w-1/2 rounded" />
        </div>
      </div>
    </template>

    <!-- Movie Grid Skeleton -->
    <template v-else-if="type === 'movie-grid'">
      <div class="cinema-grid-movies">
        <div
          v-for="n in count"
          :key="n"
          class="movie-card-skeleton overflow-hidden rounded-lg bg-cinema-bg-card"
        >
          <div class="skeleton-shimmer aspect-[2/3]" />
          <div class="p-3">
            <div class="skeleton-shimmer h-4 w-3/4 rounded" />
            <div class="mt-2 skeleton-shimmer h-3 w-1/2 rounded" />
          </div>
        </div>
      </div>
    </template>

    <!-- Hero Skeleton -->
    <template v-else-if="type === 'hero'">
      <div class="relative min-h-[60vh] overflow-hidden bg-cinema-bg-secondary md:min-h-[70vh] lg:min-h-[80vh]">
        <div class="skeleton-shimmer absolute inset-0" />
        <div class="absolute inset-0 bg-gradient-to-r from-cinema-bg via-cinema-bg/80 to-transparent" />
        <div class="cinema-container relative flex h-full min-h-[60vh] items-end pb-16 pt-32 md:items-center md:pb-0">
          <div class="max-w-2xl">
            <div class="skeleton-shimmer mb-4 h-6 w-24 rounded" />
            <div class="skeleton-shimmer h-16 w-full max-w-md rounded" />
            <div class="mt-4 flex gap-4">
              <div class="skeleton-shimmer h-4 w-16 rounded" />
              <div class="skeleton-shimmer h-4 w-16 rounded" />
              <div class="skeleton-shimmer h-4 w-20 rounded" />
            </div>
            <div class="mt-5 space-y-2">
              <div class="skeleton-shimmer h-4 w-full rounded" />
              <div class="skeleton-shimmer h-4 w-5/6 rounded" />
              <div class="skeleton-shimmer h-4 w-4/6 rounded" />
            </div>
            <div class="mt-8 flex gap-4">
              <div class="skeleton-shimmer h-14 w-40 rounded-lg" />
              <div class="skeleton-shimmer h-14 w-32 rounded-lg" />
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- Category List Skeleton -->
    <template v-else-if="type === 'categories'">
      <div class="flex gap-3 overflow-hidden">
        <div
          v-for="n in count"
          :key="n"
          class="skeleton-shimmer h-10 rounded-full"
          :style="{ width: `${60 + Math.random() * 40}px` }"
        />
      </div>
    </template>

    <!-- Video Player Skeleton -->
    <template v-else-if="type === 'video-player'">
      <div class="relative aspect-video overflow-hidden rounded-xl bg-cinema-bg-card">
        <div class="skeleton-shimmer absolute inset-0" />
        <div class="absolute inset-0 flex items-center justify-center">
          <div class="h-16 w-16 rounded-full bg-white/10" />
        </div>
      </div>
    </template>

    <!-- Movie Details Skeleton -->
    <template v-else-if="type === 'movie-details'">
      <div class="space-y-6">
        <!-- Video Player -->
        <div class="relative aspect-video overflow-hidden rounded-xl bg-cinema-bg-card">
          <div class="skeleton-shimmer absolute inset-0" />
        </div>
        
        <!-- Title and Meta -->
        <div class="space-y-4">
          <div class="skeleton-shimmer h-8 w-2/3 rounded" />
          <div class="flex gap-4">
            <div class="skeleton-shimmer h-4 w-20 rounded" />
            <div class="skeleton-shimmer h-4 w-16 rounded" />
            <div class="skeleton-shimmer h-4 w-24 rounded" />
          </div>
          <div class="flex gap-2">
            <div class="skeleton-shimmer h-8 w-20 rounded-full" />
            <div class="skeleton-shimmer h-8 w-24 rounded-full" />
            <div class="skeleton-shimmer h-8 w-16 rounded-full" />
          </div>
        </div>

        <!-- Description -->
        <div class="space-y-2">
          <div class="skeleton-shimmer h-4 w-full rounded" />
          <div class="skeleton-shimmer h-4 w-full rounded" />
          <div class="skeleton-shimmer h-4 w-4/5 rounded" />
        </div>
      </div>
    </template>

    <!-- Advertisement Skeleton -->
    <template v-else-if="type === 'advertisement'">
      <div class="relative aspect-[21/9] overflow-hidden rounded-xl bg-cinema-bg-card">
        <div class="skeleton-shimmer absolute inset-0" />
        <div class="absolute bottom-6 left-6 space-y-3">
          <div class="skeleton-shimmer h-6 w-48 rounded" />
          <div class="skeleton-shimmer h-4 w-64 rounded" />
          <div class="skeleton-shimmer h-10 w-32 rounded-lg" />
        </div>
      </div>
    </template>

    <!-- Text Line Skeleton -->
    <template v-else-if="type === 'text'">
      <div
        class="skeleton-shimmer rounded"
        :style="{ height: height, width: width }"
      />
    </template>

    <!-- Default Box Skeleton -->
    <template v-else>
      <div
        class="skeleton-shimmer rounded-lg"
        :style="{ height: height, width: width }"
      />
    </template>
  </div>
</template>

<script setup lang="ts">
// Props
interface Props {
  type?: 'movie-card' | 'movie-grid' | 'hero' | 'categories' | 'video-player' | 'movie-details' | 'advertisement' | 'text' | 'box';
  count?: number;
  width?: string;
  height?: string;
}

withDefaults(defineProps<Props>(), {
  type: 'box',
  count: 6,
  width: '100%',
  height: '100px',
});
</script>

<style scoped>
.skeleton-loader {
  --cinema-bg: #0a0a0b;
  --cinema-bg-secondary: #111113;
  --cinema-bg-card: #161618;
}

.skeleton-shimmer {
  background: linear-gradient(
    90deg,
    var(--cinema-bg-card) 25%,
    #1f1f22 50%,
    var(--cinema-bg-card) 75%
  );
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}

/* Cinema grid for movie grid skeleton */
.cinema-grid-movies {
  display: grid;
  gap: 1.25rem;
  grid-template-columns: repeat(2, 1fr);
}

@media (min-width: 640px) {
  .cinema-grid-movies {
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
  }
}

@media (min-width: 768px) {
  .cinema-grid-movies {
    grid-template-columns: repeat(4, 1fr);
  }
}

@media (min-width: 1024px) {
  .cinema-grid-movies {
    grid-template-columns: repeat(5, 1fr);
  }
}

@media (min-width: 1280px) {
  .cinema-grid-movies {
    grid-template-columns: repeat(6, 1fr);
    gap: 1.75rem;
  }
}
</style>
