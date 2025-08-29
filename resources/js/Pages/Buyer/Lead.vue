<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

// defineProps({ users: Array });

const props = defineProps({ buyers: Array, brokers: Array });

// const user = usePage().props.auth.user;
const permission = usePage().props.permissions;
const form = ref({
  reference_code: '',
  first_name: '',
  middle_name: '',
  last_name: '',
  date_of_birth: '',
  email: '',
  mobile: '',
  landline: '',
  created_at: '',
  current_status: '',
  editingId: null,
});

const showModal = ref(false);

const resetForm = () => {
  form.value = {
  reference_code: '',
  first_name: '',
  middle_name: '',
  last_name: '',
  date_of_birth: '',
  email: '',
  mobile: '',
  landline: '',
  created_at: '',
  current_status: '',
  editingId: null,
  };
  showModal.value = false;
};

const submitForm = () => {
  if (form.value.editingId) {
    router.put(`/lead/${form.value.editingId}`, form.value);
  } else {
    router.post('/lead', form.value);
  }
  resetForm();
};

const editUser = (user) => {
  console.log(user.order.seller_commission_code)
  form.value = { ...user,
    seller_commission_code:user.order.seller_commission_code, 
    editingId: user.id };
  showModal.value = true;
};

const deleteUser = (id) => {
  if (confirm('Are you sure you want to delete this User?')) {
    router.delete(`/users/${id}`);
  }
};
//add for search function 
const search = ref('');

// const filteredUsers = computed(() => {
//   if (!search.value.trim()) return props.users;
//   const term = search.value.toLowerCase();
//   return props.users.filter(user =>
//     user.name.toLowerCase().includes(term) ||
//     user.email.toLowerCase().includes(term) ||
//     user.contact?.toLowerCase().includes(term) ||
//     user.seller_commission_code?.toLowerCase().includes(term)
//   );
// });
//role 
const getRoleName = (roleId) => {
  const role = props.roles.find(r => r.id === roleId);
  return role ? role.name : 'Unknown Role';
};
const getSellerName = (sellerEmail) => {
  const seller = props.brokers.find(broker => broker.email === sellerEmail);
  return seller ? seller.name : '';
};
</script>

<template>

  <Head title="User" />
  <AuthenticatedLayout>
    <template #header>
      <!-- <div class="container mt-3 d-flex justify-content-between align-items-center">
        <h2 class="font-semibold text-xl text-gray-800">User</h2>
        <button class="btn btn-dark" @click="showModal = true">
          <i class="bi bi-plus-circle me-1"></i> Add User
        </button>
      </div> -->
    </template>
    <div class="row mb-3">
    </div>
    <div class="container mt-3" style="z-index: -1; position: relative;">
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
      <div v-for="buyer in buyers" :key="buyer.id" class="card mb-2">
        <div class="card-body">
          <h5 class="card-title">{{ buyer.name }}</h5>
          <p class="card-text">
            <strong>Reference Code:</strong> {{ buyer.reference_code }}<br>
            <strong>Name:</strong> {{ buyer.first_name + " " + buyer.last_name  }}<br>
            <strong>Seller Code:</strong> {{ buyer.landline.split('/') }}<br>
            <strong>Seller Name:</strong> {{ buyer.landline.split('/') }}<br>
            <strong>Status</strong> {{ buyer.current_status??'Prospect'}}<br>
          </p>
          <div class="text-end">
            <button v-if="permission.edit" class="btn btn-sm btn-primary me-1" @click="editUser(buyer)">Edit</button>
            <button v-if="permission.delete" class="btn btn-sm btn-danger" @click="deleteUser(buyer.id)">Delete</button>
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
                <th>Homeful ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Seller Commission Code</th>
                <th>Seller Name</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="buyer in buyers" :key="buyer.id">
                <td>{{ buyer.reference_code }}</td>
                <td>{{ buyer.first_name + " " + buyer.last_name  }}</td>
                <td>{{ buyer.email  }}</td>
                <td>{{ buyer.mobile  }}</td>
                <td>{{ buyer.order.seller_commission_code }}</td>
                <td>{{ getSellerName(buyer.landline.split('/')[0])}}</td>
                <td>{{ buyer.current_status??'Prospect' }}</td>
                <td class="text-end">
                  <button  v-if="permission.edit" class="btn btn-sm btn-primary me-1" @click="editUser(buyer)"><i class="bi bi-pen"></i></button>
                  <button  v-if="permission.delete" class="btn btn-sm btn-danger" @click="deleteUser(buyer.id)"><i class="bi bi-trash"></i></button>
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
                <div class="col-md-6 mb-3">
                  <label class="form-label">Reference Code</label>
                  <input type="text" v-model="form.reference_code" class="form-control" readonly  />
                </div><div class="col-md-6 mb-3">
                  <label class="form-label">First Name</label>
                  <input type="text" v-model="form.first_name" class="form-control" readonly  />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Middle Name</label>
                  <input type="text" v-model="form.middle_name" class="form-control" readonly  />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Last Name</label>
                  <input type="text" v-model="form.last_name" class="form-control" readonly  />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Email</label>
                  <input type="Email" v-model="form.email" class="form-control" readonly  />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Mobile</label>
                  <input type="text" v-model="form.mobile" class="form-control" readonly  />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Seller Code</label>
                  <input type="text" v-model="form.seller_commission_code" class="form-control" readonly   />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Transfer to</label>
                  <select v-model="form.seller_email" class="form-select" required >
                    <option disabled value="">Select Agent/Broker</option>
                    <option v-for="broker in brokers" :key="broker.id" :value="broker.email">
                      {{ broker.name }}
                    </option>
                  </select>
                </div>

              </div>
            </div>
            <div class="modal-footer">
              <button v-if="permission.edit" type="button" class="btn btn-secondary" @click="resetForm">Cancel</button>
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
