export const clientRoutes = [
  {
    path: "/clients/:id",
    name: "apps-client-view",
    component: () => import("@/pages/apps/client/view/[id].vue"),
    props: true,
    meta: { title: "Client View" }
  },
  {
    path: "/clients/:id/edit",
    name: "apps-client-edit",
    component: () => import("@/pages/apps/client/edit/[id].vue"),
    props: true,
    meta: { title: "Client Edit" }
  },
  {
    path: "/client/:id?/clientdomain/add",
    name: "apps-domain-clientdomain-add",
    component: () => import("@/pages/apps/domain/clientdomain/add/[id].vue"),
    props: true,
    action: 'create',
    subject: 'client',
    meta: { title: "Add Client Domain" }
  },
  {
    path: "/client/:id?/clientdomain/list",
    name: "apps-domain-clientdomain-list",
    component: () => import("@/pages/apps/domain/clientdomain/list/[id].vue"),
    props: true,
    action: 'create',
    subject: 'client',
    meta: { title: "Client Domain List" }
  },
  {
    path: "/client/:id?/clientdomain/history",
    name: "apps-domain-clientdomain-history",
    component: () => import("@/pages/apps/clientdomain/history/[id].vue"),
    props: true,
    action: 'create',
    subject: 'client',
    meta: { title: "Client Domain History" }
  },
  {
    path: "/client/:clientId?/clientdomain/:domainId?/view",
    name: "apps-domain-clientdomain-view",
    component: () => import("@/pages/apps/domain/clientdomain/view/[domainId].vue"),
    props: true,
    action: 'create',
    subject: 'client',
    meta: { title: "Client Domain View" }
  },
];
