<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { Head, router, useForm } from '@inertiajs/vue3';
import { onMounted, onUpdated, ref, watch } from 'vue';

const props = defineProps({
    banner: {
        type: Object,
    },
    empty: {
        type: Boolean,
        default: false
    },
    next: {
        type: Number,
        default: null
    }
})
const loading = ref(true)
const haveError = ref(false)
const waiting = ref(500);

const nextBanner = ref(props.next)

const goToNext = () => {
    // avanza inmediatamente al siguiente contenido
    seconds.value = 0
    showNext()
}

const seconds = ref(props.banner.duration) 

const showNext = () => {
    const duration = seconds.value * 1000
    // tiempo mostrando + espera de carga
    setTimeout(() => {
        loading.value = true
        router.visit(route('banner.display', {id: nextBanner.value}),{
            only:['banner', 'next', 'empty'],
            onSuccess:()=>{
                loading.value = false
                seconds.value = props.banner.duration
                nextBanner.value = props.next

            }
        })
    },duration+waiting.value);

}

onMounted(()=>{
    if(props.empty) {
        setTimeout(() => {
            loading.value = false        
        }, 30000);
    } else {
        loading.value = false
    }
})

const handleError = () => {
    loading.value = true
    haveError.value = true
    seconds.value = 5
    showNext();
}

const reloadPage=() => {
    window.location.reload();
}

const forceNext = () => {
    router.visit(route('banner.display', {id: nextBanner.value}))
}

</script>

<template>
    <Head :title="banner.file.name" />
    <main class="bg-black min-h-screen min-w-screen overflow-hidden grid justify-items-center content-center">
        <Transition name="fade">
        <div v-if="!loading">
            <div v-if="banner.file.type == 'IMAGEN'">
                <img :src="banner.file.url" class="max-h-screen" @error="handleError" @load="showNext">
            </div>
            <div v-if="banner.file.type == 'VIDEO'" class="bg-black">
                <video id="videoplayer" autoplay muted controls @ended="goToNext" class="max-h-screen">
                    <source :src="route('streaming',banner.id)" type="video/mp4" @error="handleError"> 
                    Your browser does not support the video tag.
                </video> 
            </div>
        </div>
        </Transition>
        <Transition name="fade">
            <div class="text-white">
                <div v-if="haveError" class="bg-gray-600 p-6 text-center rounded-2xl">
                    <h1 class="text-3xl font-bold py-5">¡Upss!</h1>
                    <p class="text-xl">Parece que tuvimos problemas al cargar el siguiente contenido:</p>
                    <div class="p-4 flex gap-2">
                        <h2 class="w-1/4 text-3xl font-bold p-3">{{ banner.id }}</h2>
                        <div class="flex-1 border-l px-5 text-center">
                            <p class="text-xl">Título: <b>{{ banner.file.name }}</b></p>
                            <p class="text-xl">Url: <b>{{ banner.file.url }}</b></p>
                        </div>
                    </div>
                </div>
                <div class="text-white fixed bottom-10 left-10 right-32">
                    <div v-if="props.empty & loading">
                        <p class="text-xl">Instituto Veracruzano de Educación para los Adultos</p>
                        <div class="flex gap-4 text-sm text-white/40">
                            <a href="https://github.com/cesariux23" target="_blank" class="flex gap-2">
                                <FontAwesomeIcon icon="fa fa-code" class="self-center text-md font-bold" />
                                <span>Developed by :</span>
                                <span class="font-bold">Departamento de Tecnologías de la Información</span>
                            </a>
                            
                        </div>
                        
                    </div>
                </div>
                <div class="fixed bottom-10 right-10 text-white grid justify-items-center" v-if="loading">
                    <Transition name="pulse">
                        <img src="/logo-white.png" class="max-h-40 lg:max-h-full" :class="{'pulsing-element':props.empty}">
                    </Transition>
                </div>
                <div class="fixed top-5 right-5 group w-56 grid grid-cols-2 justify-items-end">
                    <button type="button" class="font-bold rounded-xl transparent text-transparent group-hover:bg-black/15 group-hover:text-white/20" @click="reloadPage">
                        <div class="hover:text-white p-6 hover:bg-blue-500/40 rounded-xl">
                            <div class="text-7xl">
                                <FontAwesomeIcon icon="fa fa-rotate-right" />
                            </div>
                        </div>
                    </button>
                    <button type="button" class="font-bold rounded-xl transparent text-transparent group-hover:bg-black/15 group-hover:text-white/20" @click="forceNext">
                        <div class="hover:text-white p-6 hover:bg-green-500/40 rounded-xl">
                            <div class="text-7xl">
                                <FontAwesomeIcon icon="fa fa-chevron-right" />
                            </div>
                        </div>
                    </button>
                </div>
            </div>
        </Transition>
    </main>
</template>
<style scoped>
    .fade-enter-active,
    .fade-leave-active {
    transition: opacity 0.5s ease;
    }

    .fade-enter-from,
    .fade-leave-to {
    opacity: 0;
    }


    .pulse-enter-active,
    .pulse-leave-active {
      transition: transform 0.2s ease;
    }

    .pulse-enter-from,
    .pulse-leave-to {
      transform: scale(0.9); /* Slightly smaller when entering/leaving */
    }

    .pulse-enter-to,
    .pulse-leave-from {
      transform: scale(1.1); /* Slightly larger during the animation */
    }

    /* Additional keyframes for continuous pulsing */
    @keyframes continuous-pulse {
      0% { transform: scale(1); }
      50% { transform: scale(0.8); }
      100% { transform: scale(1); }
      
    }

    .pulsing-element {
      animation: continuous-pulse 2s infinite; /* Apply to an element for continuous pulse */
    }
</style>
