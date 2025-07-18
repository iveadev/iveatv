<script setup>
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { dateFormat } from '@/utils';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import EventForm from './Partials/EventForm.vue';

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
const emptyevent = {
    id: null,
    title:'',
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
    if(event.id){
        event.put(route('event.update', event.id),{
            onSuccess:() => {
                closeeventModal()
            }
        })
    } else {
        event.post(route('event.store'),{
            onSuccess:() => {
                closeeventModal()
            }
        })
    }
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
                                <th>Título</th>
                                <th>Archivo</th>
                                <th>Duración (seg.)</th>
                                <th>Visible</th>
                                <th>Vigencia</th>
                                <th class="w-1/5">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(event, index) in events" class="h-10 border">
                                <td>{{ event.id }}</td>
                                <td>{{ event.title }}</td>
                                <td>
                                    <p class="font-bold py-2">{{ event.file.name }}</p>
                                    <p>{{ event.file.type }}</p>
                                    <p class="text-sm text-gray-600">({{ event.file.url }})</p>
                                </td>
                                <td>{{ event.duration }}</td>
                                <td>{{ event.visible ? 'Sí' : 'No' }}</td>
                                <td>
                                    <p><span class="font-bold">De:</span> {{ dateFormat(event.visibleFrom) }}</p>
                                    <p><span class="font-bold">A  :</span> {{ dateFormat(event.visibleTo) }}</p>
                                </td>
                                <td>
                                    <div class="flex gap-2 justify-center">
                                        <SecondaryButton @click="openeventModal($event, event)">
                                            Editar
                                        </SecondaryButton>
                                        <SecondaryButton>
                                            <span class="text-red-500">Eliminar</span>
                                        </SecondaryButton>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="events.length == 0">
                                <td colspan="8">
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
