<template>
    <div class="min-h-full">
        <router-view />

        <!-- TOAST CONTAINER -->
        <div class="fixed top-5 right-5 z-[9999] max-w-sm w-full pointer-events-none px-4 sm:px-0">
            <transition-group name="toast" tag="div" class="flex flex-col gap-3">
                <div v-for="toast in toasts" :key="toast.id"
                     class="glass-card p-4 pointer-events-auto shadow-2xl transition-all duration-300 w-full"
                     :class="{
                         'border-emerald-500/30 bg-emerald-500/10': toast.type === 'success',
                         'border-red-500/30 bg-red-500/10': toast.type === 'error',
                         'border-amber-500/30 bg-amber-500/10': toast.type === 'warning'
                     }">
                    <p class="text-sm text-white/90 leading-relaxed break-words">{{ toast.message }}</p>
                </div>
            </transition-group>
        </div>
    </div>
</template>

<script>
import { toasts } from './toast';

export default {
    name: 'App',
    setup() {
        return { toasts };
    },
};
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s ease;
}

.toast-enter-from,
.toast-leave-to {
    opacity: 0;
    transform: translateX(1.5rem);
}
</style>
