export const domainRoutes = [
  {
    path: "/domains/list",
    name: "domain-list",
    component: () => import("@/pages/apps/domain/list/index.vue"),
    props: true,
    meta: {
      title: "Domain List",
      action: "view",
      subject: "clientdomain",
      roles: [1]
    }
  },
  {
    path: "/domains/:id",
    name: "domain-view",
    component: () => import("@/pages/apps/domain/view/[id].vue"),
    props: true,
    meta: {
      title: "Domain View",
      action: "view",
      subject: "clientdomain",
      roles: [1, 2]
    }
  },
  {
    path: "/domains/:id/edit",
    name: "domain-edit",
    component: () => import("@/pages/apps/domain/edit/[id].vue"),
    props: true,
    meta: {
      title: "Domain Edit",
      action: "update",
      subject: "clientdomain",
      roles: [1]
    }
  },
  {
    path: "/client/:id?/clientdomain/add",
    name: "domain-clientdomain-add",
    component: () => import("@/pages/apps/domain/clientdomain/add/[id].vue"),
    props: true,
    meta: {
      title: "Add Client Domain",
      action: 'create',
      subject: 'client',
      roles: [1],
    }
  },
  {
    path: "/client/:id?/clientdomain/list",
    name: "domain-clientdomain-list",
    component: () => import("@/pages/apps/domain/clientdomain/list/[id].vue"),
    props: true,
    meta: {
      title: "Client Domain List",
      action: 'view',
      subject: 'client',
      roles: [1],
    }
  },
  {
    path: "/:view?/clientdomain/:id?/history",
    name: "domain-clientdomain-history",
    component: () => import("@/pages/apps/clientdomain/history/[id].vue"),
    props: true,
    meta: {
      title: "Client Domain History",
      action: 'create',
      subject: 'client',
      roles: [1],
    }
  },
  {
    path: "/client/:clientId?/clientdomain/:domainId?/view",
    name: "domain-clientdomain-view",
    component: () => import("@/pages/apps/domain/clientdomain/view/[domainId].vue"),
    props: true,
    meta: {
      title: "Client Domain View",
      action: 'create',
      subject: 'client',
      roles: [1],
    }
  },
];
