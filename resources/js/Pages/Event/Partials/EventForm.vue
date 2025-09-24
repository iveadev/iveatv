<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import axios from 'axios';
import { computed, onMounted, onUpdated, ref, watch } from 'vue';

const event = defineModel()

const files = ref([])

onMounted(()=> {
    axios.get(route('file.list'))
    .then((data)=>{
        files.value = data.data
    })
})

const selectedFile = computed(()=>{
    return files.value.find(f=> f.id == event.value.file_id)
})



</script>
<template>
    <div class="flex flex-col gap-2">
        <div class="flex gap-3">
            <div class="flex-1 flex flex-col gap-1">
                <InputLabel value="Archivo" />
                <select required class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full" placeholder="Seleccione" v-model="event.file_id">
                    <option disabled selected>Selecciones uno</option>
                    <option v-for="f in files" :value="f.id">{{ f.type }} -- {{ f.name }}</option>
                </select>
                <InputError :message="event.errors.file_id" />
            </div>
            <div class=" w-32 flex flex-col gap-1" v-if="selectedFile && selectedFile.type == 'image'">
                <InputLabel value="Duración" />
                <div class="flex gap-1">
                    <TextInput v-model="event.duration" type="number" step="5" min="0" class="w-5/6"/>
                    <span class="self-center">s.</span>
                </div>
                <InputError :message="event.errors.duration" />
            </div>
            <div class="w-20 flex-col gap-1" v-if="selectedFile && selectedFile.type == 'video'">
                <InputLabel value="Sonido"/>
                <div class="flex gap-4 pt-3">
                    <Checkbox v-model:checked="event.sound" class="self-center" />
                    <span class="self-center">{{ event.sound ? 'Sí' : 'No' }}</span>
                </div>
            </div>
            
        </div>
        <div class="flex gap-2">
            <div class="w-20 flex-col gap-1">
                <InputLabel value="Proyectar"/>
                <div class="flex gap-4 pt-3">
                    <Checkbox v-model:checked="event.visible" class="self-center" />
                    <span class="self-center">{{ event.visible ? 'Sí' : 'No' }}</span>
                </div>
            </div>
            <div class="flex-1 flex flex-col gap-1">
                <InputLabel value="Visible desde"/>
                <TextInput required v-model="event.visibleFrom" type="date"/>
                <InputError :message="event.errors.visibleFrom" />
            </div>

            <div class="flex-1 flex flex-col gap-1">
                <InputLabel value="Visible hasta"/>
                <TextInput required v-model="event.visibleTo" type="date"/>
                <InputError :message="event.errors.visibleTo" />
            </div>
        </div>
    </div>
</template>