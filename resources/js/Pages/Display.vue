<script setup>
import { faGithub } from '@fortawesome/free-brands-svg-icons';
import { faCode, faMoon, faRotateRight, faChevronRight, faCalendar, faCalendarDay, faCalendarDays, faCalendarAlt, faSpinner, faCircleNotch } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { Head, router } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';

const props = defineProps({
    config: {
        type: Object,
    },
    toShow: {
        type: Object,
    }
})
const loading = ref(false)
const haveError = ref(false)
let activeTimeout = null
const banner = ref(null)
const waiting = ref(5000) // 5 segundos
const config = ref({})


const currentDate = ref('')
const currentTime = ref('')
let timer = null

const updateDateTime = () => {
  const now = new Date()

  // Opciones para formatear la fecha: "Hoy es viernes 4 de septiembre de 2026"
  const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }
  const dateText = now.toLocaleDateString('es-MX', dateOptions)

  const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true }
  const timeText = now.toLocaleTimeString('es-MX', timeOptions).toLowerCase()

  // Unimos ambos textos con la estructura deseada
  currentDate.value = dateText
  const timeElements = timeText.split(':')
  currentTime.value =  [timeElements[0],timeElements[1], ...timeElements[2].split(' ')]
}

onMounted(() => {
    //config.value = props.config
    updateDateTime()
    clearAllTimers()
    timer = setInterval(updateDateTime, 1000)
})

const nextbanner = ref(null)

const goToNext = () => {
    // avanza inmediatamente al siguiente contenido
    seconds.value = 0
    showNext()
}

const seconds = ref(5)

const showNext = () => {
    clearAllTimers();
    const duration = seconds.value * 1000
    loading.value = false
    activeTimeout = setTimeout(()=>{
        banner.value = null
        activeTimeout = setTimeout(() => {
            loading.value = true
            nextbanner.value = null
        },300);
        activeTimeout = setTimeout(() => {
            getNextContent();
        },waiting.value);
    }, duration)

}

const loadBanner = ()=> {
    //loading.value = false
    const _b = props.toShow
    if(config.standby){
        nextbanner.value = null
    }
    banner.value = _b
    seconds.value = _b.duration
}

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
    clearAllTimers();
    // genera los parametros para la petición del siguiente contenido
    const params = {
        id: nextbanner.value,
        times: config.value.times
    }
    if(config.value.date){
        params.date = config.value.date
    }

    router.get(
        route('banner.display'),
        params,
        {
            preserveState:true,
            preserveScroll:true,
            onSuccess: () => {
                loading.value = false
            }
        },
    );
}

const title = computed(()=>{
    return banner.value ? banner.value.event.file.name : 'Cargando...'
})

const updateConfig = () => {
    fetch(route('banner.getConfig'))
        .then(response => response.json())
        .then(data => {
            if(
                config.value.standby_mode != data.standby_mode ||
                config.value.event_count != data.event_count
            ){
                console.log('cambia!')
                window.location.reload();
            }

        })
}


const getNextContent = () => {
    fetch(route('banner.next', { order: props.toShow.order, date: config.value.date, id: props.toShow.id, times: config.value.times }))
        .then(response => response.json())
        .then(data => {
            nextbanner.value = data.next ? data.next.id : null;
            //config.value = data.config;
            loading.value = false
            activeTimeout = setTimeout(() => {
                loadNext();
            },300);
        })
        .catch(error => {
            console.error('Error fetching next banner:', error);
        });
}

const clearAllTimers = () => {
    if (activeTimeout) {
        clearTimeout(activeTimeout);
        activeTimeout = null;
    }
};

const showDate = computed(()=>{
    return props.toShow.order == 0 && !loading.value && !nextbanner.value && !config.value.standby_mode
})



// cambios en el elemento a mostrar
watch(
    () => props.toShow,
    () => {
        loadBanner();
    },
    { immediate: true }
);

watch(
    () => props.config,
    (newVal) => {
        config.value = newVal
        if(config.value.standby_mode){
            // se busca cada cierto tiempo cambios de estado
            console.log('Standby')
            clearAllTimers();
            setInterval(updateConfig, 300000) // 5 minutos
        }
    },
    { immediate: true }
);


</script>

