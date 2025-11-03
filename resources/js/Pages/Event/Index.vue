<script setup>
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { dateFormat } from '@/utils';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import EventForm from './Partials/EventForm.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { useToasterStore } from '@/stores/notify';

const props = defineProps({
    events:{
        type:Array,
        default: []
    },
    today:{
        type:String,
    }
})

// notificaciones
const notifyStore = useToasterStore();

const eventModalIsShowing = ref(false)
const ModalDeleteIsShowing = ref(false);

const emptyevent = {
    id: null,
    title:'',
    file_id:'',
    duration:'10',
    sound: false,
    visible: true,
    visibleFrom: props.today,
    visibleTo: props.today,
}

const event = useForm(emptyevent)
const EventToDelete = ref({})

const openeventModal = (evt, _event)=>{
    event.defaults({...emptyevent})
    if(_event){
        event.defaults({..._event})
    }
    event.reset()
    eventModalIsShowing.value = true
}

const closeEventModal = () => {
    eventModalIsShowing.value = false
    event.defaults({...emptyevent})
    event.reset()
}

const saveEvent = () => {
    if(event.id){
        event.put(route('event.update', event.id),{
            onSuccess:() => {
                closeEventModal()
                notifyStore.notify('Se actualizó el evento correctamente.')
            }
        })
    } else {
        event.post(route('event.store'),{
            onError:(e) => {
                event.errors = e;
            },
            onSuccess:() => {
                closeEventModal()
                notifyStore.notify('Se registró el evento correctamente.')
            }
        })
    }
}

const openDeleteModal = (f) => {
    ModalDeleteIsShowing.value = true
    EventToDelete.value = {...f}
}

const closeDeleteModal = () => {
    EventToDelete.value = {}
    ModalDeleteIsShowing.value = false
}
const deleteEvent = () => {
    event.reset();
    event.id = EventToDelete.value.id;
    event.delete(route('event.destroy',event.id), {
        onSuccess:()=>{
            event.reset();
            closeDeleteModal();
            notifyStore.notify('Se eliminó el evento correctamente.')
        }
    })
}

const toggleProp = (obj,prop) =>{
    const toSend = {}
    toSend[prop] = !obj[prop]
    router.put(route('event.update',obj.id),toSend,{
        onSuccess:() => {
            notifyStore.notify('Evento '+obj.id+' actualizado.', 'info')
        }
    })
}


//filtrado
const filter = ref('')
const _events = computed(()=>{
    const _f = filter.value.trim()
    if(_f.length>0){
        return props.events.filter(e=>e.file.name.search(new RegExp(_f,'i')) >=0)
    }
    return props.events
})

</script>

