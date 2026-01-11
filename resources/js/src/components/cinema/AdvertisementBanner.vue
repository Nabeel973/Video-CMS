<template>
  <!-- Advertisement Banner Component - Promotional banners and ads -->
  <div
    v-if="advertisement"
    class="advertisement-banner relative overflow-hidden rounded-xl"
    :class="[
      variant === 'large' ? 'aspect-[21/9] md:aspect-[4/1]' : 'aspect-[16/9] md:aspect-[21/9]',
      { 'cursor-pointer': advertisement.link }
    ]"
    @click="handleClick"
  >
    <!-- Background Image -->
    <div class="absolute inset-0">
      <img
        :src="advertisement.image"
        :alt="advertisement.name"
        class="h-full w-full object-cover transition-transform duration-500"
        :class="{ 'group-hover:scale-105': advertisement.link }"
        loading="lazy"
        @load="imageLoaded = true"
        @error="handleImageError"
      />
      
      <!-- Loading skeleton -->
      <div
        v-if="!imageLoaded && !imageError"
        class="absolute inset-0 cinema-skeleton"
      />
    </div>

    <!-- Gradient Overlay -->
    <div
      class="absolute inset-0"
      :class="[
        overlayPosition === 'left'
          ? 'bg-gradient-to-r from-black/90 via-black/50 to-transparent'
          : overlayPosition === 'right'
          ? 'bg-gradient-to-l from-black/90 via-black/50 to-transparent'
          : 'bg-gradient-to-t from-black/90 via-black/30 to-transparent'
      ]"
    />

    <!-- Content -->
    <div
      class="relative flex h-full items-end p-6 md:items-center md:p-8"
      :class="[
        overlayPosition === 'right' ? 'justify-end text-right' : 'justify-start text-left'
      ]"
    >
      <div :class="['max-w-md', overlayPosition === 'center' ? 'mx-auto text-center' : '']">
        <!-- Ad Label -->
        <span
          v-if="showLabel"
          class="mb-2 inline-block rounded bg-white/10 px-2 py-0.5 text-[10px] font-medium uppercase tracking-wider text-white/60 backdrop-blur-sm"
        >
          Sponsored
        </span>

        <!-- Title -->
        <h3 class="text-xl font-bold text-white md:text-2xl lg:text-3xl">
          {{ advertisement.name }}
        </h3>

        <!-- Description -->
        <p
          v-if="advertisement.description && showDescription"
          class="mt-2 text-sm text-white/70 md:text-base"
        >
          {{ advertisement.description }}
        </p>

        <!-- CTA Button -->
        <button
          v-if="advertisement.link && showCta"
          class="mt-4 inline-flex items-center gap-2 rounded-lg bg-cinema-accent px-5 py-2.5 text-sm font-semibold text-white transition-all hover:bg-cinema-accent/80 hover:shadow-lg hover:shadow-cinema-accent/30"
        >
          {{ ctaText }}
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M5 12h14M12 5l7 7-7 7" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Close Button (if dismissible) -->
    <button
      v-if="dismissible"
      class="absolute right-3 top-3 flex h-8 w-8 items-center justify-center rounded-full bg-black/50 text-white/70 backdrop-blur-sm transition-colors hover:bg-black/70 hover:text-white"
      @click.stop="handleDismiss"
      aria-label="Close advertisement"
    >
      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M18 6L6 18M6 6l12 12" />
      </svg>
    </button>

    <!-- Hover Effect Border -->
    <div
      v-if="advertisement.link"
      class="pointer-events-none absolute inset-0 rounded-xl border-2 border-transparent transition-colors group-hover:border-cinema-accent/30"
    />
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import type { Advertisement } from '@/data/mockMovies';

// Props
interface Props {
  advertisement: Advertisement | null;
  variant?: 'normal' | 'large';
  overlayPosition?: 'left' | 'right' | 'center' | 'bottom';
  showLabel?: boolean;
  showDescription?: boolean;
  showCta?: boolean;
  ctaText?: string;
  dismissible?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'normal',
  overlayPosition: 'left',
  showLabel: true,
  showDescription: true,
  showCta: true,
  ctaText: 'Learn More',
  dismissible: false,
});

// Emits
const emit = defineEmits<{
  (e: 'click', ad: Advertisement): void;
  (e: 'dismiss', ad: Advertisement): void;
}>();

// State
const imageLoaded = ref(false);
const imageError = ref(false);

// Methods
const handleClick = () => {
  if (props.advertisement?.link) {
    emit('click', props.advertisement);
    // In a real app, you might navigate or open a new tab
    // window.open(props.advertisement.link, '_blank');
  }
};

const handleDismiss = () => {
  if (props.advertisement) {
    emit('dismiss', props.advertisement);
  }
};

const handleImageError = () => {
  imageError.value = true;
  imageLoaded.value = true;
};
</script>

<style scoped>
.advertisement-banner {
  --cinema-accent: #e50914;
}

/* Animation on hover */
.advertisement-banner:hover img {
  transform: scale(1.02);
}

/* Ensure text readability */
.advertisement-banner h3,
.advertisement-banner p {
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
}
</style>
