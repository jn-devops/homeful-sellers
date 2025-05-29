<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import { Head, usePage, router, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import QRCode from 'qrcode';

const props = defineProps({
//   projects: Object,
  buyers:Object
});
console.log()
const user = usePage().props.auth.user;
const buyers = usePage().props.buyers.map(buyer => ({
    id: buyer.id, 
    name: buyer.first_name + " " + buyer.last_name, 
    email: buyer.email, 
    mobile: buyer.mobile,
    match_link: buyer.other_mobile
}));
const showModal = ref(false);
const selectedBuyer = ref({});
const hoveredBuyer = ref(null);
const showQRModal = ref(false);
const qrLink = ref('');
const qrImage = ref(''); // base64 data
async function openQRModal(link) {
  qrLink.value = link;
  showQRModal.value = true;

  try {
    qrImage.value = await QRCode.toDataURL(link, {
      width: 200,
      margin: 2,
    });
  } catch (err) {
    console.error('QR Code generation failed', err);
  }
}

function closeQRModal() {
  showQRModal.value = false;
  qrImage.value = '';
}
function copyToClipboard(text) {
    console.log(text);
  navigator.clipboard.writeText(text)
    .then(() => {
      alert('Link copied to clipboard!');
    })
    .catch(err => {
      console.error('Failed to copy text: ', err);
    });
}

</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex flex-col items-center mt-7 px-2">
        <div class="w-full sm:w-full md:w-[500px]">
          <h2 class="text-xl font-bold">{{ user.name }}</h2>
          <h5 class="text-sm font-semibold">
            Seller Booking Code:
            <span class="text-[#C38400]">{{ user.seller_commission_code }}</span>
          </h5>
        </div>
      </div>
    </template>

    <div class="flex flex-col items-center justify-center mt-3 px-2">
      <div class="w-full sm:w-full md:w-[500px] border-2 shadow-lg px-2">
        <div class="buyer-table-wrapper">
  <span class="fs-6 fw-bold">My Buyers</span>

  <div class="table-responsive">
    <table class="table table-bordered table-striped table-hover mt-2 text-sm mb-0">
      <thead class="border-white table-dark">
        <tr>
          <th style="border-radius: 16px 0 0 0 ;">Buyer Details</th>
          <th  style="border-radius: 0 16px 0 0 ;">Transaction</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="buyer in buyers"
          :key="buyer.id"
          class="position-relative"
        >
        <td style="position: relative;">
            {{ buyer.name }} <i class="bi bi bi-info-circle text-primary" style="cursor: pointer;"
            @mouseenter="hoveredBuyer = buyer"
            @mouseleave="hoveredBuyer = null"></i>
            <div
              v-if="hoveredBuyer?.id === buyer.id"
              class="hover-modal"
            >
              <p class="mb-1"><strong>Name:</strong> {{ buyer.name }}</p>
              <p class="mb-1"><strong>Email:</strong> {{ buyer.email }}</p>
              <p class="mb-0"><strong>Mobile:</strong> {{ buyer.mobile }}</p>
            </div>
          </td>
          <td>
            <a
                href="#"
                style="text-decoration: none; color:black"
                @click.prevent="openQRModal(buyer.match_link)"
            >
                View <i class="bi bi-qr-code"></i>
            </a>
            </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<!--QR modal--> 
<div v-if="showQRModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.5);">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{Buyername}}</h5>
        <button type="button" class="btn-close" @click="closeQRModal"></button>
      </div>
      <div class="modal-body text-center ">
        <img v-if="qrImage" class="d-block mx-auto"  :src="qrImage" alt="QR Code" />
        <p class="mt-3">
        <span>-------------or Copy the link------------</span><br>
      <button 
        class="btn btn-outline-primary btn-sm mt-3"
        @click="copyToClipboard(qrLink)"
      >
        Copy Link
      </button>
    </p>

      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" @click="closeQRModal">Close</button>
      </div>
    </div>
  </div>
</div>
    
      </div>
      <div class="container pt-7">
        <button
            @click="router.visit('/buyer/register')"
            class="text-sm text-center text-white bg-black fw-bold"
            style="width: 100%; height: 40px;"
        >
            Book Sales
        </button>
        <button
            class="mt-3 text-sm text-center text-white bg-secondary fw-bold"
            style="width: 100%; height: 40px;"
        >
            Register Prospect
        </button>
        </div>
      <div>
    </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.buyer-table-wrapper {
  max-width: 500px;
  width: 100%;
  overflow-x: auto;
}

.table-responsive {
  width: 100%;
  height: 400px;
  overflow-x: auto;
  overflow-y: scroll;
}
.hover-modal {
  position: absolute;
  top: 100%;
  left: 0;
  z-index: 1000;
  background-color: white;
  border: 1px solid #ddd;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  padding: 10px;
  min-width: 200px;
  white-space: normal;
  border-radius: 4px;
}
.modal {
  z-index: 1050;
}
.modal-backdrop {
  z-index: 1040;
}
table thead th {
  position: sticky;
  top: 0;
  /* color: white; */
  z-index: 1000;
}
table th {
  background-color: #343a40; /* Bootstrap dark */
  color: white;
  border: 1px solid #343a40;
}
table {
    border-collapse: separate !important;
    border-spacing: 0;
}
</style>
