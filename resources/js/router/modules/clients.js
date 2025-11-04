export const clientRoutes = [
  {
    path: '/apps/client/list',
    name: 'apps-client-list',
    component: () => import('@/pages/apps/client/list/index.vue'),
    props: true,
    meta: {
      title: 'Client List',
      action: 'view',
      subject: 'client',
      roles: [1]
    }
  },
  {
    path: '/apps/client/add',
    name: 'apps-client-add',
    component: () => import('@/pages/apps/client/add/index.vue'),
    props: true,
    meta: {
      title: 'Client Add',
      action: 'create',
      subject: 'client',
      roles: [1]
    }
  },
  {
    path: "/apps/client/:id",
    name: "apps-client-view",
    component: () => import("@/pages/apps/client/view/[id].vue"),
    props: true,
    meta: {
      title: "Client View",
      action: 'view',
      subject: 'client',
      roles: [1],
    }
  },
  {
    path: "/apps/client/:id/edit",
    name: "apps-client-edit",
    component: () => import("@/pages/apps/client/edit/[id].vue"),
    props: true,
    meta: {
      title: "Client Edit",
      action: 'update',
      subject: 'client',
      roles: [1],
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
