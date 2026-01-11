<template>
  <!-- Movie Details Page - Full movie information with video player -->
  <div class="movie-details-page cinema-app">
    <!-- Loading State -->
    <div v-if="isLoading" class="cinema-container py-24">
      <SkeletonLoader type="movie-details" />
    </div>

    <!-- Error State -->
    <div
      v-else-if="!movie"
      class="cinema-container flex min-h-[60vh] flex-col items-center justify-center py-24 text-center"
    >
      <div class="mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-white/5">
        <svg class="h-12 w-12 text-white/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
      <h1 class="text-2xl font-bold text-white">Movie Not Found</h1>
      <p class="mt-2 text-white/50">The movie you're looking for doesn't exist or has been removed.</p>
      <router-link
        to="/movies"
        class="mt-6 cinema-btn cinema-btn-primary"
      >
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M19 12H5M12 19l-7-7 7-7" />
        </svg>
        Back to Movies
      </router-link>
    </div>

    <!-- Movie Content -->
    <template v-else>
      <!-- Background Blur Effect -->
      <div class="fixed inset-0 -z-10">
        <img
          :src="movie.image"
          :alt="movie.name"
          class="h-full w-full object-cover opacity-20 blur-3xl"
        />
        <div class="absolute inset-0 bg-cinema-bg/90" />
      </div>

      <!-- Back Navigation -->
      <div class="cinema-container pt-24">
        <router-link
          to="/movies"
          class="inline-flex items-center gap-2 text-sm text-white/60 transition-colors hover:text-white"
        >
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M19 12H5M12 19l-7-7 7-7" />
          </svg>
          Back to Movies
        </router-link>
      </div>

      <!-- Main Content -->
      <main class="cinema-container py-8">
        <div class="grid gap-8 lg:grid-cols-3 lg:gap-12">
          <!-- Video & Info Column -->
          <div class="lg:col-span-2">
            <!-- Video Player Section -->
            <section class="mb-8">
              <div class="overflow-hidden rounded-xl shadow-2xl">
                <!-- YouTube Video -->
                <YouTubeEmbed
                  v-if="isYouTubeVideo"
                  :url="movie.video_link!"
                  :title="movie.name"
                  :autoplay="false"
                />

                <!-- Native Video Player -->
                <VideoPlayer
                  v-else-if="movie.video_file"
                  :src="movie.video_file"
                  :poster="movie.image"
                />

                <!-- No Video Available - Show Poster -->
                <div
                  v-else
                  class="relative aspect-video overflow-hidden bg-cinema-bg-card"
                >
                  <img
                    :src="movie.image"
                    :alt="movie.name"
                    class="h-full w-full object-cover"
                  />
                  <div class="absolute inset-0 flex flex-col items-center justify-center bg-black/60">
                    <svg class="mb-4 h-16 w-16 text-white/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                      <path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <p class="text-lg font-medium text-white">Video coming soon</p>
                    <p class="mt-1 text-sm text-white/50">Check back later for updates</p>
                  </div>
                </div>
              </div>
            </section>

            <!-- Movie Title & Meta -->
            <section class="mb-6">
              <h1 class="cinema-heading-display text-3xl font-bold tracking-tight text-white md:text-4xl">
                {{ movie.name }}
              </h1>

              <!-- Meta Info -->
              <div class="mt-4 flex flex-wrap items-center gap-4 text-sm text-white/70">
                <!-- Rating -->
                <div class="flex items-center gap-1.5 text-cinema-secondary">
                  <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                  </svg>
                  <span class="font-bold">{{ movie.rating }}</span>
                  <span class="text-white/40">/5</span>
                </div>

                <span class="h-1 w-1 rounded-full bg-white/30" />
                <span>{{ movie.release }}</span>
                <span class="h-1 w-1 rounded-full bg-white/30" />
                <span>{{ movie.duration }}</span>
                <span class="h-1 w-1 rounded-full bg-white/30" />
                <span>{{ movie.category.name }}</span>
              </div>
            </section>

            <!-- Genre Tags -->
            <section class="mb-6">
              <GenreTags
                :genres="[movie.genre]"
                :tags="movie.tags"
                size="md"
                variant="outlined"
                clickable
                @genre-click="handleGenreClick"
                @tag-click="handleTagClick"
              />
            </section>

            <!-- Description -->
            <section class="mb-8">
              <h2 class="mb-3 text-lg font-semibold text-white">Overview</h2>
              <p class="leading-relaxed text-white/70">
                {{ movie.details }}
              </p>
            </section>

            <!-- Cast Section -->
            <section v-if="movie.casts && movie.casts.length > 0" class="mb-8">
              <h2 class="mb-4 text-lg font-semibold text-white">Cast</h2>
              <div class="flex flex-wrap gap-4">
                <div
                  v-for="cast in movie.casts"
                  :key="cast.id"
                  class="flex items-center gap-3 rounded-lg bg-white/5 p-3 transition-colors hover:bg-white/10"
                >
                  <img
                    :src="cast.image"
                    :alt="cast.name"
                    class="h-12 w-12 rounded-full object-cover"
                  />
                  <div>
                    <p class="font-medium text-white">{{ cast.name }}</p>
                    <p class="text-xs text-white/50">{{ cast.role }}</p>
                  </div>
                </div>
              </div>
            </section>

            <!-- Advertisement -->
            <section v-if="advertisement" class="mb-8">
              <AdvertisementBanner
                :advertisement="advertisement"
                overlay-position="left"
                :show-label="true"
                cta-text="Learn More"
              />
            </section>
          </div>

          <!-- Sidebar -->
          <aside class="lg:col-span-1">
            <!-- Movie Poster -->
            <div class="mb-6 hidden overflow-hidden rounded-xl shadow-xl lg:block">
              <img
                :src="movie.image"
                :alt="movie.name"
                class="w-full object-cover"
              />
            </div>

            <!-- Quick Info Card -->
            <div class="mb-6 rounded-xl bg-white/5 p-6">
              <h3 class="mb-4 font-semibold text-white">Movie Info</h3>
              <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                  <dt class="text-white/50">Genre</dt>
                  <dd class="text-white">{{ movie.genre.name }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-white/50">Category</dt>
                  <dd class="text-white">{{ movie.category.name }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-white/50">Release Year</dt>
                  <dd class="text-white">{{ movie.release }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-white/50">Duration</dt>
                  <dd class="text-white">{{ movie.duration }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-white/50">Rating</dt>
                  <dd class="flex items-center gap-1 text-cinema-secondary">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                    </svg>
                    {{ movie.rating }}
                  </dd>
                </div>
              </dl>
            </div>

            <!-- Share Buttons -->
            <div class="mb-6 rounded-xl bg-white/5 p-6">
              <h3 class="mb-4 font-semibold text-white">Share</h3>
              <div class="flex gap-3">
                <button
                  v-for="social in socialButtons"
                  :key="social.name"
                  class="flex h-10 w-10 items-center justify-center rounded-full bg-white/5 text-white/60 transition-all hover:bg-cinema-accent hover:text-white"
                  :title="`Share on ${social.name}`"
                  @click="shareOn(social.name)"
                >
                  <component :is="social.icon" class="h-5 w-5" />
                </button>
              </div>
            </div>

            <!-- Tags -->
            <div v-if="movie.tags.length > 0" class="rounded-xl bg-white/5 p-6">
              <h3 class="mb-4 font-semibold text-white">Tags</h3>
              <div class="flex flex-wrap gap-2">
                <router-link
                  v-for="tag in movie.tags"
                  :key="tag.id"
                  :to="{ name: 'movies', query: { tag: tag.id } }"
                  class="rounded-full bg-white/5 px-3 py-1.5 text-xs text-white/60 transition-colors hover:bg-cinema-accent hover:text-white"
                >
                  #{{ tag.name }}
                </router-link>
              </div>
            </div>
          </aside>
        </div>

        <!-- Related Movies -->
        <section class="mt-12 border-t border-white/5 pt-12">
          <RelatedMovies
            :movies="relatedMovies"
            title="You May Also Like"
            :subtitle="`More ${movie.genre.name} movies`"
            :view-all-link="`/movies?genre=${movie.genre_id}`"
          />
        </section>
      </main>
    </template>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch, h } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useMovieStore } from '@/stores/movies';
import { useMeta } from '@/composables/use-meta';
import type { Movie, Genre, Tag } from '@/data/mockMovies';
import {
  VideoPlayer,
  YouTubeEmbed,
  GenreTags,
  RelatedMovies,
  AdvertisementBanner,
  SkeletonLoader,
} from '@/components/cinema';

// Store & Router
const movieStore = useMovieStore();
const route = useRoute();
const router = useRouter();

// State
const isLoading = ref(true);
const movie = ref<Movie | null>(null);

// Meta
const pageTitle = computed(() => 
  movie.value ? `${movie.value.name} - VistroVideo` : 'Loading... - VistroVideo'
);
useMeta({ title: pageTitle.value });

// Computed
const isYouTubeVideo = computed(() => {
  if (!movie.value) return false;
  return movie.value.video_link && 
    (movie.value.video_link.includes('youtube.com') || movie.value.video_link.includes('youtu.be'));
});

const relatedMovies = computed(() => {
  if (!movie.value) return [];
  return movieStore.getRelated(movie.value.id, 8);
});

const advertisement = computed(() => movieStore.bannerAds[0] || null);

// Social share icons
const FacebookIcon = {
  render() {
    return h('svg', { viewBox: '0 0 24 24', fill: 'currentColor' }, [
      h('path', { d: 'M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z' })
    ]);
  }
};

const TwitterIcon = {
  render() {
    return h('svg', { viewBox: '0 0 24 24', fill: 'currentColor' }, [
      h('path', { d: 'M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z' })
    ]);
  }
};

const LinkIcon = {
  render() {
    return h('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('path', { d: 'M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71' }),
      h('path', { d: 'M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71' })
    ]);
  }
};

const socialButtons = [
  { name: 'Facebook', icon: FacebookIcon },
  { name: 'Twitter', icon: TwitterIcon },
  { name: 'Copy Link', icon: LinkIcon },
];

// Methods
const loadMovie = async () => {
  isLoading.value = true;
  const movieId = parseInt(route.params.id as string);
  
  // Simulate loading
  await new Promise(resolve => setTimeout(resolve, 500));
  
  movie.value = movieStore.getMovieById(movieId) || null;
  isLoading.value = false;
};

const handleGenreClick = (genre: Genre) => {
  router.push({ name: 'movies', query: { genre: genre.id } });
};

const handleTagClick = (tag: Tag) => {
  router.push({ name: 'movies', query: { tag: tag.id } });
};

const shareOn = (platform: string) => {
  const url = window.location.href;
  const title = movie.value?.name || 'Check out this movie';
  
  switch (platform) {
    case 'Facebook':
      window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
      break;
    case 'Twitter':
      window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`, '_blank');
      break;
    case 'Copy Link':
      navigator.clipboard.writeText(url);
      alert('Link copied to clipboard!');
      break;
  }
};

// Watch for route changes
watch(
  () => route.params.id,
  () => {
    loadMovie();
  }
);

// Lifecycle
onMounted(() => {
  loadMovie();
});
</script>

<style scoped>
.movie-details-page {
  --cinema-bg: #0a0a0b;
  --cinema-bg-secondary: #111113;
  --cinema-bg-card: #161618;
  --cinema-bg-elevated: #1c1c1f;
  --cinema-accent: #e50914;
  --cinema-secondary: #f5c518;
}
</style>
