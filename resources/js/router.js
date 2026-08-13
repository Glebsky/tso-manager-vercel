import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: '/',
        name: 'market-public',
        component: () => import('./views/PublicMarketAnalytics.vue'),
        meta: { guest: true, publicLayout: true },
    },
    {
        path: '/market/public',
        redirect: '/',
    },
    {
        path: '/public/market',
        redirect: '/',
    },
    {
        path: '/:pathMatch(.*)*',
        redirect: '/',
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
