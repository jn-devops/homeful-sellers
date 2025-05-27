<script setup>
// import GenerateVoucherForm from './Partials/GenerateVoucherForm.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import GradientStyleInput from '@/Components/Inputs/GradientStyleInput.vue';
import GradientStylePhoneNumber from '@/Components/Inputs/GradientStylePhoneNumber.vue';
import GradientSelectCombobox from '@/Components/Inputs/GradientSelectCombobox.vue';
import { Head, usePage ,useForm, Link, router} from '@inertiajs/vue3';
import Agreements from '@/Components/Agreements.vue';
import { ref } from "vue";
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'bootstrap/dist/css/bootstrap.min.css';

const props = defineProps({
    projects: Array,
});
const projects = usePage().props.projects.map(item => ({
   id: item.project_code,
   name: item.name
}));
console.log(projects);
const form = useForm({
    contact_reference_code: '',
    project_code: '',
    first_name:'',
    last_name:'',
    email:'',
    mobile:'',
    date_of_birth:'',
    gross_monthly_income:'',
    cobo_gross_monthly_income:'0',
    cobo_date_of_birth:'2000-01-01',
    seller_id:usePage().props.auth.user.id,
    seller_commission_code: usePage().props.auth.user.seller_commission_code
});
const showTC = ref(false)
const coboChecked = ref(false);
const showAgreementPage = ref(false)
const disclaimerChecked = ref(false)
;
const toggleTC = () => {
    showTC.value = !showTC.value
}

const togglePP = () => {
    showAgreementPage.value = !showAgreementPage.value
}

// console.log(form)
const viewAgreements = () => {
    disclaimerChecked.value = false
    togglePP()
}
const addCoborrower = () => {
    coboChecked.value = true
    // togglePP()
}
const submit = async () => {
console.log(JSON.stringify(form.data()))
    try {
        const response = await fetch('https://contracts-staging.homeful.ph/api/register-and-point-to-match', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(form.data()),
        });

        if (!response.ok) {
            const errorData = await response.json();
            console.error('Error:', errorData);
            alert('Submission failed.');
            return;
        }

        const result = await response.json();
        console.log('Success:', result['homeful_id']);
        // form.post(route('voucher.store'))"
         // transaction_code => 'JN-TYUARP', 
     
        // match_link: 'https://contracts-staging.homeful.ph/avail/create?reference=JN-TYUARP'
            
        const res_arr = {
            contact_reference_code: result['homeful_id'],
            project_code: form.project_code  
        };
        console.log(res_arr);

        const res_voucher = await fetch(route('voucher.generate'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(res_arr),
        });
        console.log(res_voucher)
        alert('Registration successful!');
        window.location.href = '/dashboard';

    } catch (error) {
        console.error('Network or server error:', error);
        alert('An error occurred during submission.');
    }
};

</script>

