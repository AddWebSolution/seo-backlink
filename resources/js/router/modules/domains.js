export const domainRoutes = [
  {
    path: "/domains/:id",
    name: "apps-domain-view",
    component: () => import("@/pages/apps/domain/view/[id].vue"),
    props: true,
    meta: { title: "Domain View" }
  },
  {
    path: "/domains/:id/edit",
    name: "apps-domain-edit",
    component: () => import("@/pages/apps/domain/edit/[id].vue"),
    props: true,
    meta: { title: "Domain Edit" }
  },
  {
    path: "/client/:id?/clientdomain/add",
    name: "apps-clientdomain-add",
    component: () => import("@/pages/apps/clientdomain/add/[id].vue"),
    props: true,
    meta: { title: "Add Client Domain" }
  },
  {
    path: "/client/:id?/clientdomain/list",
    name: "apps-clientdomain-list",
    component: () => import("@/pages/apps/clientdomain/list/[id].vue"),
    props: true,
    meta: { title: "Client Domain List" }
  },
  {
    path: "/client/:clientId?/clientdomain/:domainId?/view",
    name: "apps-clientdomain-view",
    component: () => import("@/pages/apps/clientdomain/view/[domainId].vue"),
    props: true,
    meta: { title: "Client Domain View" }
  },
];
