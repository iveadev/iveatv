<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

const archivo = defineModel();

const checkFile = (evt) => {
    const f = evt.target.files[0]
    if(f){
        archivo.value.filename = f.name.replaceAll(' ', '');
        archivo.value.file = f
        archivo.value.type = f.type.split('/')[0].toLowerCase();
    }
}
</script>
<template>
    <div class="grid gap-3">
        <div class="flex gap-3">
            <div class="flex-1 grid gap-1">
                <InputLabel value="Nombre para mostrar"/>
                <TextInput v-model="archivo.name" required/>
                <InputError class="mt-2" :message="archivo.errors.name" />
            </div>
            <div class="grid gap-1">
                <InputLabel value="Disponible"/>
                <Checkbox v-model:checked="archivo.avalible"></Checkbox>
                <InputError class="mt-2" :message="archivo.errors.avalible" />
            </div>
        </div> 
        <div class="flex gap-3">
            <div class="grid gap-1">
                <InputLabel value="Archivo"/>
                <input type="file" @change="checkFile">
                <InputError class="mt-2" :message="archivo.errors.file" />
                <InputError class="mt-2" :message="archivo.errors.filename" />
            </div>
        </div>
        <div class="flex flex-col gap-2 border-t pt-4" v-if="archivo._filename">
            <span class="font-bold">Archivo actual:</span>
            <div class="flex gap-2">
                <span class="self-center" :title="archivo._type">
                    <FontAwesomeIcon :icon="'fa fa-'+archivo._type !='' ? archivo._type :'file'" class="text-gray-500 text-2xl" />
                </span>
                <span class="bg-blue-100 rounded flex-1 p-2 overflow-hidden text-ellipsis">
                    {{ archivo._filename }}
                </span>
            </div>
        </div>
        <div class="flex flex-col gap-2 pt-4" v-if="archivo.filename">
            <span class="font-bold">Archivo a subir:</span>
            <div class="flex gap-2">
                <span class="self-center" :title="archivo.type">
                    <FontAwesomeIcon :icon="'fa fa-'+archivo.type !='' ? archivo.type :'file'" class="text-gray-500 text-2xl" />
                </span>
                <span class="bg-emerald-100 rounded flex-1 p-2 overflow-hidden text-ellipsis">
                    {{ archivo.filename }}
                </span>
            </div>
        </div>
    </div>
</template>