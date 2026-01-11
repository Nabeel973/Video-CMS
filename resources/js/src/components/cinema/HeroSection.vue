<template>
  <!-- Hero Section Component - Featured movie banner with video background option -->
  <section class="hero-section relative min-h-[60vh] overflow-hidden md:min-h-[70vh] lg:min-h-[80vh]">
    <!-- Background Image/Video -->
    <div class="absolute inset-0">
      <!-- Video Background (if enabled) -->
      <video
        v-if="showVideo && featuredMovie.video_link"
        class="h-full w-full object-cover"
        :poster="featuredMovie.image"
        autoplay
        muted
        loop
        playsinline
      >
        <source :src="featuredMovie.video_file || ''" type="video/mp4" />
      </video>

      <!-- Image Background -->
      <img
        v-else
        :src="backgroundImage"
        :alt="featuredMovie.name"
        class="h-full w-full object-cover"
        loading="eager"
      />

      <!-- Gradient Overlays -->
      <div class="absolute inset-0 bg-gradient-to-r from-cinema-bg via-cinema-bg/80 to-transparent" />
      <div class="absolute inset-0 bg-gradient-to-t from-cinema-bg via-cinema-bg/40 to-transparent" />
      
      <!-- Noise texture overlay -->
      <div class="absolute inset-0 opacity-[0.02] mix-blend-overlay" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 256 256%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noise%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.8%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noise)%22/%3E%3C/svg%3E')" />
    </div>

    <!-- Content -->
    <div class="cinema-container relative flex h-full min-h-[60vh] items-end pb-16 pt-32 md:min-h-[70vh] md:items-center md:pb-0 lg:min-h-[80vh]">
      <div class="max-w-2xl">
        <!-- Featured Badge -->
        <div class="mb-4 flex items-center gap-3">
          <span class="inline-flex items-center gap-1.5 rounded bg-cinema-accent px-3 py-1 text-xs font-bold uppercase tracking-wider text-white">
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
            </svg>
            Featured
          </span>
          <span
            v-for="tag in featuredMovie.tags.slice(0, 2)"
            :key="tag.id"
            class="rounded-full border border-white/20 bg-white/5 px-3 py-1 text-xs font-medium text-white/80 backdrop-blur-sm"
          >
            {{ tag.name }}
          </span>
        </div>

        <!-- Title -->
        <h1 class="hero-title cinema-heading-display text-4xl font-black tracking-tight text-white md:text-5xl lg:text-6xl xl:text-7xl">
          {{ featuredMovie.name }}
        </h1>

        <!-- Meta Info -->
        <div class="mt-4 flex flex-wrap items-center gap-4 text-sm text-white/70">
          <!-- Rating -->
          <div class="flex items-center gap-1.5 text-cinema-secondary">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
            </svg>
            <span class="font-bold">{{ featuredMovie.rating }}</span>
          </div>

          <span class="h-1 w-1 rounded-full bg-white/30" />

          <!-- Year -->
          <span>{{ featuredMovie.release }}</span>

          <span class="h-1 w-1 rounded-full bg-white/30" />

          <!-- Duration -->
          <span>{{ featuredMovie.duration }}</span>

          <span class="h-1 w-1 rounded-full bg-white/30" />

          <!-- Genre -->
          <span class="rounded-full border border-white/20 px-2.5 py-0.5 text-xs">
            {{ featuredMovie.genre.name }}
          </span>
        </div>

        <!-- Description -->
        <p class="mt-5 line-clamp-3 text-base leading-relaxed text-white/70 md:text-lg md:leading-relaxed">
          {{ featuredMovie.details }}
        </p>

        <!-- Action Buttons -->
        <div class="mt-8 flex flex-wrap items-center gap-4">
          <!-- Play Button -->
          <router-link
            :to="{ name: 'movie-details', params: { id: featuredMovie.id } }"
            class="hero-btn-primary group inline-flex items-center gap-3 rounded-lg bg-cinema-accent px-8 py-4 text-base font-bold text-white shadow-lg shadow-cinema-accent/30 transition-all hover:bg-cinema-accent/90 hover:shadow-xl hover:shadow-cinema-accent/40"
          >
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
              <path d="M8 5v14l11-7z" />
            </svg>
            Watch Now
          </router-link>

          <!-- More Info Button -->
          <router-link
            :to="{ name: 'movie-details', params: { id: featuredMovie.id } }"
            class="hero-btn-secondary group inline-flex items-center gap-2 rounded-lg bg-white/10 px-6 py-4 text-base font-semibold text-white backdrop-blur-sm transition-all hover:bg-white/20"
          >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10" />
              <path d="M12 16v-4M12 8h.01" />
            </svg>
            More Info
          </router-link>
        </div>

        <!-- Cast Preview (optional) -->
        <div v-if="featuredMovie.casts && featuredMovie.casts.length > 0" class="mt-8">
          <p class="mb-2 text-xs font-medium uppercase tracking-wider text-white/50">Starring</p>
          <div class="flex items-center gap-3">
            <div
              v-for="cast in featuredMovie.casts.slice(0, 3)"
              :key="cast.id"
              class="flex items-center gap-2"
            >
              <img
                :src="cast.image"
                :alt="cast.name"
                class="h-8 w-8 rounded-full object-cover ring-2 ring-white/20"
              />
              <span class="text-sm text-white/70">{{ cast.name }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-6 left-1/2 hidden -translate-x-1/2 animate-bounce md:block">
      <svg class="h-6 w-6 text-white/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M19 14l-7 7m0 0l-7-7m7 7V3" />
      </svg>
    </div>

    <!-- Movie Slider Indicators (if multiple featured) -->
    <div v-if="showIndicators && totalSlides > 1" class="absolute bottom-6 right-8 hidden items-center gap-2 md:flex">
      <button
        v-for="n in totalSlides"
        :key="n"
        class="h-1.5 rounded-full transition-all"
        :class="[
          currentSlide === n - 1 
            ? 'w-8 bg-cinema-accent' 
            : 'w-1.5 bg-white/30 hover:bg-white/50'
        ]"
        @click="$emit('slide-change', n - 1)"
      />
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import type { Movie } from '@/data/mockMovies';
import { heroImages } from '@/data/mockMovies';

