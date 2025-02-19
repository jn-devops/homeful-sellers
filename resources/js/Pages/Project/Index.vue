<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue'
import NavLink from "@/Components/NavLink.vue";
import { Head } from '@inertiajs/vue3'

const props = defineProps({
    projects: Object
});
</script>

<template>
    <Head title="Projects" />

    <AuthenticatedLayout>
        <!-- Header for the page -->
        <template #header>
            <div class="p-7">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Projects
                </h2>
            </div>
        </template>

        <!-- Main Content -->
        <div class="pb-12">
            <!-- Center the main content and apply spacing for different breakpoints -->
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Table Container -->
                <div class="bg-white overflow-hidden shadow sm:rounded-lg p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <!-- Table Header -->
                        <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            <th scope="col" class="px-6 py-3">Name</th>
                            <th scope="col" class="px-6 py-3">Location</th>
                            <th scope="col" class="px-6 py-3">Seller Commission Code</th>
                            <th scope="col" class="px-6 py-3">Actions</th>
                        </tr>
                        </thead>

                        <!-- Table Body -->
                        <tbody class="divide-y divide-gray-200 bg-white text-sm">
                        <!-- Loop through the projects data -->
                        <tr
                            v-for="project in projects.data"
                            :key="project.id"
                            class="hover:bg-gray-100 focus-within:bg-gray-100"
                        >
                            <td class="whitespace-nowrap px-6 py-4 font-medium text-gray-900">
                                {{ project.name }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-gray-700">
                                {{ project.location }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-gray-700">
                                {{ project.seller_commission_code }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <!-- Example action link -->
                                <NavLink
                                    :href="route('projects.edit', project.id)"
                                    class="text-indigo-500 hover:text-indigo-700"
                                >
                                    Edit
                                </NavLink>
                                <NavLink
                                    :href="route('projects.show', project.id)"
                                    class="text-indigo-500 hover:text-indigo-700"
                                >
                                    Delete
                                </NavLink>
                            </td>
                        </tr>
                        <!-- If there are no projects -->
                        <tr v-if="projects.data.length === 0">
                            <td class="px-6 py-4 text-center text-gray-500" colspan="4">
                                No projects found.
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    <pagination :links="projects.links" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
