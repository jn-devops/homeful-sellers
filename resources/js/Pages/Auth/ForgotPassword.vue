<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'bootstrap/dist/css/bootstrap.min.css';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    recipient:''//remove sa prd
});
const submit = () => {
    form.post(route('password.forgot'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset(); 
        }
    });
};
</script>
<template>
    <Head title="Forgot Password" />
    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
      <div class="w-full max-w-md space-y-6">
        <div class="text-center">
            <h2 class="text-2xl font-bold text-gray-900">Seller Forgot Password</h2>
        </div>
  
        <div
            v-if="status"
            class="mb-4 text-sm font-medium text-green-600 dark:text-green-400"
        >
            {{ status }}
        </div>
        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div>
                <InputLabel for="recipient" value="Recipient for Testing(removed before PRD)" />

                <TextInput
                    id="recipient"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.recipient"
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div>
            <p class="span text-gray-900" style="
                text-align:justify!important;
                font-size:10px"
            >
            Forgot your password? No problem. Just let us know your email
            address and we will email you a password reset link that will allow
            you to choose a new one.
            </p>
        </div>
            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Email Password Reset Link
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
