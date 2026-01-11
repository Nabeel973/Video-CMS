<template>
  <!-- Category List Component - Horizontal scrollable category navigation -->
  <section class="category-list">
    <!-- Section Header -->
    <div v-if="title" class="mb-4 flex items-center justify-between">
      <h2 class="cinema-heading-display text-lg font-bold tracking-wide text-white">
        {{ title }}
      </h2>
    </div>

    <!-- Categories Container -->
    <div class="relative">
      <!-- Scroll Shadow Left -->
      <div
        v-show="canScrollLeft"
        class="pointer-events-none absolute bottom-0 left-0 top-0 z-10 w-12 bg-gradient-to-r from-cinema-bg to-transparent"
      />

      <!-- Scroll Button Left -->
      <button
        v-show="canScrollLeft"
        class="absolute left-0 top-1/2 z-20 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-white backdrop-blur-sm transition-all hover:bg-white/20"
        @click="scrollLeft"
      >
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M15 18l-6-6 6-6" />
        </svg>
      </button>

      <!-- Categories Scroll Container -->
      <div
        ref="scrollContainer"
        class="category-scroll flex gap-3 overflow-x-auto pb-2 scrollbar-hide"
        @scroll="updateScrollState"
      >
        <!-- All Category Button -->
        <button
          class="category-item shrink-0 rounded-full px-5 py-2.5 text-sm font-medium transition-all"
          :class="[
            selectedId === null
              ? 'bg-cinema-accent text-white shadow-lg shadow-cinema-accent/30'
              : 'bg-white/5 text-white/70 hover:bg-white/10 hover:text-white'
          ]"
          @click="selectCategory(null)"
        >
          All {{ itemLabel }}
        </button>

        <!-- Category Items -->
        <button
          v-for="category in items"
          :key="category.id"
          class="category-item shrink-0 rounded-full px-5 py-2.5 text-sm font-medium transition-all"
          :class="[
            selectedId === category.id
              ? 'bg-cinema-accent text-white shadow-lg shadow-cinema-accent/30'
              : 'bg-white/5 text-white/70 hover:bg-white/10 hover:text-white'
          ]"
          @click="selectCategory(category.id)"
        >
          {{ category.name }}
        </button>
      </div>

      <!-- Scroll Shadow Right -->
      <div
        v-show="canScrollRight"
        class="pointer-events-none absolute bottom-0 right-0 top-0 z-10 w-12 bg-gradient-to-l from-cinema-bg to-transparent"
      />

      <!-- Scroll Button Right -->
      <button
        v-show="canScrollRight"
        class="absolute right-0 top-1/2 z-20 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-white backdrop-blur-sm transition-all hover:bg-white/20"
        @click="scrollRight"
      >
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M9 18l6-6-6-6" />
        </svg>
      </button>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';

// Props
interface CategoryItem {
  id: number;
  name: string;
  status?: string;
}

interface Props {
  items: CategoryItem[];
  title?: string;
  itemLabel?: string;
  modelValue?: number | null;
}

const props = withDefaults(defineProps<Props>(), {
  title: '',
  itemLabel: '',
  modelValue: null,
});

// Emits
const emit = defineEmits<{
  (e: 'update:modelValue', value: number | null): void;
  (e: 'select', value: number | null): void;
}>();

// State
const scrollContainer = ref<HTMLElement | null>(null);
const canScrollLeft = ref(false);
const canScrollRight = ref(false);
const selectedId = ref<number | null>(props.modelValue);

// Methods
const selectCategory = (id: number | null) => {
  selectedId.value = id;
  emit('update:modelValue', id);
  emit('select', id);
};

const updateScrollState = () => {
  if (!scrollContainer.value) return;
  
  const { scrollLeft, scrollWidth, clientWidth } = scrollContainer.value;
  canScrollLeft.value = scrollLeft > 0;
  canScrollRight.value = scrollLeft < scrollWidth - clientWidth - 10;
};

const scrollLeft = () => {
  if (!scrollContainer.value) return;
  scrollContainer.value.scrollBy({ left: -200, behavior: 'smooth' });
};

const scrollRight = () => {
  if (!scrollContainer.value) return;
  scrollContainer.value.scrollBy({ left: 200, behavior: 'smooth' });
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
.category-scroll {
  scrollbar-width: none;
  -ms-overflow-style: none;
}

.category-scroll::-webkit-scrollbar {
  display: none;
}

.category-item {
  will-change: transform, background-color;
}

.category-item:focus-visible {
  outline: 2px solid var(--cinema-accent, #e50914);
  outline-offset: 2px;
}

/* Background variable for gradient */
.category-list {
  --cinema-bg: #0a0a0b;
  --cinema-accent: #e50914;
}
</style>