<template>
    <Head title="Eventos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex">
                <h2
                class="flex-1 text-xl font-semibold leading-tight text-red-900"
            >
                Adminsitración de eventos
            </h2>
            <SecondaryButton @click ="openeventModal($event)">
                Nuevo evento
            </SecondaryButton>
            </div>
        </template>

        <div class=" py-2">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="py-2 flex gap-1">
                    <input type="text" placeholder="Filtrar..." class="flex-1 placeholder:text-gray-300 border-gray-300 rounded" v-model="filter">
                    <button class="px-4 text-white rounded bg-red-500" @click="filter=''" v-if="filter.length>0" title="Limpiar">
                        <FontAwesomeIcon icon="fa fa-times"/>
                    </button>
                </div>
                <div
                    class="overflow-hidden bg-white shadow-lg sm:rounded-lg"
                >
                    <table class="w-full text-center">
                        <thead class="bg-gray-600 text-white">
                            <tr class="h-10">
                                <th>ID</th>
                                <th class="text-left">Archivo a reproducir</th>
                                <th class="text-left">Visibilidad</th>
                                <th title="Duración (en segundos)">
                                    <FontAwesomeIcon icon="fa fa-clock" />
                                </th>
                                <th title="En proyección">
                                    <FontAwesomeIcon icon="fa fa-display" class="self-center"/>
                                </th>
                                <th title="Reproducir sonido">
                                    <FontAwesomeIcon icon="fa fa-volume-high" />
                                </th>
                                <th class="w-48">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(event) in _events" class="h-10 border" :class="{'border-b-orange-300':!event.isCurrent}">
                                <td class="font-bold text-gray-600">{{ event.id }}</td>
                                <td :title="event.file.type">
                                    <div class="py-2 flex flex-col text-left">
                                        <span class="font-bold">
                                            {{ event.file.name }}
                                        </span>
                                        <span class="flex gap-2 text-gray-600">
                                            <FontAwesomeIcon :icon="'fa fa-'+event.file.type" class="self-center" />
                                            <span class="text-xs">{{ event.file.filename }}</span>
                                        </span>
                                        <span class="text-white font-bold text-xs" v-if="!event.isCurrent">
                                            <span class="bg-orange-500 px-1 rounded uppercase">Período no vigente</span>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex flex-col">
                                        <div class="flex gap-2">
                                            <span class="min-w-16 text-gray-500">Desde</span>
                                            <span>{{ dateFormat(event.visibleFrom) }}</span>
                                        </div>
                                        <div class="flex gap-2">
                                            <span class="min-w-16 text-gray-500">Hasta</span>
                                            <span>{{ dateFormat(event.visibleTo) }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div v-if="event.file.type =='video'" class="text-gray-400 text-xs">
                                        N/A
                                    </div>
                                    <div v-else>
                                        <b>{{ event.duration }}</b> <span class="text-gray-500">s.</span>
                                    </div>
                                </td>
                                <td>
                                    <FontAwesomeIcon icon="fa fa-check" :class="{'text-gray-400':!event.visible, 'text-green-600 font-bold':event.visible && event.isCurrent}"/>
                                </td>
                                <td :class="{'text-gray-400':!event.sound, 'font-bold':event.sound}">
                                    <FontAwesomeIcon icon="fa fa-check" :class="{'text-gray-400':!event.sound, 'text-green-600 font-bold':event.sound}"/>
                                </td>
                                <td>
                                    <div class="flex gap-2 justify-center">
                                        <SecondaryButton @click="toggleProp(event, 'visible')">
                                            <FontAwesomeIcon :icon="'fa fa-'+(event.visible ? 'stop':'play')" class="text-xl" :class="{'text-emerald-600':!event.visible}"/>
                                        </SecondaryButton>
                                        <SecondaryButton @click="toggleProp(event, 'sound')" :disabled="!event.visible || event.file.type !='video'">
                                            <FontAwesomeIcon :icon="'fa fa-'+(event.sound ? 'volume-xmark':'volume-low')" class="text-xl" :class="{'text-gray-400':!event.sound, 'text-orange-600':event.sound}"/>
                                        </SecondaryButton>
                                        <SecondaryButton @click="openeventModal($event, event)" title="Editar evento">
                                            <FontAwesomeIcon icon="fa fa-pencil" class="text-lg text-sky-500"/>
                                        </SecondaryButton>
                                        <SecondaryButton title="Eliminar" @click="openDeleteModal(event)">
                                            <span class="text-red-500 text-lg">
                                                <FontAwesomeIcon icon="fa fa-trash" />
                                            </span>
                                        </SecondaryButton>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="_events.length == 0">
                                <td colspan="7">
                                    <div class="py-6 text-xl text-gray-500">
                                        Sin eventos para mostrar
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <Modal :show="eventModalIsShowing" closeable @close="closeEventModal" max-width="4xl">
            <template #header>
                event
            </template>
            <div class="p-6">
                <EventForm v-model="event"></EventForm>
            </div>
            <template #actions>
                <PrimaryButton :disabled="!event.isDirty" class="disabled:bg-gray-300" @click="saveEvent">
                    Guardar
                </PrimaryButton>
                <SecondaryButton @click="closeEventModal">
                    Cancelar
                </SecondaryButton>
            </template>

        </Modal>
    </AuthenticatedLayout>
    <Modal :show="ModalDeleteIsShowing" closeable type="danger" @close="closeDeleteModal">
            <template #header>
                Borrando archivo
            </template>
            <div class="text-center py-6">
                <p class="font-bold">¿Deseas continuar?</p>
                <p>Se eliminará el evento y la programación asociada.</p>
            </div>
            <template #actions>
                <PrimaryButton @click="deleteEvent">
                    Eliminar
                </PrimaryButton>
                <SecondaryButton @click="closeDeleteModal">
                    Cancelar
                </SecondaryButton>
            </template>
        </Modal>
</template>

<style scoped>
th{
    padding: 10px;
}
td{
    padding: 10px;
}
</style>
