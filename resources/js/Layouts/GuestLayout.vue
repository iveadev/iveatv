<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { Link } from '@inertiajs/vue3';
</script>

<template>
        <div>
            <Link :href="route('banner.display')" class="text-white fixed top-0 bg-green-700 w-full p-5 shadow-xl justify-center text-3xl text-center" title="Iniciar reproducción">
                <FontAwesomeIcon icon="fa fa-play" class="self-center" />
            </Link>
        </div>
    <div
        class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0"
    >

        <div class="mt-20 lg:mt-0 p-3">
            <Link href="/">
                <ApplicationLogo class=" fill-current text-gray-500 max-h-20 lg:max-h-full" />
            </Link>
        </div>
        <div class="font-bold text-3xl">
            IVEA TV
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg"
        >
            <slot />
        </div>
        <div class="p-3 flex text-gray-400 text-center">
            <span>Instituto veracruzano de Educación para los Adultos, 2025.</span>
        </div>
    </div>
</template>
