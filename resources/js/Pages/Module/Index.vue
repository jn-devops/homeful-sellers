<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

defineProps({ modules: Array });

const user = usePage().props.auth.user;
const permission = usePage().props.permissions;
const form = ref({
  name: '',
  slug:'',
  description: '',
  add: false,
  edit: false,
  delete: false,
  import: false,
  export: false,
  view: false,
  editingId: null,
});

const showModal = ref(false);

const resetForm = () => {
  form.value = {
    name: '',
    slug:'',
    description: '',
    add: false,
    edit: false,
    delete: false,
    view: false,
    import: false,
    export: false,
    editingId: null
  };
  showModal.value = false;
};

// const submitForm = () => {
//   if (form.value.editingId) {
//     router.put(`/modules/${form.value.editingId}`, form.value);
//   } else {
//     router.post('/modules', form.value);
//   }
//   resetForm();
// };
watch(() => form.value.view, (newValue) => {
  if (!newValue) {
    form.value.add = false;
    form.value.edit = false;
    form.value.delete = false;
    form.value.import = false;
    form.value.export = false;
  }
});
const submitForm = () => {
  const payload = {
    ...form.value,
    add: form.value.add ? 1 : 0,
    delete: form.value.delete ? 1 : 0,
    edit: form.value.edit ? 1 : 0,
    import: form.value.import ? 1 : 0,
    export: form.value.export ? 1 : 0,
    view: form.value.view ? 1 : 0,
  };
  if (form.value.editingId) {
    router.put(`/modules/${form.value.editingId}`, payload);
  } else {
    router.post('/modules', payload);
  }
  resetForm();
};


// const editModule = (module) => {
//   form.value = { ...module, editingId: module.id };
//   showModal.value = true;
// };
const editModule = (module) => {
  form.value = {
    ...module,
    add: module.add === 1 || module.add === true,
    edit: module.edit === 1 || module.edit === true,
    delete: module.delete === 1 || module.delete === true,
    view: module.view === 1 || module.view === true,
    import: module.import === 1 || module.import === true,
    export: module.export === 1 || module.export === true,
    editingId: module.id,
  };
  showModal.value = true;
};

const deleteModule = (id) => {
  if (confirm('Are you sure you want to delete this module?')) {
    router.delete(`/modules/${id}`);
  }
};
</script>
<template>

  <Head title="Modules" />
  <AuthenticatedLayout>
    <template #header>
      <div class="container mt-3 d-flex justify-content-between align-items-center">
        <h2 class="font-semibold text-xl text-gray-800">Modules</h2>
        <button v-if="permission.add" class="btn btn-dark" @click="showModal = true">
          <i class="bi bi-plus-circle me-1"></i> Add Module
        </button>
      </div>
    </template>
    <div class="container mt-4">
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead class="table-dark">
            <tr>
              <th rowspan="2">Name</th>
              <th rowspan="2">Slug</th>
              <th rowspan="2">Description</th>
              <th colspan="6" class="text-center">Permissions</th>
              <th rowspan="2" class="text-end">Actions</th>
            </tr>
            <tr>
              <th>View</th>
              <th>Add</th>
              <th>Edit</th>
              <th>Delete</th>
              <th>Import</th>
              <th>Export</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="module in modules" :key="module.id">
              <td>{{ module.name }}</td>
              <td>{{ module.slug }}</td>
              <td>{{ module.description }}</td>
              <td class="text-center">
                <i v-if="module.view === 1" class="bi bi-check-lg text-success"></i>
              </td>
              <td class="text-center">
                <i v-if="module.add === 1" class="bi bi-check-lg text-success"></i>
              </td>
              <td class="text-center">
                <i v-if="module.edit === 1" class="bi bi-check-lg text-success"></i>
              </td>
              <td class="text-center">
                <i v-if="module.delete === 1" class="bi bi-check-lg text-success"></i>
              </td>
              <td class="text-center">
                <i v-if="module.import === 1" class="bi bi-check-lg text-success"></i>
              </td>
              <td class="text-center">
                <i v-if="module.export === 1" class="bi bi-check-lg text-success"></i>
              </td>
              <td class="text-end">
                <button v-if="permission.edit"  class="btn btn-sm btn-primary me-1" @click="editModule(module)">
                  <i class="bi bi-pen"></i>
                </button>
                <button v-if="permission.delete"  class="btn btn-sm btn-danger" @click="deleteModule(module.id)">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div v-if="showModal" class="modal fade show d-block" tabindex="-1"
      style="background: rgba(0,0,0,0.5); z-index: 1050;">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-dark text-white">
            <h5 class="modal-title">{{ form.editingId ? 'Edit Module' : 'Add New Module' }}</h5>
            <button type="button" class="btn-close" @click="resetForm"></button>
          </div>
          <form @submit.prevent="submitForm">
            <div class="modal-body">
              <div class="row col-md-6 mb-3">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Module Name</label>
                  <input type="text" v-model="form.name" class="form-control" required />
                </div><div class="col-md-6 mb-3">
                  <label class="form-label">Slug</label>
                  <input type="text" v-model="form.slug" class="form-control" required />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Description</label>
                  <textarea v-model="form.description" class="form-control" rows="1"></textarea>
                </div>
                <div class="row">
                  <div class="col-md-3 mb-3 d-flex align-items-center">
                    <input v-model="form.view" type="checkbox" class="form-check-input me-2" id="view" />
                    <label class="form-check-label" for="view">View</label>
                  </div>
                  <div class="row">
                    <template v-if="form.view">
                      <div class="col-md-3 mb-3 d-flex align-items-center">
                        <input v-model="form.add" type="checkbox" class="form-check-input me-2" id="add" />
                        <label class="form-check-label" for="add">Add</label>
                      </div>
                      <div class="col-md-3 mb-3 d-flex align-items-center">
                        <input v-model="form.edit" type="checkbox" class="form-check-input me-2" id="edit" />
                        <label class="form-check-label" for="edit">Edit</label>
                      </div>
                      <div class="col-md-3 mb-3 d-flex align-items-center">
                        <input v-model="form.delete" type="checkbox" class="form-check-input me-2" id="delete" />
                        <label class="form-check-label" for="delete">Delete</label>
                      </div>
                      <div class="col-md-3 mb-3 d-flex align-items-center">
                        <input v-model="form.import" type="checkbox" class="form-check-input me-2" id="import" />
                        <label class="form-check-label" for="import">Import</label>
                      </div>
                      <div class="col-md-3 mb-3 d-flex align-items-center">
                        <input v-model="form.export" type="checkbox" class="form-check-input me-2" id="export" />
                        <label class="form-check-label" for="export">Export</label>
                      </div>
                    </template>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="resetForm">Cancel</button>
              <button type="submit" class="btn btn-success">
                {{ form.editingId ? 'Update Module' : 'Add Module' }}
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

.modal-body {
  max-height: 70vh;
  overflow-y: auto;
}
</style>
