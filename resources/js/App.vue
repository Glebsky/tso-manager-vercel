<template>
    <div class="relative z-10 flex min-h-screen flex-col">
        <!-- Background ambient effects -->
        <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -left-40 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl"></div>
            <div class="absolute top-1/3 -right-20 w-80 h-80 bg-teal-500/8 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 left-1/3 w-96 h-96 bg-emerald-600/5 rounded-full blur-3xl"></div>
        </div>

        <main class="flex-1 w-full min-w-0 p-3 sm:p-6 md:p-8">
            <router-view v-slot="{ Component }">
                <transition name="page" mode="out-in">
                    <component :is="Component" />
                </transition>
            </router-view>
        </main>

        <!-- TOAST CONTAINER -->
        <div class="fixed top-5 right-5 z-[9999] max-w-sm w-full pointer-events-none px-4 sm:px-0">
            <transition-group name="toast" tag="div" class="flex flex-col gap-3">
                <div v-for="t in toasts" :key="t.id"
                     class="glass-card p-4 pointer-events-auto shadow-2xl transition-all duration-300 w-full"
                     :class="{
                         'border-emerald-500/30 bg-emerald-500/10': t.type === 'success',
                         'border-red-500/30 bg-red-500/10': t.type === 'error',
                         'border-amber-500/30 bg-amber-500/10': t.type === 'warning'
                     }">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                             :class="{
                                 'bg-emerald-500/20 text-emerald-400': t.type === 'success',
                                 'bg-red-500/20 text-red-400': t.type === 'error',
                                 'bg-amber-500/20 text-amber-400': t.type === 'warning'
                             }">
                            <svg v-if="t.type === 'success'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium pt-1.5"
                           :class="{
                               'text-emerald-300': t.type === 'success',
                               'text-red-300': t.type === 'error',
                               'text-amber-300': t.type === 'warning'
                           }">
                            {{ t.message }}
                        </p>
                    </div>
                </div>
            </transition-group>
        </div>
    </div>
</template>

<script setup>
import { toasts } from './toast';
</script>

<style>
[v-cloak] {
    display: none;
}

/* Toast Animations */
.toast-enter-active,
.toast-leave-active {
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.toast-enter-from {
    transform: translateX(120%) translateY(-20px);
    opacity: 0;
}
.toast-leave-to {
    transform: translateX(120%);
    opacity: 0;
}
.toast-leave-active {
    position: absolute;
    width: 100%;
}

/* Page Transition Animations */
.page-enter-active,
.page-leave-active {
    transition: opacity 0.15s ease;
}
.page-enter-from,
.page-leave-to {
    opacity: 0;
}
</style>
