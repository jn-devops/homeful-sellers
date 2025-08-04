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
  id: buyer[0].id,
  first_name: buyer[0].first_name,
  name: buyer[0].first_name + " " + buyer[0].last_name,
  email: buyer[0].email,
  mobile: buyer[0].mobile,
  match_link: buyer[0].other_mobile,
  reference_code: buyer[0].reference_code,
  date_created: buyer[0].created_at,
  status: buyer[0].current_status,
  list_attachment: buyer[1].list_attachment,
  uploaded_attachment: buyer[1].uploaded_attachment,
  total_attachment: buyer[1].total_attachments ,
  total_uploaded_attachment: buyer[1].total_uploaded_attachment
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
const showDateFilterModal = ref(false);
const dateFilter = ref('today'); 
//added 08042025 ggvivar
const startDate = ref('');
const endDate = ref('');

function applyDateRange() {
  if (startDate.value && endDate.value) {
    dateFilter.value = 'dateRange';
    showDateFilterModal.value = false;
  }
}
function clearDateFilter() {
  dateFilter.value = 'today';
  startDate.value = '';
  endDate.value = '';
  showDateFilterModal.value = false;
}
function formatDate(date) {
  if (!date) return '';
  const d = new Date(date);
  const month = String(d.getMonth() + 1).padStart(2, '0');
  const day = String(d.getDate()).padStart(2, '0');
  const year = d.getFullYear();
  return `${month}/${day}/${year}`;
}


//start added search/sort
// const filteredBuyers = computed(() => {
//   let result = buyers.filter(buyer =>
//     buyer.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
//     buyer.email.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
//     buyer.reference_code.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
//     buyer.mobile.includes(searchQuery.value)
//   );
//   result.sort((a, b) => {
//     let modifier = sortDirection.value === 'asc' ? 1 : -1;
//     return a[sortKey.value] < b[sortKey.value] ? -1 * modifier :
//            a[sortKey.value] > b[sortKey.value] ? 1 * modifier : 0;
//   });
//   return result;
// });

// const filteredBuyers = computed(() => {
//   const now = new Date();
//   const todayStart = new Date(now.getFullYear(), now.getMonth(), now.getDate());

//   let result = buyers.filter(buyer => {
//     const matchesSearch =
//       buyer.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
//       buyer.email.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
//       buyer.reference_code.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
//       buyer.mobile.includes(searchQuery.value);

//     if (!matchesSearch) return false;

//     const createdDate = new Date(buyer.date_created);

//     if (dateFilter.value === 'today') {
//       return createdDate >= todayStart;
//     } else if (dateFilter.value === '7days') {
//       const sevenDaysAgo = new Date();
//       sevenDaysAgo.setDate(now.getDate() - 7);
//       return createdDate >= sevenDaysAgo;
//     } else if (dateFilter.value === '30days') {
//       const thirtyDaysAgo = new Date();
//       thirtyDaysAgo.setDate(now.getDate() - 30);
//       return createdDate >= thirtyDaysAgo;
//     }

//     return true; // no date filter
//   });

//   result.sort((a, b) => {
//     let modifier = sortDirection.value === 'asc' ? 1 : -1;
//     return a[sortKey.value] < b[sortKey.value] ? -1 * modifier :
//            a[sortKey.value] > b[sortKey.value] ? 1 * modifier : 0;
//   });

//   return result;
// });

const filteredBuyers = computed(() => {
  const now = new Date();
  const todayStart = new Date(now.getFullYear(), now.getMonth(), now.getDate());

  let result = buyers.filter(buyer => {
    const matchesSearch =
      buyer.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      buyer.email.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      buyer.reference_code.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      buyer.mobile.includes(searchQuery.value);

    if (!matchesSearch) return false;

    const createdDate = new Date(buyer.date_created);

    if (dateFilter.value === 'today') {
      return createdDate >= todayStart;
    } else if (dateFilter.value === '7days') {
      const sevenDaysAgo = new Date();
      sevenDaysAgo.setDate(now.getDate() - 7);
      return createdDate >= sevenDaysAgo;
    } else if (dateFilter.value === '30days') {
      const thirtyDaysAgo = new Date();
      thirtyDaysAgo.setDate(now.getDate() - 30);
      return createdDate >= thirtyDaysAgo;
    } else if (dateFilter.value === 'dateRange') {
      const start = new Date(startDate.value);
      const end = new Date(endDate.value);
      end.setHours(23, 59, 59, 999); // Include full end date
      return createdDate >= start && createdDate <= end;
    }

    return true; // default if no filter
  });

  result.sort((a, b) => {
    let modifier = sortDirection.value === 'asc' ? 1 : -1;
    return a[sortKey.value] < b[sortKey.value] ? -1 * modifier :
           a[sortKey.value] > b[sortKey.value] ? 1 * modifier : 0;
  });

  return result;
});