<template>
    <Head :title="title" />
    <main class="bg-black text-white overflow-hidden min-h-full">
        <div class="fixed top-5 right-5 group w-56 grid grid-cols-2 justify-items-end">
            <button type="button" class="font-bold rounded-xl transparent text-transparent group-hover:bg-black/15 group-hover:text-white/20" @click="reloadPage">
                <div class="hover:text-white p-6 hover:bg-blue-500/40 rounded-xl">
                    <div class="text-7xl">
                        <FontAwesomeIcon :icon="faRotateRight" />
                    </div>
                </div>
            </button>
            <button type="button" class="font-bold rounded-xl transparent text-transparent group-hover:bg-black/15 group-hover:text-white/20" @click="getNextContent">
                <div class="hover:text-white p-6 hover:bg-green-500/40 rounded-xl">
                    <div class="text-7xl">
                        <FontAwesomeIcon :icon="faChevronRight" />
                    </div>
                </div>
            </button>
        </div>
        <div class="min-h-screen min-w-screen overflow-hidden" v-if="!haveError && !config.empty && !config.standby_mode">
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
                <p class="text-xl w-64 text-center font-bold">Instituto Veracruzano de Educación para los Adultos</p>
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

            <div class="text-white w-full text-center fixed bottom-0 text-white">
                <div v-if="config.standby_mode" class="pb-16">
                    <FontAwesomeIcon :icon="faMoon" class="text-4xl text-yellow-500" />
                    <div class="font-bold">Standby</div>
                    <div class="text-white/50 py-3">
                        {{ currentTime[0] }}:{{ currentTime[1] }} {{ currentTime[3] }}
                    </div>
                </div>
            </div>
        </div>
        <!-- loading -->
        <Transition name="fade">
            <div v-if="loading && !config.empty" class="fixed top-0 left-0 w-full h-full bg-black/50  flex justify-center items-center flex flex-col gap-2">
                <img src="/logo-white.png" class="self-center"></img>
                <div class="text-4xl font-bold pt-5 flex gap-1">
                    <div>
                        {{ currentTime[0] }}
                    </div>
                    <span>:</span>
                    <div>
                        {{ currentTime[1] }}
                    </div>
                    <span>:</span>
                    <div class="w-12 p-auto" :class="{'text-yellow-500': currentTime[2]%2 ==0}">
                        {{ currentTime[2] }}
                    </div>
                    <div>
                        {{ currentTime[3] }}
                    </div>
                </div>
            </div>
        </Transition>

        <Transition name="slide-up">
            <div v-if="loading && config.show_about" class="text-white w-full text-center fixed bottom-0 text-white">
                <a class="text-sm flex flex-col place-content-center" href="https://github.com/cesariux23" target="_blank">
                    <span class="flex gap-2 place-content-center font-boldtext-rose-800">
                        <FontAwesomeIcon :icon="faCode" class="self-center text-md" />
                        <span>Developed by:</span>
                    </span>
                    <div class="py-4 flex flex-col bg-gray-600/40">
                        <span class="text-xl font-bold">Instituto Veracruzano de Educación para los Adultos</span>
                        <span class="font-bold">Departamento de Tecnologías de la Información</span>
                        <span class="flex gap-2 place-content-center text-xs text-white/50">
                            <FontAwesomeIcon :icon="faGithub" class="self-center " />
                            cesariux23
                        </span>
                    </div>
                </a>
            </div>
        </Transition>


        <!-- Banner de fecha -->
         <Transition name="slide-up">
            <div
            class="fixed bottom-0 w-full px-4 py-2 flex bg-rose-900 text-xl border-t-4 border-yellow-600"
            v-if ="showDate"
            >
                <p class="font-bold flex-1 text-center">Hoy es {{ currentDate }}.</p>
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

    .fade-enter-to,
    .fade-leave-from {
        opacity: 1;
    }


    .pulse-enter-active,
    .pulse-leave-active {
      transition: transform 0.5s ease;
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
      50% { transform: scale(0.9); }
      100% { transform: scale(1); }

    }

    .pulsing-element {
        animation: continuous-pulse 2s infinite; /* Apply to an element for continuous pulse */
    }

    .slide-up-enter-active,
    .slide-up-leave-active {
    transition: all 0.4s ease-out;
    }

    /* 2. Define starting state for enter & ending state for leave */
    .slide-up-enter-from,
    .slide-up-leave-to {
    transform: translateY(100%);
    opacity: 0;
    }
</style>
