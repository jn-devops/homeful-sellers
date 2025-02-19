<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel, Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { Bars3Icon, BellIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import {useForm, usePage, Link} from '@inertiajs/vue3';
</script>
<template>
    <Disclosure as="nav" class="bg-white shadow" v-slot="{ open }">
        <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
            <div class="relative flex h-16 justify-between">
                <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
                <!-- Mobile menu button -->
                <DisclosureButton class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#C38400]">
                    <span class="absolute -inset-0.5" />
                    <span class="sr-only">Open main menu</span>
                    <Bars3Icon v-if="!open" class="block size-6" aria-hidden="true" />
                    <XMarkIcon v-else class="block size-6" aria-hidden="true" />
                </DisclosureButton>
                </div>
                <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
                <div class="flex shrink-0 items-center cursor-pointer">
                    <Link :href="route('voucher.create')" >
                        <img class="h-7 w-auto" :src="usePage().props.data.appURL+'/images/default_logo.svg'" />
                    </Link>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    <!-- Current: "border-[#C38400] text-gray-900", Default: "border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700" -->
                    <Link :href="route('voucher.create')" :class="route().current('voucher.create') ? 'border-[#C38400] text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'" class="inline-flex items-center border-b-2  px-1 pt-1 text-sm font-medium ">Voucher</Link>
                    <Link :href="route('projects.index')" :class="route().current('projects.index') ? 'border-[#C38400] text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'" class="inline-flex items-center border-b-2  px-1 pt-1 text-sm font-medium ">Projects</Link>
                </div>
                </div>
                <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
                <button class="relative rounded-full bg-white p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-[#C38400] focus:ring-offset-2">
                    <span class="absolute -inset-1.5" />
                    <span class="sr-only">View notifications</span>
                    <BellIcon class="size-6" aria-hidden="true" />
                </button>

                <!-- Profile dropdown -->
                <Menu as="div" class="relative ml-3">
                    <div>
                    <MenuButton class="relative flex rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-[#C38400] focus:ring-offset-2">
                        <span class="absolute -inset-1.5" />
                        <span class="sr-only">Open user menu</span>
                        <svg class="size-8 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M12 20a7.966 7.966 0 0 1-5.002-1.756l.002.001v-.683c0-1.794 1.492-3.25 3.333-3.25h3.334c1.84 0 3.333 1.456 3.333 3.25v.683A7.966 7.966 0 0 1 12 20ZM2 12C2 6.477 6.477 2 12 2s10 4.477 10 10c0 5.5-4.44 9.963-9.932 10h-.138C6.438 21.962 2 17.5 2 12Zm10-5c-1.84 0-3.333 1.455-3.333 3.25S10.159 13.5 12 13.5c1.84 0 3.333-1.455 3.333-3.25S13.841 7 12 7Z" clip-rule="evenodd"/>
                        </svg>

                        <!-- <img class="size-8 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="" /> -->
                    </MenuButton>
                    </div>
                    <transition enter-active-class="transition ease-out duration-200" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                    <MenuItems class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black/5 focus:outline-none">
                        <MenuItem v-slot="{ active }">
                            <Link :href="route('profile.edit')" :class="[active ? 'bg-gray-100 outline-none' : '', 'block px-4 py-2 text-sm text-gray-700']">Profile</Link>
                        </MenuItem>
                        <MenuItem v-slot="{ active }">
                            <Link :href="route('sync-projects.create')" :class="[active ? 'bg-gray-100 outline-none' : '', 'block px-4 py-2 text-sm text-gray-700']">Sync Project</Link>
                        </MenuItem>
                        <MenuItem v-slot="{ active }">
                            <Link :href="route('logout')"
                                method="post"
                                as="button" :class="[active ? 'bg-gray-100 outline-none' : '', 'block px-4 py-2 text-sm text-gray-700']">Logout</Link>
                        </MenuItem>
                    </MenuItems>
                    </transition>
                </Menu>
                </div>
            </div>
        </div>

        <DisclosurePanel class="sm:hidden">
            <div class="space-y-1 pb-4 pt-2">
                <!-- Current: "bg-indigo-50 border-[#C38400] text-indigo-700", Default: "border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700" -->
                <DisclosureButton as="a" href="#" :class="route().current('voucher.create') ? 'border-[#C38400] bg-indigo-50 py-2 pl-3 pr-4 text-base font-medium text-indigo-700' : 'border-transparent py-2 pl-3 pr-4 text-base font-medium text-gray-500 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700'" class="block border-l-4 ">
                    <Link :href="route('voucher.create')" >
                        Voucher
                    </Link>
                </DisclosureButton>
                <DisclosureButton as="a" href="#" class="block border-l-4 ">
                    <Link :href="route('projects.index')" :class="route().current('projects.index') ? 'border-[#C38400] bg-indigo-50 py-2 pl-3 pr-4 text-base font-medium text-indigo-700' : 'border-transparent py-2 pl-3 pr-4 text-base font-medium text-gray-500 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700'" class="block border-l-4 ">
                        Projects
                    </Link>
                </DisclosureButton>
            </div>
        </DisclosurePanel>
    </Disclosure>
</template>
