import { createRouter, createWebHistory } from 'vue-router';
import { t } from './lang';

const routes = [
    {
        path: '/',
        name: 'market-public',
        component: () => import('./views/PublicMarketAnalytics.vue'),
        meta: { guest: true, publicLayout: true, rawTitle: 'TSO Market Analytics' },
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

router.afterEach((to) => {
    if (to.meta?.rawTitle) {
        document.title = to.meta.rawTitle;
    } else if (to.name === 'market-public') {
        document.title = 'TSO Market Analytics';
    } else if (to.meta?.titleKey) {
        const pageTitle = t(to.meta.titleKey);
        document.title = pageTitle ? `${pageTitle} · TSO Manager` : 'TSO Manager';
    } else {
        document.title = 'TSO Market Analytics';
    }
});

export default router;
