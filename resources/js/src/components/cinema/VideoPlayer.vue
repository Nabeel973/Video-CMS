<template>
  <!-- Video Player Component - Native HTML5 video player with custom controls -->
  <div
    class="video-player relative overflow-hidden rounded-xl bg-black"
    :class="{ 'cursor-none': isPlaying && !showControls }"
    @mousemove="handleMouseMove"
    @mouseleave="hideControls"
  >
    <!-- Video Element -->
    <video
      ref="videoRef"
      class="h-full w-full object-contain"
      :src="src"
      :poster="poster"
      :autoplay="autoplay"
      :loop="loop"
      :muted="muted"
      preload="metadata"
      playsinline
      @loadedmetadata="handleLoadedMetadata"
      @timeupdate="handleTimeUpdate"
      @play="isPlaying = true"
      @pause="isPlaying = false"
      @ended="handleEnded"
      @waiting="isBuffering = true"
      @playing="isBuffering = false"
      @error="handleError"
    />

    <!-- Loading Spinner -->
    <div
      v-if="isBuffering"
      class="absolute inset-0 flex items-center justify-center bg-black/30"
    >
      <div class="h-12 w-12 animate-spin rounded-full border-4 border-white/20 border-t-cinema-accent" />
    </div>

    <!-- Play/Pause Overlay (Center) -->
    <div
      v-show="!isPlaying && !isBuffering"
      class="absolute inset-0 flex cursor-pointer items-center justify-center bg-black/40 transition-opacity"
      @click="togglePlay"
    >
      <button
        class="flex h-20 w-20 items-center justify-center rounded-full bg-cinema-accent text-white shadow-2xl shadow-cinema-accent/40 transition-transform hover:scale-110"
      >
        <svg class="ml-1 h-8 w-8" viewBox="0 0 24 24" fill="currentColor">
          <path d="M8 5v14l11-7z" />
        </svg>
      </button>
    </div>

    <!-- Video Controls -->
    <div
      class="video-controls absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/90 via-black/60 to-transparent p-4 transition-opacity duration-300"
      :class="{ 'opacity-0': isPlaying && !showControls }"
    >
      <!-- Progress Bar -->
      <div class="mb-3">
        <div
          class="group relative h-1 cursor-pointer rounded-full bg-white/20"
          @click="seek"
          @mousemove="handleProgressHover"
          @mouseleave="hoverTime = null"
        >
          <!-- Buffered -->
          <div
            class="absolute h-full rounded-full bg-white/30"
            :style="{ width: `${bufferedPercent}%` }"
          />
          <!-- Played -->
          <div
            class="absolute h-full rounded-full bg-cinema-accent transition-all"
            :style="{ width: `${progressPercent}%` }"
          />
          <!-- Scrubber -->
          <div
            class="absolute top-1/2 h-3 w-3 -translate-x-1/2 -translate-y-1/2 scale-0 rounded-full bg-cinema-accent shadow-lg transition-transform group-hover:scale-100"
            :style="{ left: `${progressPercent}%` }"
          />
          <!-- Hover Time Tooltip -->
          <div
            v-if="hoverTime !== null"
            class="absolute -top-8 -translate-x-1/2 rounded bg-black/80 px-2 py-1 text-xs text-white"
            :style="{ left: `${hoverPosition}%` }"
          >
            {{ formatTime(hoverTime) }}
          </div>
        </div>
      </div>

      <!-- Control Buttons -->
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <!-- Play/Pause -->
          <button
            class="text-white transition-colors hover:text-cinema-accent"
            @click="togglePlay"
          >
            <svg v-if="isPlaying" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
              <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" />
            </svg>
            <svg v-else class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
              <path d="M8 5v14l11-7z" />
            </svg>
          </button>

          <!-- Skip Backward -->
          <button
            class="text-white/70 transition-colors hover:text-white"
            @click="skip(-10)"
          >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 5V1L7 6l5 5V7c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6H4c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8z" />
              <text x="9" y="15" font-size="7" fill="currentColor">10</text>
            </svg>
          </button>

          <!-- Skip Forward -->
          <button
            class="text-white/70 transition-colors hover:text-white"
            @click="skip(10)"
          >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 5V1l5 5-5 5V7c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6h2c0 4.42-3.58 8-8 8s-8-3.58-8-8 3.58-8 8-8z" />
              <text x="9" y="15" font-size="7" fill="currentColor">10</text>
            </svg>
          </button>

          <!-- Volume -->
          <div class="group relative flex items-center">
            <button
              class="text-white/70 transition-colors hover:text-white"
              @click="toggleMute"
            >
              <svg v-if="isMuted || volume === 0" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                <path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z" />
              </svg>
              <svg v-else-if="volume < 0.5" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                <path d="M18.5 12c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM5 9v6h4l5 5V4L9 9H5z" />
              </svg>
              <svg v-else class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z" />
              </svg>
            </button>
            
            <!-- Volume Slider -->
            <div class="ml-2 w-0 overflow-hidden transition-all group-hover:w-20">
              <input
                type="range"
                min="0"
                max="1"
                step="0.1"
                :value="volume"
                class="volume-slider h-1 w-full cursor-pointer appearance-none rounded-full bg-white/30"
                @input="setVolume"
              />
            </div>
          </div>

          <!-- Time Display -->
          <span class="text-sm text-white/70">
            {{ formatTime(currentTime) }} / {{ formatTime(duration) }}
          </span>
        </div>

        <div class="flex items-center gap-3">
          <!-- Playback Speed -->
          <div class="relative">
            <button
              class="text-sm text-white/70 transition-colors hover:text-white"
              @click="showSpeedMenu = !showSpeedMenu"
            >
              {{ playbackRate }}x
            </button>
            <div
              v-show="showSpeedMenu"
              class="absolute bottom-full right-0 mb-2 rounded-lg bg-black/90 p-2 backdrop-blur-sm"
            >
              <button
                v-for="speed in playbackSpeeds"
                :key="speed"
                class="block w-full rounded px-3 py-1 text-left text-sm transition-colors"
                :class="playbackRate === speed ? 'bg-cinema-accent text-white' : 'text-white/70 hover:bg-white/10'"
                @click="setPlaybackRate(speed)"
              >
                {{ speed }}x
              </button>
            </div>
          </div>

          <!-- Fullscreen -->
          <button
            class="text-white/70 transition-colors hover:text-white"
            @click="toggleFullscreen"
          >
            <svg v-if="!isFullscreen" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
              <path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z" />
            </svg>
            <svg v-else class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
              <path d="M5 16h3v3h2v-5H5v2zm3-8H5v2h5V5H8v3zm6 11h2v-3h3v-2h-5v5zm2-11V5h-2v5h5V8h-3z" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div
      v-if="hasError"
      class="absolute inset-0 flex flex-col items-center justify-center bg-black/80"
    >
      <svg class="mb-4 h-16 w-16 text-cinema-accent" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
      </svg>
      <p class="text-lg font-medium text-white">Error loading video</p>
      <p class="mt-1 text-sm text-white/60">Please try again later</p>
      <button
        class="mt-4 cinema-btn cinema-btn-primary"
        @click="retryLoad"
      >
        Retry
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';

