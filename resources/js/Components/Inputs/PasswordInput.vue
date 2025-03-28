<script setup>
import { ExclamationCircleIcon } from '@heroicons/vue/20/solid';
import { onMounted, ref } from 'vue';

const props = defineProps({
  label: {
    type: String,
    default: '',
  },
  required: {
    type: Boolean,
    default: false,
  },
  placeholder: {
    type: String,
    default: 'Enter text...',
  },
  errorMessage: {
    type: String,
    default: null,
  },
  helperMessage: {
    type: String,
    default: null,
  },
  type: {
    type: String,
    default: 'text',
  },
  max: {
    type: Number,
    default: null,
  },
  readOnly: {
    type: Boolean,
    default: false,
  },
  noBorderRadius: {
    type: Boolean,
    default: false
  }
});

const model = defineModel({
    type: String,
});

const input = ref(null);
const passwordVisible = ref(false);

function togglePasswordVisibility() {
  passwordVisible.value = !passwordVisible.value;
}

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });

</script>
<template>
    <div>
        <label class="block text-sm/6 font-medium" :class="errorMessage ? 'text-red-600' : 'text-gray-900'">{{label}} <span v-if="required" class="text-red-600">*</span></label>
        <div class="grid grid-cols-1">
            <div 
              class="flex flex-row items-center py-0.5 bg-white focus-within:ring-0 outline outline-1 outline-gray-300 -outline-offset-1 focus-within:outline focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-[#006FFD]"
              :class="{
                'rounded-md' : !noBorderRadius,
                'text-red-900 outline-red-300 placeholder:text-red-300': errorMessage,
                'text-gray-900 outline-gray-300 placeholder:text-gray-400': !errorMessage,
              }"  
            >
              <div class="grow">
                <input 
                    v-model="model"
                    :maxlength="max"
                    ref="input"
                    :type="passwordVisible ? 'text' : 'password'"
                    :readonly="readOnly"
                    class="col-start-1 row-start-1 block w-full py-1.5 pl-3 pr-10 text-sm sm:pr-9 border-0 focus:ring-0"
                    :class="{
                      'text-red-900 outline-red-300 placeholder:text-red-300': errorMessage,
                      'text-gray-900 outline-gray-300 placeholder:text-gray-400': !errorMessage,
                    }"
                    :placeholder="placeholder" 
                />
              </div>
              <div class="flex-none  px-2" @click="togglePasswordVisibility">
                <svg v-if="passwordVisible" class="w-6 h-6" :class="errorMessage ? 'text-red-600' : 'text-gray-600'" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                  <path fill-rule="evenodd" d="M4.998 7.78C6.729 6.345 9.198 5 12 5c2.802 0 5.27 1.345 7.002 2.78a12.713 12.713 0 0 1 2.096 2.183c.253.344.465.682.618.997.14.286.284.658.284 1.04s-.145.754-.284 1.04a6.6 6.6 0 0 1-.618.997 12.712 12.712 0 0 1-2.096 2.183C17.271 17.655 14.802 19 12 19c-2.802 0-5.27-1.345-7.002-2.78a12.712 12.712 0 0 1-2.096-2.183 6.6 6.6 0 0 1-.618-.997C2.144 12.754 2 12.382 2 12s.145-.754.284-1.04c.153-.315.365-.653.618-.997A12.714 12.714 0 0 1 4.998 7.78ZM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd"/>
                </svg>
                <svg v-if="!passwordVisible" class="w-6 h-6" :class="errorMessage ? 'text-red-600' : 'text-gray-600'" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                  <path d="m4 15.6 3.055-3.056A4.913 4.913 0 0 1 7 12.012a5.006 5.006 0 0 1 5-5c.178.009.356.027.532.054l1.744-1.744A8.973 8.973 0 0 0 12 5.012c-5.388 0-10 5.336-10 7A6.49 6.49 0 0 0 4 15.6Z"/>
                  <path d="m14.7 10.726 4.995-5.007A.998.998 0 0 0 18.99 4a1 1 0 0 0-.71.305l-4.995 5.007a2.98 2.98 0 0 0-.588-.21l-.035-.01a2.981 2.981 0 0 0-3.584 3.583c0 .012.008.022.01.033.05.204.12.402.211.59l-4.995 4.983a1 1 0 1 0 1.414 1.414l4.995-4.983c.189.091.386.162.59.211.011 0 .021.007.033.01a2.982 2.982 0 0 0 3.584-3.584c0-.012-.008-.023-.011-.035a3.05 3.05 0 0 0-.21-.588Z"/>
                  <path d="m19.821 8.605-2.857 2.857a4.952 4.952 0 0 1-5.514 5.514l-1.785 1.785c.767.166 1.55.25 2.335.251 6.453 0 10-5.258 10-7 0-1.166-1.637-2.874-2.179-3.407Z"/>
                </svg>

              </div>
            </div>
        </div>
        <p v-if="errorMessage" class="mt-1 text-sm text-red-600" id="email-error">{{ errorMessage }}</p>
        <p v-if="helperMessage" class="mt-1 text-sm text-gray-500" id="email-description">{{ helperMessage }}</p>
    </div>
</template>
  