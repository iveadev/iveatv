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

const programationModalIsShowing = ref(false)
const emptyProgramation = {
    id: null,
    event_id:'',
    duration:'10',
    visible: false
}

const programation = useForm(emptyProgramation)

const openProgramationModal = (evt, _event)=>{
    programation.defaults({...emptyProgramation})
    if(_event){
        programation.defaults({..._event})
    }
    programation.reset()
    programationModalIsShowing.value = true
}

const closeeventModal = () => {
    programationModalIsShowing.value = false
    programation.defaults({...emptyProgramation})
    programation.reset()
}

const saveProgramation = () => {
    programation.post(route('programation.store'),{
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
                    class="overflow-hidden bg-white shadow-lg sm:rounded-lg"
                >
                    <table class="w-full text-center">
                        <thead class="bg-gray-600 text-white">
                            <tr class="h-10">
                                <th>Orden</th>
                                <th>Archivo</th>
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
                                <td class="font-bold">
                                    <div class="flex gap-3">
                                        <FontAwesomeIcon :icon="'fa fa-'+ (banner.event.file.type == 'VIDEO' ? 'video':'image')" class="self-center text-gray-500" />
                                        {{ banner.event.file.type }}
                                    </div>
                                    {{ banner.event.file.name }}
                                    
                                </td>
                                <td>{{ banner.duration }} s.</td>
                                <td>{{ banner.visible ? 'Sí' : 'No' }}</td>
                                <td>
                                    <div class="flex gap-2 justify-center">
                                        <SecondaryButton @click="openProgramationModal($event, banner)">
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

        <Modal :show="programationModalIsShowing" closeable @close="closeeventModal" max-width="md">
            <template #header>
                {{ dateFormat(today) }}
            </template>
            <div class="py-3 bg-amber-200 text-amber-800 flex gap-2">
                <div class="w-20 text-right">Archivo:</div>
                <FontAwesomeIcon :icon="'fa fa-'+ (programation.event.file.type == 'VIDEO' ? 'video':'image')" class="self-center" />
                <div class="font-bold">{{ programation.event.file.name }}</div>
            </div>
            <div class="p-5">
                <div class="flex gap-4">
                    <div class="flex-1 flex flex-col gap-1">
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
                </div>
            </div>
            <template #actions>
                <PrimaryButton :disabled="!programation.isDirty" class="disabled:bg-gray-300" @click="saveProgramation">
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
