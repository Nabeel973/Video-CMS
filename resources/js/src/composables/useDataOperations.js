import axios from 'axios';
import Swal from 'sweetalert2';
import { ref } from 'vue';

export function useDataOperations(endpoint) {
  const loading = ref(false);
  const rows = ref([]);
  const totalRows = ref(0);

  // Helper function to get singular title from endpoint
  const getSingularTitle = (endpoint) => {
    const singularMap = {
      'users': 'User',
      'roles': 'Role',
      'permissions': 'Permission',
      'categories': 'Category',
      'videos': 'Video',
      'tags': 'Tag',
      'advertisement': 'Advertisement',
    };
    
    return singularMap[endpoint] || endpoint.slice(0, -1); // Remove 's' as fallback
  };

  const fetchData = async (searchQuery = '', transformFn = null) => {
    try {
      const params = {};
      if (searchQuery && searchQuery.trim() !== '') {
        params.search = searchQuery.trim();
      }

      const response = await axios.get(`/${endpoint}`, { params });
      
      if (!response.data || !response.data.data) {
        throw new Error('Invalid response format');
      }

      rows.value = transformFn ? transformFn(response.data.data, endpoint) : response.data.data;
      totalRows.value = response.data.meta ? response.data.meta.total : rows.value.length;
    } catch (error) {
      console.error(`Error fetching ${endpoint}:`, error);
      Swal.fire({
        title: 'Error!',
        text: `Failed to fetch ${endpoint}. Please try again.`,
        icon: 'error',
        confirmButtonText: 'OK'
      });
    }
  };

  const fetchItem = async (id) => {
    try {
      const itemId = typeof id === 'object' ? id.id : id;
      const response = await axios.get(`/${endpoint}/${itemId}`);
      
      if (response.data && response.data.data) {
        return response.data.data;
      } else {
        throw new Error('Invalid response format');
      }
    } catch (error) {
      console.error(`Error fetching ${endpoint} item:`, error);
      Swal.fire({
        title: 'Error!',
        text: `Failed to fetch ${endpoint} details. Please try again.`,
        icon: 'error',
        confirmButtonText: 'OK'
      });
      return null;
    }
  };

  const deleteItem = async (id, singularTitle = null, onSuccess = null) => {
    const itemId = typeof id === 'object' ? id.id : id;
    
    // Use provided title or auto-determine from endpoint
    const title = singularTitle || getSingularTitle(endpoint);

    const result = await Swal.fire({
      title: 'Are you sure?',
      text: `This ${title.toLowerCase()} will be permanently deleted!`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    });

    if (result.isConfirmed) {
      try {
        const response = await axios.delete(`/${endpoint}/${itemId}`);
        
        if (response.data && response.data.message) {
          if (onSuccess) await onSuccess();
          
          Swal.fire({
            title: 'Deleted!',
            text: response.data.message || `${title} has been deleted.`,
            icon: 'success',
            confirmButtonText: 'OK'
          });
        } else {
          throw new Error('Invalid response format');
        }
      } catch (error) {
        console.error(`Error deleting ${title.toLowerCase()}:`, error);
        Swal.fire({
          title: 'Error!',
          text: error.response?.data?.message || `Failed to delete ${title.toLowerCase()}. Please try again.`,
          icon: 'error',
          confirmButtonText: 'OK'
        });
      }
    }
  };

  return {
    loading,
    rows,
    totalRows,
    fetchData,
    fetchItem,
    deleteItem,
  };
}