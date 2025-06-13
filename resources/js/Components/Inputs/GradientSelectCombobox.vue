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
import DefaultGradientBorder from '../Container/DefaultGradientBorder.vue';

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
    // const selectedOption = ref(props.options.find(item => item.id === props.modelValue))
    const selectedOption = ref(props.options.find(item => item.id === props.modelValue) || null)

    const filteredOptions = computed(() =>
        query.value === ''
            ? props.options
            : props.options.filter((person) => {
                return person.name.toLowerCase().includes(query.value.toLowerCase())
            }),
    )

    // watch(selectedOption, (newValue) => {
    //     emit('update:modelValue', newValue)
    // })
    watch(selectedOption, (newValue) => {
    emit('update:modelValue', newValue?.id ?? null)
    })
    
</script>
<template>
    <label for="input" class="font-bold text-sm">{{ label }} <span v-if="required" class="text-red-600">*</span></label>
    <DefaultGradientBorder>
        <Combobox as="div" v-model="selectedOption" @update:modelValue="query = ''">
            <div class="relative">
                <ComboboxInput class="block w-full h-full bg-white text-base text-gray-900 border-none ring-0 outline outline-0  outline-gray-300 placeholder:text-gray-400 focus:outline-0 focus:ring-0" @change="query = $event.target.value" @blur="query = ''" :display-value="(option) => option?.name" placeholder="Select from the options" autocomplete="off" />
                <ComboboxButton class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-0">
                    <ChevronUpDownIcon class="size-5 text-gray-400" aria-hidden="true" />
                </ComboboxButton>
    
                <ComboboxOptions v-if="filteredOptions.length > 0" class="absolute z-10 mt-1 max-h-60 w-full overflow-auto bg-white py-1 text-base shadow-lg ring-1 ring-gray-400 focus:outline-none sm:text-sm">
                    <ComboboxOption v-for="person in filteredOptions" :key="person.id" :value="person" as="template" v-slot="{ active, selected }">
                        <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-[#C38400] text-white outline-none' : 'text-gray-900']">
                            <span :class="['block truncate', selected && 'font-semibold']">
                            {{ person.name }}
                            </span>
    
                            <span v-if="selected" :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-[#C38400]']">
                                <CheckIcon class="size-5" aria-hidden="true" />
                            </span>
                        </li>
                    </ComboboxOption>
                </ComboboxOptions>
            </div>
        </Combobox>
    </DefaultGradientBorder>
    <p v-if="errorMessage" class="mt-2 text-sm text-red-600" id="email-error">{{ errorMessage }}</p>
</template>
  
