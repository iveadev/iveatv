<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { useToasterStore } from '@/stores/notify';
import { computed } from 'vue';

const notifyStore = useToasterStore();
const toasts = computed(()=>notifyStore.getToasts);

const notificationClasses = (type)=>{
    let base ='rounded-md shadow-xl text-md text-white font-semi w-full';
    let color = ''
    switch (type) {
        case 'success':
            color = 'emerald'
            break;
        case 'danger':
            color = 'red'
            break;
        case 'warning':
            color = 'yellow'
            break;
        default:
            color = 'blue'
            break;
    }
    return base+' bg-'+color+'-500 hover:bg-'+color+'-500/80';
}
const notificationIcon = (type) => {
    let base = "fa fa-"
    let icon = ''
    switch(type){
        case 'success':
            icon = 'check-circle';
            break;
        case 'warning':
            icon = 'triangle-exclamation'
            break
        case 'danger':
            icon = 'circle-xmark'
            break
        default:
            icon='info-circle';
            break;
    }
    return base + icon;
}

const dismiss = (id) => {
    notifyStore.removeId(id);
}

</script>
<template>
    <Transition name="slide-fade">
    <div class="fixed bottom-0 right-0 w-full flex flex-col flex-col-reverse gap-3 items-end p-6" v-if="toasts.length > 0">
            <div v-for="toast in toasts">
                <div class="flex gap-4 px-2 py-4" :class="notificationClasses(toast.type)">
                    <font-awesome-icon :icon="notificationIcon(toast.type)" class="text-2xl self-center px-1"/>
                    <span class="self-center grow font-medium">{{ toast.message }}</span>
                    <button @click="dismiss(toast.id)">
                        <font-awesome-icon icon="fa-times" class="text-2xl px-2" />
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>
<style lang="css" scoped>
/*
  Enter and leave animations can use different
  durations and timing functions.
*/
.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateX(20px);
  opacity: 0;
}
</style>