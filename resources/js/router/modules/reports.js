export const reportRoutes = [
  {
    path: "/report/:id",
    name: "apps-report-view",
    component: () => import("@/pages/apps/report/view/[id].vue"),
    props: true,
    meta: {
      title: "Report View",
      action: 'view',
      subject: 'report',
      roles: [1, 2],
    }
  },
  {
    path: "/report/list",
    name: "apps-report-list",
    component: () => import("@/pages/apps/report/list/index.vue"),
    props: true,
    meta: {
      title: "Report List",
      action: 'view',
      subject: 'report',
      roles: [1, 2],
    }
  },
  {
    path: "/report/backlink/:id",
    name: "apps-report-backlink-view",
    component: () => import("@/pages/apps/report/backlink/view/[id].vue"),
    props: true,
    meta: {
      title: "Backlink Report View",
      action: 'view',
      subject: 'report',
      roles: [1, 2],
    }
  },


  // Keyword Report //

  {
    path: "/keywordreport/list",
    name: "apps-keywordreport-list",
    component: () => import("@/pages/apps/keywordreport/list/index.vue"),
    props: true,
    meta: {
      title: "Keyword Report List",
      action: 'view',
      subject: 'keywordreport',
      roles: [1, 2],
    }
  },
  {
    path: "/keywordreport/:id",
    name: "apps-keywordreport-view",
    component: () => import("@/pages/apps/keywordreport/view/[id].vue"),
    props: true,
    meta: {
      title: "Keyword Report View",
      action: 'view',
      subject: 'keywordreport',
      roles: [1, 2],
    }
  },
  {
    path: "/keywordreport/keyword/:id",
    name: "apps-keywordreport-keyword-view",
    component: () => import("@/pages/apps/keywordreport/keyword/view/[id].vue"),
    props: true,
    meta: {
      title: "Keyword Report Detail",
      action: 'view',
      subject: 'keywordreport',
      roles: [1, 2],
    }
  },
];
