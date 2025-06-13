<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';

const props = defineProps({
  projects: Object,
});

const user = usePage().props.auth.user;
</script>

<template>
  <Head title="Voucher" />

  <AuthenticatedLayout>
    <!-- User Info Header -->
    <template #header>
      <div class="flex flex-col items-center mt-7 px-2">
        <div class="w-full sm:w-full md:w-[500px]">
          <h2 class="text-xl font-bold">{{ user.name }}</h2>
          <h5 class="text-sm font-semibold">
            Seller Code:
            <span class="text-[#C38400]">{{ user.seller_commission_code }}</span>
          </h5>
        </div>
      </div>
    </template>

    <!-- Voucher Form -->
    <div class="flex flex-col items-center justify-center mt-7 px-2">
      <div class="w-full sm:w-full md:w-[500px] border-2 shadow-lg p-7">
        <GenerateVoucherForm
          :must-verify-email="mustVerifyEmail"
          :status="status"
          class="w-full"
        />
      </div>
    </div>

    <!-- Property Card Section -->
    <div class="flex flex-col items-center justify-center mt-10 px-2">
      <article class="relative flex flex-col px-2 pt-7 pb-2.5 mx-auto w-full max-w-[480px] aspect-[1.184]">
        <img
          src="https://cdn.builder.io/api/v1/image/assets/c7c2f2d7fc8b495681201dbed66d341f/78b68ea440a62d3109a2da7ccd98110c7843d721?placeholderIfAbsent=true"
          class="object-cover absolute inset-0 size-full"
          alt="Property image"
        />
        <div class="relative flex flex-col justify-center self-start ml-3 leading-none text-white">
          <p class="text-xs">Pagsibol Village</p>
          <p class="mt-1.5 text-3xl font-bold">₱</p>
        </div>
        <div class="relative flex gap-2.5 px-6 py-7 mt-32 w-full bg-white shadow-sm">
          <button
            class="overflow-hidden gap-2 self-stretch px-4 py-5 text-xs font-semibold whitespace-nowrap min-h-[52px] text-neutral-800"
          >
            Details
          </button>
          <button
            class="overflow-hidden gap-9 self-stretch px-9 py-5 text-sm font-medium leading-none text-center text-white bg-black min-h-[51px] shadow-[0px_4px_4px_rgba(0,0,0,0.25)]"
          >
            Generate Code
          </button>
        </div>
      </article>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
</style>
