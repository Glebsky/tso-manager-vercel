import { createRouter, createWebHistory } from 'vue-router';
import { t, locale } from './lang';

const SEO_TITLES = {
    ru: 'TSO Market Analytics — Цены, спрос и арбитраж The Settlers Online',
    uk: 'TSO Market Analytics — Ціни, попит та арбітраж The Settlers Online',
    en: 'TSO Market Analytics — Trade Prices, Demand & Arbitrage for The Settlers Online',
};

function getSeoTitle(langCode = locale) {
    return SEO_TITLES[langCode] || SEO_TITLES.en;
}

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

router.afterEach((to) => {
    if (to.meta?.rawTitle) {
        document.title = to.meta.rawTitle;
    } else if (to.name === 'market-public' || to.path === '/') {
        document.title = getSeoTitle();
    } else if (to.meta?.titleKey) {
        const pageTitle = t(to.meta.titleKey);
        document.title = pageTitle ? `${pageTitle} · TSO Manager` : getSeoTitle();
    } else {
        document.title = getSeoTitle();
    }
});

export default router;
