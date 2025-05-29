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
          <div class="absolute inset-y-0 right-3 flex items-center cursor-pointer" @click="togglePassword">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-5.523 0-10-4.477-10-10S6.477 1 12 1c5.523 0 10 4.477 10 10 0 1.247-.228 2.442-.646 3.534M15 15l5 5m0 0l-5-5m5 5V19a2 2 0 00-2-2h-4" />
            </svg>
          </div>
        </div>

        <div class="text-right">
          <a href="#" class="text-sm text-blue-600 hover:underline">Forgot password?</a>
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

import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
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
</script>

<style scoped>
</style>
