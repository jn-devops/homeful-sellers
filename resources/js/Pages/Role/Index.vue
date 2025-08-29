<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

// defineProps({ roles: Array, modules: Array});
// const props = defineProps({
//   roles: Array,
//   modules: Array,
// });
const { roles, modules } = defineProps({
  roles: Array,
  modules: Array,
});
const form = ref({
  name: '',
  description: '',
  module_id: [], 
  editingId: null,
});

const showModal = ref(false);

const resetForm = () => {
  form.value = {
    name: '',
    description: '',
    module_id: [], // clear selected modules
    editingId: null
  };
  showModal.value = false;
};


// const submitForm = () => {
//   if (form.value.editingId) {
//     router.put(`/roles/${form.value.editingId}`, form.value);
//   } else {
//     router.post('/roles', form.value);
//   }
//   resetForm();
// };

//for displaying modules
const getModules = (moduleIds) => {
  if (!Array.isArray(moduleIds) || moduleIds.length === 0) return '-';
  if (!Array.isArray(modules) || modules.length === 0) return '-';
  return modules
    .filter(module => module && module.id !== undefined && moduleIds.includes(module.id))
    .map(module => module.name)
    .join(', ');
};


const submitForm = () => {
  const payload = {
    name: form.value.name,
    description: form.value.description,
    module_id: form.value.module_id,
  };

  if (form.value.editingId) {
    router.put(`/roles/${form.value.editingId}`, payload);
  } else {
    router.post('/roles', payload);
  }
  resetForm();
};


// const editRole = (role) => {
//   form.value = { ...role, editingId: role.id };
//   showModal.value = true;
// };
const editRole = (role) => {
  form.value = { 
    name: role.name, 
    description: role.description,
    module_id: role.module_id || [], 
    editingId: role.id 
  };
  showModal.value = true;
};


const deleteRole = (id) => {
  if (confirm('Are you sure you want to delete this role?')) {
    router.delete(`/roles/${id}`);
  }
};
</script>

<template>
    <Head title="User Roles" />
    <AuthenticatedLayout>
      <template #header>
        <div class="container mt-3 d-flex justify-content-between align-items-center">
          <h2 class="font-semibold text-xl text-gray-800">User Roles</h2>
          <button class="btn btn-dark" @click="showModal = true">
            <i class="bi bi-plus-circle me-1"></i> Add Role
          </button>
        </div>
      </template>
      <div class="container mt-4">
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Modules</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="role in roles" :key="role.id">
                <td>{{ role.id }}</td>
                <td>{{ role.name }}</td>
                <td>{{ role.description }}</td>
                <td>{{ getModules(role.module_id) }}</td>
                <td class="text-end">
                  <button class="btn btn-sm btn-primary me-1" @click="editRole(role)"><i class="bi bi-pen"></i></button>
                  <button class="btn btn-sm btn-danger" @click="deleteRole(role.id)"><i class="bi bi-trash"></i></button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <!-- Modal -->
      <div v-if="showModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5); z-index: 1050;">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">
            <div class="modal-header bg-dark text-white">
              <h5 class="modal-title">{{ form.editingId ? 'Edit Role' : 'Add New Role' }}</h5>
              <button type="button" class="btn-close" @click="resetForm"></button>
            </div>
            <form @submit.prevent="submitForm">
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Role Name</label>
                    <input type="text" v-model="form.name" class="form-control" required />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Description</label>
                    <textarea v-model="form.description" class="form-control" rows="2"></textarea>
                  </div>
                  <div class="row">
                    <div class="col-md-3 mb-2" v-for="module in modules" :key="module.id">
                      <div class="form-check">
                        <input
                          class="form-check-input"
                          type="checkbox"
                          :id="'module-' + module.id"
                          :value="module.id"
                          v-model="form.module_id"
                        />
                        <label class="form-check-label" :for="'module-' + module.id">
                          {{ module.name }}
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="resetForm">Cancel</button>
                <button type="submit" class="btn btn-success">
                  {{ form.editingId ? 'Update Role' : 'Add Role' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  </template>
  <style scoped>
  .modal-backdrop {
    z-index: 1040;
  }
  </style>