// for sorting via date created
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

//sync Buyer 
async function syncBuyer(referenceCode) {
  // const url = `api/buyer/sync/${referenceCode}`;
  const url = route('api.sync.update', { referenceCode });

  try {
    const response = await fetch(url, {
      method: 'GET',
      headers: { 'Accept': 'application/json' }
    });
    if (!response.ok) throw new Error('Sync failed');
    const data = await response.json();
    console.log('Sync result:', data);
    showToast(`Synced data for Homeful ID ${referenceCode}`);
  } catch (error) {
    console.error('Sync error:', error);
    showToast('Failed to sync. Please try again.');
  }
}
const greeting = computed(() => {
  const hour = new Date().getHours();
  if (hour < 12) return 'Good Morning!';
  else if (hour < 18) return 'Good Afternoon!';
  else return 'Good Evening!';
});


</script>

<template>
<Head title="Dashboard" />
<AuthenticatedLayout>
  <!-- Header with user info -->
  <template #header>
    <div class="flex flex-col items-center mt-7 px-2">
      <div class="w-full md:w-[500px]">
        <h2 class="text-xl font-bold">Hi {{ user.name }}, {{ greeting }}</h2>
        <h5 class="text-sm font-semibold">
          <!-- Seller Code: <span class="text-brand">{{ user.seller_commission_code }}</span> -->
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
      <div class="d-flex justify-content-end mb-2">
      <button class="btn btn-outline-secondary btn-sm" @click="showDateFilterModal = true">
        <i class="bi bi-calendar3"></i> Filter by Date
      </button>
    </div>
    <!-- <div v-if="dateFilter !== 'all'" class="text-muted small mb-2">
  Filtered by:
  <strong v-if="dateFilter === 'today'">Today</strong>
  <strong v-else-if="dateFilter === '7days'">Last 7 Days</strong>
  <strong v-else-if="dateFilter === '30days'">Last 30 Days</strong>
</div> -->
<div v-if="dateFilter !== 'all'" class="text-muted small mb-2">
  <strong>Filtered by:</strong>
  <strong v-if="dateFilter === 'today'">Today</strong>
  <strong v-else-if="dateFilter === '7days'">Last 7 Days</strong>
  <strong v-else-if="dateFilter === '30days'">Last 30 Days</strong>
  <strong v-else-if="dateFilter === '30days'">All</strong>
  <strong v-else-if="dateFilter === 'dateRange'">
     <br>{{ formatDate(startDate) }} - {{ formatDate(endDate) }}
  </strong>
