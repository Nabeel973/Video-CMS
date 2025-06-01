import { ref } from 'vue';

export function useSearch(onSearch) {
  const search = ref('');
  const searchTimeout = ref(null);

  const handleSearch = () => {
    if (searchTimeout.value) {
      clearTimeout(searchTimeout.value);
    }

    searchTimeout.value = setTimeout(() => {
      onSearch(search.value);
    }, 500);
  };

  return {
    search,
    handleSearch,
  };
}