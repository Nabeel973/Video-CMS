<template>
  <!-- Cinema Footer Component - Site footer with links and social -->
  <footer class="cinema-footer border-t border-white/5 bg-cinema-bg-secondary">
    <div class="cinema-container">
      <!-- Main Footer Content -->
      <div class="grid gap-8 py-12 md:grid-cols-2 lg:grid-cols-4">
        <!-- Brand Column -->
        <div class="lg:col-span-1">
          <router-link
            to="/movies"
            class="inline-flex items-center gap-2 text-xl font-bold tracking-tight text-white"
          >
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-cinema-accent">
              <svg class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                <path d="M18 4l2 4h-3l-2-4h-2l2 4h-3l-2-4H8l2 4H7L5 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4h-4z" />
              </svg>
            </div>
            <span>
              <span class="text-cinema-accent">Vistro</span>Video
            </span>
          </router-link>
          
          <p class="mt-4 text-sm leading-relaxed text-white/50">
            Your ultimate destination for movies and entertainment. Stream thousands of movies, TV shows, and documentaries.
          </p>

          <!-- Social Links -->
          <div class="mt-6 flex gap-3">
            <a
              v-for="social in socialLinks"
              :key="social.name"
              :href="social.url"
              target="_blank"
              rel="noopener noreferrer"
              class="flex h-10 w-10 items-center justify-center rounded-full bg-white/5 text-white/60 transition-all hover:bg-cinema-accent hover:text-white"
              :aria-label="social.name"
            >
              <component :is="social.icon" class="h-5 w-5" />
            </a>
          </div>
        </div>

        <!-- Quick Links -->
        <div>
          <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-white">
            Quick Links
          </h3>
          <ul class="space-y-3">
            <li v-for="link in quickLinks" :key="link.path">
              <router-link
                :to="link.path"
                class="text-sm text-white/50 transition-colors hover:text-cinema-accent"
              >
                {{ link.label }}
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Genres -->
        <div>
          <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-white">
            Genres
          </h3>
          <ul class="space-y-3">
            <li v-for="genre in displayedGenres" :key="genre.id">
              <router-link
                :to="{ name: 'movies', query: { genre: genre.id } }"
                class="text-sm text-white/50 transition-colors hover:text-cinema-accent"
              >
                {{ genre.name }}
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Newsletter -->
        <div>
          <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-white">
            Stay Updated
          </h3>
          <p class="mb-4 text-sm text-white/50">
            Subscribe to get the latest updates on new releases and exclusive content.
          </p>
          
          <form class="flex gap-2" @submit.prevent="handleSubscribe">
            <input
              v-model="email"
              type="email"
              placeholder="Enter your email"
              class="flex-1 rounded-lg bg-white/5 px-4 py-2.5 text-sm text-white placeholder-white/40 outline-none ring-1 ring-white/10 transition-all focus:ring-cinema-accent/50"
              required
            />
            <button
              type="submit"
              class="rounded-lg bg-cinema-accent px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-cinema-accent/80"
            >
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" />
              </svg>
            </button>
          </form>

          <p v-if="subscribeMessage" class="mt-2 text-xs text-cinema-secondary">
            {{ subscribeMessage }}
          </p>
        </div>
      </div>

      <!-- Bottom Bar -->
      <div class="flex flex-col items-center justify-between gap-4 border-t border-white/5 py-6 md:flex-row">
        <p class="text-sm text-white/40">
          © {{ currentYear }} VistroVideo. All rights reserved.
        </p>
        
        <div class="flex flex-wrap items-center justify-center gap-6">
          <router-link
            v-for="link in legalLinks"
            :key="link.path"
            :to="link.path"
            class="text-xs text-white/40 transition-colors hover:text-white/70"
          >
            {{ link.label }}
          </router-link>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
import { ref, computed, h } from 'vue';
import { useMovieStore } from '@/stores/movies';

// Store
const movieStore = useMovieStore();

// State
const email = ref('');
const subscribeMessage = ref('');

// Computed
const currentYear = computed(() => new Date().getFullYear());
const displayedGenres = computed(() => movieStore.activeGenres.slice(0, 6));

// Navigation Links
const quickLinks = [
  { path: '/movies', label: 'Home' },
  { path: '/movies?filter=trending', label: 'Trending' },
  { path: '/movies?filter=new', label: 'New Releases' },
  { path: '/movies?category=2', label: 'TV Series' },
  { path: '/movies?category=5', label: 'Documentaries' },
];

const legalLinks = [
  { path: '#', label: 'Terms of Service' },
  { path: '#', label: 'Privacy Policy' },
  { path: '#', label: 'Cookie Policy' },
  { path: '#', label: 'Contact Us' },
];

// Social Icons (inline SVG components)
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

const InstagramIcon = {
  render() {
    return h('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2' }, [
      h('rect', { x: '2', y: '2', width: '20', height: '20', rx: '5', ry: '5' }),
      h('path', { d: 'M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z' }),
      h('line', { x1: '17.5', y1: '6.5', x2: '17.51', y2: '6.5' })
    ]);
  }
};

const YoutubeIcon = {
  render() {
    return h('svg', { viewBox: '0 0 24 24', fill: 'currentColor' }, [
      h('path', { d: 'M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z' })
    ]);
  }
};

const socialLinks = [
  { name: 'Facebook', url: 'https://facebook.com', icon: FacebookIcon },
  { name: 'Twitter', url: 'https://twitter.com', icon: TwitterIcon },
  { name: 'Instagram', url: 'https://instagram.com', icon: InstagramIcon },
  { name: 'YouTube', url: 'https://youtube.com', icon: YoutubeIcon },
];

// Methods
const handleSubscribe = () => {
  if (email.value) {
    subscribeMessage.value = 'Thanks for subscribing!';
    email.value = '';
    setTimeout(() => {
      subscribeMessage.value = '';
    }, 3000);
  }
};
</script>

<style scoped>
.cinema-footer {
  --cinema-bg-secondary: #111113;
  --cinema-accent: #e50914;
  --cinema-secondary: #f5c518;
}
</style>
