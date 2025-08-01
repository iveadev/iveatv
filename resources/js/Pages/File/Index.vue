<script setup>
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import FileForm from './FileForm.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { dateFormat } from '@/utils';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { useToasterStore } from '@/stores/notify';

const props = defineProps({
    files:{
        type: Array
    }
});

// notificaciones
const notifyStore = useToasterStore();


const ModalIsShowing = ref(false);
const ModalDeleteIsShowing = ref(false);
const FileToDelete = ref({});
const emptyFile = {
    id: null,
    name: '',
    filename: '',
    type:'IMAGEN',
    file:'',
    avalible: true,
}

const file = useForm(emptyFile)

const OpenModal= (evt, f = null) => {
    const _file = f ? {...f} : {...emptyFile}
    file.defaults(_file);
    file.reset();
    file.clearErrors();
    ModalIsShowing.value = true
}
const closeModal = () => {
    ModalIsShowing.value = false
    file.defaults({...emptyFile});
    file.reset();
}

const openDeleteModal = (f) => {
    ModalDeleteIsShowing.value = true
    FileToDelete.value = {...f}
}

const closeDeleteModal = () => {
    FileToDelete.value = {}
    ModalDeleteIsShowing.value = false
}
const deleteFile = () => {
    file.id = FileToDelete.value.id;
    file.delete(route('file.destroy',file.id), {
        onSuccess:()=>{
            file.reset();
            closeDeleteModal();
            notifyStore.notify('Se eliminó el archivo correctamente.')
            
        }
    })
    
}

const saveFile = () => {
    if(file.id){
        file.post(route('file.update',file.id), {
            _method: 'put',
            onError:(errors) =>{
                file.errors = errors
            },
            onSuccess:() =>{
                closeModal();
                notifyStore.notify('Se actualizó el archivo correctamente.')
            }
        })

    } else {
        file.post(route('file.store'), {
            onError:(errors) =>{
                console.log(errors)
                file.errors = errors
            },
            onSuccess:() =>{
                closeModal();
                notifyStore.notify('Se registró el archivo correctamente.')
            }
        })
    }
}

</script>
<template>
    <Head title="Archivos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex">
                <h2
                class="flex-1 text-xl font-semibold leading-tight text-gray-800"
            >
                Administrador de archivos
            </h2>
            <SecondaryButton @click ="OpenModal($event)">
                Nuevo archivo
            </SecondaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-lg sm:rounded-lg"
                >
                    <table class="w-full text-center">
                        <thead class="bg-gray-600 text-white">
                            <tr class="h-10">
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Filename</th>
                                <th>Tipo</th>
                                <th>Disponible</th>
                                <th>Fecha de actualización</th>
                                <th class="w-48">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="f in files" class="border">
                                <td>{{ f.id }}</td>
                                <td>{{ f.name }}</td>
                                <td>{{ f.filename }}</td>
                                <td>
                                    <div class="flex gap-3">
                                        <FontAwesomeIcon :icon="'fa fa-'+ (f.type == 'VIDEO' ? 'video':'image')" class="self-center text-gray-500" />
                                        {{ f.type }}
                                    </div>
                                    
                                </td>
                                <td>{{ f.avalible ? 'Sí' : 'No' }}</td>
                                <td>{{ dateFormat(f.updated_at) }}</td>
                                <td>
                                    <div class="flex gap-2 justify-center">
                                        <SecondaryButton @click="OpenModal($event,f)">
                                            <FontAwesomeIcon icon="fa-pencil" class="text-lg" />
                                        </SecondaryButton>
                                        <SecondaryButton @click="openDeleteModal(f)">
                                            <span class="text-red-500">
                                                <FontAwesomeIcon icon="fa-trash" class="text-lg" />
                                            </span>
                                        </SecondaryButton>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="files.length == 0">
                                <td colspan="7">
                                    <div class="py-6 text-xl text-gray-500">
                                        Sin archivos
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <Modal :show="ModalIsShowing" @close="closeModal">
            <template #header>
                {{file.id ? 'Editar' : 'Nuevo'}} Archivo
            </template>
            <div class="p-5">
                <FileForm v-model="file"></FileForm>
                <progress v-if="file.progress" :value="file.progress.percentage" max="100" class="w-full">
                    {{ file.progress.percentage }}%
                </progress>
            </div>
            <template #actions>
                <PrimaryButton @click="saveFile" :disabled="!file.isDirty" class="disabled:bg-gray-300">
                    Guardar
                </PrimaryButton>
                <SecondaryButton @click="closeModal">
                    Cancelar
                </SecondaryButton>
            </template>
        </Modal>
        <Modal :show="ModalDeleteIsShowing" closeable type="danger" @close="closeDeleteModal">
            <template #header>
                Borrando archivo
            </template>
            <div class="text-center py-6">
                <p class="font-bold">¿Deseas continuar?</p>
                <p>Se eliminará el archivo y los eventos asociados</p>
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