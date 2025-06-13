<script setup>
import GenerateVoucherForm from './Partials/GenerateVoucherForm.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';

const props = defineProps({
    projects: Object,
    voucher_input :Array
});

console.log(usePage().props.auth)

</script>

<template>
    <Head title="Voucher" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col items-center mt-7 px-2">
                <div class="w-full sm:w-full md:w-[500px]">
                    <h2 class="text-xl font-bold">{{usePage().props.auth.user.name}}</h2>
                    <h5 class="text-sm font-semibold">Seller Code: <span class="text-[#C38400]">{{ usePage().props.auth.user.seller_commission_code }}</span></h5>
                </div>
            </div>
        </template>

        <div class="flex flex-col items-center justify-center mt-7 px-2">
            <div class="w-full sm:w-full md:w-[500px] border-2 shadow-lg p-7">
                <GenerateVoucherForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="w-full"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
