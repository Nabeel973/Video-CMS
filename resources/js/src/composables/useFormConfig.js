import axios from 'axios';
import { ref } from 'vue';

export function useFormConfig() {
  // Reactive state for dynamic options
  const genres = ref([]);
  const categories = ref([]);
  const tags = ref([]);

  // Fetch genres from API
  const fetchGenres = async () => {
    try {
      const response = await axios.get('/genres');
      if (response.data && response.data.data) {
        genres.value = response.data.data.map(genre => ({
          value: genre.id,
          label: genre.name
        }));
      }
    } catch (error) {
      console.error('Error fetching genres:', error);
    }
  };

  // Fetch categories from API
  const fetchCategories = async () => {
    try {
      const response = await axios.get('/categories');
      if (response.data && response.data.data) {
        categories.value = response.data.data.map(category => ({
          value: category.id,
          label: category.name
        }));
      }
    } catch (error) {
      console.error('Error fetching categories:', error);
    }
  };

  // Fetch tags from API
  const fetchTags = async () => {
    try {
      const response = await axios.get('/tags');
      if (response.data && response.data.data) {
        tags.value = response.data.data.map(tag => ({
          value: tag.id,
          label: tag.name
        }));
      }
    } catch (error) {
      console.error('Error fetching tags:', error);
    }
  };

  // Generate release years for dropdown
  const getReleaseYears = () => {
    const currentYear = new Date().getFullYear();
    const startYear = 1900;
    const endYear = currentYear + 5; // Include 5 years ahead
    const years = [];
    
    for (let year = endYear; year >= startYear; year--) {
      years.push({ value: year.toString(), label: year.toString() });
    }
    
    return years;
  };

  const getFormFields = (endpoint, isEdit = false) => {
    const commonFields = [
      {
        name: 'name',
        type: 'text',
        label: 'Name',
        placeholder: 'Enter name',
        required: true,
        disabledMessage: 'You cannot modify your own role name'
      },
      {
        name: 'status',
        type: 'select',
        label: 'Status',
        required: true,
        options: [
          { value: 'active', label: 'Active' },
          { value: 'inactive', label: 'Inactive' }
        ],
        disabledMessage: 'You cannot modify your own status'
      }
    ];

    const endpointFields = {
      users: [
        {
          name: 'name',
          type: 'text',
          label: 'Name',
          placeholder: 'Enter full name',
          required: true
        },
        {
          name: 'email',
          type: 'email',
          label: 'Email',
          placeholder: 'Enter email address',
          required: true
        },
        {
          name: 'password',
          type: 'password',
          label: 'Password',
          placeholder: isEdit ? 'Leave blank to keep current password' : 'Enter password',
          required: !isEdit,
          hint: isEdit ? 'Leave blank to keep current' : null
        },
        {
          name: 'password_confirmation',
          type: 'password',
          label: 'Confirm Password',
          placeholder: isEdit ? 'Leave blank to keep current password' : 'Confirm password',
          required: !isEdit
        },
        {
          name: 'role_id',
          type: 'select',
          label: 'Role',
          placeholder: 'Select Role',
          required: true
        },
        {
          name: 'status',
          type: 'select',
          label: 'Status',
          required: true,
          options: [
            { value: 'active', label: 'Active' },
            { value: 'inactive', label: 'Inactive' }
          ]
        }
      ],
      roles: commonFields,
      advertisements: [
        {
          name: 'name',
          type: 'text',
          label: 'Advertisement Name',
          placeholder: 'Enter advertisement name',
          required: true
        },
        {
          name: 'type',
          type: 'switch',
          label: 'Advertisement Type',
          options: [
            { value: 'text', label: 'Text' },
            { value: 'image', label: 'Image' }
          ],
          required: true
        },
        {
          name: 'description',
          type: 'textarea',
          label: 'Description',
          placeholder: 'Enter advertisement description',
          required: false,
          conditional: {
            field: 'type',
            value: 'text'
          }
        },
        {
          name: 'image',
          type: 'file',
          label: 'Advertisement Image',
          accept: 'image/*',
          required: false,
          conditional: {
            field: 'type',
            value: 'image'
          }
        },
        {
          name: 'status',
          type: 'select',
          label: 'Status',
          options: [
            { value: 'active', label: 'Active' },
            { value: 'inactive', label: 'Inactive' }
          ],
          required: true
        },
      ],
      movies: [
        {
          name: 'name',
          type: 'text',
          label: 'Name',
          placeholder: 'Enter movie name',
          required: true,
          gridColumn: 'md:col-span-4'
        },
        {
          name: 'tags',
          type: 'multiselect',
          label: 'Tags',
          placeholder: 'Select tags',
          required: false,
          gridColumn: 'md:col-span-4',
          options: tags.value,
          dynamic: true
        },
        {
          name: 'genre_id',
          type: 'select',
          label: 'Genre',
          placeholder: 'Select genre',
          required: true,
          gridColumn: 'md:col-span-4',
          options: genres.value,
          dynamic: true
        },
        {
          name: 'release',
          type: 'select',
          label: 'Release',
          placeholder: 'Select release year',
          required: true,
          gridColumn: 'md:col-span-4',
          options: getReleaseYears()
        },
        {
          name: 'rating',
          type: 'number',
          label: 'Rating',
          placeholder: 'Enter rating (0.0 - 5.0)',
          required: true,
          gridColumn: 'md:col-span-4',
          hint: 'Decimal value between 0.0 and 5.0'
        },
        {
          name: 'duration',
          type: 'duration',
          label: 'Duration',
          required: true,
          gridColumn: 'md:col-span-4'
        },
        {
          name: 'category_id',
          type: 'select',
          label: 'Category',
          placeholder: 'Select category',
          required: true,
          gridColumn: 'md:col-span-4',
          options: categories.value,
          dynamic: true
        },
        {
          name: 'video_source',
          type: 'select',
          label: 'Video Source',
          placeholder: 'Select video source',
          required: true,
          gridColumn: 'md:col-span-4',
          options: [
            { value: 'upload', label: 'Upload Video' },
            { value: 'link', label: 'Add Link' }
          ]
        },
        {
          name: 'status',
          type: 'select',
          label: 'Status',
          options: [
            { value: 'active', label: 'Active' },
            { value: 'inactive', label: 'Inactive' }
          ],
          required: true,
          gridColumn: 'md:col-span-4'
        },
        {
          name: 'video_link',
          type: 'text',
          label: 'Video Link',
          placeholder: 'Enter video link (required)',
          required: true,
          gridColumn: 'md:col-span-6',
          conditional: {
            field: 'video_source',
            value: 'link'
          }
        },
        {
          name: 'details',
          type: 'textarea',
          label: 'Details',
          placeholder: 'Enter movie details',
          required: false,
          gridColumn: 'md:col-span-6'
        },
        {
          name: 'image',
          type: 'file',
          label: 'Image for Video',
          accept: 'image/*',
          required: false,
          gridColumn: 'md:col-span-6',
          compact: true
        },
        {
          name: 'video_file',
          type: 'file',
          label: 'Upload Video',
          accept: 'video/*',
          required: false,
          gridColumn: 'md:col-span-6',
          compact: true,
          conditional: {
            field: 'video_source',
            value: 'upload'
          }
        },
        {
          name: 'cast_info',
          type: 'cast_table',
          label: 'Add Cast Info Section',
          required: false,
          gridColumn: 'md:col-span-12'
        }
      ],
      // Add more endpoints as needed
    };

    return endpointFields[endpoint] || commonFields;
  };

  const getInitialFormData = (endpoint) => {
    const baseData = {
      id: null,
      name: '',
      status: 'active'
    };

    const endpointData = {
      users: {
        ...baseData,
        email: '',
        password: '',
        password_confirmation: '',
        role_id: ''
      },
      roles: baseData,
      advertisements: {
        id: null,
        name: '',
        type: 'text',
        description: '',
        image: null,
        status: 'active'
      },
      movies: {
        id: null,
        name: '',
        tags: [],
        genre_id: '',
        release: '',
        rating: '',
        duration: '',
        video_source: 'upload',
        image: null,
        video_link: '',
        video_file: null,
        category_id: '',
        details: '',
        cast_info: [],
        status: 'active'
      },
      // Add more endpoints as needed
    };

    return endpointData[endpoint] || baseData;
  };

  return {
    getFormFields,
    getInitialFormData,
    fetchGenres,
    fetchCategories,
    fetchTags,
    genres,
    categories,
    tags
  };
}