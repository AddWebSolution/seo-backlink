export const keywordRoutes = [
  {
    path: "/apps/keywords/add",
    name: "apps-keyword-add",
    component: () => import("@/pages/apps/keyword/add/add.vue"),
    props: true,
    meta: {
      title: "Keyword Add",
      action: 'create',
      subject: 'keyword',
      roles: [1, 2],
    }
  },
  {
    path: '/apps/keywords/list',
    name: 'apps-keyword-list',
    component: () => import('@/pages/apps/keyword/list/index.vue'),
    props: true,
    meta: {
      title: 'Keyword List',
      action: 'view',
      subject: 'keyword',
      roles: [1]
    }
  },
  {
    path: "/apps/keywords/:id",
    name: "apps-keyword-view",
    component: () => import("@/pages/apps/keyword/view/[id].vue"),
    props: true,
    meta: {
      title: "Keyword View",
      action: 'view',
      subject: 'keyword',
      roles: [1, 2],
    }
  },
  {
    path: "/apps/keywords/:id/edit",
    name: "apps-keyword-edit",
    component: () => import("@/pages/apps/keyword/edit/[id].vue"),
    props: true,
    meta: {
      title: "Keyword Edit",
      action: 'update',
      subject: 'keyword',
      roles: [1, 2],
    }
  },
];
