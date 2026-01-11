<template>
  <!-- Cinema Layout - Public-facing movie streaming UI layout -->
  <div class="cinema-layout cinema-app min-h-screen bg-cinema-bg">
    <!-- Screen Loader -->
    <div
      v-show="store.isShowMainLoader"
      class="fixed inset-0 z-[60] flex items-center justify-center bg-cinema-bg"
    >
      <div class="text-center">
        <!-- Animated Logo -->
        <div class="mb-4 flex items-center justify-center">
          <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-cinema-accent animate-pulse">
            <svg class="h-8 w-8 text-white" viewBox="0 0 24 24" fill="currentColor">
              <path d="M18 4l2 4h-3l-2-4h-2l2 4h-3l-2-4H8l2 4H7L5 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4h-4z" />
            </svg>
          </div>
        </div>
        
        <!-- Loading Spinner -->
        <div class="h-8 w-8 mx-auto animate-spin rounded-full border-2 border-white/20 border-t-cinema-accent" />
        
        <p class="mt-4 text-sm text-white/50">Loading...</p>
      </div>
    </div>

    <!-- Scroll to Top Button -->
    <Transition name="fade-slide-up">
      <button
        v-if="showTopButton"
        class="fixed bottom-6 right-6 z-40 flex h-12 w-12 items-center justify-center rounded-full bg-cinema-accent text-white shadow-lg shadow-cinema-accent/30 transition-all hover:bg-cinema-accent/90 hover:scale-110"
        @click="goToTop"
        aria-label="Scroll to top"
      >
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M18 15l-6-6-6 6" />
        </svg>
      </button>
    </Transition>

    <!-- Navigation -->
    <CinemaNavbar />

    <!-- Main Content Area -->
    <div class="cinema-main">
      <router-view v-slot="{ Component, route }">
        <Transition :name="transitionName" mode="out-in">
          <component :is="Component" :key="route.path" />
        </Transition>
      </router-view>
    </div>

    <!-- Footer -->
    <CinemaFooter />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import { useAppStore } from '@/stores/index';
import { CinemaNavbar, CinemaFooter } from '@/components/cinema';

// Store
const store = useAppStore();
const route = useRoute();

// State
const showTopButton = ref(false);
const lastScrollY = ref(0);

// Computed
const transitionName = computed(() => {
  // Use different transitions based on navigation direction
  return 'cinema-fade';
});

// Methods
const handleScroll = () => {
  const currentScrollY = window.scrollY;
  
  // Show button when scrolled down more than 300px
  showTopButton.value = currentScrollY > 300;
  
  lastScrollY.value = currentScrollY;
};

const goToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth',
  });
};

// Lifecycle
onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true });
  
  // Set dark theme for cinema layout
  document.body.classList.add('dark');
  
  // Hide main loader after initial load
  setTimeout(() => {
    store.isShowMainLoader = false;
  }, 500);
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>

<style scoped>
.cinema-layout {
  --cinema-bg: #0a0a0b;
  --cinema-bg-secondary: #111113;
  --cinema-bg-card: #161618;
  --cinema-bg-elevated: #1c1c1f;
  --cinema-accent: #e50914;
  --cinema-secondary: #f5c518;
}

/* Background styling */
.cinema-app {
  background-color: var(--cinema-bg);
  color: #ffffff;
}

/* Page Transitions */
.cinema-fade-enter-active,
.cinema-fade-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.cinema-fade-enter-from {
  opacity: 0;
  transform: translateY(10px);
}

.cinema-fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Scroll button transition */
.fade-slide-up-enter-active,
.fade-slide-up-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.fade-slide-up-enter-from,
.fade-slide-up-leave-to {
  opacity: 0;
  transform: translateY(20px);
}
</style>

<style>
/* Global cinema styles */
.cinema-app {
  font-family: 'DM Sans', 'Inter', system-ui, sans-serif;
}

/* Custom scrollbar for cinema layout */
.cinema-app::-webkit-scrollbar {
  width: 8px;
}

.cinema-app::-webkit-scrollbar-track {
  background: #0a0a0b;
}

.cinema-app::-webkit-scrollbar-thumb {
  background: #3f3f46;
  border-radius: 4px;
}

.cinema-app::-webkit-scrollbar-thumb:hover {
  background: #52525b;
}

/* Ensure proper spacing for fixed navbar */
.cinema-main {
  min-height: 100vh;
}

/* Cinema container */
.cinema-container {
  width: 100%;
  max-width: 1440px;
  margin: 0 auto;
  padding: 0 1rem;
}

@media (min-width: 640px) {
  .cinema-container {
    padding: 0 1.5rem;
  }
}

@media (min-width: 1024px) {
  .cinema-container {
    padding: 0 2rem;
  }
}

/* Cinema headings */
.cinema-heading-display {
  font-family: 'Bebas Neue', 'Oswald', sans-serif;
  letter-spacing: 0.05em;
  text-transform: uppercase;
}

/* Cinema buttons */
.cinema-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  font-size: 0.875rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-radius: 0.5rem;
  transition: all 0.3s ease;
  cursor: pointer;
  border: none;
  outline: none;
}

.cinema-btn-primary {
  background: linear-gradient(135deg, #e50914 0%, #b8070f 100%);
  color: #ffffff;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.cinema-btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 0 30px rgba(229, 9, 20, 0.5);
}

.cinema-btn-secondary {
  background: #1c1c1f;
  color: #ffffff;
  border: 1px solid #27272a;
}

.cinema-btn-secondary:hover {
  background: #27272a;
  border-color: #3f3f46;
}

/* Skeleton animation */
.cinema-skeleton {
  background: linear-gradient(
    90deg,
    #161618 25%,
    #1f1f22 50%,
    #161618 75%
  );
  background-size: 200% 100%;
  animation: skeleton-shimmer 1.5s infinite;
}

@keyframes skeleton-shimmer {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}

/* Animation fade-in */
.cinema-animate-fade-in {
  animation: cinema-fade-in 0.6s ease-out forwards;
}

@keyframes cinema-fade-in {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Movie grid responsive */
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

/* Horizontal scroll */
.cinema-scroll-x {
  display: flex;
  gap: 1rem;
  overflow-x: auto;
  scroll-snap-type: x mandatory;
  -webkit-overflow-scrolling: touch;
  padding-bottom: 1rem;
  scrollbar-width: none;
  -ms-overflow-style: none;
}

.cinema-scroll-x::-webkit-scrollbar {
  display: none;
}

.cinema-scroll-x > * {
  scroll-snap-align: start;
  flex-shrink: 0;
}
</style>
