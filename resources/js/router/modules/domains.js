export const domainRoutes = [
  {
    path: "/domains/:id",
    name: "apps-domain-view",
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
    path: "/apps/domains/:id/edit",
    name: "apps-domain-edit",
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
    path: "/apps/client/:id?/clientdomain/add",
    name: "apps-domain-clientdomain-add",
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
    path: "/apps/client/:id?/clientdomain/list",
    name: "apps-domain-clientdomain-list",
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
    path: "/apps/:view?/clientdomain/:id?/history",
    name: "apps-domain-clientdomain-history",
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
    path: "/apps/client/:clientId?/clientdomain/:domainId?/view",
    name: "apps-domain-clientdomain-view",
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
