export const rivalDomainRoutes = [
  {
    path: "/client/:clientId/clientdomain/:domainId/rivaldomain/add",
    name: "apps-domain-clientdomain-rivaldomain-add",
    component: () => import("@/pages/apps/domain/clientdomain/[clientId]/rivaldomain/add/[domainId].vue"),
    props: true,
    meta: { title: "Add Rival Domain" }
  },
  {
    path: "/client/:clientId/clientdomain/:domainId/rivaldomain/list",
    name: "apps-domain-clientdomain-rivaldomain-list",
    component: () => import("@/pages/apps/domain/clientdomain/[clientId]/rivaldomain/list/[domainId].vue"),
    props: true,
    meta: { title: "Rival Domain List" }
  },
  {
    path: "/client/:clientId/clientdomain/:domainId/rivaldomain/:id/view",
    name: "apps-domain-clientdomain-rivaldomain-view",
    component: () => import("@/pages/apps/domain/clientdomain/[clientId]/rivaldomain/view/[id].vue"),
    props: true,
    meta: { title: "Rival Domain View" }
  },
  {
    path: "/rivalsdomains/:id",
    name: "apps-rivaldomain-view",
    component: () => import("@/pages/apps/rivaldomain/view/[id].vue"),
    props: true,
    meta: { title: "Rival Domain View" }
  },
  {
    path: "/rivaldomains/:id/edit",
    name: "apps-rivaldomain-edit",
    component: () => import("@/pages/apps/rivaldomain/edit/[id].vue"),
    props: true,
    meta: { title: "Rival Domain Edit" }
  },
];
