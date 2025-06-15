import { computed } from 'vue';

export function useFormConfig() {
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
      // Add more endpoints as needed
    };

    return endpointData[endpoint] || baseData;
  };

  return {
    getFormFields,
    getInitialFormData,
  };
}