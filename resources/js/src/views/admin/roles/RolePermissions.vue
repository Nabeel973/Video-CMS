<template>
  <div class="role-permissions">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Manage Permissions for {{ role.name }}</h1>
      <button class="btn btn-secondary" @click="goBack">Back to Roles</button>
    </div>
    
    <div v-if="loading" class="text-center">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
    
    <div v-else>
      <form @submit.prevent="savePermissions">
        <div class="mb-3">
          <button type="button" class="btn btn-sm btn-outline-primary me-2" @click="selectAll">Select All</button>
          <button type="button" class="btn btn-sm btn-outline-secondary" @click="deselectAll">Deselect All</button>
        </div>
        
        <div class="row">
          <div v-for="(permissions, group) in groupedPermissions" :key="group" class="col-md-6 mb-4">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ formatGroupName(group) }}</h5>
                <div>
                  <button type="button" class="btn btn-sm btn-outline-primary me-2" @click="selectGroup(group)">Select All</button>
                  <button type="button" class="btn btn-sm btn-outline-secondary" @click="deselectGroup(group)">Deselect All</button>
                </div>
              </div>
              <div class="card-body">
                <div v-for="permission in permissions" :key="permission.id" class="form-check">
                  <input 
                    class="form-check-input" 
                    type="checkbox" 
                    :id="'permission-' + permission.id" 
                    :value="permission.id" 
                    v-model="selectedPermissions"
                  >
                  <label class="form-check-label" :for="'permission-' + permission.id">
                    {{ permission.name }}
                    <small class="text-muted d-block" v-if="permission.description">{{ permission.description }}</small>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="mt-4">
          <button type="submit" class="btn btn-primary" :disabled="saving">
            <span v-if="saving" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            Save Permissions
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
// export default {
//   data() {
//     return {
//       role: {},
//       groupedPermissions: {},
//       selectedPermissions: [],
//       loading: true,
//       saving: false
//     }
//   },
//   mounted() {
//     this.fetchRoleData();
//     this.fetchPermissions();
//   },
//   methods: {
//     fetchRoleData() {
//       axios.get(`/api/roles/${this.$route.params.id}`)
//         .then(response => {
//           this.role = response.data.data;
//         })
//         .catch(error => {
//           console.error('Error fetching role:', error);
//         });
//     },
//     fetchPermissions() {
//       Promise.all([
//         axios.get('/api/permissions'),
//         axios.get(`/api/roles/${this.$route.params.id}/permissions`)
//       ])
//       .then(([permissionsResponse, rolePermissionsResponse]) => {
//         this.groupedPermissions = permissionsResponse.data.data;
//         this.selectedPermissions = rolePermissionsResponse.data.data;
//         this.loading = false;
//       })
//       .catch(error => {
//         console.error('Error fetching permissions:', error);
//         this.loading = false;
//       });
//     },
//     savePermissions() {
//       this.saving = true;
//       axios.post(`/api/roles/${this.$route.params.id}/permissions`, {
//         permissions: this.selectedPermissions
//       })
//       .then(response => {
//         this.saving = false;
//         this.$router.push({ name: 'roles' });
//       })
//       .catch(error => {
//         console.error('Error saving permissions:', error);
//         this.saving = false;
//       });
//     },
//     selectAll() {
//       this.selectedPermissions = [];
//       Object.values(this.groupedPermissions).flat().forEach(permission => {
//         this.selectedPermissions.push(permission.id);
//       });
//     },
//     deselectAll() {
//       this.selectedPermissions = [];
//     },
//     selectGroup(group) {
//       const groupPermissionIds = this.groupedPermissions[group].map(p => p.id);
//       this.selectedPermissions = [...new Set([...this.selectedPermissions, ...groupPermissionIds])];
//     },
//     deselectGroup(group) {
//       const groupPermissionIds = this.groupedPermissions[group].map(p => p.id);
//       this.selectedPermissions = this.selectedPermissions.filter(id => !groupPermissionIds.includes(id));
//     },
//     formatGroupName(group) {
//       if (!group) return 'General';
//       return group.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
//     },
//     goBack() {
//       this.$router.push({ name: 'roles' });
//     }
//   }
// }
export default {
  data() {
    return {
      role: {},
      groupedPermissions: {},
      selectedPermissions: [],
      loading: true,
      saving: false
    }
  },
  mounted() {
    this.fetchRoleData();
    this.fetchPermissions();
  },
  methods: {
    async fetchRoleData() {
      try {
        const res = await fetch(`/api/roles/${this.$route.params.id}`);
        const json = await res.json();
        this.role = json.data;
      } catch (error) {
        console.error('Error fetching role:', error);
      }
    },
    async fetchPermissions() {
      try {
        const [permissionsRes, rolePermissionsRes] = await Promise.all([
          fetch('/api/permissions'),
          fetch(`/api/roles/${this.$route.params.id}/permissions`)
        ]);

        const permissionsJson = await permissionsRes.json();
        const rolePermissionsJson = await rolePermissionsRes.json();

        this.groupedPermissions = permissionsJson.data;
        this.selectedPermissions = rolePermissionsJson.data;
        this.loading = false;
      } catch (error) {
        console.error('Error fetching permissions:', error);
        this.loading = false;
      }
    },
    async savePermissions() {
      this.saving = true;
      try {
        const res = await fetch(`/api/roles/${this.$route.params.id}/permissions`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            permissions: this.selectedPermissions
          })
        });

        if (!res.ok) throw new Error('Failed to save permissions');
        this.$router.push({ name: 'roles' });
      } catch (error) {
        console.error('Error saving permissions:', error);
      } finally {
        this.saving = false;
      }
    },
    selectAll() {
      this.selectedPermissions = [];
      Object.values(this.groupedPermissions).flat().forEach(permission => {
        this.selectedPermissions.push(permission.id);
      });
    },
    deselectAll() {
      this.selectedPermissions = [];
    },
    selectGroup(group) {
      const groupPermissionIds = this.groupedPermissions[group].map(p => p.id);
      this.selectedPermissions = [...new Set([...this.selectedPermissions, ...groupPermissionIds])];
    },
    deselectGroup(group) {
      const groupPermissionIds = this.groupedPermissions[group].map(p => p.id);
      this.selectedPermissions = this.selectedPermissions.filter(id => !groupPermissionIds.includes(id));
    },
    formatGroupName(group) {
      if (!group) return 'General';
      return group.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    },
    goBack() {
      this.$router.push({ name: 'roles' });
    }
  }
}

</script>

<style scoped>
.card {
  height: 100%;
}
</style>