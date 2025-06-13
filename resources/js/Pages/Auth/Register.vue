<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PlainBlackButton from '@/Components/Buttons/PlainBlackButton.vue';
import TextInput from '@/Components/Inputs/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PasswordInput from '@/Components/Inputs/PasswordInput.vue';
import { computed, ref } from 'vue';
import SecondaryPlainBlack from '@/Components/Buttons/SecondaryPlainBlack.vue';

const form = useForm({
    name: '',
    email: '',
    seller_commission_code: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const disclaimerChecked = ref(false);

const showPassword = computed(() => props.autoPassword === '');
</script>

<template>
    <GuestLayout>
        <Head title="Register" />
        <div class="flex flex-col mt-10 px-8">
            <div class="flex flex-row items-start w-full">
                <div class="basis-1/3 ">
                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/>
                    </svg>
                </div>
                <div class="basis-1/3 text-center font-bold">
                    Sign Up
                </div>
                <div class="basis-1/3">
                </div>
            </div>
            <div class="w-full mt-10">
                <p class="text-xl font-extrabold">
                    Create an Account to <br> Get Started
                </p>
            </div>
            <form @submit.prevent="submit">
                <div class="w-full mt-5">
                    <TextInput
                        placeholder="Full Name"
                        label="Full Name"
                        required
                        no-border-radius
                        v-model="form.name"
                        :error-message="form.errors.name"
                    />
                </div>
                <div class="w-full mt-5">
                    <TextInput
                        placeholder="Email"
                        label="Email"
                        required
                        no-border-radius
                        v-model="form.email"
                        :error-message="form.errors.email"
                    />
                </div>
                <div class="w-full mt-5">
                    <TextInput
                        placeholder="Seller Booking Code"
                        label="Seller Booking Code"
                        required
                        no-border-radius
                        v-model="form.seller_commission_code"
                        :error-message="form.errors.seller_commission_code"
                    />
                </div>
                <div class="w-full mt-5">
                    <PasswordInput
                        placeholder="Password"
                        label="Password"
                        required
                        no-border-radius
                        v-model="form.password"
                        :error-message="form.errors.password"
                    />
                </div>
                <div class="w-full mt-5">
                    <PasswordInput
                        placeholder="Password"
                        label="Confirm Password"
                        required
                        no-border-radius
                        v-model="form.password_confirmation"
                        :error-message="form.errors.password_confirmation"
                    />
                </div>
                <div class="mt-7 px-2">
                    <div class="flex items-center mb-4 gap-2">
                        <input type="checkbox" v-model="disclaimerChecked" id="default-checkbox" class="w-4 h-4 text-[#F7C947] rounded-sm focus:ring-[#E94572]">
                        <label for="default-checkbox" class="ms-2 text-xs font-medium text-gray-900 dark:text-gray-300">
                            I've read and agree with the <b class="underline cursor-pointer">Terms and Conditions</b> and the <b class="underline cursor-pointer">Privacy Policy</b>.
                        </label>
                    </div>
                </div>
                <div class="mt-9">
                    <PlainBlackButton type="submit" :disabled="!disclaimerChecked">
                        Register
                    </PlainBlackButton>
                </div>
                <div class="mt-3 mb-10">
                    <Link :href="route('login')" >
                        <SecondaryPlainBlack>
                            Login
                        </SecondaryPlainBlack>
                    </Link>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