<template>
    <Head title="Register" />

    <AuthenticatedLayout>
        <!-- <template #header>
            <div class="flex flex-col items-center mt-7 px-2">
                <div class="w-full sm:w-full md:w-[500px]">
                    <h2 class="text-xl font-bold">{{usePage().props.auth.user.name}}</h2>
                    <h5 class="text-sm font-semibold">Seller Code: <span class="text-[#C38400]">{{ usePage().props.auth.user.seller_commission_code }}</span></h5>
                </div>
            </div>
        </template> -->
        <template #header>
            <Link :href="route('dashboard')" style="text-decoration: none;"> <svg class="w-6 h-6 text-gray-800 dark:text-white position-absolute ml-2 mt-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/>
                    </svg>
                </Link>
            <div class="flex flex-col items-center justify-center mt-1 px-2 fw-bold">
                Create an Account
            </div>
        </template>
        <div class="flex flex-col items-center justify-center mt-7 px-2">
            <div class="w-full sm:w-full md:w-[500px] border-2 shadow-lg p-7 h-100">
                <!-- <h4 class="fw-bold">Register Buyer</h4> -->
            <div class="">
          
            <form @submit.prevent="submit">
                <div>
                    <GradientStyleInput
                        label="First Name"
                        placeholder="Enter Name"
                        required
                        v-model="form.first_name"
                        :error="form.errors.first_name"
                    />
                </div>
                <div>
                    <GradientStyleInput
                        label="Last Name"
                        placeholder="Enter Name"
                        required
                        v-model="form.last_name"
                        :error="form.errors.last_name"
                    />
                </div>
                <div>
                    <GradientStylePhoneNumber
                        label="Mobile Number"
                        placeholder="+63 XXX XXXX XXX"
                        required
                        v-model="form.mobile"
                        :error="form.errors.mobile"
                    />
                </div>
                <div>
                    <GradientStyleInput
                        label="Email Address"
                        placeholder="Enter Email"
                        required
                        type="email"
                        v-model="form.email"
                        :error="form.errors.email"
                    />
                </div>
                <div>
                    <GradientStyleInput
                            label="Birthdate"
                            type="date"
                            required
                            v-model="form.date_of_birth"
                            :error="form.errors.date_of_birth"
                        />
                        </div>
                        <div>
                        <GradientStyleInput
                            label="Gross Monthly Income"
                            type="number"
                            placeholder="Enter Gross Monthly Income"
                            required
                            v-model="form.gross_monthly_income"
                            :error="form.errors.gross_monthly_income"
                        />
                    </div>
                    <div>
                        <div class="flex items-center mt-2 mb-4 gap-2" @click="showCoborrow">
                        <input type="checkbox" @click="addCoborrower" v-model="coboChecked" id="default-checkbox" class="w-5 h-5 text-[#F7C947] rounded-sm focus:ring-[#E94572]" style=" border-radius:25%;">
                        <label class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                            Add Coborrower
                        </label>
                        </div>
                        <div v-if="coboChecked">
                        <div>
                         <GradientStyleInput
                            label="Coborrower Birthdate"
                            type="date"
                            required
                            v-model="form.cobo_date_of_birth"
                            :error="form.errors.cobo_date_of_birth"
                        />
                        </div>
                        <div>
                        <GradientStyleInput
                            label="Coborrower Gross Monthly Income"
                            type="number"
                            placeholder="Enter Gross Monthly Income"
                            required
                            v-model="form.cobo_gross_monthly_income"
                            :error="form.errors.cobo_gross_monthly_income"
                        />
                        </div>
                    </div>
                    </div>
                    <div>
                <GradientSelectCombobox 
                    label="Projects"
                    required
                    :options="projects"
                    v-model="form.project_code"
                    :error-message="form.errors.project_code"
                />
              </div>
                <div class="mt-3 ">
                    <div class="flex items-center mb-4 gap-2" @click="viewAgreements">
                        <input type="checkbox"  @click="viewAgreements" v-model="disclaimerChecked" id="default-checkbox" class="w-5 h-5 text-[#F7C947] rounded-sm focus:ring-[#E94572]" style=" border-radius:25%;">
                         <!-- <div v-if="disclaimerChecked" class="w-6 h-5 bg-[#F7C947] rounded border-2 border-black">
                            <svg class="w-full h-full text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 11.917 9.724 16.5 19 7.5"/>
                            </svg>
                         </div>
                         <div v-else class="w-[25px] h-[20px] rounded border-2 border-black">
                         </div> -->
                        <label class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                            I've read and agree with the <b class="underline cursor-pointer">Terms of Use</b> and the <b class="underline cursor-pointer">Privacy Policy</b>.
                        </label>
                    </div>
                    <div class="mt-3">
                    <!-- <PlainBlackButton type="submit" :disabled="!disclaimerChecked"> -->

                    <div class="container pt-3">
                    <button
                        class="text-sm text-center text-white bg-black fw-bold"
                        type="submit"
                        style="width: 100%; height: 40px;"
                    >
                        Register
                    </button>
                    </div>
                </div>
                </div>
            </form>
            <Agreements 
                v-if="showAgreementPage" 
                @close="togglePP"
                v-model:disclaimerChecked="disclaimerChecked"
            />
            <Agreements 
                v-if="showAgreementPage" 
                @close="togglePP"
                v-model:disclaimerChecked="disclaimerChecked"
            />
        </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
