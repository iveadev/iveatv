<script setup>
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { dateFormat } from '@/utils';
import { useToasterStore } from '@/stores/notify';

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

// notificaciones
const notifyStore = useToasterStore();

const programationModalIsShowing = ref(false)
const ModalDeleteIsShowing = ref(false)
const enableDrag =ref(true)
const to = ref(null)

const emptyProgramation = {
    id: null,
    event_id:'',
    duration:'10',
    visible: false,
    sound: false,
}

const programation = useForm(emptyProgramation)

const openProgramationModal = (evt, _prog)=>{
    programation.defaults({...emptyProgramation})
    if(_prog){
        programation.defaults({..._prog})
    }
    programation.reset()
    programationModalIsShowing.value = true
}

const closeProgramationModal = () => {
    programationModalIsShowing.value = false
    programation.defaults({...emptyProgramation})
    programation.reset()
}

const openDeleteModal = (_prog) => {
    programation.defaults({..._prog})
    ModalDeleteIsShowing.value = true
    programation.reset()
}

const deleteProg = () => {
    programation.delete(route('programation.destroy', programation.id),{
        onSuccess:() => {
            closeDeleteModal()
            notifyStore.notify('Se eliminó correctamente.')
        }
    })
}

const closeDeleteModal = () =>{
    programation.defaults({...emptyProgramation})
    programation.reset()
    ModalDeleteIsShowing.value = false
}

