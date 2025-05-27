<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const showPasswordModal = ref(false);

const user = usePage().props.auth.user;
const form = useForm({

    name: user.name,
    email: user.email,
    seller_commission_code: user.seller_commission_code,
});

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
        <div class="bg-light">
            <div class="px-6 py-3" style="display:flex">
            <Link :href="route('dashboard')" style="text-decoration: none;"><h5 class="fw-bold text-black">Home</h5></Link>
            <i class="bi bi-chevron-compact-right"></i><h5 class="text-secondary">My Profile</h5>
            </div>
            <div>
             <h2 class="px-6 fw-bold">My Profile</h2>
             </div>
             <div class="mx-3 bg-white">
                <div class="my-3 py-3 container">
                    <div class="row mb-2">
                        <div class="col-6 fw-bold">Full Name:</div>
                        <div class="col-6">{{ form.name }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6 fw-bold">Phone Number:</div>
                        <div class="col-6">{{ form.name }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6 fw-bold">Email Address:</div>
                        <div class="col-6">{{ form.email }}</div>
                    </div>
                    <div class="row mb-2" style="display: none;">
                        <div class="col-sm-3 fw-bold">Seller Code:</div>
                        <div class="col-sm-9">{{ form.seller_commission_code }}</div>
                    </div>
                    <div>
                    <button class="btn btn-primary col-12" @click="showPasswordModal = true">
                        Change Password
                    </button>
                    </div>

</div>

             </div>
        </div>
        </template>
    <!-- Modal Component -->
<div v-if="showPasswordModal">
  <!-- Backdrop -->
  <div
    class="modal-backdrop fade show"
    style="z-index: 1040; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);"
    @click="showPasswordModal = false"
  ></div>

  <div
    class="modal d-block"
    tabindex="-1"
    style="z-index: 1050; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);"
    role="dialog"
  >
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Change Password</h5>
          <button type="button" class="btn-close" @click="showPasswordModal = false"></button>
        </div>
        <div class="modal-body">
          <UpdatePasswordForm />
        </div>
      </div>
    </div>
  </div>
</div>

    </AuthenticatedLayout>
</template>
