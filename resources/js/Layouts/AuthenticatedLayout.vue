<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
import DefaultNavigation from '@/Components/DefaultNavigation.vue';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div class="pt-16">
        <DefaultNavigation />
        <slot name="header" />
        <slot />
    </div>
</template>