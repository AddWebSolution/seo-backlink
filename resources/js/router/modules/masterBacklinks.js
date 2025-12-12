export const masterBacklinksRoutes = [

  {
    path: "/master-backlinks/list",
    name: "master-backlinks-list",
    component: () => import("@/pages/apps/master-backlinks/list/index.vue"),
    props: true,
    meta: {
      title: 'Master Backlinks',
      action: 'view',
      subject: 'report',
      roles: [1],
    },
  },
];
