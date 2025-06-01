import { ref } from 'vue';

export function useTableConfig() {
  const getColumnsForEndpoint = (endpoint) => {
    const baseColumns = [
      { field: 'id', title: 'ID', isUnique: true, hide: false },
      { field: 'name', title: 'Name', hide: false },
    ];

    const commonColumns = [
      { field: 'status', title: 'Status', hide: false },
      { field: 'created_at', title: 'Created At', hide: false },
      { field: 'actions', title: 'Actions', sortable: false, hide: false },
    ];

    const endpointSpecificColumns = {
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

    const specificColumns = endpointSpecificColumns[endpoint] || [
      { field: 'created_by', title: 'Created By', hide: false },
      { field: 'updated_by', title: 'Updated By', hide: false },
    ];

    return [
      ...baseColumns,
      ...specificColumns,
      ...commonColumns,
    ];
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

  const transformRowData = (data, endpoint) => {
    if (endpoint === 'users') {
      return data.map(item => ({
        id: item.id,
        name: item.name,
        email: item.email,
        roles: item.roles,
        role_ids: item.role_ids,
        status: item.status || 'inactive',
        created_at: item.created_at,
        actions: item.id,
        can_edit: item.can_edit,
        can_delete: item.can_delete
      }));
    }

    return data.map(item => ({
      id: item.id,
      name: item.name,
      status: item.status || 'inactive',
      created_by: item.created_by?.name || 'N/A',
      updated_by: item.updated_by?.name || 'N/A',
      created_at: item.created_at,
      actions: item.id
    }));
  };

  return {
    getColumnsForEndpoint,
    getFormFieldsForEndpoint,
    transformRowData,
  };
}