import { createRouter, createWebHistory } from 'vue-router';
import { setupGuards } from './guards';
import { clientRoutes } from './modules/clients';
import { domainRoutes } from './modules/domains';
import { rivalDomainRoutes } from './modules/rivalDomains';
import { keywordRoutes } from './modules/keywords';
import { reportRoutes } from './modules/reports';
import { dashboardRoutes } from './modules/dashboards';

export const redirects = [
  { path: '/', name: 'index', redirect: { name: 'login' } },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  scrollBehavior(to) {
    if (to.hash) return { el: to.hash, behavior: 'smooth', top: 60 };
    return { top: 0 };
  },
  extendRoutes: pages => [
    ...redirects,
    ...pages,
    ...clientRoutes,
    ...domainRoutes,
    ...rivalDomainRoutes,
    ...keywordRoutes,
    ...reportRoutes,
    ...dashboardRoutes
  ],
});

setupGuards(router);

export { router };
export default function (app) {
  app.use(router);
}
