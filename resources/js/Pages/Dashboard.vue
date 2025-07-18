<script setup>
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { dateFormat } from '@/utils';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    banners:{
        type:Array,
        default: []
    },
    today:{
        type:Date,
    }
})

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-3">
                <h2
                    class="text-xl font-semibold leading-tight text-gray-800 text-center"
                >
                    Programación del día
                </h2>
                <div class="grid grid-cols-3 gap-5">
                    <div>
                        <SecondaryButton>Anterior</SecondaryButton>
                    </div>
                    <b class="text-red-800 text-center">{{today}}</b>
                    <div class="grid justify-items-end">
                        <SecondaryButton>Siguiente</SecondaryButton>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <table class="w-full text-center">
                        <thead class="bg-gray-600 text-white">
                            <tr class="h-10">
                                <th>ID</th>
                                <th>Tipo de contenido</th>
                                <th>Título</th>
                                <th>Archivo</th>
                                <th>Duración (seg.)</th>
                                <th>¿Visible?</th>
                                <th>Visible desde</th>
                                <th>Visible hasta</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="banner in banners" class="h-10">
                                <td>{{ banner.id }}</td>
                                <td>{{ banner.type }}</td>
                                <td>{{ banner.title }}</td>
                                <td>{{ banner.url }}</td>
                                <td>{{ banner.duration }}</td>
                                <td>{{ banner.visible }}</td>
                                <td>{{ dateFormat(banner.visibleFrom) }}</td>
                                <td>{{ dateFormat(banner.visibleTo) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
th{
    padding: 10px;
}
td{
    padding: 10px;
}
</style>
