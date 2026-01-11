<template>
  <!-- YouTube Embed Component - Responsive YouTube video player -->
  <div class="youtube-embed relative overflow-hidden rounded-xl bg-black">
    <!-- Loading State -->
    <div
      v-if="!isLoaded && !hasError"
      class="absolute inset-0 flex items-center justify-center"
    >
      <div class="h-12 w-12 animate-spin rounded-full border-4 border-white/20 border-t-cinema-accent" />
    </div>

    <!-- Thumbnail Preview (before playing) -->
    <div
      v-if="!isPlaying && thumbnailUrl && !autoplay"
      class="youtube-thumbnail absolute inset-0 cursor-pointer"
      @click="play"
    >
      <img
        :src="thumbnailUrl"
        :alt="title || 'Video thumbnail'"
        class="h-full w-full object-cover"
        @load="isLoaded = true"
      />
      
      <!-- Dark overlay -->
      <div class="absolute inset-0 bg-black/40 transition-colors hover:bg-black/30" />
      
      <!-- Play button -->
      <div class="absolute inset-0 flex items-center justify-center">
        <button
          class="youtube-play-btn flex h-20 w-20 items-center justify-center rounded-full bg-cinema-accent text-white shadow-2xl shadow-cinema-accent/40 transition-transform hover:scale-110"
        >
          <svg class="ml-1 h-8 w-8" viewBox="0 0 24 24" fill="currentColor">
            <path d="M8 5v14l11-7z" />
          </svg>
        </button>
      </div>

      <!-- Video Title -->
      <div v-if="title" class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/90 to-transparent p-6">
        <h3 class="text-lg font-semibold text-white">{{ title }}</h3>
      </div>
    </div>

    <!-- YouTube iFrame -->
    <div
      v-show="isPlaying || autoplay"
      class="youtube-iframe-container"
    >
      <iframe
        ref="iframeRef"
        :src="embedUrl"
        :title="title || 'YouTube video player'"
        class="absolute inset-0 h-full w-full"
        frameborder="0"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
        allowfullscreen
        @load="handleIframeLoad"
        @error="handleError"
      />
    </div>

    <!-- Error State -->
    <div
      v-if="hasError"
      class="absolute inset-0 flex flex-col items-center justify-center bg-black/80"
    >
      <svg class="mb-4 h-16 w-16 text-cinema-accent" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
      </svg>
      <p class="text-lg font-medium text-white">Unable to load video</p>
      <p class="mt-1 text-sm text-white/60">Please check your connection and try again</p>
      <a
        :href="originalUrl"
        target="_blank"
        rel="noopener noreferrer"
        class="mt-4 cinema-btn cinema-btn-primary"
      >
        Watch on YouTube
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6M15 3h6v6M10 14L21 3" />
        </svg>
      </a>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';

// Props
interface Props {
  url: string;
  title?: string;
  autoplay?: boolean;
  startTime?: number;
  showRelated?: boolean;
  quality?: 'default' | 'hqdefault' | 'mqdefault' | 'sddefault' | 'maxresdefault';
}

const props = withDefaults(defineProps<Props>(), {
  title: '',
  autoplay: false,
  startTime: 0,
  showRelated: false,
  quality: 'maxresdefault',
});

// Emits
const emit = defineEmits<{
  (e: 'play'): void;
  (e: 'load'): void;
  (e: 'error'): void;
}>();

// Refs
const iframeRef = ref<HTMLIFrameElement | null>(null);

// State
const isPlaying = ref(false);
const isLoaded = ref(false);
const hasError = ref(false);

// Extract video ID from various YouTube URL formats
const extractVideoId = (url: string): string | null => {
  const patterns = [
    /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/,
    /^([^"&?\/\s]{11})$/,
  ];

  for (const pattern of patterns) {
    const match = url.match(pattern);
    if (match) {
      return match[1];
    }
  }
  return null;
};

// Computed
const videoId = computed(() => extractVideoId(props.url));

const thumbnailUrl = computed(() => {
  if (!videoId.value) return null;
  return `https://img.youtube.com/vi/${videoId.value}/${props.quality}.jpg`;
});

const embedUrl = computed(() => {
  if (!videoId.value) return '';
  
  const params = new URLSearchParams({
    rel: props.showRelated ? '1' : '0',
    modestbranding: '1',
    playsinline: '1',
    enablejsapi: '1',
  });

  if (props.autoplay || isPlaying.value) {
    params.set('autoplay', '1');
  }

  if (props.startTime > 0) {
    params.set('start', props.startTime.toString());
  }

  return `https://www.youtube.com/embed/${videoId.value}?${params.toString()}`;
});

const originalUrl = computed(() => {
  if (!videoId.value) return props.url;
  return `https://www.youtube.com/watch?v=${videoId.value}`;
});

// Methods
const play = () => {
  isPlaying.value = true;
  emit('play');
};

const handleIframeLoad = () => {
  isLoaded.value = true;
  emit('load');
};

const handleError = () => {
  hasError.value = true;
  emit('error');
};

// Lifecycle
onMounted(() => {
  if (props.autoplay) {
    isPlaying.value = true;
  }
});

// Watch for URL changes
watch(() => props.url, () => {
  isPlaying.value = false;
  isLoaded.value = false;
  hasError.value = false;
});
</script>

<style scoped>
.youtube-embed {
  --cinema-accent: #e50914;
  aspect-ratio: 16 / 9;
  width: 100%;
}

.youtube-iframe-container {
  position: relative;
  width: 100%;
  height: 100%;
}

.youtube-play-btn {
  animation: pulse-glow 2s ease-in-out infinite;
}

@keyframes pulse-glow {
  0%, 100% {
    box-shadow: 0 0 20px rgba(229, 9, 20, 0.4);
  }
  50% {
    box-shadow: 0 0 40px rgba(229, 9, 20, 0.6);
  }
}

/* YouTube red color option */
.youtube-embed--youtube-colors .youtube-play-btn {
  background-color: #ff0000;
}
</style>
