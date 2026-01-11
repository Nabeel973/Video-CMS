/**
 * Movie Store - Pinia Store for Video Management System
 * Handles movie data, filtering, and state management
 */

import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { Movie, Genre, Category, Tag, Advertisement } from '@/data/mockMovies';
import {
  movies as mockMovies,
  genres as mockGenres,
  categories as mockCategories,
  tags as mockTags,
  advertisements as mockAds,
  featuredMovie as mockFeatured,
  getMoviesByGenre,
  getMoviesByCategory,
  getTrendingMovies,
  getNewReleases,
  getRelatedMovies,
  searchMovies,
} from '@/data/mockMovies';

export const useMovieStore = defineStore('movies', () => {
  // State
  const movies = ref<Movie[]>(mockMovies);
  const genres = ref<Genre[]>(mockGenres);
  const categories = ref<Category[]>(mockCategories);
  const tags = ref<Tag[]>(mockTags);
  const advertisements = ref<Advertisement[]>(mockAds);
  const featuredMovie = ref<Movie>(mockFeatured);
  const isLoading = ref(false);
  const searchQuery = ref('');
  const selectedGenre = ref<number | null>(null);
  const selectedCategory = ref<number | null>(null);

  // Getters
  const filteredMovies = computed(() => {
    let result = movies.value;

    if (searchQuery.value) {
      result = searchMovies(searchQuery.value);
    }

    if (selectedGenre.value) {
      result = result.filter(m => m.genre_id === selectedGenre.value);
    }

    if (selectedCategory.value) {
      result = result.filter(m => m.category_id === selectedCategory.value);
    }

    return result;
  });

  const trendingMovies = computed(() => getTrendingMovies());
  const newReleases = computed(() => getNewReleases());
  const activeGenres = computed(() => genres.value.filter(g => g.status === 'active'));
  const activeCategories = computed(() => categories.value.filter(c => c.status === 'active'));
  const activeTags = computed(() => tags.value.filter(t => t.status === 'active'));
  const activeAds = computed(() => advertisements.value.filter(a => a.status === 'active'));
  const bannerAds = computed(() => activeAds.value.filter(a => a.type === 'banner'));

  // Actions
  const getMovieById = (id: number): Movie | undefined => {
    return movies.value.find(m => m.id === id);
  };

  const getMoviesForGenre = (genreId: number): Movie[] => {
    return getMoviesByGenre(genreId);
  };

  const getMoviesForCategory = (categoryId: number): Movie[] => {
    return getMoviesByCategory(categoryId);
  };

  const getRelated = (movieId: number, limit = 6): Movie[] => {
    return getRelatedMovies(movieId, limit);
  };

  const setSearchQuery = (query: string) => {
    searchQuery.value = query;
  };

  const setSelectedGenre = (genreId: number | null) => {
    selectedGenre.value = genreId;
  };

  const setSelectedCategory = (categoryId: number | null) => {
    selectedCategory.value = categoryId;
  };

  const clearFilters = () => {
    searchQuery.value = '';
    selectedGenre.value = null;
    selectedCategory.value = null;
  };

  // Simulate loading (for skeleton loaders)
  const simulateLoading = async (duration = 1000) => {
    isLoading.value = true;
    await new Promise(resolve => setTimeout(resolve, duration));
    isLoading.value = false;
  };

  return {
    // State
    movies,
    genres,
    categories,
    tags,
    advertisements,
    featuredMovie,
    isLoading,
    searchQuery,
    selectedGenre,
    selectedCategory,

    // Getters
    filteredMovies,
    trendingMovies,
    newReleases,
    activeGenres,
    activeCategories,
    activeTags,
    activeAds,
    bannerAds,

    // Actions
    getMovieById,
    getMoviesForGenre,
    getMoviesForCategory,
    getRelated,
    setSearchQuery,
    setSelectedGenre,
    setSelectedCategory,
    clearFilters,
    simulateLoading,
  };
});
