export const keywordRoutes = [
  {
    path: "/keywords/:id",
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
    path: "/keywords/:id/edit",
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
