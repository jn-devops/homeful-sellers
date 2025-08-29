<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

// defineProps({ users: Array });
const props = defineProps({ users: Array, roles: Array , sellers: Array });

const form = ref({
  name: '',
  first_name:'',
  middle_name:'',
  last_name:'',
  password:'',
  email: '',
  contact: '',
  seller_commission_code: '',
  user_role_id:'',
  active:'',
  birthdate: '', 
  send_email: true,
  editingId: null,
});

const showModal = ref(false);

const resetForm = () => {
  form.value = {
    name: '',
    first_name:'',
    middle_name:'',
    last_name:'',
    password:'',
    email: '',
    contact: '',
    seller_commission_code: '',
    user_role_id:'',
    active:'',
    birthdate: '', 
    send_email: true,
    editingId: null,
  };
  showModal.value = false;
};
watch(
  () => [form.value.first_name, form.value.middle_name, form.value.last_name],
  ([first, middle, last]) => {
    form.value.name = [first, middle, last].filter(Boolean).join(' ');
  },
  { immediate: true }
);

watch(
  () => [form.value.last_name, form.value.birthdate, form.value.editingId],
  ([lastName, birthdate, editingId]) => {
    if (!editingId && lastName && birthdate) {
      const date = new Date(birthdate);
      const mm = String(date.getMonth() + 1).padStart(2, '0');
      const dd = String(date.getDate()).padStart(2, '0');
      const yyyy = date.getFullYear();
      form.value.password = `${lastName}${mm}${dd}${yyyy}`;
    }
  }
);

// const submitForm = () => {
//   if (form.value.editingId) {
//     router.put(`/users/${form.value.editingId}`, form.value);
//   } else {
//     router.post('/users', form.value);
//   }
//   resetForm();
// };

const submitForm = () => {
  if (!form.value.name.trim()) {
    alert('Full name is required!');
    return;
  }
  if (form.value.editingId) {
    router.put(`/users/${form.value.editingId}`, form.value);
  } else {
    router.post('/users', form.value);
  }
  resetForm();
};
const editUser = (user) => {
  form.value = { ...user, editingId: user.id };
  showModal.value = true;
};

const deleteUser = (id) => {
  if (confirm('Are you sure you want to delete this User?')) {
    router.delete(`/users/${id}`);
  }
};
//add for search function 
const search = ref('');

const filteredUsers = computed(() => {
  if (!search.value.trim()) return props.users;
  const term = search.value.toLowerCase();
  return props.users.filter(user =>
    user.name.toLowerCase().includes(term) ||
    user.email.toLowerCase().includes(term) ||
    user.contact?.toLowerCase().includes(term) ||
    user.seller_commission_code?.toLowerCase().includes(term)
  );
});

//role 
const getRoleName = (roleId) => {
  const role = props.roles.find(r => r.id === roleId);
  return role ? role.name : 'N/A';
};
//active button
const toggleUserStatus = (user) => {
  const updatedUser = {
    ...user,
    active: user.active ? 0 : 1,
  };
  console.log(updatedUser);
  router.put(`/users/${user.id}`, updatedUser);
};

</script>