const saveProgramation = () => {
    programation.put(route('programation.update', programation.id),{
        onSuccess:() => {
            closeProgramationModal()
            notifyStore.notify('Se actualizó correctamente.')
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

// drag&drop section


const moveItem = (arr, from, to) => {
    arr.splice(to, 0, arr.splice(from, 1)[0]);
}

const getTo = (evt) => {
    let tr= evt.target.closest('tr')
    to.value = tr.dataset.index
}

const reOrder = (evt) => {
    let tr = evt.target.closest('tr')
    let from = tr.dataset.index
    if( to.value !=null && to.value!=from){
        moveItem(props.banners, from, to.value)
        // hace permanentes los cambios
        const newOrder = []
        props.banners.forEach((b,idx)=>{
            newOrder.push(b.id)
        })
        router.put(route('programation.reorder'), {newOrder},{
            onSuccess: () => {
                notifyStore.notify('Se actualizó el orden de presentación.', 'info')
            }
        })
    }

    to.value = null
    enableDrag.value = false
}

const toggleProp = (obj,prop) =>{
    const toSend = {}
    toSend[prop] = !obj[prop]
    router.put(route('programation.update',obj.id),toSend,{
        onSuccess:() => {
            notifyStore.notify('Item '+obj.id+' actualizado.', 'info')
        }
    })
}

</script>

<template>
    <Head title="Programación" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex">
                <h2
                class="flex-1 text-xl font-semibold leading-tight text-red-900"
            >
                Programación del día
            </h2>
            <!-- <SecondaryButton @click="enableDrag = !enableDrag">
                Cambiar orden
            </SecondaryButton> -->
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
                        <b class="text-amber-600 text-center">
                            <TextInput v-model="pickedDate" type="date" @change="pickDate"/>
                        </b>
                    </button>
                    <div class="text-right">
                        <SecondaryButton @click="goToDay(nextDay)">
                            <div class="flex gap-2">
                                Siguiente
                                <FontAwesomeIcon icon="fa fa-chevron-right" class="self-center" />
                            </div>
                        </SecondaryButton>
                    </div>
                </div>

                <div
                    class="overflow-hidden bg-white shadow-lg sm:rounded-lg"
                >
                    <table class="w-full text-center">
                        <thead class="bg-gray-600 text-white">
                            <tr class="h-10">
                                <th>ID</th>
                                <th title="Orden de presentación">Orden</th>
                                <th>Archivo</th>
                                <th title="Duración (en segundos)">
                                    <FontAwesomeIcon icon="fa-clock" />
                                </th>
                                <th title="En proyección">
                                    <FontAwesomeIcon icon="fa-display" />
                                </th>
                                <th title="Reproducir sonido">
                                    <FontAwesomeIcon icon="fa fa-volume-high" />
                                </th>
                                <th class="w-1/5">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr 
                                v-for="(banner, index) in banners"
                                class="h-10 border [&.is-dragging]:cursor-grabbing"
                                :key="index"
                                :class="{'border-2 border-amber-600': index == to}"
                                :draggable="enableDrag"
                                @dragend.stop="reOrder"
                                @dragleave.stop="getTo"
                                :data-index="index"
                            >
                                <td class="text-gray-600 font-bold">{{ banner.id }}</td>
                                <td class="font-bold text-amber-600 text-lg">{{ index +1 }}</td>
                                <td :title="banner.event.file.type">
                                    <div class="py-2 flex flex-col text-left">
                                        <span class="font-bold">
                                            {{ banner.event.file.name }}
                                        </span>
                                        <span class="flex gap-2">
                                            <FontAwesomeIcon :icon="'fa fa-'+banner.event.file.type" :class="'self-center text-'+banner.event.file.color+'-500'" />
                                            <span class="text-xs text-gray-500">{{ banner.event.file.filename }}</span>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div v-if="banner.event.file.type =='video'" class="text-gray-400 text-xs">
                                        N/A
                                    </div>
                                    <div v-else>
                                        <b>{{ banner.duration }}</b> <span class="text-gray-500">s.</span>
                                    </div>
                                </td>
                                <td>
                                    <FontAwesomeIcon icon="fa fa-check" :class="{'text-gray-200':!banner.visible, 'text-green-600 font-bold':banner.visible}"/>
                                </td>
                                <td :class="{'text-gray-400':!banner.sound, 'font-bold':banner.sound}">
                                    <FontAwesomeIcon icon="fa fa-check" :class="{'text-gray-200':!banner.sound, 'text-green-600 font-bold':banner.sound}"/>
                                </td>
                                <td>
                                    <div class="flex gap-1 justify-items-center group">
                                        <SecondaryButton @click="toggleProp(banner, 'visible')">
                                            <FontAwesomeIcon :icon="'fa fa-'+(banner.visible ? 'stop':'play')" class="text-xl" :class="{'text-gray-400':!banner.visible}"/>
                                        </SecondaryButton>
                                        <SecondaryButton @click="toggleProp(banner, 'sound')" :disabled="!banner.visible || banner.event.file.type !='video'">
                                            <FontAwesomeIcon :icon="'fa fa-'+(banner.sound ? 'volume-xmark':'volume-low')" class="text-xl" :class="{'text-gray-400':!banner.sound, 'text-orange-600':banner.sound}"/>
                                        </SecondaryButton>
                                        <SecondaryButton @click="openProgramationModal($event, banner)">
                                            <FontAwesomeIcon icon="fa-pencil" class="text-lg text-sky-500" />
                                        </SecondaryButton>
                                        <SecondaryButton>
                                            <span class="text-red-500" @click="openDeleteModal(banner)">
                                                <FontAwesomeIcon icon="fa-trash" class="text-lg" />
                                            </span>
                                        </SecondaryButton>
                                        <div class="flex-1 text-right" @mouseenter="enableDrag = true" @mouseleave="enableDrag = false">
                                            <span title="Mover" class="p-2 opacity-0 group-hover:opacity-100 text-amber-600">
                                                <FontAwesomeIcon icon="fa fa-hand" class="self-center" />
                                            </span>
                                        </div>
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

        <Modal :show="programationModalIsShowing" closeable @close="closeProgramationModal" max-width="lg" type="notify">
            <template #header>
                {{ dateFormat(today) }}
            </template>
            <div class="py-3 bg-gray-300 flex gap-2">
                <div class="w-20 text-right">Archivo:</div>
                <FontAwesomeIcon :icon="'fa fa-'+ (programation.event.file.type == 'video' ? 'video':'image')" class="self-center" />
                <div class="font-bold">{{ programation.event.file.name }}</div>
            </div>
            <div class="p-5">
                <div class="flex gap-4">
                    <div class="flex-1 flex flex-col gap-1" v-if="programation.event.file.type=='image'">
                        <InputLabel value="Duración" />
                        <div class="flex gap-1">
                            <TextInput v-model="programation.duration" type="number" step="5" min="0" class="w-5/6"/>
                            <span class="self-center">s.</span>
                        </div>
                        <InputError :message="programation.errors.duration" />
                    </div>
                    <div class="w-20 flex-col gap-1">
                        <InputLabel value="Proyectar"/>
                        <div class="flex gap-4 pt-3">
                            <Checkbox v-model:checked="programation.visible" class="self-center" />
                            <span class="self-center">{{ programation.visible ? 'Sí' : 'No' }}</span>
                        </div>
                    </div>
                    <div class="w-20 flex-col gap-1">
                        <InputLabel value="Sonido"/>
                        <div class="flex gap-4 pt-3">
                            <Checkbox v-model:checked="programation.sound" class="self-center" />
                            <span class="self-center">{{ programation.sound ? 'Sí' : 'No' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <template #actions>
                <PrimaryButton :disabled="!programation.isDirty" class="disabled:bg-gray-300" @click="saveProgramation">
                    Guardar
                </PrimaryButton>
                <SecondaryButton @click="closeProgramationModal">
                    Cancelar
                </SecondaryButton>
            </template>
        </Modal>

        <Modal :show="ModalDeleteIsShowing" closeable type="danger" @close="closeDeleteModal">
            <template #header>
                Borrando programación
            </template>
            <div class="text-center py-6">
                <p class="font-bold">¿Deseas continuar?</p>
                <p>Este proceso no es reversible.</p>
            </div>
            <template #actions>
                <PrimaryButton @click="deleteProg">
                    Eliminar
                </PrimaryButton>
                <SecondaryButton @click="closeDeleteModal">
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
