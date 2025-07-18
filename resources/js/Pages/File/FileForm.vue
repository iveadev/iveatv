<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const archivo = defineModel();

const checkFile = (evt) => {
    const f = evt.target.files[0]
    if(f){
        archivo.value.filename = f.name.replaceAll(' ', '');
        archivo.value.file = f
    }
}
</script>
<template>
    <div class="grid gap-3">
        <div class="flex gap-3">
            <div class="flex-1 grid gap-1">
                <InputLabel value="Título"/>
                <TextInput v-model="archivo.title" required/>
                <InputError class="mt-2" :message="archivo.errors.title" />
            </div>
            <div class="grid gap-1">
                <InputLabel value="Disponible"/>
                <Checkbox v-model:checked="archivo.avalible"></Checkbox>
                <InputError class="mt-2" :message="archivo.errors.avalible" />
            </div>
        </div>
        <div class="grid gap-1">
            <InputLabel value="Archivo"/>
            <input type="file" required @change="checkFile">
            <InputError class="mt-2" :message="archivo.errors.file" />
            <InputError class="mt-2" :message="archivo.errors.filename" />
        </div>
        <div class="flex gap-3 border-t pt-4" v-if="archivo.id">
            <span class="font-bold self-center">Archivo actual:</span>
            <span class="bg-blue-200 rounded flex-1 px-5 py-2">
                {{ archivo.filename }}
            </span>
        </div>
    </div>
</template>