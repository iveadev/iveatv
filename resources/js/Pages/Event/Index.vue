<script setup>
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { dateFormat } from '@/utils';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import EventForm from './Partials/EventForm.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

const props = defineProps({
    events:{
        type:Array,
        default: []
    },
    today:{
        type:String,
    }
})

const eventModalIsShowing = ref(false)
const ModalDeleteIsShowing = ref(false);

const emptyevent = {
    id: null,
    title:'',
    file_id:'',
    duration:'10',
    sound: false,
    visible: false,
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
            }
        })
    } else {
        event.post(route('event.store'),{
            onError:(e) => {
                event.errors = e;
            },
            onSuccess:() => {
                closeEventModal()
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
const deleteFile = () => {
    event.reset();
    event.id = EventToDelete.value.id;
    event.delete(route('event.destroy',event.id), {
        onSuccess:()=>{
            event.reset();
            closeDeleteModal();
        }
    })
    
}

</script>

<template>
    <Head title="Eventos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex">
                <h2
                class="flex-1 text-xl font-semibold leading-tight text-gray-800"
            >
                Adminsitración de eventos
            </h2>
            <SecondaryButton @click ="openeventModal($event)">
                Nuevo evento
            </SecondaryButton>
            </div>
        </template>

        <div class="p-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-lg sm:rounded-lg"
                >
                    <table class="w-full text-center">
                        <thead class="bg-gray-600 text-white">
                            <tr class="h-10">
                                <th>ID</th>
                                <th>Archivo</th>
                                <th title="Duración (en segundos)">
                                    <FontAwesomeIcon icon="fa fa-clock" />
                                </th>
                                <th title="En proyección">
                                    <FontAwesomeIcon icon="fa fa-display" class="self-center"/>
                                </th>
                                <th>Desde</th>
                                <th>Hasta</th>
                                <th class="w-48">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(event) in events" class="h-10 border">
                                <td class="font-bold">{{ event.id }}</td>
                                <td :title="event.file.type">
                                    <div class="py-2 flex gap-3">
                                        <FontAwesomeIcon :icon="'fa fa-'+ (event.file.type == 'VIDEO' ? 'video':'image')" class="self-center text-gray-500" />
                                        <span>
                                            {{ event.file.name }}
                                        </span>
                                    </div>
                                </td>
                                <td>{{ event.duration }} s.</td>
                                <td>{{ event.visible ? 'Sí' : 'No' }}</td>
                                <td>{{ dateFormat(event.visibleFrom) }}</td>
                                <td>{{ dateFormat(event.visibleTo) }}</td>
                                <td>
                                    <div class="flex gap-2 justify-center">
                                        <SecondaryButton @click="openeventModal($event, event)" title="Editar evento">
                                            <FontAwesomeIcon icon="fa fa-pencil" class="text-lg"/>
                                        </SecondaryButton>
                                        <SecondaryButton title="Eliminar" @click="openDeleteModal(event)">
                                            <span class="text-red-500 text-lg">
                                                <FontAwesomeIcon icon="fa fa-trash" />
                                            </span>
                                        </SecondaryButton>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="events.length == 0">
                                <td colspan="7">
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
                <PrimaryButton @click="deleteFile">
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
