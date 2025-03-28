<script setup>
    import { computed, ref, watch, watchEffect } from 'vue'
    import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid'
    import {
        Combobox,
        ComboboxButton,
        ComboboxInput,
        ComboboxLabel,
        ComboboxOption,
        ComboboxOptions,
    } from '@headlessui/vue'

    const props = defineProps({
        label: {
            type: String,
            default: 'Default Label',
        },
        required: {
            type: Boolean,
            default: false,
        },
        errorMessage: {
            type: String,
            default: null,
        },
        helperMessage: {
            type: String,
            default: null,
        },
        options: {
            type: Array,
            default: null,
        },
        modelValue: {
            type: [String],
            default: null,
        },
    });

    const emit = defineEmits(['update:modelValue'])

    const query = ref('')
    const selectedOption = ref(props.options.find(item => item.id === props.modelValue))
    const filteredOptions = computed(() =>
        query.value === ''
            ? props.options
            : props.options.filter((person) => {
                return person.name.toLowerCase().includes(query.value.toLowerCase())
            }),
    )

    watch(selectedOption, (newValue) => {
        emit('update:modelValue', newValue.id)
    })

</script>
<template>
    <Combobox as="div" v-model="selectedOption" @update:modelValue="query = ''">
        <ComboboxLabel class="block text-sm/6 font-medium text-gray-900">{{ label }} <span v-if="required" class="text-red-600">*</span></ComboboxLabel>
        <div class="relative mt-2">
            <ComboboxInput class="block w-full rounded-md bg-white py-1.5 pl-3 pr-12 text-base text-gray-900 focus:ring-0 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-[#CC035C] sm:text-sm/6" @change="query = $event.target.value" @blur="query = ''" :display-value="(option) => option?.name" placeholder="Select from the options" />
            <ComboboxButton class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                <ChevronUpDownIcon class="size-5 text-gray-400" aria-hidden="true" />
            </ComboboxButton>

            <ComboboxOptions v-if="filteredOptions.length > 0" class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm">
                <ComboboxOption v-for="person in filteredOptions" :key="person.id" :value="person" as="template" v-slot="{ active, selected }">
                    <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-[#CC035C] text-white outline-none' : 'text-gray-900']">
                        <span :class="['block truncate', selected && 'font-semibold']">
                        {{ person.name }}
                        </span>

                        <span v-if="selected" :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-[#CC035C]']">
                            <CheckIcon class="size-5" aria-hidden="true" />
                        </span>
                    </li>
                </ComboboxOption>
            </ComboboxOptions>
        </div>
    </Combobox>
    <p v-if="errorMessage" class="mt-2 text-sm text-red-600" id="email-error">{{ errorMessage }}</p>
    <p v-if="helperMessage" class="mt-2 text-sm text-gray-500" id="email-description">{{ helperMessage }}</p>
</template>

