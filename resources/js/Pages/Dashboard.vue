<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import { Head, usePage, router, Link } from '@inertiajs/vue3';
import { ref,computed} from 'vue';
import QRCode from 'qrcode';

const props = defineProps({
//   projects: Object,
  buyers:Object
});
const user = usePage().props.auth.user;
const buyers = usePage().props.buyers.map(buyer => ({
    id: buyer.id, 
    first_name: buyer.first_name,
    name: buyer.first_name + " " + buyer.last_name, 
    email: buyer.email, 
    mobile: buyer.mobile,
    match_link: buyer.other_mobile,
    reference_code: buyer.reference_code,
    date_created: buyer.created_at
}));
// const showModal = ref(false);
// const selectedBuyer = ref({});
const hoveredBuyer = ref(null);
const showQRModal = ref(false);
const qrLink = ref('');
const qrImage = ref(''); // base64 data

const confirmationModal = ref(false);
const confirmationMessage = ref('');
const onConfirmAction = ref(() => {});
const selectedActionBuyer = ref(null);

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
const sendSMS = async (buyer) => {
  const smsBody = {
    mobile: "63" + buyer['mobile'].substring(1),
    message: `Hi Mr/Mrs/Ms ${buyer['first_name']} Please complete your booking with ${user.name} with link below: ${buyer['match_link']} Thank you!`,
  };
  try {
    const response = await fetch(route('api.sendSMS'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(smsBody),
    });
    alert(buyer['name'] + ' booking link sent thru SMS');
  } catch (e) {
    console.log(e);
  }
};

const sendEmail = async (buyer) => {
  const emailBody = {
    template: "buyerTemplate",
    recipient: buyer['email'] || "ggvivar@joy-nostalg.com",
    mailBody: {
      subject: "Welcome Buyer!",
      first_name: buyer['first_name'] || 'N/A',
      seller_name: user.name || "N/A",
      matchlink: buyer['match_link'] || "N/A",
    },
  };
  try {
    const response = await fetch(route('api.sendEmail'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(emailBody),
    });
    alert(buyer['name'] + ' booking link sent thru email');
  } catch (e) {
    console.log(e);
  }
};

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
//start added to control sending of email/sms
function showConfirmation(message, action, buyer) {
  confirmationMessage.value = message;
  onConfirmAction.value = action;
  selectedActionBuyer.value = buyer;
  confirmationModal.value = true;
}

function confirmAction() {
  if (onConfirmAction.value && selectedActionBuyer.value) {
    onConfirmAction.value(selectedActionBuyer.value);
  }
  confirmationModal.value = false;
}

function cancelAction() {
  confirmationModal.value = false;
}
//end 
//start added search/sort

const searchQuery = ref('');
const sortKey = ref('name');
const sortDirection = ref('asc');

const filteredBuyers = computed(() => {
  let result = buyers.filter(buyer =>
    buyer.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    buyer.email.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    buyer.mobile.includes(searchQuery.value)
  );

  result.sort((a, b) => {
    let modifier = sortDirection.value === 'asc' ? 1 : -1;
    if (a[sortKey.value] < b[sortKey.value]) return -1 * modifier;
    if (a[sortKey.value] > b[sortKey.value]) return 1 * modifier;
    return 0;
  });

  return result;
});

function setSort(key) {
  if (sortKey.value === key) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortKey.value = key;
    sortDirection.value = 'asc';
  }
}
//end


</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
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

    <div class="flex flex-col items-center justify-center mt-3 px-2">
      <div class="w-full sm:w-full md:w-[500px] border-2 shadow-lg px-2">
        <div class="buyer-table-wrapper">
    <span class="fs-6 fw-bold">My Buyers</span>
<div class="mt-2 mb-2 flex">
  <input 
    v-model="searchQuery" 
    type="text" 
    class="form-control form-control-sm" 
    placeholder="Search buyer by name, email, or mobile" 
  />
</div>
    <div class="table-responsive">
<table class="table table-bordered table-striped table-hover mt-2 text-sm mb-0">
  <thead class="border-white table-dark">
    <tr>
      <th style="border-radius: 16px 0 0 0 ; cursor: pointer;" @click="setSort('date_created')">
        Buyer Details 
        <i :class="sortKey === 'date_created' ? (sortDirection === 'asc' ? 'bi bi-sort-alpha-down' : 'bi bi-sort-alpha-up') : 'bi bi-arrow-down-up'"></i>
      </th>
      <th style="border-radius: 0 16px 0 0 ;">Transaction</th>
    </tr>
  </thead>
  <tbody>
    <tr v-for="buyer in filteredBuyers" :key="buyer.id">
      
      <td style="position: relative;">
  {{ buyer.name }}
  <i class="bi bi-info-circle text-primary" style="cursor: pointer;"
     @mouseenter="hoveredBuyer = buyer"
     @mouseleave="hoveredBuyer = null"></i>

  <div v-if="hoveredBuyer?.id === buyer.id" class="hover-modal">
    <p class="mb-1"><strong>Homeful Id:</strong> {{ buyer.reference_code }}</p>
    <p class="mb-1"><strong>Name:</strong> {{ buyer.name }}</p>
    <p class="mb-1"><strong>Email:</strong> {{ buyer.email }}</p>
    <p class="mb-0"><strong>Mobile:</strong> {{ buyer.mobile }}</p>
    <p class="mb-0"><strong>Date Created:</strong> {{ buyer.date_created }}</p>
    
  </div>
</td>

      <td class="text-end">
        <a href="#" style="text-decoration: none; color:black" @click.prevent="openQRModal(buyer.match_link)">
            View <i class="bi bi-qr-code"></i>
        </a>
        <a class="mx-2" href="#" @click.prevent="showConfirmation('Send booking link to buyer ' + buyer.name + ' via Email?', sendEmail, buyer)">
            <i class="bi bi-envelope"></i>
        </a>
        <a href="#" @click.prevent="showConfirmation('Send booking link to buyer ' + buyer.name + ' via SMS?', sendSMS, buyer)">
            <i class="bi bi-send"></i>
        </a>
      </td>
    </tr>
  </tbody>
</table>

    </div>
</div>
<!--Generated QR modal--> 
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
          <!-- <button
              class="mt-3 text-sm text-center text-white bg-secondary fw-bold"
              style="width: 100%; height: 40px;"
          >
              Register Prospect
          </button> -->
          </div>
        <div>
      </div>
      <!--added-->
      <!-- Confirmation -->
      <div v-if="confirmationModal" class="modal fade show d-block" style="background: rgba(0, 0, 0, 0.5);">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header bg-dark text-white">
              <span class="fs-6 fw-bold modal-title">Homeful Confirmation</span>
              <button type="button" class="btn-close" @click="cancelAction"></button>
            </div>
            <div class="modal-body">
              <p>{{ confirmationMessage }}</p>
            </div>
            <div class="modal-footer d-flex justify-content-center">
              <button class="fs-7 btn btn-secondary" @click="cancelAction">Cancel</button>
              <button class="fs-7 btn btn-primary" @click="confirmAction">Confirm</button>
            </div>
          </div>
        </div>
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
table {
    border-collapse: separate !important;
    border-spacing: 0;
}
.hover-modal {
  position: absolute;
  top: 110%; 
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

</style>
