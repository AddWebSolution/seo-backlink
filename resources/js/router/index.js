// import { createRouter, createWebHistory } from 'vue-router/auto'
// import { setupLayouts } from 'virtual:meta-layouts'
// import { setupGuards } from './guards';
// import { clientRoutes } from './modules/clients';
// import { userRoutes} from "@/router/modules/users.js";
// import { domainRoutes } from './modules/domains';
// import { rivalDomainRoutes } from './modules/rivalDomains';
// import { keywordRoutes } from './modules/keywords';
// import { reportRoutes } from './modules/reports';
// import { dashboardRoutes } from './modules/dashboards';
//
// export const redirects = [
//   { path: '/', name: 'index', redirect: { name: 'login' } },
// ];
//
// function recursiveLayouts(route) {
//   if (route.children) {
//     for (let i = 0; i < route.children.length; i++)
//       route.children[i] = recursiveLayouts(route.children[i])
//
//     return route
//   }
//
//   return setupLayouts([route])[0]
// }
//
// const router = createRouter({
//   history: createWebHistory('/'),
//   scrollBehavior(to) {
//     if (to.hash) return { el: to.hash, behavior: 'smooth', top: 60 };
//     return { top: 0 };
//   },
//   extendRoutes: pages => [
//     ...redirects,
//     ...[
//       ...pages,
//       ...clientRoutes,
//       ...userRoutes,
//       ...domainRoutes,
//       ...rivalDomainRoutes,
//       ...keywordRoutes,
//       ...reportRoutes,
//       ...dashboardRoutes
//     ].map(route => recursiveLayouts(route)),
//   ],
// });
//
// setupGuards(router);
//
// export { router };
// export default function (app) {
//   app.use(router);
// }
import { createRouter, createWebHistory } from 'vue-router/auto'
import { setupLayouts } from 'virtual:meta-layouts'
import { setupGuards } from './guards'

// Import all your manual module routes
import { clientRoutes } from './modules/clients'
import { userRoutes } from '@/router/modules/users.js'
import { domainRoutes } from './modules/domains'
import { rivalDomainRoutes } from './modules/rivalDomains'
import { keywordRoutes } from './modules/keywords'
import { reportRoutes } from './modules/reports'
import { dashboardRoutes } from './modules/dashboards'

/* -------------------------------------------
    Redirects
------------------------------------------- */
export const redirects = [
    { path: '/', name: 'index', redirect: { name: 'login' } },
]

/* -------------------------------------------
    Combine Manual Routes
------------------------------------------- */
const manualRoutes = [
    ...clientRoutes,
    ...userRoutes,
    ...domainRoutes,
    ...rivalDomainRoutes,
    ...keywordRoutes,
    ...reportRoutes,
    ...dashboardRoutes,
]

/* -------------------------------------------
   ⚙️ Create Router
------------------------------------------- */
const router = createRouter({
    history: createWebHistory('/'),

    scrollBehavior(to) {
        if (to.hash) return { el: to.hash, behavior: 'smooth', top: 60 }
        return { top: 0 }
    },

    extendRoutes: (autoRoutes) => {
        // Remove auto-generated routes that duplicate manual ones (by name or path)
        const filteredAuto = autoRoutes.filter(auto =>
            !manualRoutes.some(manual =>
                manual.name === auto.name || manual.path === auto.path
            )
        )

        // Apply layouts to both manual and auto routes
        const autoWithLayouts = setupLayouts(filteredAuto)
        const manualWithLayouts = setupLayouts(manualRoutes).map(route => {
            const original = manualRoutes.find(r => r.path === route.path)
            if (original?.meta) {
                route.meta = { ...route.meta, ...original.meta }
            }
            return route
        })

        // Final merged route list (no duplicates, with layouts + meta)
        const finalRoutes = [
            ...redirects,
            ...autoWithLayouts,
            ...manualWithLayouts,
        ]

        console.log(' Final Route Count:', finalRoutes.length)
        console.log(' Sample Route:', finalRoutes.find(r => r.path.includes('/users')))

        return finalRoutes
    },
})

/* -------------------------------------------
   🧠 Guards (CASL + Auth)
------------------------------------------- */
setupGuards(router)

export { router }

export default function install(app) {
    app.use(router)
}
