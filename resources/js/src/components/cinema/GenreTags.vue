<template>
  <!-- Genre Tags Component - Displays genre and tag badges -->
  <div class="genre-tags" :class="wrapperClass">
    <!-- Genres -->
    <template v-if="genres.length > 0">
      <component
        :is="clickable ? 'button' : 'span'"
        v-for="genre in displayedGenres"
        :key="`genre-${genre.id}`"
        class="genre-tag"
        :class="[
          size === 'sm' ? 'text-xs px-2.5 py-1' : 'text-sm px-3 py-1.5',
          variant === 'filled' 
            ? 'bg-cinema-accent text-white' 
            : 'bg-white/5 text-white/80 border border-white/10 hover:bg-white/10 hover:text-white',
        ]"
        @click="clickable && handleGenreClick(genre)"
      >
        {{ genre.name }}
      </component>
    </template>

    <!-- Tags -->
    <template v-if="tags.length > 0 && showTags">
      <component
        :is="clickable ? 'button' : 'span'"
        v-for="tag in displayedTags"
        :key="`tag-${tag.id}`"
        class="genre-tag"
        :class="[
          size === 'sm' ? 'text-xs px-2.5 py-1' : 'text-sm px-3 py-1.5',
          'bg-cinema-secondary/10 text-cinema-secondary border border-cinema-secondary/20 hover:bg-cinema-secondary/20',
        ]"
        @click="clickable && handleTagClick(tag)"
      >
        <svg v-if="showTagIcon" class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="currentColor">
          <path d="M5.5 7a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm7.707 14.707a1 1 0 01-1.414 0l-9.5-9.5A1 1 0 012 11.5V4a2 2 0 012-2h7.5a1 1 0 01.707.293l9.5 9.5a1 1 0 010 1.414l-8.5 8.5z" />
        </svg>
        {{ tag.name }}
      </component>
    </template>

    <!-- Show More -->
    <button
      v-if="hasMore"
      class="genre-tag text-xs bg-white/5 text-white/60 px-2.5 py-1 hover:bg-white/10 hover:text-white"
      @click="showAll = !showAll"
    >
      {{ showAll ? 'Show Less' : `+${hiddenCount} more` }}
    </button>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import type { Genre, Tag } from '@/data/mockMovies';

// Props
interface Props {
  genres?: Genre[];
  tags?: Tag[];
  showTags?: boolean;
  showTagIcon?: boolean;
  limit?: number;
  size?: 'sm' | 'md';
  variant?: 'filled' | 'outlined';
  clickable?: boolean;
  wrapperClass?: string;
}

const props = withDefaults(defineProps<Props>(), {
  genres: () => [],
  tags: () => [],
  showTags: true,
  showTagIcon: false,
  limit: 0,
  size: 'sm',
  variant: 'outlined',
  clickable: false,
  wrapperClass: 'flex flex-wrap gap-2',
});

// Emits
const emit = defineEmits<{
  (e: 'genre-click', genre: Genre): void;
  (e: 'tag-click', tag: Tag): void;
}>();

// State
const showAll = ref(false);

// Computed
const allItems = computed(() => {
  return [...props.genres, ...(props.showTags ? props.tags : [])];
});

const displayedGenres = computed(() => {
  if (props.limit === 0 || showAll.value) {
    return props.genres;
  }
  return props.genres.slice(0, props.limit);
});

const displayedTags = computed(() => {
  if (!props.showTags) return [];
  if (props.limit === 0 || showAll.value) {
    return props.tags;
  }
  const remainingLimit = Math.max(0, props.limit - props.genres.length);
  return props.tags.slice(0, remainingLimit);
});

const hasMore = computed(() => {
  if (props.limit === 0) return false;
  return allItems.value.length > props.limit;
});

const hiddenCount = computed(() => {
  if (props.limit === 0) return 0;
  return allItems.value.length - props.limit;
});

// Methods
const handleGenreClick = (genre: Genre) => {
  emit('genre-click', genre);
};

const handleTagClick = (tag: Tag) => {
  emit('tag-click', tag);
};
</script>

<style scoped>
.genre-tag {
  display: inline-flex;
  align-items: center;
  border-radius: 9999px;
  font-weight: 500;
  letter-spacing: 0.02em;
  transition: all 150ms ease;
  white-space: nowrap;
}

button.genre-tag {
  cursor: pointer;
}

button.genre-tag:focus-visible {
  outline: 2px solid #e50914;
  outline-offset: 2px;
}

.genre-tag:active {
  transform: scale(0.95);
}

/* CSS Variables */
.genre-tags {
  --cinema-accent: #e50914;
  --cinema-secondary: #f5c518;
}
</style>
