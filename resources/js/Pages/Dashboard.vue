<script setup>
// Import layout, bootstrap, and Vue essentials
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import { Head, usePage, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import QRCode from 'qrcode';

// Get logged in user & buyers data from props
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

// UI states
const expandedBuyerId = ref(null); // Track which buyer row is expanded
const showQRModal = ref(false);
const qrLink = ref('');
const qrImage = ref('');
const confirmationModal = ref(false);
const confirmationMessage = ref('');
const onConfirmAction = ref(() => {});
const selectedActionBuyer = ref(null);
const toastMessage = ref('');
const searchQuery = ref('');
const sortKey = ref('name');
const sortDirection = ref('asc');

// Generate QR code and open modal
async function openQRModal(link) {
  qrLink.value = link;
  showQRModal.value = true;
  try {
    qrImage.value = await QRCode.toDataURL(link, { width: 200, margin: 2 });
  } catch (err) {
    console.error('QR Code generation failed', err);
  }
}

// Close QR modal
function closeQRModal() {
  showQRModal.value = false;
  qrImage.value = '';
}

// Copy link to clipboard
function copyToClipboard(text) {
  navigator.clipboard.writeText(text).then(() => {
    showToast('Link copied to clipboard!');
  }).catch(err => {
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

// Cancel confirmation modal
function cancelAction() {
  confirmationModal.value = false;
}
//end 

// Show toast message
function showToast(message) {
  toastMessage.value = message;
  setTimeout(() => { toastMessage.value = ''; }, 3000);
}

// Send booking link via SMS
async function sendSMS(buyer) {
  const smsBody = {
    mobile: "63" + buyer.mobile.substring(1),
    message: `Hi ${buyer.first_name}, please complete your booking with ${user.name}: ${buyer.match_link}`
  };
  try {
    await fetch(route('api.sendSMS'), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(smsBody)
    });
    showToast(`${buyer.name} booking link sent via SMS`);
  } catch (e) {
    console.log(e);
  }
}

// Send booking link via Email
async function sendEmail(buyer) {
  const emailBody = {
    template: "buyerTemplate",
    recipient: buyer.email,
    mailBody: {
      subject: "Welcome Buyer!",
      first_name: buyer.first_name,
      seller_name: user.name,
      matchlink: buyer.match_link
    }
  };
  try {
    await fetch(route('api.sendEmail'), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(emailBody)
    });
    showToast(`${buyer.name} booking link sent via Email`);
  } catch (e) {
    console.log(e);
  }
}

// Filter & sort buyers list based on search and sort key
//start added search/sort
const filteredBuyers = computed(() => {
  let result = buyers.filter(buyer =>
    buyer.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    buyer.email.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    buyer.reference_code.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    buyer.mobile.includes(searchQuery.value)
  );
  result.sort((a, b) => {
    let modifier = sortDirection.value === 'asc' ? 1 : -1;
    return a[sortKey.value] < b[sortKey.value] ? -1 * modifier :
           a[sortKey.value] > b[sortKey.value] ? 1 * modifier : 0;
  });
  return result;
});

// Change sort key / direction
function setSort(key) {
  if (sortKey.value === key) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortKey.value = key;
    sortDirection.value = 'asc';
  }
}
//end

// Toggle expand/collapse buyer row
function toggleExpand(buyerId) {
  expandedBuyerId.value = expandedBuyerId.value === buyerId ? null : buyerId;
}
</script>

<template>
<Head title="Dashboard" />
<AuthenticatedLayout>
  <!-- Header with user info -->
  <template #header>
    <div class="flex flex-col items-center mt-7 px-2">
      <div class="w-full md:w-[500px]">
        <h2 class="text-xl font-bold">{{ user.name }}</h2>
        <h5 class="text-sm font-semibold">
          Seller Code: <span class="text-brand">{{ user.seller_commission_code }}</span>
        </h5>
      </div>
    </div>
  </template>

  <div class="flex flex-col items-center mt-4 px-2">
    <div class="w-full md:w-[500px] border rounded shadow bg-white p-3">
      <div class="row mb-2">
        <div class="col-6 fs-6 fw-bold d-flex align-items-center">
          My Buyers
          <!-- Modern badge showing buyers count -->
          <span class="buyers-badge ms-2">{{ buyers.length }}</span>
        </div>
        <div class="col-6 text-end">
          <button @click="router.visit('/buyer/register')" class="btn btn-dark rounded-btn">
            <i class="bi bi-person-add"></i> Book Sale
          </button>
        </div>
      </div>
      <input v-model="searchQuery" class="form-control form-control-sm mb-2 rounded" placeholder="Search buyer by name, Homeful ID, email, or mobile" />

      <div class="table-responsive">
        <table class="table table-bordered table-hover text-sm align-middle">
          <thead class="table-dark">
            <tr>
              <th @click="setSort('date_created')" style="cursor:pointer;">
                Buyer Details
                <transition name="fade">
                  <template v-if="sortKey==='date_created'">
                    <i :class="sortDirection==='asc' ? 'bi bi-sort-alpha-down' : 'bi bi-sort-alpha-up'"></i>
                  </template>
                  <template v-else>
                    <i class="bi bi-arrow-down-up"></i>
                  </template>
                </transition>
              </th>
              <th>Transaction</th>
            </tr>
          </thead>
          <tbody>
            <!-- Loop buyers list -->
            <template v-for="buyer in filteredBuyers" :key="buyer.id">
              <tr>
                <td>
                  <!-- (i) icon moved left, toggles expand -->
                  <i class="bi bi-info-circle text-primary me-1" style="cursor:pointer;"
                     @click="toggleExpand(buyer.id)"></i>
                  {{ buyer.name }}
                </td>
                <td class="text-end">
                  <div class="d-inline-flex gap-1">
                    <!-- Transaction buttons inside circle, keep functions -->
                    <a href="#" @click.prevent="openQRModal(buyer.match_link)" class="circle-btn"><i class="bi bi-qr-code"></i></a>
                    <a href="#" @click.prevent="showConfirmation('Send booking link via Email?', sendEmail, buyer)" class="circle-btn text-primary"><i class="bi bi-envelope"></i></a>
                    <a href="#" @click.prevent="showConfirmation('Send booking link via SMS?', sendSMS, buyer)" class="circle-btn text-success"><i class="bi bi-send"></i></a>
                  </div>
                </td>
              </tr>
              <!-- Expanded details row -->
              <tr v-if="expandedBuyerId===buyer.id" class="fade-in">
                <td colspan="2" class="bg-light rounded p-2">
                  <p class="mb-1"><strong>Homeful ID:</strong> {{ buyer.reference_code }}</p>
                  <p class="mb-1"><strong>Email:</strong> {{ buyer.email }}</p>
                  <p class="mb-1"><strong>Mobile:</strong> {{ buyer.mobile }}</p>
                  <p class="mb-0"><strong>Date Created:</strong> {{ buyer.date_created }}</p>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modals & Toast: keep functions unchanged -->
  <!-- QR Modal -->
  <div v-if="showQRModal" class="modal fade show d-block" style="background: rgba(0,0,0,0.5); z-index:1050;">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title">Booking Link QR</h5>
          <button type="button" class="btn-close" @click="closeQRModal"></button>
        </div>
        <div class="modal-body text-center">
          <img v-if="qrImage" :src="qrImage" class="d-block mx-auto mb-2" />
          <p class="small text-muted">Share this QR or copy the link below</p>
          <button class="btn btn-outline-primary btn-sm rounded-btn" @click="copyToClipboard(qrLink)">Copy Link</button>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary rounded-btn" @click="closeQRModal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Confirmation Modal -->
  <div v-if="confirmationModal" class="modal fade show d-block" style="background: rgba(0,0,0,0.5); z-index:1050;">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-dark text-white">
          <h6 class="modal-title">Homeful Confirmation</h6>
          <button type="button" class="btn-close" @click="cancelAction"></button>
        </div>
        <div class="modal-body">{{ confirmationMessage }}</div>
        <div class="modal-footer d-flex justify-content-center">
          <button class="btn btn-secondary btn-sm rounded-btn" @click="cancelAction">Cancel</button>
          <button class="btn btn-primary btn-sm rounded-btn" @click="confirmAction">Confirm</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Toast -->
  <div v-if="toastMessage" class="position-fixed bottom-0 end-0 m-3">
    <div class="toast show text-white bg-success">
      <div class="toast-body">{{ toastMessage }}</div>
    </div>
  </div>
</AuthenticatedLayout>
</template>

<style scoped>
:root { --brand-color: #C38400; }
.text-brand { color: var(--brand-color); }
.rounded-btn { border-radius: 12px; font-size: 0.875rem; }

/* Modern badge next to My Buyers */
.buyers-badge {
  display: inline-block;
  background: #fff8e6;
  color: var(--brand-color);
  font-size: 0.75rem;
  font-weight: 600;
  border: 1px solid var(--brand-color);
  border-radius: 999px;
  padding: 2px 8px;
  box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

/* Circle transaction buttons */
.circle-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 30px; height: 30px;
  background: white;
  border-radius: 50%;
  border: 1px solid #ddd;
  text-decoration: none;
  
  font-size: 0.9rem;
  transition: background 0.2s, color 0.2s;
}
.circle-btn:hover {
  background: #f1f1f1;
}

/* Fade animation for expanded row */
.fade-in {
  animation: fadeIn 0.3s ease-in;
}
@keyframes fadeIn {
  from { opacity:0; transform: translateY(-4px); }
  to { opacity:1; transform: translateY(0); }
}
</style>