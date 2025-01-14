<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    project: Object
});

const form = useForm({
    _method: 'PUT',
    seller_commission_code: props.project.seller_commission_code,
});

const updateProject = () => {
    form.post(route('projects.update', {project: props.project.id}), {
        errorBag: 'updateProject',
        preserveScroll: true,
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Project
            </h2>
        </template>
        <div class="py-12">
            <div
                class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
            >
                <div class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">{{ project.name }}</div>

                <div class="text-l font-semibold leading-tight text-gray-800 dark:text-gray-200">{{ project.location }}</div>
                <form
                    @submit.prevent="updateProject"
                    class="mt-6 space-y-6"
                >
                    <div>
                        <InputLabel for="seller_commission_code" value="Seller Commission Code" />

                        <TextInput
                            id="seller_commission_code"
                            type="text"
                            class="mt-1 block w-full"
                            v-model=form.seller_commission_code
                            required
                            autofocus
                        />

                        <InputError class="mt-2" :message="form.errors.seller_commission_code" />
                    </div>

                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p
                                v-if="form.recentlySuccessful"
                                class="text-sm text-gray-600 dark:text-gray-400"
                            >
                                Saved.
                            </p>
                        </Transition>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