// Props
interface Props {
  featuredMovie: Movie;
  showVideo?: boolean;
  showIndicators?: boolean;
  totalSlides?: number;
  currentSlide?: number;
}

const props = withDefaults(defineProps<Props>(), {
  showVideo: false,
  showIndicators: false,
  totalSlides: 1,
  currentSlide: 0,
});

// Emits
defineEmits<{
  (e: 'slide-change', index: number): void;
}>();

// Computed
const backgroundImage = computed(() => {
  // Use movie image or fallback to hero images
  if (props.featuredMovie.image) {
    // Convert poster to larger hero image if using unsplash
    return props.featuredMovie.image.replace('300x450', '1920x1080').replace('w=300&h=450', 'w=1920&h=1080');
  }
  return heroImages[0];
});
</script>

<style scoped>
.hero-section {
  --cinema-bg: #0a0a0b;
  --cinema-accent: #e50914;
  --cinema-secondary: #f5c518;
}

/* Animated title entrance */
.hero-title {
  animation: hero-title-enter 0.8s ease-out forwards;
}

@keyframes hero-title-enter {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Button hover effects */
.hero-btn-primary {
  position: relative;
  overflow: hidden;
}

.hero-btn-primary::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
  transform: translateX(-100%);
  transition: transform 0.5s ease;
}

.hero-btn-primary:hover::before {
  transform: translateX(100%);
}

/* Text shadow for readability */
.hero-section h1,
.hero-section p {
  text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
}

/* Line clamp for description */
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
