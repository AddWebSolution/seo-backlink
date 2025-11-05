export const clientRoutes = [
  {
    path: '/client/list',
    name: 'client-list',
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
    path: '/client/add',
    name: 'client-add',
    component: () => import('@/pages/apps/client/add/add.vue'),
    props: true,
    meta: {
      title: 'Client Add',
      action: 'create',
      subject: 'client',
      roles: [1]
    }
  },
  {
    path: "/client/:id",
    name: "client-view",
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
    path: "/client/:id/edit",
    name: "client-edit",
    component: () => import("@/pages/apps/client/edit/[id].vue"),
    props: true,
    meta: {
      title: "Client Edit",
      action: 'update',
      subject: 'client',
      roles: [1],
    }
  }
];