<template>

  <Head title="User" />
  <AuthenticatedLayout>
    <template #header>
      <div class="container mt-3 d-flex justify-content-between align-items-center">
        <h2 class="font-semibold text-xl text-gray-800">User</h2>
        <button class="btn btn-dark" @click="showModal = true">
          <i class="bi bi-plus-circle me-1"></i> Add User
        </button>
      </div>
    </template>

    <div class="row mb-3">
    </div>
    <div class="container mt-3">

      <div class="row justify-content-end">
        <div class="col-md-4">
          <div class="input-group">
            <span class="input-group-text bg-white border-end-0">
              <i class="bi bi-search"></i>
            </span>
            <input type="text" v-model="search" class="form-control border-start-0"
              placeholder="Search by name, email, contact, or seller code..." />
          </div>
        </div>
      </div>
    </div>
    <div class="d-md-none mt-3">
      <div v-for="user in filteredUsers" :key="user.id" class="card mb-2">
        <div class="card-body">
          <h5 class="card-title">{{ user.name }}</h5>
          <p class="card-text">
            <strong>Email:</strong> {{ user.email }}<br>
            <strong>Contact:</strong> {{ user.contact }}<br>
            <strong>Seller Code:</strong> {{ user.seller_commission_code }}<br>
            <strong>Role:</strong> {{ getRoleName(user.user_role_id) }}
          </p>
          <div class="text-end">
            <button class="btn btn-sm btn-primary me-1" @click="editUser(user)">Edit</button>
            <button class="btn btn-sm btn-danger" @click="deleteUser(user.id)">Delete</button>
          </div>
        </div>
      </div>
    </div>
    <div class="d-none d-md-block table-responsive">
      <div class="container mt-4">
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead class="table-dark">
              <tr>
                <th>Name</th>
                <th>Seller Commision Code</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Role</th>
                <th>Active</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in filteredUsers" :key="user.id">
                <td>{{ user.name }} <br> {{ user.seller_code }}</td>
                <td>{{ user.seller_commission_code }}</td>
                <td>{{ user.email }}</td>
                <td>{{ user.contact }}</td>
                <td>{{ getRoleName(user.user_role_id) }}</td>
                <!-- <td>{{ user.active?'Active':'Deactivate' }}</td>
                  -->
                  <td>
                <div class="form-check form-switch d-flex justify-content-center">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    :id="'activeSwitch' + user.id"
                    :checked="user.active"
                    @change="toggleUserStatus(user)"
                  />
                </div>
              </td>
                <td class="text-end">
                  <button class="btn btn-sm btn-primary me-1" @click="editUser(user)"><i class="bi bi-pen"></i></button>
                  <button class="btn btn-sm btn-danger" @click="deleteUser(user.id)"><i class="bi bi-trash"></i></button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>



    <!-- Modal -->
    <div v-if="showModal" class="modal fade show d-block" tabindex="-1"
      style="background: rgba(0,0,0,0.5); z-index: 1050;">
      
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-dark text-white">
            <h5 class="modal-title">{{ form.editingId ? 'Edit User' : 'Add New User' }}</h5>
            <button type="button" class="btn-close" @click="resetForm"></button>
          </div>
          <form @submit.prevent="submitForm">
            <div class="modal-body">
              <div class="row">

                <div class="col-md-12 mb-3">
                  <!-- <label class="form-label">Full Name</label> -->
                  <h1>{{ form.name }}</h1>
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">First Name</label>
                  <input type="text" v-model="form.first_name" class="form-control" required />
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Middle Name</label>
                  <input type="text" v-model="form.middle_name" class="form-control" />
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Last Name</label>
                  <input type="text" v-model="form.last_name" class="form-control" required />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Birthdate</label>
                  <input type="date" v-model="form.birthdate" class="form-control" />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Password</label>
                  <input type="password" v-model="form.password" class="form-control" placeholder="********" :readonly="!form.editingId"></input>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Email</label>
                  <input type="Email" v-model="form.email" class="form-control" required />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Contact</label>
                  <input type="tel" v-model="form.contact" class="form-control" required />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Seller Code</label>
                  <select v-model="form.seller_commission_code" class="form-select" required>
                    <option disabled value="">Select Seller Code</option>
                    <option v-for="seller in sellers" :key="seller.id" :value="seller.seller_commission_code_noah">
                      {{ seller.seller_commission_code_noah }} -  {{ seller.seller_name }}
                    </option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Role</label>
                  <select v-model="form.user_role_id" class="form-select" required>
                    <option disabled value="">Select Role</option>
                    <option v-for="role in roles" :key="role.id" :value="role.id">
                      {{ role.name }}
                    </option>
                  </select>
                </div>
                <div class="col-md-12 mb-3">
                  <input
                    type="checkbox"
                    v-model="form.send_email"
                    :id="'sendNotificationCheckbox'"
                    class="form-check-input me-2"
                  />
                  <label class="form-check-label" :for="'sendNotificationCheckbox'">
                    {{ form.editingId ? 'Send Update Email Notification' : 'Send Email Notification' }}
                  </label>
                </div>

               </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="resetForm">Cancel</button>
              <button type="submit" class="btn btn-success">
                {{ form.editingId ? 'Update User' : 'Add User' }}
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
