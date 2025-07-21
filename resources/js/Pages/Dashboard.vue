<script setup>
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { dateFormat } from '@/utils';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import EventForm from './Profile/Partials/EventForm.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    banners:{
        type:Array,
        default: []
    },
    today:{
        type:String,
    },
    previousDay:{
        type:String,
    },
    nextDay:{
        type:String,
    }
})

const eventModalIsShowing = ref(false)
const emptyevent = {
    id: null,
    file_id:'',
    duration:'10',
    visible: false,
    visibleFrom: props.today+'T00:00',
    visibleTo: props.today+'T23:59',
}

const event = useForm(emptyevent)

const openeventModal = (evt, _event)=>{
    event.defaults({...emptyevent})
    if(_event){
        console.log('hay')
        event.defaults({..._event})
    }
    event.reset()
    eventModalIsShowing.value = true
}

const closeeventModal = () => {
    eventModalIsShowing.value = false
    event.defaults({...emptyevent})
    event.reset()
}

const saveevent = () => {
    event.post(route('event.store'),{
        onSuccess:() => {
            closeeventModal()
        }
    })
}

const pickedDate =ref(props.today)

const goToDay = (day) => {
    router.visit(route(route().current(),{date: day}))
}

const pickDate = () => {
    goToDay(pickedDate.value)
}

</script>

<template>
    <Head title="Programación" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex">
                <h2
                class="flex-1 text-xl font-semibold leading-tight text-gray-800"
            >
                Programación del día
            </h2>
            </div>
        </template>

        <div class="">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

                <div class="grid grid-cols-3 gap-5 py-4">
                    <div>
                        <SecondaryButton @click="goToDay(previousDay)">
                            <div class="flex gap-2">
                                <FontAwesomeIcon icon="fa fa-chevron-left" class="self-center" />
                                Anterior
                            </div>
                        </SecondaryButton>
                    </div>
                    <button type="button" class="px-3 rounded" title="Seleccionar otra fecha">
                        <b class="text-red-800 text-center">
                            <TextInput v-model="pickedDate" type="date" @change="pickDate"/>
                        </b>
                    </button>
                    <div class="grid justify-items-end">
                        <SecondaryButton @click="goToDay(nextDay)">
                            <div class="flex gap-2">
                                Siguiente
                                <FontAwesomeIcon icon="fa fa-chevron-right" class="self-center" />
                            </div>
                        </SecondaryButton>
                    </div>
                </div>

                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <table class="w-full text-center">
                        <thead class="bg-gray-600 text-white">
                            <tr class="h-10">
                                <th>Orden</th>
                                <th>Título</th>
                                <th>Tipo de contenido</th>
                                <th title="Duración (en segundos)">
                                    <FontAwesomeIcon icon="fa-clock" />
                                </th>
                                <th title="En proyección">
                                    <FontAwesomeIcon icon="fa-display" />
                                </th>
                                <th class="w-1/5">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(banner, index) in banners" class="h-10 border">
                                <td>{{ index +1 }}</td>
                                <td class="font-bold">{{ banner.event.file.name }}</td>
                                <td>{{ banner.event.file.type }}</td>
                                <td>{{ banner.event.duration }} s.</td>
                                <td>{{ banner.visible ? 'Sí' : 'No' }}</td>
                                <td>
                                    <div class="flex gap-2 justify-center">
                                        <SecondaryButton @click="openeventModal($event, banner)">
                                            <FontAwesomeIcon icon="fa-pencil" class="text-lg" />
                                        </SecondaryButton>
                                        <SecondaryButton>
                                            <span class="text-red-500">
                                                <FontAwesomeIcon icon="fa-trash" class="text-lg" />
                                            </span>
                                        </SecondaryButton>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="banners.length == 0">
                                <td colspan="10">
                                    <div class="py-6 text-xl text-gray-500">
                                        Sin eventos programados
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <Modal :show="eventModalIsShowing" closeable @close="closeeventModal" max-width="4xl">
            <template #header>
                event
            </template>
            <div class="p-5">
                <EventForm v-model="event"></EventForm>
            </div>
            <template #actions>
                <PrimaryButton :disabled="!event.isDirty" class="disabled:bg-gray-300" @click="saveevent">
                    Guardar
                </PrimaryButton>
                <SecondaryButton @click="closeeventModal">
                    Cancelar
                </SecondaryButton>
            </template>

        </Modal>
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
