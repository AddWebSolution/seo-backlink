export const keywordRoutes = [
  {
    path: "/keywords/add",
    name: "keyword-add",
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
    path: '/keywords/list',
    name: 'keyword-list',
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
    path: "/keywords/:id",
    name: "keyword-view",
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
    path: "/keywords/:id/edit",
    name: "keyword-edit",
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
