export function useTableConfig() {
  // const getColumnsForEndpoint = (endpoint) => {
  //   const configs = {
  //     advertisements: [
  //       {
  //         field: 'id',
  //         title: 'ID',
  //         type: 'number',
  //         width: '80px'
  //       },
  //       {
  //         field: 'name',
  //         title: 'Name',
  //         type: 'string'
  //       },
  //       {
  //         field: 'type',
  //         title: 'Type',
  //         type: 'string'
  //       },
  //       {
  //         field: 'description',
  //         title: 'Description',
  //         type: 'string'
  //       },
  //       {
  //         field: 'status',
  //         title: 'Status',
  //         type: 'string'
  //       },
  //       {
  //         field: 'created_at',
  //         title: 'Created At',
  //         type: 'date'
  //       },
  //       {
  //         field: 'actions',
  //         title: 'Actions',
  //         type: 'string',
  //         width: '120px'
  //       }
  //     ],
  //     users: [
  //       { field: 'email', title: 'Email', hide: false },
  //       { field: 'roles', title: 'Role', hide: false },
  //     ],
  //     roles: [
  //       { field: 'created_by', title: 'Created By', hide: false },
  //       { field: 'updated_by', title: 'Updated By', hide: false },
  //     ],
  //     // Add more endpoints as needed
  //   };

  //   return configs[endpoint] || [];
  // };


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

  // const getFormFieldsForEndpoint = (endpoint) => {
  //   const baseFields = ['name', 'status'];
    
  //   const endpointFields = {
  //     users: ['name', 'email', 'password', 'password_confirmation', 'role_id', 'status'],
  //     roles: ['name', 'status'],
  //     // Add more endpoints as needed
  //   };

  //   return endpointFields[endpoint] || baseFields;
  // };

  const transformRowData = (data) => {
    return data.map(item => ({
      ...item,
      type: item.type ? item.type.charAt(0).toUpperCase() + item.type.slice(1) : 'Text',
      description: item.description ? 
        (item.description.length > 50 ? item.description.substring(0, 50) + '...' : item.description) 
        : '-',
      // Handle relational data - extract name from objects
      created_by: item.created_by && typeof item.created_by === 'object' 
        ? item.created_by.name 
        : item.created_by,
      updated_by: item.updated_by && typeof item.updated_by === 'object' 
        ? item.updated_by.name 
        : item.updated_by,
      // Handle roles if it's an array of objects
      roles: item.roles && Array.isArray(item.roles) 
        ? item.roles.map(role => typeof role === 'object' ? role.name : role).join(', ')
        : item.roles,
      // Format created_at date
      created_at: item.created_at 
        ? new Date(item.created_at).toLocaleDateString()
        : item.created_at
    }));
  };

  return {
    getColumnsForEndpoint,
    // getFormFieldsForEndpoint,
    transformRowData,
  };
}