<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import { Head, usePage, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import QRCode from 'qrcode';

const props = defineProps({ buyers: Object });
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

const showQRModal = ref(false);
const qrLink = ref('');
const qrImage = ref('');

const confirmationModal = ref(false);
const confirmationMessage = ref('');
const onConfirmAction = ref(() => {});
const selectedActionBuyer = ref(null);

const searchQuery = ref('');
const sortKey = ref('name');
const sortDirection = ref('asc');
const expandedBuyerId = ref(null);

function toggleBuyerCollapse(id) {
  expandedBuyerId.value = expandedBuyerId.value === id ? null : id;
}

async function openQRModal(link) {
  qrLink.value = link;
  showQRModal.value = true;
  try {
    qrImage.value = await QRCode.toDataURL(link, { width: 200, margin: 2 });
  } catch (err) {
    console.error('QR Code generation failed', err);
  }
}

const sendSMS = async (buyer) => {
  const smsBody = {
    mobile: "63" + buyer.mobile.substring(1),
    message: `Hi Mr/Mrs/Ms ${buyer.first_name}! Please complete your booking with ${user.name} via the link below: ${buyer.match_link} Thank you!`,
  };
  try {
    await fetch(route('api.sendSMS'), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(smsBody),
    });
    alert(buyer.name + ' booking link sent via SMS');
  } catch (e) { console.log(e); }
};

const sendEmail = async (buyer) => {
  const emailBody = {
    template: "buyerTemplate",
    recipient: buyer.email || "example@example.com",
    mailBody: {
      subject: "Welcome Buyer!",
      first_name: buyer.first_name || 'N/A',
      seller_name: user.name || "N/A",
      matchlink: buyer.match_link || "N/A",
    },
  };
  try {
    await fetch(route('api.sendEmail'), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(emailBody),
    });
    alert(buyer.name + ' booking link sent via Email');
  } catch (e) { console.log(e); }
};

function closeQRModal() {
  showQRModal.value = false;
  qrImage.value = '';
}

function copyToClipboard(text) {
  navigator.clipboard.writeText(text).then(() => {
    alert('Link copied to clipboard!');
  }).catch(err => {
    console.error('Failed to copy text: ', err);
  });
}

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
</script>

<template>
  <Head title="Dashboard" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex flex-col items-center mt-7 px-2">
        <div class="w-full sm:w-full md:w-[500px] text-center">
          <h2 class="text-xl font-bold">Welcome, {{ user.name }}</h2>
          <h5 class="text-sm font-medium text-gray-600">
            Your Booking Code: <span class="text-yellow-600 font-semibold">{{ user.seller_commission_code }}</span>
          </h5>
        </div>
      </div>
    </template>

    <div class="flex flex-col items-center mt-5 px-2">
      <div class="w-full sm:w-full md:w-[500px] bg-white rounded-xl shadow-lg p-4">
        <!-- Search -->
        <div class="mb-4">
          <h3 class="text-lg font-semibold mb-2">My Buyers</h3>
          <div class="relative">
            <input v-model="searchQuery" type="text" class="form-control form-control-sm pr-10" placeholder="Search buyer by name, email, or mobile" />
            <i class="bi bi-search absolute top-2 end-2 text-gray-500"></i>
          </div>
        </div>

        <!-- Buyer Cards -->
        <div v-for="buyer in filteredBuyers" :key="buyer.id" class="rounded-xl p-4 mb-4 shadow-sm bg-gray-50 hover:shadow-md transition-all">
          <div class="flex justify-between items-start gap-4">
            <div>
              <div class="flex items-center gap-2 font-semibold text-[17px]">
                {{ buyer.name }}
                <i class="bi bi-info-circle text-blue-500 cursor-pointer" @click="toggleBuyerCollapse(buyer.id)"></i>
              </div>
            </div>
            <div class="flex items-center space-x-2">
              <div class="action-btn" @click.prevent="openQRModal(buyer.match_link)">
                <i class="bi bi-qr-code"></i>
              </div>
              <div class="action-btn" @click.prevent="showConfirmation('Send booking link via Email?', sendEmail, buyer)">
                <i class="bi bi-envelope"></i>
              </div>
              <div class="action-btn" @click.prevent="showConfirmation('Send booking link via SMS?', sendSMS, buyer)">
                <i class="bi bi-send"></i>
              </div>
            </div>
          </div>

          <!-- Collapse Info -->
          <transition name="fade">
            <div v-if="expandedBuyerId === buyer.id" class="mt-3 text-sm text-gray-700 bg-white rounded-md p-3 border border-gray-200">
              <div><strong>Email:</strong> {{ buyer.email }}</div>
              <div><strong>Mobile:</strong> {{ buyer.mobile }}</div>
              <div><strong>Reference Code:</strong> {{ buyer.reference_code }}</div>
              <div><strong>Created At:</strong> {{ buyer.date_created }}</div>
            </div>
          </transition>
        </div>

        <!-- QR Modal -->
        <div v-if="showQRModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.5); z-index: 1050;">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Booking QR Code</h5>
                <button type="button" class="btn-close" @click="closeQRModal"></button>
              </div>
              <div class="modal-body text-center">
                <img v-if="qrImage" :src="qrImage" alt="QR Code" class="mx-auto" />
                <p class="mt-3">or copy the link:</p>
                <button class="btn btn-outline-primary btn-sm" @click="copyToClipboard(qrLink)">Copy Link</button>
              </div>
              <div class="modal-footer">
                <button class="btn btn-secondary" @click="closeQRModal">Close</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Confirmation Modal -->
        <div v-if="confirmationModal" class="modal fade show d-block" style="background: rgba(0, 0, 0, 0.5); z-index: 1050;">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Confirmation</h5>
                <button type="button" class="btn-close" @click="cancelAction"></button>
              </div>
              <div class="modal-body">
                <p>{{ confirmationMessage }}</p>
              </div>
              <div class="modal-footer justify-content-center">
                <button class="btn btn-secondary" @click="cancelAction">Cancel</button>
                <button class="btn btn-primary" @click="confirmAction">Confirm</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Buttons -->
        <div class="mt-4">
          <button @click="router.visit('/buyer/register')" class="btn btn-dark w-100 mb-2">Book Sales</button>
          <!-- <button class="btn btn-secondary w-100">Register Prospect</button> -->
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: all 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-5px);
}
.action-btn {
  background: #fff;
  border-radius: 9999px;
  padding: 0.5rem;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  transition: transform 0.2s, box-shadow 0.2s;
  cursor: pointer;
}
.action-btn:hover {
  transform: scale(1.1);
  box-shadow: 0 4px 10px rgba(0,0,0,0.15);
}
</style>