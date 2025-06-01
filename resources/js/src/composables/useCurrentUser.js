import { ref } from 'vue';
import axios from 'axios';

export function useCurrentUser() {
  const currentUser = ref({});

  const fetchCurrentUser = async () => {
    try {
      const response = await axios.get('/auth/user');
      if (response.data) {
        currentUser.value = response.data;
      }
    } catch (error) {
      console.error('Error fetching current user:', error);
    }
  };

  return {
    currentUser,
    fetchCurrentUser,
  };
}