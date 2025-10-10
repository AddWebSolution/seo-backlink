import { createRouter, createWebHistory } from 'vue-router/auto'
import { setupLayouts } from 'virtual:meta-layouts'
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

function recursiveLayouts(route) {
  if (route.children) {
    for (let i = 0; i < route.children.length; i++)
      route.children[i] = recursiveLayouts(route.children[i])

    return route
  }

  return setupLayouts([route])[0]
}

const router = createRouter({
  history: createWebHistory('/'),
  scrollBehavior(to) {
    if (to.hash) return { el: to.hash, behavior: 'smooth', top: 60 };
    return { top: 0 };
  },
  extendRoutes: pages => [
    ...redirects,
    ...[
      ...pages,
      ...clientRoutes,
      ...domainRoutes,
      ...rivalDomainRoutes,
      ...keywordRoutes,
      ...reportRoutes,
      ...dashboardRoutes
    ].map(route => recursiveLayouts(route)),
  ],
});

setupGuards(router);

export { router };
export default function (app) {
  app.use(router);
}
