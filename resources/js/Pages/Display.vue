<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { Head, router } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
    config: {
        type: Object,
    },
    toShow: {
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
const config = ref(props.config)
const banner = ref(null)

const nextbanner = ref(props.next)

const goToNext = () => {
    // avanza inmediatamente al siguiente contenido
    seconds.value = 0
    showNext()
}

const seconds = ref(0) 

const showNext = () => { 
    const duration = seconds.value * 1000
    // tiempo mostrando + espera de carga
    setTimeout(()=>{
        banner.value = null
    }, duration)
    setTimeout(() => {
        loading.value = true
        loadNext()
        
    },duration+waiting.value);

}

const loadBanner = ()=> {
    // Limpia e inicializa el banner de acuerdo a los parametros
    loading.value = false
    config.value.empty = false
    config.value.standby = false
    const _b = props.toShow
    if(config.standby){
        nextbanner.value = null
    }
    banner.value = _b
    seconds.value = _b.duration
}

onMounted(()=>{
    if(props.config.empty) {
        setTimeout(()=>{
            // Muestra unos segundos la pantalla vacia
            loadBanner()
        },props.config.waiting * 1000);
    } else {
        // carga la informacion del banner
        loadBanner()
    }
})

const handleError = () => {
    // limpia valores y muestras 5 segundos la pantalla de error
    config.value.empty = false
    config.value.standby = false
    haveError.value = true
    seconds.value = 10
    showNext();
}

const reloadPage=() => {
    window.location.reload();
}

const loadNext = () => {
    // genera los parametros para la petición del siguiente contenido
    const params = {
        id: nextbanner.value,
        times: config.value.times
    }
    if(config.value.date){
        params.date = config.value.date
    }
    window.location.replace(route('banner.display', params));
}

const title = computed(()=>{
    return banner.value ? banner.value.event.file.name : 'Cargando...'
})

</script>

<template>
    <Head :title="title" />
    <main class="bg-black text-white overflow-hidden min-h-screen min-w-screen">
        <div class="fixed top-5 right-5 group w-56 grid grid-cols-2 justify-items-end">
            <button type="button" class="font-bold rounded-xl transparent text-transparent group-hover:bg-black/15 group-hover:text-white/20" @click="reloadPage">
                <div class="hover:text-white p-6 hover:bg-blue-500/40 rounded-xl">
                    <div class="text-7xl">
                        <FontAwesomeIcon icon="fa fa-rotate-right" />
                    </div>
                </div>
            </button>
            <button type="button" class="font-bold rounded-xl transparent text-transparent group-hover:bg-black/15 group-hover:text-white/20" @click="loadNext">
                <div class="hover:text-white p-6 hover:bg-green-500/40 rounded-xl">
                    <div class="text-7xl">
                        <FontAwesomeIcon icon="fa fa-chevron-right" />
                    </div>
                </div>
            </button>
        </div>
        <div class="min-h-screen min-w-screen overflow-hidden" v-if="!haveError && !config.empty">
            <div v-if="loading" class="grid justify-items-center place-content-center gap-6 min-h-screen min-w-screen">
                <img src="/logo-white.png"></img>
            </div>
            <div class="grid justify-items-center place-content-center min-h-screen min-w-screen">
                <Transition name="fade">
                    <div v-if="banner">
                            <div v-if="banner.event.file.type == 'image'">
                                <img :src="banner.event.file.url" class="max-h-screen" @error="handleError" @load="showNext">
                            </div>
                        <div v-if="banner.event.file.type == 'video'">
                            <video id="videoplayer" autoplay :muted="!banner.sound" controls @ended="goToNext" @error="handleError" class="max-h-screen overflow-hidden">
                                <source :src="route('streaming',banner.event.file.id)" type="video/mp4" @error="handleError"> 
                                Your browser does not support the video tag.
                            </video> 
                        </div>
                    </div>
                </Transition>
            </div>  
        </div>
        <div v-else class="min-h-screen min-w-screen">
            <div class="grid justify-items-center gap-4 place-content-center min-h-screen min-w-screen">
                <img src="/logo-white.png"></img>
                <p class="text-xl w-64 text-center">Instituto Veracruzano de Educación para los Adultos</p>
                <div v-if="haveError" class="bg-gray-600 p-6 text-center rounded-2xl">
                    <h1 class="text-2xl font-bold py-5">¡Upss!</h1>
                    <p>Parece que tuvimos problemas al cargar el siguiente contenido:</p>
                    <div class="p-4 flex gap-2">
                        <h2 class="w-1/3 text-4xl font-bold p-3">
                            <p class="text-xs">ID del Archivo</p>
                            <p>{{ banner.event.file.id }}</p>

                        </h2>
                        <div class="flex-1 border-l px-5 text-center">
                            <div class="text-xl text-amber-500">
                                <p><FontAwesomeIcon :icon="'fa fa-'+banner.event.file.type" class="self-center text-2xl" /></p>
                                <p class="text-bold">{{ banner.event.file.name }}</p>
                            </div>
                            <p class="text-lg">Url: <b>{{ banner.event.file.url }}</b></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-white w-full text-center fixed bottom-10 text-white/40">
                <div v-if="config.standby" class="pb-16">
                    <FontAwesomeIcon icon="fa fa-moon" class="text-4xl text-yellow-200/50" />
                    <p>Standby</p>
                </div>
                    <a href="https://github.com/cesariux23" target="_blank" class="flex gap-2 place-content-center" v-if="!haveError">
                        <FontAwesomeIcon icon="fa fa-code" class="self-center text-md font-bold text-white" />
                        <span class="font-bold">Developed by :</span>
                        <span>Departamento de Tecnologías de la Información</span>
                    </a>
            </div>
        </div>
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