// Props
interface Props {
  src: string;
  poster?: string;
  autoplay?: boolean;
  loop?: boolean;
  muted?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  poster: '',
  autoplay: false,
  loop: false,
  muted: false,
});

// Emits
const emit = defineEmits<{
  (e: 'play'): void;
  (e: 'pause'): void;
  (e: 'ended'): void;
  (e: 'timeupdate', time: number): void;
}>();

// Refs
const videoRef = ref<HTMLVideoElement | null>(null);

// State
const isPlaying = ref(false);
const isBuffering = ref(false);
const isMuted = ref(props.muted);
const isFullscreen = ref(false);
const hasError = ref(false);
const showControls = ref(true);
const showSpeedMenu = ref(false);

const currentTime = ref(0);
const duration = ref(0);
const buffered = ref(0);
const volume = ref(1);
const playbackRate = ref(1);
const hoverTime = ref<number | null>(null);
const hoverPosition = ref(0);

const controlsTimeout = ref<ReturnType<typeof setTimeout> | null>(null);

const playbackSpeeds = [0.5, 0.75, 1, 1.25, 1.5, 2];

// Computed
const progressPercent = computed(() => {
  if (duration.value === 0) return 0;
  return (currentTime.value / duration.value) * 100;
});

const bufferedPercent = computed(() => {
  if (duration.value === 0) return 0;
  return (buffered.value / duration.value) * 100;
});

// Methods
const togglePlay = () => {
  if (!videoRef.value) return;
  
  if (isPlaying.value) {
    videoRef.value.pause();
    emit('pause');
  } else {
    videoRef.value.play();
    emit('play');
  }
};

const toggleMute = () => {
  if (!videoRef.value) return;
  isMuted.value = !isMuted.value;
  videoRef.value.muted = isMuted.value;
};

const setVolume = (e: Event) => {
  if (!videoRef.value) return;
  const target = e.target as HTMLInputElement;
  volume.value = parseFloat(target.value);
  videoRef.value.volume = volume.value;
  isMuted.value = volume.value === 0;
};