</div>


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
                  {{ buyer.name }}<br>
                  <span :class="buyer.status == null || buyer.status == ''? 'bg-warning px-2 ml-4 rounded' : 'bg-success px-2 ml-4 rounded text-white'">
                    {{ buyer.status == null || buyer.status == '' ? 'Prospect' : buyer.status }}
                  </span>
                </td>
                <td class="text-end">
                  <div class="d-inline-flex gap-1">
                    <!-- Transaction buttons inside circle, keep functions -->
                    <!-- <a href="#" @click.prevent="openQRModal(buyer.match_link)" class="circle-btn"><i class="bi bi-qr-code"></i></a>
                    <a href="#" @click.prevent="showConfirmation('Send booking link via Email?', sendEmail, buyer)" class="circle-btn text-primary"><i class="bi bi-envelope"></i></a>
                    <a href="#" @click.prevent="showConfirmation('Send booking link via SMS?', sendSMS, buyer)" class="circle-btn text-success"><i class="bi bi-send"></i></a> -->
                    <!-- Email button -->
                  <a href="#" 
                    @click.prevent="buyer.status == null || buyer.status === '' ? openQRModal(buyer.match_link) : null"
                    class="circle-btn"
                    :class="{ 'disabled-btn': buyer.status != null && buyer.status !== '' }"
                    :aria-disabled="buyer.status != null && buyer.status !== ''">
                    <i class="bi bi-qr-code"></i>
                  </a>
                  <a href="#" 
                    @click.prevent="buyer.status == null || buyer.status === '' ? showConfirmation('Send booking link via Email?', sendEmail, buyer) : null"
                    class="circle-btn text-primary"
                    :class="{ 'disabled-btn': buyer.status != null && buyer.status !== '' }"
                    :aria-disabled="buyer.status != null && buyer.status !== ''">
                    <i class="bi bi-envelope"></i>
                  </a>
                  <a href="#" 
                    @click.prevent="buyer.status == null || buyer.status === '' ? showConfirmation('Send booking link via SMS?', sendSMS, buyer) : null"
                    class="circle-btn text-success"
                    :class="{ 'disabled-btn': buyer.status != null && buyer.status !== '' }"
                    :aria-disabled="buyer.status != null && buyer.status !== ''">
                    <i class="bi bi-send"></i>
                  </a>
                  </div>
                </td>
              </tr>
              <!-- Expanded details row -->
              <tr v-if="expandedBuyerId===buyer.id" class="fade-in">
                <td colspan="2" class="bg-light rounded p-2">
                  <p class="mb-1"><strong>Homeful ID:</strong> {{ buyer.reference_code }}</p>
                  <p class="mb-1"><strong>Email:</strong> {{ buyer.email }}</p>
                  <p class="mb-1"><strong>Mobile:</strong> {{ buyer.mobile }}</p>
                  <p class="mb-1"><strong>Date Created:</strong> {{ buyer.date_created }}</p>
                  <p class="mb-1"><b>Document List:</b> {{ buyer.total_attachment == null || buyer.total_uploaded_attachment == null ? 'N/A' : buyer.total_uploaded_attachment+'/'+buyer.total_attachment }} 
                     <i class="bi bi-folder-x text-danger"></i></p>
                  <!-- <p class="mb-0 btn btn-primary"><i class="bi bi-arrow-repeat">Sync</i></p> -->
                  <button class="btn btn-primary btn-sm" @click="syncBuyer(buyer.reference_code)">
                     <i class="bi bi-arrow-repeat"></i> Sync
                  </button>

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
  <!--Date filter modal-->
  <!-- Date Filter Modal -->
<!-- <div v-if="showDateFilterModal" class="modal fade show d-block" style="background: rgba(0,0,0,0.5); z-index:1050;">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h6 class="modal-title">Filter Buyers by Date</h6>
        <button type="button" class="btn-close" @click="showDateFilterModal = false"></button>
      </div>
      <div class="modal-body">
        <div class="d-grid gap-2">
          <button class="btn btn-outline-primary" @click="dateFilter = 'today'; showDateFilterModal = false">Today</button>
          <button class="btn btn-outline-primary" @click="dateFilter = '7days'; showDateFilterModal = false">Last 7 Days</button>
          <button class="btn btn-outline-primary" @click="dateFilter = '30days'; showDateFilterModal = false">Last 30 Days</button>
          <button class="btn btn-outline-secondary" @click="dateFilter = 'all'; showDateFilterModal = false">Clear Filter</button>
        </div>
      </div>
    </div>
  </div>
</div> -->
<!-- Date Filter Modal -->
<div v-if="showDateFilterModal" class="modal fade show d-block" style="background: rgba(0,0,0,0.5); z-index:1050;">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h6 class="modal-title">Filter Buyers by Date</h6>
        <button type="button" class="btn-close" @click="showDateFilterModal = false"></button>
      </div>
      <div class="modal-body">
        <div class="d-grid gap-2 mb-3">
          <button class="btn btn-outline-primary" @click="dateFilter = 'today'; showDateFilterModal = false">Today</button>
          <button class="btn btn-outline-primary" @click="dateFilter = '7days'; showDateFilterModal = false">Last 7 Days</button>
          <button class="btn btn-outline-primary" @click="dateFilter = '30days'; showDateFilterModal = false">Last 30 Days</button>
          <button class="btn btn-outline-primary" @click="dateFilter = 'all'; showDateFilterModal = false">All</button>
        </div>
        <div class="pt-2">
          <label class="form-label fw-bold">Select Date</label><br>
          <label class="form-label">From:</label>
          <input type="date" v-model="startDate" class="form-control form-control-sm mb-2" />
          <label class="form-label">To:</label>
          <input type="date" v-model="endDate" class="form-control form-control-sm mb-2" />
          <button class="btn btn-outline-success btn-sm" @click="applyDateRange">Apply</button>
        </div>
        <button class="btn btn-outline-secondary btn-sm mt-3" @click="clearDateFilter">Clear Filter</button>
      </div>
    </div>
  </div>
</div>


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
.disabled-btn {
  pointer-events: none;
  opacity: 0.4;
  cursor: not-allowed;
}

</style>