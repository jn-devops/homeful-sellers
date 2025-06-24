<script setup>
import {
  Disclosure,
  DisclosureButton,
  DisclosurePanel,
  Menu,
  MenuButton,
  MenuItem,
  MenuItems
} from '@headlessui/vue'
import { Bars3Icon, BellIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { router, usePage } from '@inertiajs/vue3'

// Navigation handler
function navigate(href) {
  router.visit(href)
}
</script>

<template>
  <Disclosure
    as="nav"
    class="bg-white shadow-sm border-b border-gray-200 fixed top-0 inset-x-0 z-50"
    v-slot="{ open }"
  >
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="flex h-16 items-center justify-between">
        <!-- Logo -->
        <div class="flex flex-1 items-center justify-between">
          <div class="flex items-center text-lg font-semibold text-black cursor-pointer transition hover:scale-105">
            Seller Booking App
          </div>

          <!-- Desktop Nav Buttons -->
          <div class="hidden sm:flex sm:space-x-8">
            <button
              @click="navigate(route('dashboard'))"
              :class="route().current('dashboard')
                ? 'text-[#C38400] font-semibold'
                : 'text-gray-500 hover:text-gray-700'"
              class="inline-flex items-center px-2 pt-1 text-sm transition duration-150"
            >
              Dashboard
            </button>
            <button
              @click="navigate(route('projects.index'))"
              :class="route().current('projects.index')
                ? 'text-[#C38400] font-semibold'
                : 'text-gray-500 hover:text-gray-700'"
              class="inline-flex items-center px-2 pt-1 text-sm transition duration-150"
            >
              Projects
            </button>
          </div>
        </div>

        <!-- Notification + Profile -->
        <div class="flex items-center space-x-4">
          <button class="relative p-1 text-gray-400 hover:text-gray-600 transition">
            <BellIcon class="size-6" />
          </button>

          <!-- Profile Dropdown -->
          <Menu as="div" class="relative">
            <MenuButton class="rounded-full focus:outline-none focus:ring-2 focus:ring-[#C38400] focus:ring-offset-2">
              <svg class="size-8 text-gray-800" fill="currentColor" viewBox="0 0 24 24">
                <path
                  fill-rule="evenodd"
                  d="M12 20a7.966 7.966 0 0 1-5.002-1.756v-.683c0-1.794 1.492-3.25 3.333-3.25h3.334c1.84 0 3.333 1.456 3.333 3.25v.683A7.966 7.966 0 0 1 12 20ZM2 12C2 6.477 6.477 2 12 2s10 4.477 10 10c0 5.5-4.44 9.963-9.932 10h-.138C6.438 21.962 2 17.5 2 12Zm10-5c-1.84 0-3.333 1.455-3.333 3.25S10.159 13.5 12 13.5c1.84 0 3.333-1.455 3.333-3.25S13.841 7 12 7Z"
                  clip-rule="evenodd"
                />
              </svg>
            </MenuButton>

            <transition
              enter-active-class="transition ease-out duration-200"
              enter-from-class="transform opacity-0 scale-95"
              enter-to-class="transform opacity-100 scale-100"
              leave-active-class="transition ease-in duration-75"
              leave-from-class="transform opacity-100 scale-100"
              leave-to-class="transform opacity-0 scale-95"
            >
              <MenuItems class="absolute right-0 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none">
                <MenuItem v-slot="{ active }">
                  <button
                    @click="navigate(route('profile.edit'))"
                    :class="[active ? 'bg-gray-100' : '', 'block w-full text-left px-4 py-2 text-sm text-gray-700']"
                  >
                    Profile
                  </button>
                </MenuItem>
                <MenuItem v-slot="{ active }">
                  <button
                    @click="navigate(route('dashboard'))"
                    :class="[active ? 'bg-gray-100' : '', 'block w-full text-left px-4 py-2 text-sm text-gray-700']"
                  >
                    Homepage
                  </button>
                </MenuItem>
                <MenuItem v-slot="{ active }">
                  <form method="post" :action="route('logout')">
                    <button
                      type="submit"
                      :class="[active ? 'bg-gray-100' : '', 'block w-full text-left px-4 py-2 text-sm text-gray-700']"
                    >
                      Logout
                    </button>
                  </form>
                </MenuItem>
              </MenuItems>
            </transition>
          </Menu>
        </div>
      </div>
    </div>

    <!-- Mobile Nav -->
    <DisclosurePanel class="sm:hidden">
      <div class="space-y-1 pt-2 pb-3 px-2">
        <button
          @click="navigate(route('voucher.create'))"
          class="block w-full text-left rounded-md px-3 py-2 text-base font-medium"
          :class="route().current('voucher.create')
            ? 'bg-[#C38400]/10 text-[#C38400]'
            : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'"
        >
          Voucher
        </button>
        <button
          @click="navigate(route('projects.index'))"
          class="block w-full text-left rounded-md px-3 py-2 text-base font-medium"
          :class="route().current('projects.index')
            ? 'bg-[#C38400]/10 text-[#C38400]'
            : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'"
        >
          Projects
        </button>
      </div>
    </DisclosurePanel>
  </Disclosure>
</template>