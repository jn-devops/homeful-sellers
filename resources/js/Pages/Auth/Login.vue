<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="w-full max-w-md space-y-6">
      <div class="text-center">
        <h2 class="text-2xl font-bold text-gray-900">Welcome to Seller’s Portal!</h2>
      </div>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label for="email">Email Address</label>
          <input 
            name="email"
            v-model="form.email"
            type="email" 
            placeholder="Email Address"
            class="w-full border border-gray-300 rounded-md px-4 py-2 gap-2 focus:outline-none focus:ring-2 focus:ring-black"
            required
          />
        </div>

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

        <div class="text-right">
          <a href="/forgot-password" class="text-sm text-blue-600 text-decoration-none">Forgot password?</a>
        </div>

        <button 
          type="submit"
          class="w-full bg-black text-white py-2 rounded-md hover:bg-gray-900 transition-colors"
        >
          Login
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const showPassword = ref(false);
const togglePassword = () => {
  showPassword.value = !showPassword.value;
};
defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
  console.log("nice");
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
//

</script>

<style scoped>
</style>
