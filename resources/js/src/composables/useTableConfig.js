import { ref } from 'vue';

export function useTableConfig() {
  const getColumnsForEndpoint = (endpoint) => {
    const configs = {
      advertisements: [
        {
          field: 'id',
          title: 'ID',
          type: 'number',
          width: '80px'
        },
        {
          field: 'name',
          title: 'Name',
          type: 'string'
        },
        {
          field: 'type',
          title: 'Type',
          type: 'string'
        },
        {
          field: 'description',
          title: 'Description',
          type: 'string'
        },
        {
          field: 'status',
          title: 'Status',
          type: 'string'
        },
        {
          field: 'created_at',
          title: 'Created At',
          type: 'date'
        },
        {
          field: 'actions',
          title: 'Actions',
          type: 'string',
          width: '120px'
        }
      ],
      users: [
        { field: 'email', title: 'Email', hide: false },
        { field: 'roles', title: 'Role', hide: false },
      ],
      roles: [
        { field: 'created_by', title: 'Created By', hide: false },
        { field: 'updated_by', title: 'Updated By', hide: false },
      ],
      // Add more endpoints as needed
    };

    return configs[endpoint] || [];
  };

  const getFormFieldsForEndpoint = (endpoint) => {
    const baseFields = ['name', 'status'];
    
    const endpointFields = {
      users: ['name', 'email', 'password', 'password_confirmation', 'role_id', 'status'],
      roles: ['name', 'status'],
      // Add more endpoints as needed
    };

    return endpointFields[endpoint] || baseFields;
  };

  const transformRowData = (data) => {
    return data.map(item => ({
      ...item,
      type: item.type ? item.type.charAt(0).toUpperCase() + item.type.slice(1) : 'Text',
      description: item.description ? 
        (item.description.length > 50 ? item.description.substring(0, 50) + '...' : item.description) 
        : '-'
    }));
  };

  return {
    getColumnsForEndpoint,
    getFormFieldsForEndpoint,
    transformRowData,
  };
}