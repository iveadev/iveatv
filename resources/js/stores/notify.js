import { defineStore } from "pinia";

export const useToasterStore = defineStore("toaster-store", {
  state: () => ({
    toasts: [],
  }),
  getters:{
    getToasts(){
        return this.toasts
    }
  },
  actions: {
    notify(message='Se registró correctamente.', type ='success'){
        const toast = {
            message,
            type,
            id: Math.random() * 1000,
        }
        this.toasts.push(toast);

      setTimeout(() => {
        this.removeId(toast.id)
      }, 3000);
    },
    removeId(id) {
        this.toasts = this.toasts.filter((t) => t.id !== id);
    }
  },
});