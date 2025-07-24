<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import axios from 'axios';
import { onMounted, ref } from 'vue';

const event = defineModel()

const files = ref([])

onMounted(()=> {
    axios.get(route('file.list'))
    .then((data)=>{
        files.value = data.data
    })
})

</script>
<template>
    <div class="grid gap-2">
        <div class="flex gap-2">
            <div class="flex-1 grid gap-1">
                <InputLabel value="Archivo" />
                <select required class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Seleccione" v-model="event.file_id">
                    <option disabled selected>Selecciones uno</option>
                    <option v-for="f in files" :value="f.id"><span class="font-bold">{{ f.title }}</span> ({{ f.type }} -- {{ f.filename }})</option>
                </select>
            </div>
             <div class="w-20 grid gap-1">
                <InputLabel value="Sonido"/>
                <Checkbox v-model:checked="event.sound"></Checkbox>
            </div>
            <div class="grid gap-1">
                <InputLabel value="Duración" />
                <div class="flex gap-3">
                    <TextInput v-model="event.duration" type="number" step="5" min="0"/>
                    <span class="self-center text-gray-500">segundos</span>
                </div>
            </div>
        </div>
        <div class="flex gap-2">
            <div class="w-20 grid gap-1">
                <InputLabel value="Visible"/>
                <Checkbox v-model:checked="event.visible"></Checkbox>
            </div>
            <div class="flex-1 grid gap-1">
                <InputLabel value="Visible desde"/>
                <TextInput required v-model="event.visibleFrom" type="datetime-local"/>
            </div>

            <div class="flex-1 grid gap-1">
                <InputLabel value="Visible hasta"/>
                <TextInput required v-model="event.visibleTo" type="datetime-local"/>
            </div>
        </div>
    </div>
</template>