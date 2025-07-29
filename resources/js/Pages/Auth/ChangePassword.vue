<script setup>
// import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import {ref} from 'vue';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'bootstrap/dist/css/bootstrap.min.css';

const showPassword = ref(false);
const togglePassword = () => {
  showPassword.value = !showPassword.value;
};
const showRePassword = ref(false);
const toggleRePassword = () => {
  showRePassword.value = !showRePassword.value;
};
const props = defineProps({
  user: Object,
});
const passwordMismatch = ref(false);
const form = useForm({
  email: props.user?.email || '',
  type: props.user?.type || '',
  password: '',
  confirm_password: '',
  change_type: 'change',
});

const submit = () => {
  passwordMismatch.value = false;

  if (form.password && form.confirm_password && form.password !== form.confirm_password) {
    passwordMismatch.value = true;
    return;
  }

  form.post(route('password.change')); // <-- make sure this route exists in your backend
};
</script>
<template>
    <Head title="Forgot Password" />
    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
      <div class="w-full max-w-md space-y-6">
        <div class="text-center">
            <h2 class="text-2xl font-bold text-gray-900">Welcome to Seller’s Portal!</h2>
        </div>
  
        <div
            v-if="status"
            class="mb-4 text-sm font-medium text-green-600 dark:text-green-400"
        >
            {{ status }}
        </div>
        <form @submit.prevent="submit">
            <div>
                <!-- <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    disabled
                />
                <InputLabel for="password" value="New Password" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="password"
                />
                <InputLabel for="confirm_password" value="Re-Type Password" />
                <TextInput
                    id="confirm_password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.confirm_password"
                    required
                    autocomplete="password"
                /> -->
                <!-- <InputError class="mt-2" :message="form.errors.email" /> -->
                <!-- <InputError
                v-if="passwordMismatch"
                message="Passwords do not match"
                class="mt-2"
                /> -->
                <div class="relative">
          <label for="password">Password</label>
          <input 
            name="password"
            v-model="form.password"
            :type="showPassword ? 'text' : 'password'"
            placeholder="Password"
            class="w-full border border-gray-300 rounded-md px-4 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-black"
            required
          />
          <div class="fs-3 absolute inset-y-0 pt-4 right-3 flex items-center cursor-pointer" @click="togglePassword">
            <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
          </div>
        </div>
        <div class="relative">
          <label for="confirm_password">Re-Type Password</label>
          <input 
            name="confirm_password"
            v-model="form.confirm_password"
            :type="showRePassword ? 'text' : 'password'"
            placeholder="Re-Type Password"
            class="w-full border border-gray-300 rounded-md px-4 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-black"
            required
          />
          <div class="fs-3 absolute inset-y-0 pt-4 right-3 flex items-center cursor-pointer" @click="toggleRePassword">
            <i :class="showRePassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
          </div>
        </div>
            </div>
            <div>
            <p class="span text-gray-900" style="
                text-align:justify!important;
                font-size:10px"
            >
            </p>
        </div>
        <InputError
                v-if="passwordMismatch"
                message="Passwords do not match"
                class="mt-2"
                />
            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Change Password
                </PrimaryButton>
            </div>
        </form>
        
      </div>
    </div>
</template>
<!-- <template>
    <GuestLayout>

       
    </GuestLayout>
</template> -->