const seek = (e: MouseEvent) => {
  if (!videoRef.value) return;
  const rect = (e.currentTarget as HTMLElement).getBoundingClientRect();
  const percent = (e.clientX - rect.left) / rect.width;
  videoRef.value.currentTime = percent * duration.value;
};

const skip = (seconds: number) => {
  if (!videoRef.value) return;
  videoRef.value.currentTime = Math.max(0, Math.min(duration.value, currentTime.value + seconds));
};

const setPlaybackRate = (rate: number) => {
  if (!videoRef.value) return;
  playbackRate.value = rate;
  videoRef.value.playbackRate = rate;
  showSpeedMenu.value = false;
};

const toggleFullscreen = async () => {
  const container = videoRef.value?.parentElement;
  if (!container) return;

  try {
    if (!document.fullscreenElement) {
      await container.requestFullscreen();
      isFullscreen.value = true;
    } else {
      await document.exitFullscreen();
      isFullscreen.value = false;
    }
  } catch (err) {
    console.error('Fullscreen error:', err);
  }
};

const handleLoadedMetadata = () => {
  if (!videoRef.value) return;
  duration.value = videoRef.value.duration;
};

const handleTimeUpdate = () => {
  if (!videoRef.value) return;
  currentTime.value = videoRef.value.currentTime;
  
  // Update buffered
  if (videoRef.value.buffered.length > 0) {
    buffered.value = videoRef.value.buffered.end(videoRef.value.buffered.length - 1);
  }
  
  emit('timeupdate', currentTime.value);
};

const handleEnded = () => {
  isPlaying.value = false;
  emit('ended');
};

const handleError = () => {
  hasError.value = true;
  isBuffering.value = false;
};

const retryLoad = () => {
  hasError.value = false;
  if (videoRef.value) {
    videoRef.value.load();
  }
};

const handleProgressHover = (e: MouseEvent) => {
  const rect = (e.currentTarget as HTMLElement).getBoundingClientRect();
  const percent = (e.clientX - rect.left) / rect.width;
  hoverPosition.value = percent * 100;
  hoverTime.value = percent * duration.value;
};

const handleMouseMove = () => {
  showControls.value = true;
  
  if (controlsTimeout.value) {
    clearTimeout(controlsTimeout.value);
  }
  
  if (isPlaying.value) {
    controlsTimeout.value = setTimeout(() => {
      showControls.value = false;
    }, 3000);
  }
};

const hideControls = () => {
  if (isPlaying.value) {
    showControls.value = false;
  }
};

const formatTime = (seconds: number): string => {
  if (isNaN(seconds) || !isFinite(seconds)) return '0:00';
  
  const mins = Math.floor(seconds / 60);
  const secs = Math.floor(seconds % 60);
  return `${mins}:${secs.toString().padStart(2, '0')}`;
};

// Keyboard shortcuts
const handleKeydown = (e: KeyboardEvent) => {
  if (!videoRef.value) return;
  
  switch (e.key) {
    case ' ':
    case 'k':
      e.preventDefault();
      togglePlay();
      break;
    case 'ArrowLeft':
      e.preventDefault();
      skip(-10);
      break;
    case 'ArrowRight':
      e.preventDefault();
      skip(10);
      break;
    case 'ArrowUp':
      e.preventDefault();
      volume.value = Math.min(1, volume.value + 0.1);
      videoRef.value.volume = volume.value;
      break;
    case 'ArrowDown':
      e.preventDefault();
      volume.value = Math.max(0, volume.value - 0.1);
      videoRef.value.volume = volume.value;
      break;
    case 'm':
      toggleMute();
      break;
    case 'f':
      toggleFullscreen();
      break;
  }
};

// Fullscreen change handler
const handleFullscreenChange = () => {
  isFullscreen.value = !!document.fullscreenElement;
};

// Lifecycle
onMounted(() => {
  document.addEventListener('keydown', handleKeydown);
  document.addEventListener('fullscreenchange', handleFullscreenChange);
});

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown);
  document.removeEventListener('fullscreenchange', handleFullscreenChange);
  if (controlsTimeout.value) {
    clearTimeout(controlsTimeout.value);
  }
});

// Watch for muted prop changes
watch(() => props.muted, (newVal) => {
  isMuted.value = newVal;
  if (videoRef.value) {
    videoRef.value.muted = newVal;
  }
});
</script>

<style scoped>
.video-player {
  --cinema-accent: #e50914;
}

/* Volume slider styling */
.volume-slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: white;
  cursor: pointer;
}

.volume-slider::-moz-range-thumb {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: white;
  cursor: pointer;
  border: none;
}

/* Fullscreen adjustments */
:fullscreen .video-player {
  width: 100vw;
  height: 100vh;
  border-radius: 0;
}

:fullscreen video {
  width: 100%;
  height: 100%;
}
</style>
