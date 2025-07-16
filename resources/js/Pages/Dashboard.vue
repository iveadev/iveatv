<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    banners:{
        type:Array,
        default: []
    }
})

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <table>
                        <thead></thead>
                        <tbody>
                            <tr v-for="banner in banners">
                                <td>{{ banner.id }}</td>
                                <td>{{ banner.type }}</td>
                                <td>{{ banner.title }}</td>
                                <td>{{ banner.url }}</td>
                                <td>{{ banner.duration }}</td>
                                <td>{{ banner.visibleTo }}</td>
                                <td>{{ banner.visible }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
